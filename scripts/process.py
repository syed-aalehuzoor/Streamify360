from paramiko import SSHClient, AutoAddPolicy

# Initialize the SSH client
ssh = SSHClient()

# Set the policy to automatically add the host key to known_hosts
ssh.set_missing_host_key_policy(AutoAddPolicy())

# Connect to the server using the SSH key
try:
    ssh.connect(hostname='48.217.244.62', username='ubuntu', key_filename='id_rsa')

    # Test the connection by running a simple command
    stdin, stdout, stderr = ssh.exec_command('echo "Connection successful!"')
    print(stdout.read().decode())
    print('elo')

except Exception as e:
    print(f"An error occurred: {e}")

finally:
    # Close the connection
    ssh.close()
