<x-admin-panel>
    <div class="py-12 bg-gray-100">
        <div class="max-w-7xl mx-auto px-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">All Users</h1>

            @if (session('success'))
                <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-lg shadow-md">
                    {{ session('success') }}
                </div>
            @endif
            @error('ip')
                <div class="mb-6 p-4 bg-red-100 text-red-800 rounded-lg shadow-md">
                    {{ $message }}
                </div>
            @enderror

            <div class="overflow-x-auto">
                <div class="inline-block min-w-full py-2 align-middle">
                    <div class="mt-4">
                        <div class="shadow overflow-hidden border-b border-gray-200 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200 bg-white">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Name
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Type
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($users as $user)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $user->name }}
                                                <div class="text-xs">
                                                    {{ $user->email }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $user->usertype }}
                                            </td>
                                            <td class="px-6 gap-2 py-4 whitespace-nowrap text-sm font-medium flex space-x-2">
                                                <a href="{{ route('users.edit', $user->id) }}" class="text-blue-600 hover:bg-blue-100 hover:text-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 px-4 py-2 rounded-md border border-blue-300">Edit</a>
                                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:bg-red-100 hover:text-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 px-4 py-2 rounded-md border border-red-300">Delete</button>
                                                </form>
                                                <a href="{{ route('users.suspend', $user->id) }}" class="text-yellow-600 hover:bg-yellow-100 hover:text-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 px-4 py-2 rounded-md border border-yellow-300">Suspend</a>
                                                <a href="{{ route('users.activate', $user->id) }}" class="text-green-600 hover:bg-green-100 hover:text-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 px-4 py-2 rounded-md border border-green-300">Activate</a>
                                                <a href="{{ route('users.show', $user->id) }}" class="text-indigo-600 hover:bg-indigo-100 hover:text-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 px-4 py-2 rounded-md border border-indigo-300">View</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="mt-4">
                                {{ $users->links('vendor.pagination.tailwind') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
        </div>
    </div>
</x-admin-panel>
