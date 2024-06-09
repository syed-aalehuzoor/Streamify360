import subprocess
import re
import sys
total_duration = 100

command = 'ffmpeg -y -i "6964465180.mp4" -i "6964465180.png" -filter_complex "[1:v]scale=w=iw:h=ih[logo];[0:v][logo]overlay=format=auto,ass=\'6964465180.ass\'" -c:v libx264 -preset veryfast -crf 22 -c:a aac -b:a 128k -metadata:s:a:0 language=ur -movflags +faststart "output_6964465180.mp4"'

process = subprocess.Popen(command, stderr=subprocess.PIPE, text=True)
pattern = re.compile(r"time=(\d+:\d+:\d+\.\d+)")  # Regex pattern to capture the time
stderror = []
try:
    while True:
        line = process.stderr.readline()
        if not line:
            break
        stderror.append(line)
        match = pattern.search(line)
        if match:
            current_time = sum(x * float(t) for x, t in zip([3600, 60, 1, 0.01], match.group(1).split(':')))
            progress = (current_time / total_duration) * 100
            print(f"\rProgress: {progress:.2f}%", end='')
finally:
    process.wait()
    if process.returncode != 0:
        print("\nFFmpeg exited with an error.")
        print(stderror)
        sys.exit(1)
    else:
        print("\nConversion completed successfully.")
