<!-- Top Header -->
<header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 lg:px-6 sticky top-0 z-10">
    <div class="flex items-center gap-3 lg:gap-4">
        <!-- Toggle Sidebar Button -->
        <button onclick="toggleSidebar()" class="p-2 text-gray-500 hover:text-primary hover:bg-primary-light rounded-lg transition-colors">
            <svg id="toggle-icon" class="w-5 h-5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        <div>
            <h1 class="text-base lg:text-lg font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h1>
        </div>
    </div>
    <div class="flex items-center gap-2 lg:gap-3">
        <!-- Notification -->
        <button class="p-2 text-gray-500 hover:text-primary hover:bg-primary-light rounded-lg transition-colors relative">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full"></span>
        </button>
        <!-- User Dropdown -->
        <div class="flex items-center gap-2 lg:gap-3 pl-2 lg:pl-3 border-l border-gray-200">
            <div class="header-user-info text-right hidden md:block">
                <p class="text-sm font-medium text-gray-700">{{ Auth::user()->nama_instansi }}</p>
                <p class="text-xs text-gray-500">{{ Auth::user()->username }}</p>
            </div>
            <!-- Logout -->
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Keluar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</header>
