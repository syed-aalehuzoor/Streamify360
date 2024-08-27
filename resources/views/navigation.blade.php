<!-- Desktop Navigation -->
<aside class="bg-primary w-72 z-20 hidden shadow-xl sm:block h-fit rounded-lg"

x-data="{ 
        openDropdown: '{{ request()->routeIs('all-videos', 'add-video') ? 'videos' : (request()->routeIs('general-settings', 'video-settings') ? 'settings' : '') }}'
    }"
    id="sidebar-multi-level-sidebar">
    
    <div class="py-4 text-gray-500">
        <!-- Sidebar Menu -->
        <nav class="mt-3">
            <ul>
                <!-- Dashboard Link -->
                <li class="relative px-6 text-gray-600 ">
                    <hr class="border-accent">
                    <a href="{{ route('dashboard') }}"
                    class="flex items-center p-3 hover:bg-gray-100 hover:text-secondary {{ request()->routeIs('dashboard') ? 'bg-gray-100 text-secondary' : '' }}"
                    @click="openDropdown = 'Dashboard'">
                        <span>{{ __('Dashboard') }}</span>
                    </a>
                    <hr class="border-accent">
                </li>

                <!-- Videos Dropdown -->
                <li class="group relative px-6 py-3">
                    <span :class="{ 'bg-secondary': openDropdown === 'videos' }" class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>
                    <a href="#" 
                    class="flex items-center justify-between p-2 text-sm transition-colors text-gray-600 hover:bg-gray-100 hover:text-secondary {{ request()->routeIs('all-videos', 'add-video') ? 'bg-gray-100 text-secondary' : '' }}"
                    @click.prevent="openDropdown = openDropdown === 'videos' ? '' : 'videos'">
                        <div class="flex items-center">
                            <i class="fa-solid fa-photo-film w-5 h-5 mr-2"></i>
                            <span>Videos</span>
                        </div>
                        <svg :class="{ 'transform rotate-180': openDropdown === 'videos' }"
                            class="w-4 h-4 transition-transform transform"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </a>

                    <div x-show="openDropdown === 'videos'" x-cloak class="ml-4 bg-gray-50 text-gray-700 rounded-lg">
                        <a href="{{ route('all-videos') }}"
                        class="block p-2 hover:bg-gray-100 hover:text-secondary text-sm {{ request()->routeIs('all-videos') ? 'bg-gray-100 text-secondary' : 'text-gray-600' }}">
                            All Videos
                        </a>
                        <a href="{{ route('add-video') }}"
                        class="block p-2 hover:bg-gray-100 hover:text-secondary text-sm {{ request()->routeIs('add-video') ? 'bg-gray-100 text-secondary' : 'text-gray-600' }}">
                            Upload New
                        </a>
                    </div>
                </li>

                <!-- Settings Dropdown -->
                <li class="group relative px-6 py-3">
                    <span :class="{ 'bg-secondary': openDropdown === 'settings' }" class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>
                    <a href="#" 
                    class="flex items-center justify-between p-2 text-sm transition-colors text-gray-600 hover:bg-gray-100 hover:text-secondary {{ request()->routeIs('general-settings', 'video-settings') ? 'bg-gray-100 text-secondary' : '' }}"
                    @click.prevent="openDropdown = openDropdown === 'settings' ? '' : 'settings'">
                        <div class="flex items-center">
                            <i class="fa-solid fa-wrench w-5 h-5 mr-2"></i>
                            <span>Settings</span>
                        </div>
                        <svg :class="{ 'transform rotate-180': openDropdown === 'settings' }"
                            class="w-4 h-4 transition-transform transform"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </a>
                    <div x-show="openDropdown === 'settings'" x-cloak class="ml-4 bg-gray-50 text-gray-700 rounded-lg">
                        <a href="{{ route('general-settings') }}"
                        class="block p-2 hover:bg-gray-100 hover:text-secondary text-sm {{ request()->routeIs('general-settings') ? 'bg-gray-100 text-secondary' : 'text-gray-600' }}">
                            General Settings
                        </a>
                        <a href="{{ route('video-settings') }}"
                        class="block p-2 hover:bg-gray-100 hover:text-secondary text-sm {{ request()->routeIs('video-settings') ? 'bg-gray-100 text-secondary' : 'text-gray-600' }}">
                            Video Settings
                        </a>
                    </div>
                </li>
            </ul>
        </nav>
    </div>
</aside>


<!-- Mobile Navigation -->
<div 
    x-show="openSidebar" x-cloak 
    class="bg-primary z-40 left-0 fixed h-full w-60 text-gray-500 sm:hidden"
    x-data="{ 
        isSideMenuOpen: false, 
        openDropdown: '{{ request()->routeIs('admin-all-videos', 'Processes', 'abuse-reports') ? 'videos' : (request()->routeIs('admin-servers', 'admin-add-server') ? 'servers' : (request()->routeIs('general-settings', 'video-settings') ? 'settings' : (request()->routeIs('admin-users') ? 'users' : ''))) }}' 
    }">

        <div class="py-4">
            <nav class="mt-3">
                <ul>
                    <!-- Dashboard Link -->
                    <li class="relative px-6 text-gray-600 ">
                        <hr class="border-accent">
                        <a href="{{ route('dashboard') }}"
                        class="flex items-center p-3 hover:bg-gray-100 hover:text-secondary {{ request()->routeIs('dashboard') ? 'bg-gray-100 text-secondary' : '' }}"
                        @click="openDropdown = 'Dashboard'">
                            <span>{{ __('Dashboard') }}</span>
                        </a>
                        <hr class="border-accent">
                    </li>
    
                    <!-- Videos Dropdown -->
                    <li class="group relative px-6 py-3">
                        <span :class="{ 'bg-secondary': openDropdown === 'videos' }" class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>
                        <a href="#" 
                        class="flex items-center justify-between p-2 text-sm transition-colors text-gray-600 hover:bg-gray-100 hover:text-secondary {{ request()->routeIs('all-videos', 'add-video') ? 'bg-gray-100 text-secondary' : '' }}"
                        @click.prevent="openDropdown = openDropdown === 'videos' ? '' : 'videos'">
                            <div class="flex items-center">
                                <i class="fa-solid fa-photo-film w-5 h-5 mr-2"></i>
                                <span>Videos</span>
                            </div>
                            <svg :class="{ 'transform rotate-180': openDropdown === 'videos' }"
                                class="w-4 h-4 transition-transform transform"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </a>
    
                        <div x-show="openDropdown === 'videos'" x-cloak class="ml-4 bg-gray-50 text-gray-700 rounded-lg">
                            <a href="{{ route('all-videos') }}"
                            class="block p-2 hover:bg-gray-100 hover:text-secondary text-sm {{ request()->routeIs('all-videos') ? 'bg-gray-100 text-secondary' : 'text-gray-600' }}">
                                All Videos
                            </a>
                            <a href="{{ route('add-video') }}"
                            class="block p-2 hover:bg-gray-100 hover:text-secondary text-sm {{ request()->routeIs('add-video') ? 'bg-gray-100 text-secondary' : 'text-gray-600' }}">
                                Upload New
                            </a>
                        </div>
                    </li>
    
                    <!-- Settings Dropdown -->
                    <li class="group relative px-6 py-3">
                        <span :class="{ 'bg-secondary': openDropdown === 'settings' }" class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>
                        <a href="#" 
                        class="flex items-center justify-between p-2 text-sm transition-colors text-gray-600 hover:bg-gray-100 hover:text-secondary {{ request()->routeIs('general-settings', 'video-settings') ? 'bg-gray-100 text-secondary' : '' }}"
                        @click.prevent="openDropdown = openDropdown === 'settings' ? '' : 'settings'">
                            <div class="flex items-center">
                                <i class="fa-solid fa-wrench w-5 h-5 mr-2"></i>
                                <span>Settings</span>
                            </div>
                            <svg :class="{ 'transform rotate-180': openDropdown === 'settings' }"
                                class="w-4 h-4 transition-transform transform"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </a>
                        <div x-show="openDropdown === 'settings'" x-cloak class="ml-4 bg-gray-50 text-gray-700 rounded-lg">
                            <a href="{{ route('general-settings') }}"
                            class="block p-2 hover:bg-gray-100 hover:text-secondary text-sm {{ request()->routeIs('general-settings') ? 'bg-gray-100 text-secondary' : 'text-gray-600' }}">
                                General Settings
                            </a>
                            <a href="{{ route('video-settings') }}"
                            class="block p-2 hover:bg-gray-100 hover:text-secondary text-sm {{ request()->routeIs('video-settings') ? 'bg-gray-100 text-secondary' : 'text-gray-600' }}">
                                Video Settings
                            </a>
                        </div>
                    </li>
                </ul>
            </nav>
        </div>
</div>
