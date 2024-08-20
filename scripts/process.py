import mysql.connector
from mysql.connector import Error
from dotenv import load_dotenv
import os

from paramiko import SSHClient, AutoAddPolicy, RSAKey

from argparse import ArgumentParser
parser = ArgumentParser(description="Argument parsing using argparse.")
parser.add_argument('--id', type=str, required=True, help="Video Id")
args = parser.parse_args()
video_id = args.id

print(f"Video Id: {video_id}")

# Load environment variables from .env file
load_dotenv('../.env')

# Get database credentials from environment variables
db_host = os.getenv('DB_HOST')
db_user = os.getenv('DB_USERNAME')
db_password = os.getenv('DB_PASSWORD')
db_name = os.getenv('DB_DATABASE')

# Connect to the MySQL database
try:
    connection = mysql.connector.connect(
        host=db_host,
        user=db_user,
        password=db_password,
        database=db_name
    )

    if connection.is_connected():
        print("Successfully connected to the database")

    # Perform database operations here...

except mysql.connector.Error as e:
    print(f"Error: {e}")

finally:
    if connection.is_connected():
        connection.close()
        print("MySQL connection is closed")


# Initialize the SSH client
ssh = SSHClient()

# Set the policy to automatically add the host key to known_hosts
ssh.set_missing_host_key_policy(AutoAddPolicy())

key = RSAKey.from_private_key_file(filename='id_rsa')

# Connect to the server using the SSH key
ssh.connect(hostname='48.217.244.62', username='root', pkey=key, allow_agent=False, look_for_keys=False)

# Test the connection by running a simple command
stdin, stdout, stderr = ssh.exec_command('echo "Connection successful!"')
print(stdout.read().decode())

# Close the connection
ssh.close()
