<aside 
    x-data="{ open: false, openDropdown: '{{ request()->routeIs('all-videos', 'add-video') ? 'videos' : (request()->routeIs('general-settings', 'video-settings') ? 'settings' : (request()->routeIs('all-servers', 'add-server') ? 'servers' : '')) }}', selected: null }"
    id="sidebar-multi-level-sidebar"
    class="bg-primary w-72 z-20 hidden left-0 inset-y-0 h-full sm:block overflow-y-auto">
    
    <div class="py-4 font-bold text-gray-500">
        <!-- Sidebar Menu -->
        <nav class="mt-5">
            
            <a href="{{ route('admin') }}" class="px-6 flex text-lg text-black font-semibold">
                <span class="text-xl font-semibold">Streamify <sup class="text-sm font-medium">360</sup></span>
            </a>

            <ul class="mt-6">
                
                <!-- Dashboard Link -->
                <li class="relative px-6">
                    <hr class="border-accent">
                    <!-- Dashboard Link -->
                    <a href="{{ route('admin') }}" class="flex items-center p-3" @click="selected = 'Dashboard'">
                        <span>{{ __('Dashboard') }}</span>
                    </a>
                    <hr class="border-accent">
                </li>

                <!-- Videos Dropdown -->
                <li class="relative px-6 py-3" x-data="{ id: 'videos' }">
                    <span class="absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>
                    <a href="{{ route('all-videos') }}" class="flex items-center justify-between p-2 text-sm transition-colors" @click.prevent="openDropdown = openDropdown === id ? '' : id">
                        <div class="flex items-center">
                            <i class="fa-solid fa-photo-film w-5 h-5 mr-2 text-gray-500"></i> <!-- Font Awesome Video Icon -->
                            <span>Videos</span>
                        </div>
                        <svg :class="{ 'transform rotate-180': openDropdown === id }" class="w-4 h-4 transition-transform transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </a>

                    <div x-show="openDropdown === id" x-cloak class="ml-4 p-1 bg-accent text-black rounded-lg">
                        <div class="text-gray-600 uppercase text-xs font-bold p-2">
                            Video:
                        </div>
                        <a href="{{ route('all-videos') }}" class="block p-2 text-sm hover:bg-accent rounded-lg {{ request()->routeIs('all-videos') ? 'text-primary font-bold' : '' }}">
                            All Videos
                        </a>
                        <a href="{{ route('add-video') }}" class="block p-2 text-sm hover:bg-accent rounded-lg {{ request()->routeIs('add-video') ? 'text-primary font-bold' : '' }}">
                            Add New Video
                        </a>
                    </div>
                </li>

                <!-- Settings Dropdown -->
                <li class="relative px-6 py-3" x-data="{ id: 'settings' }">
                    
                    <a href="#" class="flex items-center justify-between p-2 transition-colors text-sm" @click.prevent="openDropdown = openDropdown === id ? '' : id">
                        <div class="flex items-center">
                            <i class="fa-solid fa-wrench w-5 h-5 mr-2 text-gray-500"></i> <!-- Font Awesome Wrench Icon -->
                            <span>Settings</span>
                        </div>
                        <svg :class="{ 'transform rotate-180': openDropdown === id }" class="w-4 h-4 transition-transform transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </a>
                    <div x-show="openDropdown === id" x-cloak class="ml-4 p-1 bg-accent text-black rounded-lg">
                        <div class="text-gray-600 uppercase text-xs font-bold p-2">
                            Settings:
                        </div>
                        <a href="{{ route('general-settings') }}" class="block p-2 text-sm hover:bg-accent rounded-lg {{ request()->routeIs('general-settings') ? 'text-primary font-bold' : '' }}">
                            General Settings
                        </a>
                        <a href="{{ route('video-settings') }}" class="block p-2 text-sm hover:bg-accent rounded-lg {{ request()->routeIs('video-settings') ? 'text-primary font-bold' : '' }}">
                            Video Default Settings
                        </a>
                    </div>
                </li>

                <!-- Servers Dropdown -->
                <li class="relative px-6 py-3" x-data="{ id: 'servers' }">
                    <a href="#" class="flex items-center justify-between p-2 transition-colors text-sm" @click.prevent="openDropdown = openDropdown === id ? '' : id">
                        <div class="flex items-center">
                            <i class="fa-solid fa-server w-5 h-5 mr-2 text-gray-500"></i> <!-- Font Awesome Server Icon -->
                            <span>Servers</span>
                        </div>
                        <svg :class="{ 'transform rotate-180': openDropdown === id }" class="w-4 h-4 transition-transform transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </a>
                    <div x-show="openDropdown === id" x-cloak class="ml-4 p-1 bg-accent text-black rounded-lg">
                        <div class="text-gray-600 uppercase text-xs font-bold p-2">
                            Servers:
                        </div>
                        <a href="{{ route('all-servers') }}" class="block p-2 text-sm hover:bg-accent rounded-lg {{ request()->routeIs('all-servers') ? 'text-primary font-bold' : '' }}">
                            All Servers
                        </a>
                        <a href="{{ route('add-server') }}" class="block p-2 text-sm hover:bg-accent rounded-lg {{ request()->routeIs('add-server') ? 'text-primary font-bold' : '' }}">
                            Add Server
                        </a>
                    </div>
                </li>
            </ul>

        </nav>

    </div>

</aside>

<!-- Mobile Navigation -->
<aside 
    x-data="{ open: false, openDropdown: '{{ request()->routeIs('all-videos', 'add-video') ? 'videos' : (request()->routeIs('general-settings', 'video-settings') ? 'settings' : (request()->routeIs('all-servers', 'add-server') ? 'servers' : '')) }}' }" 
    id="sidebar-multi-level-sidebar"
    x-show="!openSidebar"
    class="bg-primary w-72 z-20 fixed left-0 inset-y-0 h-full sm:hidden overflow-y-auto">
    
    <div class="py-4 font-bold text-gray-500">
        <!-- Sidebar Menu -->
        <nav class="mt-5">
            <div class="w-full text-right">
                <button @click="openSidebar = !openSidebar" aria-label="Close Sidebar" class="text-secondary hover:text-gray-300 focus:outline-none focus:text-gray-300">
                    <i class="fa-solid fa-xmark h-16 w-16"></i>
                </button>            
            </div>
            <a href="{{ route('admin') }}" class="px-6 flex text-lg text-black font-semibold">
                <span class="text-xl font-semibold">Streamify <sup class="text-sm font-medium">360</sup></span>
            </a>

            <ul class="mt-6">
                
                <!-- Dashboard Link -->
                <li class="relative px-6">
                    <hr class="border-accent">
                    <!-- Dashboard Link -->
                    <a href="{{ route('admin') }}" class="flex items-center p-3 transition-colors":class="{'bg-secondary': selected === 'Dashboard'}" @click="selected = 'Dashboard'">
                        <span>{{ __('Dashboard') }}</span>
                    </a>
                    <hr class="border-accent">
                </li>

                <!-- Videos Dropdown -->
                <li class="relative px-6 py-3" x-data="{ id: 'videos' }">
                    <span class="absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>
                    <a href="{{ route('all-videos') }}" class="flex items-center justify-between p-2 text-sm transition-colors" @click.prevent="openDropdown = openDropdown === id ? '' : id">
                        <div class="flex items-center">
                            <i class="fa-solid fa-photo-film w-5 h-5 mr-2 text-gray-500"></i> <!-- Font Awesome Video Icon -->
                            <span>Videos</span>
                        </div>
                        <svg :class="{ 'transform rotate-180': openDropdown === id }" class="w-4 h-4 transition-transform transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </a>

                    <div x-show="openDropdown === id" x-cloak class="ml-4 p-1 bg-accent text-black rounded-lg">
                        <div class="text-gray-600 uppercase text-xs font-bold p-2">
                            Video:
                        </div>
                        <a href="{{ route('all-videos') }}" class="block p-2 text-sm hover:bg-accent rounded-lg {{ request()->routeIs('all-videos') ? 'text-primary font-bold' : '' }}">
                            All Videos
                        </a>
                        <a href="{{ route('add-video') }}" class="block p-2 text-sm hover:bg-accent rounded-lg {{ request()->routeIs('add-video') ? 'text-primary font-bold' : '' }}">
                            Add New Video
                        </a>
                    </div>
                </li>

                <!-- Settings Dropdown -->
                <li class="relative px-6 py-3" x-data="{ id: 'settings' }">
                    
                    <a href="#" class="flex items-center justify-between p-2 transition-colors text-sm" @click.prevent="openDropdown = openDropdown === id ? '' : id">
                        <div class="flex items-center">
                            <i class="fa-solid fa-wrench w-5 h-5 mr-2 text-gray-500"></i> <!-- Font Awesome Wrench Icon -->
                            <span>Settings</span>
                        </div>
                        <svg :class="{ 'transform rotate-180': openDropdown === id }" class="w-4 h-4 transition-transform transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </a>
                    <div x-show="openDropdown === id" x-cloak class="ml-4 p-1 bg-accent text-black rounded-lg">
                        <div class="text-gray-600 uppercase text-xs font-bold p-2">
                            Settings:
                        </div>
                        <a href="{{ route('general-settings') }}" class="block p-2 text-sm hover:bg-accent rounded-lg {{ request()->routeIs('general-settings') ? 'text-primary font-bold' : '' }}">
                            General Settings
                        </a>
                        <a href="{{ route('video-settings') }}" class="block p-2 text-sm hover:bg-accent rounded-lg {{ request()->routeIs('video-settings') ? 'text-primary font-bold' : '' }}">
                            Video Default Settings
                        </a>
                    </div>
                </li>

                <!-- Servers Dropdown -->
                <li class="relative px-6 py-3" x-data="{ id: 'servers' }">
                    <a href="#" class="flex items-center justify-between p-2 transition-colors text-sm" @click.prevent="openDropdown = openDropdown === id ? '' : id">
                        <div class="flex items-center">
                            <i class="fa-solid fa-server w-5 h-5 mr-2 text-gray-500"></i> <!-- Font Awesome Server Icon -->
                            <span>Servers</span>
                        </div>
                        <svg :class="{ 'transform rotate-180': openDropdown === id }" class="w-4 h-4 transition-transform transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </a>
                    <div x-show="openDropdown === id" x-cloak class="ml-4 p-1 bg-accent text-black rounded-lg">
                        <div class="text-gray-600 uppercase text-xs font-bold p-2">
                            Servers:
                        </div>
                        <a href="{{ route('all-servers') }}" class="block p-2 text-sm hover:bg-accent rounded-lg {{ request()->routeIs('all-servers') ? 'text-primary font-bold' : '' }}">
                            All Servers
                        </a>
                        <a href="{{ route('add-server') }}" class="block p-2 text-sm hover:bg-accent rounded-lg {{ request()->routeIs('add-server') ? 'text-primary font-bold' : '' }}">
                            Add Server
                        </a>
                    </div>
                </li>
            </ul>

        </nav>

    </div>

</aside>