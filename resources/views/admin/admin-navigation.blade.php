<!-- Desktop Navigation -->
<aside 
    x-data="{ 
        openDropdown: '{{ request()->routeIs('admin-all-videos', 'Processes', 'abuse-reports') ? 'videos' : (request()->routeIs('admin-servers', 'admin-add-server') ? 'servers' : (request()->routeIs('general-settings', 'video-settings') ? 'settings' : (request()->routeIs('admin-users') ? 'users' : ''))) }}'
    }"
    id="sidebar-multi-level-sidebar"
    class="bg-primary w-72 z-20 hidden left-0 ml-4 mt-8 shadow-xl sm:block h-fit rounded-lg">
    
    <div class="py-4 text-gray-500">
        <!-- Sidebar Menu -->
        <nav class="mt-3">
            <ul>
                <!-- Dashboard Link -->
                <li class="relative px-6 text-gray-600 ">
                    <hr class="border-accent">
                    <a href="{{ route('admin') }}"
                    class="flex items-center p-3 hover:bg-gray-100 hover:text-secondary {{ request()->routeIs('admin') ? 'bg-gray-100 text-secondary' : '' }}"
                    @click="openDropdown = 'Dashboard'">
                        <span>{{ __('Dashboard') }}</span>
                    </a>
                    <hr class="border-accent">
                </li>

                <!-- Videos Dropdown -->
                <li class="group relative px-6 py-3">
                    <span :class="{ 'bg-secondary': openDropdown === 'videos' }" class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>
                    <a href="#" 
                    class="flex items-center justify-between p-2 text-sm transition-colors text-gray-600 hover:bg-gray-100 hover:text-secondary {{ request()->routeIs('admin-all-videos', 'Processes', 'abuse-reports') ? 'bg-gray-100 text-secondary' : '' }}"
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
                        <a href="{{ route('admin-all-videos') }}"
                        class="block p-2 hover:bg-gray-100 hover:text-secondary text-sm {{ request()->routeIs('admin-all-videos') ? 'bg-gray-100 text-secondary' : 'text-gray-600' }}">
                            Videos
                        </a>
                        <a href="{{ route('Processes') }}"
                        class="block p-2 hover:bg-gray-100 hover:text-secondary text-sm {{ request()->routeIs('Processes') ? 'bg-gray-100 text-secondary' : 'text-gray-600' }}">
                            Processes
                        </a>
                        <a href="{{ route('abuse-reports') }}"
                        class="block hover:bg-gray-100 hover:text-secondary p-2 text-sm {{ request()->routeIs('abuse-reports') ? 'bg-gray-100 text-secondary' : 'text-gray-600' }}">
                            Abuse Reports
                        </a>
                    </div>
                </li>

                <!-- Servers Dropdown -->
                <li class="group relative px-6 py-3">
                    <span :class="{ 'bg-secondary': openDropdown === 'servers' }" class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>
                    <a href="#" 
                    class="flex items-center justify-between p-2 text-sm transition-colors text-gray-600 hover:bg-gray-100 hover:text-secondary {{ request()->routeIs('admin-servers', 'admin-add-server') ? 'bg-gray-100 text-secondary' : '' }}"
                    @click.prevent="openDropdown = openDropdown === 'servers' ? '' : 'servers'">
                        <div class="flex items-center">
                            <i class="fa-solid fa-server w-5 h-5 mr-2"></i>
                            <span>Servers</span>
                        </div>
                        <svg :class="{ 'transform rotate-180': openDropdown === 'servers' }"
                            class="w-4 h-4 transition-transform transform"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </a>
                    <div x-show="openDropdown === 'servers'" x-cloak class="ml-4 bg-gray-50 text-gray-700 rounded-lg">
                        <a href="{{ route('admin-servers') }}"
                        class="block p-2 hover:bg-gray-100 hover:text-secondary text-sm {{ request()->routeIs('admin-servers') ? 'bg-gray-100 text-secondary' : 'text-gray-600' }}">
                            All Servers
                        </a>
                        <a href="{{ route('admin-add-server') }}"
                        class="block p-2 hover:bg-gray-100 hover:text-secondary text-sm {{ request()->routeIs('admin-add-server') ? 'bg-gray-100 text-secondary' : 'text-gray-600' }}">
                            Add Server
                        </a>
                    </div>
                </li>

                <!-- Users Dropdown -->
                <li class="group relative px-6 py-3">
                    <span :class="{ 'bg-secondary': openDropdown === 'users' }" class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>
                    <a href="#" 
                    class="flex items-center justify-between p-2 text-sm transition-colors text-gray-600 hover:bg-gray-100 hover:text-secondary {{ request()->routeIs('admin-users') ? 'bg-gray-100 text-secondary' : '' }}"
                    @click.prevent="openDropdown = openDropdown === 'users' ? '' : 'users'">
                        <div class="flex items-center">
                            <i class="fa-solid fa-user w-5 h-5 mr-2"></i>
                            <span>Users</span>
                        </div>
                        <svg :class="{ 'transform rotate-180': openDropdown === 'users' }"
                            class="w-4 h-4 transition-transform transform"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </a>
                    <div x-show="openDropdown === 'users'" x-cloak class="ml-4 bg-gray-50 text-gray-700 rounded-lg">
                        <a href="{{ route('admin-users') }}"
                        class="block p-2 hover:bg-gray-100 hover:text-secondary text-sm {{ request()->routeIs('admin-users') ? 'bg-gray-100 text-secondary' : 'text-gray-600' }}">
                            All Users
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
    x-data="{ 
        isSideMenuOpen: false, 
        openDropdown: '{{ request()->routeIs('admin-all-videos', 'Processes', 'abuse-reports') ? 'videos' : (request()->routeIs('admin-servers', 'admin-add-server') ? 'servers' : (request()->routeIs('general-settings', 'video-settings') ? 'settings' : (request()->routeIs('admin-users') ? 'users' : ''))) }}' 
    }"
    class="z-40 bg-primary text-gray-500 sm:hidden">

    <div x-show="isSideMenuOpen" @click="isSideMenuOpen = false" x-cloak class="fixed inset-0 z-10 bg-black opacity-50"></div>

    <div x-show="isSideMenuOpen" x-cloak class="fixed inset-y-0 z-20 w-64 mt-16 overflow-y-auto bg-primary">
        <div class="py-4">
            <nav class="mt-3">
                <ul>
                    <!-- Same structure as Desktop Navigation for consistency -->
                </ul>
            </nav>
        </div>
    </div>
</div>
