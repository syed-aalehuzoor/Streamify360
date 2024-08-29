import os
import logging
import mysql.connector
from mysql.connector import Error
from dotenv import load_dotenv
from argparse import ArgumentParser
from paramiko import SSHClient, AutoAddPolicy, RSAKey
import re
from time import sleep, time

# Global Constants
HLS_BASE_DIR = "/var/www/html/streams/"
QUALITIES = ["360p", "480p", "720p"]
VIDEO_SETTINGS = {
    "360p": "640x360",
    "480p": "854x480",
    "720p": "1280x720",
}
BITRATES = {
    "360p": "400k",
    "480p": "700k",
    "720p": "1200k",
}
BANDWIDTHS = {
    "360p": "400000",
    "480p": "700000",
    "720p": "1200000",
}

# Initialize logging
script_dir = os.path.dirname(os.path.abspath(__file__))
logging.basicConfig(filename=os.path.join(script_dir, 'output.log'), level=logging.INFO,
                    format='%(asctime)s - %(levelname)s - %(message)s')

# Load environment variables
load_dotenv(os.path.join(script_dir, '../.env'))

# Argument Parsing
parser = ArgumentParser(description="Script to process Video.")
parser.add_argument('--id', type=str, required=True, help="Video Id")
args = parser.parse_args()
video_id = args.id

logging.info(f"Video Id: {video_id}")

# Establish a connection to the MySQL database
try:
    connection = mysql.connector.connect(
        host=os.getenv('DB_HOST'),
        user=os.getenv('DB_USERNAME'),
        password=os.getenv('DB_PASSWORD'),
        database=os.getenv('DB_DATABASE')
    )
    if connection.is_connected():
        logging.info("Successfully connected to the database")
except Error as e:
    logging.error(f"Database error: {e}")
    raise

cursor = connection.cursor(dictionary=True)

try:
    # Fetch video data from the database
    query_video = "SELECT id, userid, serverid, video_filepath, thumbnail_url, subtitle_filepath, logo_filepath FROM videos WHERE id = %s"
    cursor.execute(query_video, (video_id,))
    video = cursor.fetchone()

    if not video:
        logging.warning(f"No video found with ID: {video_id}")
        raise Exception(f"No video found with ID: {video_id}")

    # Fetch server data
    query_server = "SELECT ip, ssh_port, username, domain, status, type FROM servers WHERE id = %s"
    cursor.execute(query_server, (video['serverid'],))
    storage_server = cursor.fetchone()

    if not storage_server:
        logging.warning(f"No server found with ID: {video['serverid']}")
        raise Exception(f"No server found with ID: {video['serverid']}")

    # Get encoding server
    query_user_server = """
    SELECT id, ip, ssh_port, username, domain, status, total_videos
    FROM servers
    WHERE type = 'encoder' AND public_userid = %s
    """
    cursor.execute(query_user_server, (video['userid'],))
    user_specific_servers = cursor.fetchall()

    encoding_server = None
    if user_specific_servers:
        encoding_server = min(user_specific_servers, key=lambda x: x['total_videos'])
    else:
        query_public_servers = """
        SELECT id, ip, ssh_port, username, status, total_videos
        FROM servers
        WHERE type = 'encoder' AND public_userid = 'public'
        """

        start_time = time()
        while time() - start_time < 60:
            cursor.execute(query_public_servers)
            encoder_servers = cursor.fetchall()

            if not encoder_servers:
                raise Exception("No encoder servers available")

            suitable_servers = [server for server in encoder_servers if server['total_videos'] < 10]
            if suitable_servers:
                encoding_server = min(suitable_servers, key=lambda x: x['total_videos'])
                break

            sleep(5)
        if not encoding_server:
            raise Exception("No suitable encoding server found")

    private_key_path = os.path.join(script_dir, 'id_rsa')

    with SSHClient() as client:
        client.set_missing_host_key_policy(AutoAddPolicy())
        key = RSAKey.from_private_key_file(filename=private_key_path)
        client.connect(hostname=encoding_server['ip'], port=encoding_server['ssh_port'], username=encoding_server['username'], pkey=key,
                       allow_agent=False, look_for_keys=False)

        # Update video status to 'Connecting to Server'
        update_query = "UPDATE videos SET status = %s WHERE id = %s"
        cursor.execute(update_query, ('Getting Ready', video_id))
        connection.commit()

        # Process video
        video_key = video['id']
        input_file = os.path.join(script_dir, '..', 'storage', 'app', video['video_filepath'])
        video_name = f'{video_key}{os.path.splitext(input_file)[1]}'
        hls_video_dir = f'{HLS_BASE_DIR}{video_key}'
        domains = [f"player{i:02d}.{storage_server['domain']}" for i in range(1, 11)]

        with client.open_sftp() as sftp:
            sftp.put(localpath=input_file, remotepath=f'/home/ubuntu/{video_name}')
            sftp.chdir(HLS_BASE_DIR)
            sftp.mkdir(video_key)

        # Get video duration
        cmd = f'ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 /home/ubuntu/{video_name}'
        stdin, stdout, stderr = client.exec_command(cmd)
        total_duration = float(stdout.readline())

        for quality in QUALITIES:
            cursor.execute(update_query, (f'Processing {quality} Started...', video_id))
            connection.commit()
            sleep(1)
            quality_dir = f'{hls_video_dir}/{quality}'
            client.exec_command(f'mkdir -p {quality_dir}')
            command = f"ffmpeg -y -i /home/ubuntu/{video_name} -vf scale={VIDEO_SETTINGS[quality]} -map 0 -b:v {BITRATES[quality]} -c:a aac -b:a 128k -crf 27 -preset veryfast -hls_time 10 -hls_list_size 0 -hls_segment_filename {quality_dir}/%03d.ts {quality_dir}/playlist.m3u8"
            stdin, stdout, stderr = client.exec_command(command=command)

            pattern = re.compile(r"time=(\d+:\d+:\d+\.\d+)")
            while True:
                if stdout.channel.recv_ready():
                    output = stdout.channel.recv(1024).decode()
                    print(output, end='')
                if stderr.channel.recv_stderr_ready():
                    line = stderr.channel.recv_stderr(1024).decode()
                    match = pattern.search(line)
                    if match:
                        current_time = sum(x * float(t) for x, t in zip([3600, 60, 1, 0.01], match.group(1).split(':')))
                        progress = (current_time / total_duration) * 100
                        cursor.execute(update_query, (f'Processing {quality}: {progress:.2f}%', video_id))
                        connection.commit()
                if stdout.channel.exit_status_ready():
                    if stdout.channel.recv_exit_status() == 0:
                        cursor.execute(update_query, (f'Processing {quality}: 100%', video_id))
                        connection.commit()
                    break

            with client.open_sftp() as sftp:
                ts_files = sftp.listdir_attr(quality_dir)
                for ts_file in ts_files:
                    if ts_file.filename.endswith('ts'):
                        ts_path = f'{quality_dir}/{ts_file.filename}'
                        html_path = ts_path.replace(".ts", ".html")
                        sftp.rename(oldpath=ts_path, newpath=html_path)

                m3u8_file = f'{quality_dir}/playlist.m3u8'
                with sftp.open(filename=m3u8_file, mode='w+') as m3u8:
                    m3u8.write("#EXTM3U\n")
                    m3u8.write("#EXT-X-VERSION:3\n")
                    m3u8.write("#EXT-X-PLAYLIST-TYPE:VOD\n")
                    m3u8.write("#EXT-X-TARGETDURATION:10\n")

                    html_sequence = 0
                    for html_file in sorted(sftp.listdir(quality_dir)):
                        if html_file.endswith(".html"):
                            domain = domains[html_sequence % len(domains)]
                            m3u8.write(f"#EXTINF:10.000000,\n")
                            m3u8.write(f"https://{domain}/streams/{video_key}/{quality}/{html_file}\n")
                            html_sequence += 1

                    total_duration = html_sequence * 10
                    m3u8.write("#EXT-X-ENDLIST\n")
                    m3u8.write(f"#EXT-X-TOTALDURATION:{total_duration}\n")

        master_m3u8 = f'{hls_video_dir}/master.m3u8'
        with client.open_sftp() as sftp:
            with sftp.open(filename=master_m3u8, mode='w+') as master:
                master.write("#EXTM3U\n")
                for quality in QUALITIES:
                    master.write(f"#EXT-X-STREAM-INF:BANDWIDTH={BANDWIDTHS[quality]},RESOLUTION={VIDEO_SETTINGS[quality]}\n")
                    master.write(f"https://play.{storage_server['domain']}/streams/{video_key}/{quality}/playlist.m3u8\n")

        client.exec_command(f'scp -i /home/root/id_rsa {hls_video_dir} username@remote_server:{hls_video_dir}')

        final_master_manifest_url = f"https://play.{storage_server['domain']}/streams/{video_key}/master.m3u8"
        cursor.execute(update_query, ('live', video_id))
        update_query = "UPDATE videos SET manifest_url = %s WHERE id = %s"
        cursor.execute(update_query, (final_master_manifest_url, video_id))
        connection.commit()

        os.remove(input_file)

        # Transfer the processed video to the storage server

        try:
            client.exec_command(f'scp -i /home/ubuntu/id_rsa -r {hls_video_dir} {storage_server['username']}@{storage_server['ip']}:{hls_video_dir}')

            logging.info(f"Transferred video {video_key} to storage server at {storage_server['domain']}")
        finally:
            logging.info("Storage server SSH connection closed")

except Exception as e:
    logging.error(f"Error: {e}", exc_info=True)
    cursor.execute(update_query, ('Error occurred during processing', video_id))
    connection.commit()
finally:
    if cursor:
        cursor.close()
    if connection:
        connection.close()
    logging.info("MySQL connection is closed")
