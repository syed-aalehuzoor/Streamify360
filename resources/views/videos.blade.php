<x-app-layout>
    <div class="max-w-7xl mx-auto px-6 py-8">
        <h1 class="font-bold text-lg text-gray-800 mb-8">Videos</h1>
        
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-50 text-green-700 border border-green-200 rounded-lg shadow">
                {{ session('success') }}
            </div>
        @endif

        @error('ip')
            <div class="mb-6 p-4 bg-red-50 text-red-700 border border-red-200 rounded-lg shadow">
                {{ $message }}
            </div>
        @enderror

        <div class="overflow-x-auto bg-white shadow rounded-lg">
            <div class="p-6 border-b border-gray-200 flex justify-end">
                <form method="GET" action="{{ route('all-videos') }}">
                        <input type="text" name="query" value="{{ request('query') }}" placeholder="Search videos..." class="h-8">
                        <button type="submit" class="h-8">Search</button>
                </form>
            </div>

            <table class="min-w-full bg-white divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                            Name
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                            Details
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($videos as $video)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <a href="{{ route('video.player', $video->id) }}" class="hover:underline" target="_blank">
                                    {{ $video->name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if ($video->status !== 'live')
                                    @livewire('video-status', ['videoId' => $video->id], key($video->id))
                                @else
                                    {{ $video->status }}
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex space-x-2">
                                <a href="{{ route('videos.edit', $video->id) }}" class="text-blue-600 hover:bg-blue-100 hover:text-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 px-4 py-2 rounded-md border border-blue-300">Edit</a>
                                <form action="{{ route('videos.destroy', $video->id) }}" method="POST" class="inline" onsubmit="return confirmDeletion()">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:bg-red-100 hover:text-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 px-4 py-2 rounded-md border border-red-300">
                                        Delete
                                    </button>
                                </form>
                                
                                <script>
                                    function confirmDeletion() {
                                        return confirm('Are you sure you want to delete this video?');
                                    }
                                </script>
                                
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="px-6 py-4">
                {{ $videos->links('vendor.pagination.tailwind') }}
            </div>
        </div>
    </div>
</x-app-layout>