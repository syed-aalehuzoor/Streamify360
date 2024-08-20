<x-admin-panel>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-6">
            <h1 class="text-2xl font-semibold text-gray-800 mb-6">Add New Servers</h1>
            <form action="{{ route('admin-store-server') }}" method="POST" class="bg-white shadow-xl rounded-lg p-6 space-y-6">
                @csrf
            
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name:</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
            
                    <div>
                        <label for="ip" class="block text-sm font-medium text-gray-700">IP Address:</label>
                        <input type="text" id="ip" name="ip" value="{{ old('ip') }}" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('ip')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
            
                    <div>
                        <label for="ssh_port" class="block text-sm font-medium text-gray-700">SSH Port:</label>
                        <input type="number" id="ssh_port" name="ssh_port" value="{{ old('ssh_port') }}" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('ssh_port')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
            
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700">Username:</label>
                        <input type="text" id="username" name="username" value="{{ old('username') }}" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('username')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
            
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Type:</label>
                        <select id="type" name="type" required class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="encoder" {{ old('type') == 'encoder' ? 'selected' : '' }}>Encoder</option>
                            <option value="storage" {{ old('type') == 'storage' ? 'selected' : '' }}>Storage</option>
                        </select>
                        @error('type')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div id="domain-container" style="display: none;">
                        <label for="domain" class="block text-sm font-medium text-gray-700">Domain:</label>
                        <input type="text" id="domain" name="domain" value="{{ old('domain') }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('domain')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div id="limit-container" style="display: none;">
                        <label for="limit" class="block text-sm font-medium text-gray-700">Limit:</label>
                        <input type="number" id="limit" name="limit" value="{{ old('limit') }}" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('limit')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const typeSelect = document.getElementById('type');
                            const limitContainer = document.getElementById('limit-container');
                            const domainContainer = document.getElementById('domain-container');
                    
                            function updateVisibility() {
                                if (typeSelect.value === 'encoder') {
                                    limitContainer.style.display = 'block';
                                    domainContainer.style.display = 'none';
                                    document.getElementById('limit').setAttribute('required', 'required');
                                    document.getElementById('domain').removeAttribute('required');
                                } else if (typeSelect.value === 'storage') {
                                    limitContainer.style.display = 'none';
                                    domainContainer.style.display = 'block';
                                    document.getElementById('limit').removeAttribute('required');
                                    document.getElementById('domain').setAttribute('required', 'required');
                                } else {
                                    limitContainer.style.display = 'none';
                                    domainContainer.style.display = 'none';
                                    document.getElementById('limit').removeAttribute('required');
                                    document.getElementById('domain').removeAttribute('required');
                                }
                            }
                    
                            // Initialize visibility on page load
                            updateVisibility();
                    
                            // Update visibility on type change
                            typeSelect.addEventListener('change', updateVisibility);
                        });
                    </script>
                    
                </div>
                <div class="relative rounded-lg">
                    <div class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-gray-200">
                        <label for="serverconfig" class="block text-sm font-medium text-gray-700">Authorization Key:</label>
                        <textarea name="serverconfig" id="serverconfig" class="mt-2 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-gray-100">sudo echo "ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAACAQCu9F1Su+5hSVyuZYA9xnSiIDCSMhixl4/wkZEk5QghzI0Z2TyM6P4De6qmBGnqOqvVLyQ1VAxUXer175E/wvc+KpV4DFqdp5YtEucTHLXcxq3c7X39TEEzdzQ8EQff5KpanSGvbfI08mxOPpJ5zkyfbE4e/drhXlk0/2j2vLkc2e/CzK5zF4GjzU41LcU7GpLXJz7KNnTYSaUoZHkxyw0riW7x48LnmipWa9ddLkseKeNRgJ0WlTe2mNSiKZnls3EG/fjcvmKRc6ErpnhELQn6NSUdKTz9XxUyjlxywcEcGSJ9Qf+sgdDDthXRYrg4HRhExOrT+j7ORhhvhVkRam6hRcpbmuKqIydZopSJ1fZMzAfeJNbecPvsJFCjm9Zi6EEoW/5cNQ4/FtDzmcC1QyStGjPDG/i5aPev7HEabGEuwLDqGE9OD+1OWLEFPejb8Nebbd3Vni1+OnJdCdZNVTLKTPKJ2Y02AsWQTLyw6M7h+knUTCNwF8KM/r/cRxZvF09CjPBvNyIy6LYrxMfCNw0rRBofiRukK7lE4fNZudpuwv6Cbt3Qgb32w69N5SMscdcjw3yx7Eoa4sSMGUgo7oNYX2lv/nYaCtuCi7kPbxVOLF4IFqvS+uYgEXi3ioTLJGLJeDicokk/d/Lgiqp5W3R7lcqZPrhw3WyuPHS0J/Wpfw== vss@streamify360" >> ~/.ssh/authorized_keys</textarea>
                        <h6 class="text-sm mt-1">
                            Run this command on the server that you're adding as root user.
                        </h6>
                    </div>

                    <button onclick="copyToClipboard()" class="absolute top-0 right-0 mt-2 mr-2 bg-secondary text-white px-3 py-1 text-xs rounded">
                        Copy
                    </button>
                </div>
                
                <script>
                function copyToClipboard() {
                    // Get the textarea element
                    var copyText = document.getElementById("serverconfig");
                
                    // Select the text
                    copyText.select();
                    copyText.setSelectionRange(0, 99999); // For mobile devices
                
                    // Copy the text to clipboard
                    document.execCommand("copy");
                
                    // Optionally, alert the user that the text has been copied
                    alert("Command copied to clipboard!");
                }
                </script>
                
                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-secondary hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Add Server
                    </button>
                </div>
            </form>
            
        </div>
    </div>
</x-admin-panel>
