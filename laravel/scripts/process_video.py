from azure.batch import models, BatchServiceClient
from azure.common.credentials import ServicePrincipalCredentials
from time import sleep
import os
import wget
import mysql.connector
from dotenv import load_dotenv
from azure.storage.blob import ContainerSasPermissions, BlobSasPermissions, generate_blob_sas, generate_container_sas, BlobServiceClient
import datetime
from concurrent.futures import ThreadPoolExecutor
from pytubefix import YouTube
from paramiko import SSHClient, AutoAddPolicy, RSAKey
from urllib.parse import urlparse
import gdown
import http.client
import json
import traceback

script_dir = os.path.dirname(os.path.abspath(__file__))

thumbnail_dir = os.path.join(script_dir, '../storage/app/public/thumbnails')
thumbnail_dir = os.path.normpath(thumbnail_dir)  # Normalize the path

image_extensions = ['jpg', 'png', 'jpeg', 'gif', 'bmp', 'tiff', 'webp']  # Add more extensions if needed

load_dotenv(os.path.join(script_dir, '../.env'))

STORAGE_ACCOUNT_NAME = os.getenv('AZURE_STORAGE_NAME')
STORAGE_ACCOUNT_KEY = os.getenv('AZURE_STORAGE_KEY')
CONTAINER_NAME = os.getenv('AZURE_STORAGE_CONTAINER')

allowed_threads = {
    'basic': os.getenv('BASIC_PLAN_THREADS'),
    'premium': os.getenv('PREMIUM_PLAN_THREADS'),
    'enterprise': os.getenv('ENTERPRISE_PLAN_THREADS')
}

def is_youtube_url(url):
    parsed_url = urlparse(url)
    return 'youtube.com' in parsed_url.netloc or 'youtu.be' in parsed_url.netloc

def is_google_drive_url(url):
    return 'drive.google.com' in url

def is_direct_mp4_url(url):
    return urlparse(url).scheme in ('http', 'https') and url.lower().endswith('.mp4')

def upload_to_blob(local_filepath, blob_name):
    blob_service_client = BlobServiceClient(account_url=f"https://{STORAGE_ACCOUNT_NAME}.blob.core.windows.net/", credential=STORAGE_ACCOUNT_KEY)
    container_client = blob_service_client.get_container_client(CONTAINER_NAME)
    blob_client = container_client.get_blob_client(blob=blob_name)
    with open(local_filepath, 'rb') as data:   
        blob_client.upload_blob(data=data)
    if os.path.exists(local_filepath):
        os.remove(local_filepath)

def download_from_youtube(video_key, url):
    try:
        print(f'Downloading Video From Youtube for Video: {video_key}')
        output_path = os.path.join(script_dir, f'temps/{video_key}.mp4')

        conn = http.client.HTTPSConnection("youtube86.p.rapidapi.com")

        payload = f'{{"url":"{url}"}}'

        headers = {
            'x-rapidapi-key': "b869ea41d5msha6e0cb4c347213bp1692efjsne76c58f4eeff",
            'x-rapidapi-host': "youtube86.p.rapidapi.com",
            'Content-Type': "application/json",
            'X-Forwarded-For': "70.41.3.18"
        }

        conn.request("POST", "/api/youtube/links", payload, headers)
        res = conn.getresponse()
        data = res.read()
        json_data = json.loads(data.decode("utf-8"))  # Parse the JSON

        for video in json_data:
            video_url = None
            qualities = []
            for url in video['urls']:
                if url['extension'] == 'mp4' and url.get('audioCodec') and url['audioCodec'] == 'mp4a' :
                    qualities.append(url['quality'])

            if qualities:  # Check if qualities list is not empty
                highest_quality = max(qualities)
                for url in video['urls']:
                    if url['extension'] == 'mp4' and url['quality'] == highest_quality:
                        video_url = url['url']
                        input_file = wget.download(url=video_url, out=output_path)
                        local_filepath = os.path.join(output_path, input_file)    
                        blob_name = f'videos/{video_key}.mp4'
                        upload_to_blob(local_filepath=local_filepath, blob_name=blob_name)
                        return blob_name
    except Exception as e:
        print(e)
        print(traceback.print_exc())
        return None

def download_from_drive(video_key, url):
    try:
        print(f'Downloading Video From Drive for Video: {video_key}')
        filename = gdown.download(url=url, quiet=True, fuzzy=True)
        local_filepath = os.path.join(script_dir, filename)
        blob_name = f'videos/{video_key}.mp4'
        upload_to_blob(local_filepath=local_filepath, blob_name=blob_name)
        return blob_name
    except:
        return None

def download_Direct_mp4(video_key, url):
    try:
        print(f'Downloading Mp4 Video for Video: {video_key}')
        output_path = os.path.join(script_dir, f'temps')
        input_file = wget.download(url=url, out=output_path)
        local_filepath = os.path.join(output_path, input_file)
        blob_name = f'videos/{video_key}.mp4'
        upload_to_blob(local_filepath=local_filepath, blob_name=blob_name)
        return blob_name
    except:
        return None

def create_sas_url(blob_name=None, container_name=None, expire_after: int = 24):
    start_time = datetime.datetime.now(datetime.timezone.utc)
    expiry_time = start_time + datetime.timedelta(hours=expire_after)
    if blob_name:
        sas_token = generate_blob_sas(
            account_name=STORAGE_ACCOUNT_NAME,
            container_name=CONTAINER_NAME,
            blob_name=blob_name,
            account_key=STORAGE_ACCOUNT_KEY,
            permission=BlobSasPermissions(read=True),
            expiry=expiry_time,
            start=start_time
        )
        return f"https://{STORAGE_ACCOUNT_NAME}.blob.core.windows.net/{CONTAINER_NAME}/{blob_name}?{sas_token}"

    elif container_name:
        
        sas_token = generate_container_sas(
            account_name=STORAGE_ACCOUNT_NAME,
            container_name=CONTAINER_NAME,
            account_key=STORAGE_ACCOUNT_KEY,
            permission=ContainerSasPermissions(write=True),
            expiry=expiry_time,
            start=start_time
        )
        return f"https://{STORAGE_ACCOUNT_NAME}.blob.core.windows.net/{CONTAINER_NAME}?{sas_token}"
    else:
         return None


# MySQL Connection setup
connection = mysql.connector.connect(
    host=os.getenv('DB_HOST'),
    user=os.getenv('DB_USERNAME'),
    password=os.getenv('DB_PASSWORD'),
    database=os.getenv('DB_DATABASE')
)

cursor = connection.cursor(dictionary=True)

# Query to get videos that need processing
initiated_video_query = "SELECT id, userid, serverid, video_url, thumbnail_url, subtitle_url, logo_url FROM videos WHERE status = 'Initiated'"
deleted_video_query = "SELECT id, userid, serverid, video_url, thumbnail_url, subtitle_url, logo_url FROM videos WHERE status = 'Deleted'"

TENANT_ID = os.getenv('AZURE_TENANT_ID')
RESOURCE = "https://batch.core.windows.net/"
CLIENT_ID = os.getenv('AZURE_CLIENT_ID')
SECRET = os.getenv('AZURE_CLIENT_SECRET')
BATCH_ACCOUNT_URL = "https://hlsencoder.eastus.batch.azure.com"

credentials = ServicePrincipalCredentials(
    client_id=CLIENT_ID,
    secret=SECRET,
    tenant=TENANT_ID,
    resource=RESOURCE
)

# Initialize Azure Batch Client with correct credentials
batch_client = BatchServiceClient(credentials, batch_url=BATCH_ACCOUNT_URL)

def get_file_extension(file_url):
    """
    Helper function to get the file extension from the URL.
    """
    return os.path.splitext(file_url)[1]  # Get the file extension from the URL

def post_task(video, server):
    """
    Function to post a task to the Azure Batch service for processing a video.
    """
    try:
        # Extract data from the video record
        id = video['id']
        subtitle_url = video['subtitle_url']
        logo_url = video['logo_url']
        domain = server['domain']
        userplan_query = "SELECT userplan FROM users WHERE id = %s"
        cursor.execute(userplan_query, (video['userid'],))
        user = cursor.fetchone()
        userplan = user['userplan']
        threads = allowed_threads[userplan]
        subtitle_ext = get_file_extension(subtitle_url) if subtitle_url else ''
        logo_ext = get_file_extension(logo_url) if logo_url else ''

        # Create dynamic job_id and file names based on the video ID
        job_id = f"job1"
        subtitle_filename = f"subtitle-{id}{subtitle_ext}" if subtitle_ext else ''
        logo_filename = f"logo-{id}{logo_ext}" if logo_ext else ''

        # Create resource files list dynamically
        resource_files = []
        resource_files.append(
                 models.ResourceFile(
                      http_url=create_sas_url(blob_name=f'run.py'),
                      file_path=f'run.py'
            ))
        if is_youtube_url(video['video_url']):
            blob_name = download_from_youtube(video_key=id, url=video['video_url'])
        elif is_google_drive_url(video['video_url']):
            blob_name = download_from_drive(video_key=id, url=video['video_url'])
        elif is_direct_mp4_url(video['video_url']):
            blob_name = download_Direct_mp4(video_key=id, url=video['video_url'])
        else:
            blob_name = video['video_url']
            
        if blob_name is None:
            # Update video status in the database to 'Processing' after posting job
            update_query = "UPDATE videos SET status = 'Failed' WHERE id = %s"
            cursor.execute(update_query, (id,))
            connection.commit()
            return
        extension = os.path.splitext(blob_name)[1]
        video_filename = f"video-{id}{extension}"

        resource_files.append(
                models.ResourceFile(
                    http_url=create_sas_url(blob_name=blob_name),
                    file_path=video_filename
        ))

        # Build the command line
        command = f'python3 run.py --key {id} --domain {domain} --max_workers {threads} --video {video_filename}'
        if logo_url:
            resource_files.append(models.ResourceFile(
                http_url=create_sas_url(blob_name=video['logo_url']),
                file_path=logo_filename
            ))
            command += f' --logo {logo_filename}'

        if subtitle_url:
            resource_files.append(models.ResourceFile(
                http_url=create_sas_url(blob_name=video['subtitle_url']),
                file_path=subtitle_filename
            ))
            command += f' --subtitle {subtitle_filename}'
        container_sas_url = create_sas_url(container_name=CONTAINER_NAME)
        output_files = [models.OutputFile(
             file_pattern=f'{id}.zip',
             destination=models.OutputFileDestination(
                  container=models.OutputFileBlobContainerDestination(
                       container_url=container_sas_url,
                       path=f'out_videos/{id}.zip'
                  )
             ),
             upload_options=models.OutputFileUploadOptions(
                  upload_condition=models.OutputFileUploadCondition.task_completion
             )
             )]

        # Create the task with dynamically generated file names
        task = models.TaskAddParameter(
            id=id,
            command_line=command,  # Single string with command line
            resource_files=resource_files,
            output_files=output_files,
        )

        print(f"Adding task to job {job_id}")
        batch_client.task.add(job_id, task)

        # Update video status in the database to 'Processing' after posting job
        update_query = "UPDATE videos SET status = 'Processing' WHERE id = %s"
        cursor.execute(update_query, (id,))
        connection.commit()

    except Exception as e:
        print(f"An error occurred while posting job {id}: {e}")
        # Update video status in the database to 'Processing' after posting job
        update_query = "UPDATE videos SET status = 'Failed' WHERE id = %s"
        cursor.execute(update_query, (id,))
        connection.commit()

def move_video_and_delete_task():
    mover_batch_client = BatchServiceClient(credentials, batch_url=BATCH_ACCOUNT_URL)
    while True:
        try:
            print('Checking For Pending Tasks...')
            # Fetch the completed tasks from Azure Batch
            job_id = 'job1'  # Assuming a fixed job_id, this could be made dynamic if needed
            tasks = mover_batch_client.task.list(job_id=job_id)
            for task in tasks:
                if task.state == models.TaskState.completed:
                    try:
                        # Extract video ID from the task ID (task IDs are in the format render-task-{video_id})
                        task_id = task.id
                        video_id = task_id

                        # Download the processed video from Blob Storage
                        blob_name = f'out_videos/{video_id}.zip'
                        download_url = f"https://{STORAGE_ACCOUNT_NAME}.blob.core.windows.net/{CONTAINER_NAME}/{blob_name}"
                        # Fetch server details from the database for this video
                        cursor.execute("SELECT name, ip, ssh_port, username, domain FROM servers WHERE id = (SELECT serverid FROM videos WHERE id = %s)", (video_id,))
                        server = cursor.fetchone()

                        if server:
                            # Transfer the video to the server
                            remote_path = f"/var/www/html/streams/"
                            ssh_client = SSHClient()
                            ssh_client.set_missing_host_key_policy(AutoAddPolicy)
                            pkey = RSAKey.from_private_key_file('streamify360.pem')

                            ssh_client.connect(server['ip'], port=server['ssh_port'], username=server['username'], pkey=pkey, allow_agent=False, look_for_keys=False)

                            print(f'Downloading file: {download_url}')

                            # Download file
                            if ssh_client.exec_command(f'sudo wget -P "{remote_path}" "{download_url}"')[1].channel.recv_exit_status() == 0:
                                downloaded_file = f"{remote_path}/{download_url.split('/')[-1]}"
                                if ssh_client.exec_command(f'unzip {downloaded_file} -d {remote_path}{video_id}/')[1].channel.recv_exit_status() == 0:
                                    if ssh_client.exec_command(f'rm -f {downloaded_file}')[1].channel.recv_exit_status() == 0:
                                        print('done uploading video')
                                        for ext in image_extensions:
                                            #C:\Users\Syed Aalehuzoor\Desktop\Streamify360\laravel\scripts\..\storage/app/public/thumbnails\kRAdPFEKMu5.jpg
                                            if os.path.exists(os.path.join(thumbnail_dir, f'{video_id}.{ext}')):
                                                with ssh_client.open_sftp() as sftp:
                                                    sftp.put(localpath=os.path.join(thumbnail_dir, f'{video_id}.{ext}'), remotepath=f'{remote_path}{video_id}/thumbnail.{ext}')
                                                os.remove(os.path.join(thumbnail_dir, f'{video_id}.{ext}'))
                                                # Update the video status in the database
                                                update_query = f"UPDATE videos SET thumbnail_url = 'https://streambox.{server['domain']}/streams/{video_id}/thumbnail.{ext}' WHERE id = %s"
                                                cursor.execute(update_query, (video_id,))
                                                connection.commit()
                                                break
                            ssh_client.close()

                            # Update the video status in the database
                            update_query = "UPDATE videos SET status = 'live' WHERE id = %s"
                            cursor.execute(update_query, (video_id,))
                            update_query = f"UPDATE videos SET manifest_url = 'https://streambox.{server['domain']}/streams/{video_id}/master.m3u8' WHERE id = %s"
                            cursor.execute(update_query, (video_id,))
                            connection.commit()

                            # Delete the task from Azure Batch
                            mover_batch_client.task.delete(job_id=job_id, task_id=task_id)

                            print(f"Successfully processed and moved video {video_id} to server {server['domain']}")
                    except Exception as e:
                        print(f'error moving video: {e}')
                        # Update the video status in the database
                        update_query = "UPDATE videos SET status = 'Failed' WHERE id = %s"
                        cursor.execute(update_query, (video_id,))
                        update_query = f"UPDATE videos SET manifest_url = 'https://streambox.{server['domain']}/streams/{video_id}/master.m3u8' WHERE id = %s"
                        cursor.execute(update_query, (video_id,))
                        connection.commit()
            # Sleep for a few seconds before checking again
            sleep(10)

        except Exception as e:
            print(f"Error in move_video_and_delete_task: {e}")
            sleep(60)  # Sleep for 30 seconds

executer = ThreadPoolExecutor(max_workers=1)
executer.submit(move_video_and_delete_task)

def run_polling():
    # Loop to process jobs
    while True:
        try:
            print('Checking for Videos...')
            with mysql.connector.connect(host=os.getenv('DB_HOST'), user=os.getenv('DB_USERNAME'), password=os.getenv('DB_PASSWORD'), database=os.getenv('DB_DATABASE')) as connection:
                cursor = connection.cursor(dictionary=True)
                cursor.execute(initiated_video_query)
                videos = cursor.fetchall()
                if videos:
                    for video in videos:
                            print(video)
                            serverid = video['serverid']
                            server_query = "SELECT name, ip, ssh_port, username, domain FROM servers WHERE id = %s"
                            cursor.execute(server_query, (serverid,))
                            server = cursor.fetchone()
                            post_task( video, server)
                            sleep(2)  # Add delay to avoid overwhelming API requests#

                cursor.execute(deleted_video_query)
                videos = cursor.fetchall()
                if videos:
                    for video in videos:
                        print(f'Deleting Video {video['id']}')
                        serverid = video['serverid']
                        server_query = "SELECT name, ip, ssh_port, username, domain FROM servers WHERE id = %s"
                        cursor.execute(server_query, (serverid,))
                        server = cursor.fetchone()
                        ssh_client = SSHClient()
                        ssh_client.set_missing_host_key_policy(AutoAddPolicy)
                        pkey = RSAKey.from_private_key_file(os.path.join(script_dir, 'streamify360.pem'))
                        ssh_client.connect(server['ip'], port=server['ssh_port'], username=server['username'], pkey=pkey, allow_agent=False, look_for_keys=False)
                        ssh_client.exec_command(f'rm -rf /var/www/html/streams/{video['id']}')
                        ssh_client.close()
                        delete_video_query = "DELETE FROM videos WHERE id = %s"
                        cursor.execute(delete_video_query, (video['id'],))
                        connection.commit()  # Commit the transaction

                print("Waiting 5 seconds before checking for new Videos...")
                sleep(5)  # Re-query the database after a delay
        except Exception as e:
            print('Error Getting Videos to process. Waiting for 5 min before trying again')
            sleep( 5 * 60 )

# Sample video data (replace the URLs with actual or testable ones)
sample_video = {
    'id': 2,
    'userid': 1,
    'serverid': 1,
    'video_url': 'https://example.com/path/to/sample_video.mp4',  # Replace with a valid URL
    'thumbnail_url': 'https://example.com/path/to/sample_thumbnail.jpg',  # Replace with a valid URL
    'subtitle_url': None,  # Optional, replace with valid URL
    'logo_url': None  # Optional, replace with a valid URL
}

# Sample server data
sample_server = {
    'id': 456,
    'domain': 'example-server.com'
}

if __name__ == '__main__':
    # Call the post_job function with the sample video and server data
    #post_task(sample_video, sample_server)
    run_polling()