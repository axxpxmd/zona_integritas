<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Zona Integritas</title>
    <link rel="icon" type="image/png" href="{{ asset('images/tangsel.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: '#0164CA',
                            dark: '#0150A8',
                            light: '#E8F4FD',
                        },
                        secondary: '#F7D558',
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }

        /* Prevent horizontal scroll */
        html, body {
            overflow-x: hidden;
        }

        /* Sidebar Base */
        .sidebar {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }

        /* Sidebar Collapsed State - Desktop */
        .sidebar.collapsed {
            width: 76px;
        }

        .sidebar.collapsed .sidebar-logo-text,
        .sidebar.collapsed .sidebar-user-info,
        .sidebar.collapsed .section-label {
            display: none;
        }

        .sidebar.collapsed .sidebar-text {
            opacity: 0;
            width: 0;
            overflow: hidden;
            white-space: nowrap;
        }

        .sidebar.collapsed .logo-wrapper {
            justify-content: center;
            padding: 0;
        }

        .sidebar.collapsed .logo-header {
            padding: 20px 18px;
        }

        .sidebar.collapsed .nav-wrapper {
            padding: 16px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .sidebar.collapsed .nav-wrapper > div {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .sidebar.collapsed .nav-link {
            width: 48px;
            height: 48px;
            padding: 0 !important;
            gap: 0 !important;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 0 4px 0;
        }

        .sidebar.collapsed .nav-link svg {
            margin: 0 !important;
        }

        .sidebar.collapsed .user-section {
            padding: 16px 14px;
            justify-content: center;
        }

        .sidebar.collapsed .user-avatar {
            margin: 0;
        }

        /* Main Content */
        .main-content {
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .main-content.expanded {
            margin-left: 76px;
        }

        /* Tooltip - Desktop only */
        .nav-link {
            position: relative;
        }
        .sidebar.collapsed .nav-link::after {
            content: attr(data-tooltip);
            position: absolute;
            left: 60px;
            top: 50%;
            transform: translateY(-50%) scale(0.9);
            background: #0f172a;
            color: white;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s ease;
            z-index: 1000;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.2);
        }
        .sidebar.collapsed .nav-link::before {
            content: '';
            position: absolute;
            left: 52px;
            top: 50%;
            transform: translateY(-50%) scale(0.9);
            border: 6px solid transparent;
            border-right-color: #0f172a;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s ease;
            z-index: 1000;
        }
        .sidebar.collapsed .nav-link:hover::after,
        .sidebar.collapsed .nav-link:hover::before {
            opacity: 1;
            visibility: visible;
            transform: translateY(-50%) scale(1);
        }

        /* Overlay */
        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 15;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        /* ========== MOBILE RESPONSIVE ========== */
        @media (max-width: 1024px) {
            /* Sidebar - Hidden by default on mobile */
            .sidebar {
                transform: translateX(-100%);
                width: 280px !important;
                z-index: 50;
            }
            .sidebar.mobile-open {
                transform: translateX(0);
            }

            /* Reset collapsed state on mobile */
            .sidebar.collapsed {
                width: 280px !important;
                transform: translateX(-100%);
            }
            .sidebar.collapsed.mobile-open {
                transform: translateX(0);
            }
            .sidebar.collapsed .sidebar-logo-text,
            .sidebar.collapsed .sidebar-user-info,
            .sidebar.collapsed .section-label,
            .sidebar.collapsed .sidebar-text {
                display: block;
                opacity: 1;
                width: auto;
                visibility: visible;
            }
            .sidebar.collapsed .logo-wrapper {
                justify-content: flex-start;
            }
            .sidebar.collapsed .logo-header {
                padding: 20px;
            }
            .sidebar.collapsed .nav-wrapper {
                padding: 20px 16px;
                align-items: stretch;
            }
            .sidebar.collapsed .nav-wrapper > div {
                align-items: stretch;
            }
            .sidebar.collapsed .nav-link {
                width: auto;
                height: auto;
                padding: 12px 16px !important;
                gap: 12px !important;
                justify-content: flex-start;
                margin: 0 0 4px 0;
            }
            .sidebar.collapsed .nav-link::after,
            .sidebar.collapsed .nav-link::before {
                display: none !important;
            }
            .sidebar.collapsed .user-section {
                padding: 16px;
                justify-content: flex-start;
            }

            /* Main Content - Full width on mobile */
            .main-content {
                margin-left: 0 !important;
            }
            .main-content.expanded {
                margin-left: 0 !important;
            }

            /* Hide desktop toggle icon behavior */
            #toggle-icon {
                transform: none !important;
            }
        }

        @media (max-width: 640px) {
            .sidebar {
                width: 100% !important;
                max-width: 300px;
            }
            .sidebar.collapsed {
                width: 100% !important;
                max-width: 300px;
            }

            /* Header adjustments */
            .header-user-info {
                display: none !important;
            }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="flex min-h-screen">
        <!-- Overlay for mobile -->
        <div id="sidebar-overlay" class="sidebar-overlay lg:hidden" onclick="closeSidebarMobile()"></div>

        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar w-64 bg-gradient-to-b from-primary to-primary-dark text-white flex-shrink-0 fixed h-full flex flex-col z-20">
            <!-- Logo Header -->
            <div class="logo-header px-5 py-5 border-b border-white/10">
                <div class="logo-wrapper flex items-center gap-3 transition-all duration-300">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-black/10">
                        <img src="{{ asset('images/tangsel.png') }}" alt="Logo Tangsel" class="w-7 h-7 object-contain">
                    </div>
                    <div class="sidebar-logo-text overflow-hidden">
                        <h1 class="text-base font-bold leading-tight whitespace-nowrap">Zona Integritas</h1>
                        <p class="text-[10px] text-white/60 whitespace-nowrap">Kota Tangerang Selatan</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="nav-wrapper flex-1 py-5 px-4 overflow-y-auto overflow-x-hidden">
                <p class="section-label text-[10px] font-semibold text-white/40 uppercase tracking-wider mb-3">Menu Utama</p>
                <div class="space-y-1.5">
                    <a href="{{ route('dashboard') }}"
                       data-tooltip="Beranda"
                       class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                              {{ request()->routeIs('dashboard') ? 'bg-white text-primary shadow-lg shadow-black/10' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span class="sidebar-text transition-all duration-300 whitespace-nowrap">Beranda</span>
                    </a>
                    <a href="{{ route('kuesioner.index') }}"
                       data-tooltip="Kuesioner"
                       class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                              {{ request()->routeIs('kuesioner.*') ? 'bg-white text-primary shadow-lg shadow-black/10' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="sidebar-text transition-all duration-300 whitespace-nowrap">Kuesioner</span>
                    </a>
                </div>

                <p class="section-label text-[10px] font-semibold text-white/40 uppercase tracking-wider mb-3 mt-6">Master Data</p>
                <div class="space-y-1.5">
                    <a href="{{ route('periode.index') }}"
                       data-tooltip="Data Periode"
                       class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                              {{ request()->routeIs('periode.*') ? 'bg-white text-primary shadow-lg shadow-black/10' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="sidebar-text transition-all duration-300 whitespace-nowrap">Data Periode</span>
                    </a>
                    <a href="{{ route('komponen.index') }}"
                       data-tooltip="Data Komponen"
                       class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                              {{ request()->routeIs('komponen.*') ? 'bg-white text-primary shadow-lg shadow-black/10' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="sidebar-text transition-all duration-300 whitespace-nowrap">Data Komponen</span>
                    </a>
                    <a href="{{ route('opd.index') }}"
                       data-tooltip="Data OPD"
                       class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                              {{ request()->routeIs('opd.*') ? 'bg-white text-primary shadow-lg shadow-black/10' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span class="sidebar-text transition-all duration-300 whitespace-nowrap">Data OPD</span>
                    </a>
                    <a href="{{ route('user.index') }}"
                       data-tooltip="Data Pengguna"
                       class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                              {{ request()->routeIs('user.*') ? 'bg-white text-primary shadow-lg shadow-black/10' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span class="sidebar-text transition-all duration-300 whitespace-nowrap">Data Pengguna</span>
                    </a>
                </div>
            </nav>

            <!-- User Section at Bottom -->
            <div class="user-section p-4 border-t border-white/10 bg-black/10 flex items-center gap-3 transition-all duration-300">
                <div class="user-avatar w-10 h-10 rounded-xl bg-secondary flex items-center justify-center flex-shrink-0 shadow-lg shadow-black/10">
                    <span class="text-primary font-bold text-sm">{{ strtoupper(substr(Auth::user()->username, 0, 1)) }}</span>
                </div>
                <div class="sidebar-user-info flex-1 min-w-0 overflow-hidden">
                    <p class="text-sm font-semibold truncate">{{ Auth::user()->nama_instansi }}</p>
                    <p class="text-xs text-white/50 truncate capitalize">{{ Auth::user()->role }}</p>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div id="main-content" class="main-content flex-1 ml-0 lg:ml-64" style="background-color: #F5F5F5">
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

            <!-- Page Content -->
            <main class="p-4 lg:p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        const isMobile = () => window.innerWidth < 1024;

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const toggleIcon = document.getElementById('toggle-icon');
            const overlay = document.getElementById('sidebar-overlay');

            if (isMobile()) {
                // Mobile behavior - slide in/out
                sidebar.classList.toggle('mobile-open');
                overlay.classList.toggle('active');
                document.body.style.overflow = sidebar.classList.contains('mobile-open') ? 'hidden' : '';
            } else {
                // Desktop behavior - collapse/expand
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');

                if (sidebar.classList.contains('collapsed')) {
                    toggleIcon.style.transform = 'rotate(180deg)';
                    localStorage.setItem('sidebarCollapsed', 'true');
                } else {
                    toggleIcon.style.transform = 'rotate(0deg)';
                    localStorage.setItem('sidebarCollapsed', 'false');
                }
            }
        }

        function closeSidebarMobile() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        // Restore sidebar state from localStorage (desktop only)
        document.addEventListener('DOMContentLoaded', function() {
            if (!isMobile()) {
                const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                if (isCollapsed) {
                    document.getElementById('sidebar').classList.add('collapsed');
                    document.getElementById('main-content').classList.add('expanded');
                    document.getElementById('toggle-icon').style.transform = 'rotate(180deg)';
                }
            }
        });

        // Handle resize events
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                const sidebar = document.getElementById('sidebar');
                const mainContent = document.getElementById('main-content');
                const overlay = document.getElementById('sidebar-overlay');

                if (isMobile()) {
                    // Reset to mobile state
                    sidebar.classList.remove('mobile-open');
                    overlay.classList.remove('active');
                    document.body.style.overflow = '';
                } else {
                    // Apply desktop state from localStorage
                    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                    if (isCollapsed) {
                        sidebar.classList.add('collapsed');
                        mainContent.classList.add('expanded');
                    } else {
                        sidebar.classList.remove('collapsed');
                        mainContent.classList.remove('expanded');
                    }
                }
            }, 250);
        });
    </script>

    @stack('scripts')
</body>
</html>
