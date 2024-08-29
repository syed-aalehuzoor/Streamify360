<x-app-layout>        

    <div class="max-w-7xl mx-auto px-12">
        <div class="bg-white shadow-xl rounded-lg p-6 space-y-6">

            <div x-data="{ activeTab: 1 }" class="max-w-xl mx-auto">
                <!-- Tabs -->
                <div class="flex justify-around border-b border-gray-300">
                    <button 
                        :class="activeTab === 1 ? 'border-b-2 border-secondary text-secondary' : 'text-gray-600'"
                        @click="activeTab = 1"
                        class="flex items-center py-2 px-4 focus:outline-none">
                        <!-- Icon -->
                        <i class="fa-solid fa-upload w-5 h-5 mr-2"></i>
                        <span>Upload</span>
                    </button>
                    <button 
                        :class="activeTab === 2 ? 'border-b-2 border-secondary text-secondary' : 'text-gray-600'"
                        @click="activeTab = 2"
                        class="flex items-center py-2 px-4 focus:outline-none">
                        <!-- Icon -->
                        <i class="fa-brands fa-youtube w-5 h-5 mr-2"></i>
                        <span>YouTube URL</span>
                    </button>
                    <button 
                        :class="activeTab === 3 ? 'border-b-2 border-secondary text-secondary' : 'text-gray-600'"
                        @click="activeTab = 3"
                        class="flex items-center py-2 px-4 focus:outline-none">
                        <!-- Icon -->
                        <i class="fa-brands fa-google-drive w-5 h-5 mr-2"></i>
                        <span>Drive URL</span>
                    </button>
                    <button 
                        :class="activeTab === 4 ? 'border-b-2 border-secondary text-secondary' : 'text-gray-600'"
                        @click="activeTab = 4"
                        class="flex items-center py-2 px-4 focus:outline-none">
                        <!-- Icon -->
                        <i class="fa-solid fa-link w-5 h-5 mr-2"></i>
                        <span>Direct URL</span>
                    </button>
                </div>
            
                <!-- Tab Content -->
                <div class="p-4 bg-white mt-4 rounded-lg shadow">
                    <div x-show="activeTab === 1">
                        @livewire('video-form')
                    </div>
                    <div x-show="activeTab === 2">
                        <h2 class="text-lg font-semibold">Content for Tab 2</h2>
                        <p>This is the content for the second tab.</p>
                    </div>
                    <div x-show="activeTab === 3">
                        <h2 class="text-lg font-semibold">Content for Tab 3</h2>
                        <p>This is the content for the third tab.</p>
                    </div>
                    <div x-show="activeTab === 4">
                        <h2 class="text-lg font-semibold">Content for Tab 4</h2>
                        <p>This is the content for the fourth tab.</p>
                    </div>
                </div>
            </div>
            
        </div>
    </div>

</x-app-layout>
