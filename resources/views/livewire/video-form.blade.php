<div>
    <h1 class="font-semibold text-lg text-gray-800 mb-6 leading-4">Add New Video</h1>

    <form wire:submit.prevent="save" enctype="multipart/form-data">
        <div class="grid grid-cols-1 gap-6">

            <div>
                <label for="videoname" class="block text-sm font-medium text-gray-700">Video Name (required):</label>
                <input type="text" wire:model="videoname" id="videoname" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                @error('videoname') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div
                x-data="{ isUploading: false, progress: 0 }"
                x-on:livewire-upload-start="isUploading = true"
                x-on:livewire-upload-finish="isUploading = false"
                x-on:livewire-upload-error="isUploading = false"
                x-on:livewire-upload-progress="progress = $event.detail.progress"
                >
                <label for="video" class="block text-sm font-medium text-gray-700">Video File (required):</label>
                <input type="file" wire:model="video" id="video" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <div x-show="isUploading">
                    <progress max="100" x-bind:value="progress"></progress>
                </div>
                @error('video') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="subtitle" class="block text-sm font-medium text-gray-700">Subtitle (optional):</label>
                <input type="file" wire:model="subtitle" id="subtitle" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                @error('subtitle') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="logo" class="block text-sm font-medium text-gray-700">Logo (optional):</label>
                <input type="file" wire:model="logo" id="logo" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                @error('logo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="thumbnail" class="block text-sm font-medium text-gray-700">Thumbnail (optional):</label>
                <input type="file" wire:model="thumbnail" id="thumbnail" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                @error('thumbnail') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="inline-flex mt-4 items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-secondary hover:bg-secondary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Upload Video
            </button>
        </div>
    </form>
</div>
