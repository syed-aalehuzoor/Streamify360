<x-app-layout>        
    <div class="max-w-7xl mx-auto px-6">

        <h1 class="font-semibold text-lg text-gray-800 mb-6 leading-4">Edit Video</h1>

        <!-- Display any success or error messages -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 text-green-700 border border-green-200 rounded-lg shadow">
                {{ session('success') }}
            </div>
        @endif
    
        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    
        <!-- Edit Video Form -->
        <form action="{{ route('videos.edit', $video->id) }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-xl rounded-lg p-6 space-y-6">
            @csrf

            <!-- Video Name -->
            <div class="space-y-2">
                <label for="videoname" class="block text-sm font-medium text-gray-700">Video Name</label>
                <input type="text" name="videoname" id="videoname" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ old('videoname', $video->name) }}">
            </div>

            <!-- Thumbnail File -->
            <div class="space-y-2">
                <label for="thumbnail" class="block text-sm font-medium text-gray-700">Upload Thumbnail</label>
                <input type="file" name="thumbnail" id="thumbnail" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100">
                @if($video->thumbnail_url)
                    <p class="text-sm text-gray-500 mt-2">Current thumbnail:</p>
                    <img src="{{ $video->thumbnail_url }}" alt="Thumbnail" class="max-w-xs rounded-md">
                @endif
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" class="inline-flex justify-center px-4 py-2 bg-secondary border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Save Changes
                </button>
            </div>
        </form>

    </div>
</x-app-layout>
