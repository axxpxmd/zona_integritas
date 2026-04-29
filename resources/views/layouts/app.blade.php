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
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="flex min-h-screen">
        <!-- Overlay for mobile -->
        <div id="sidebar-overlay" class="sidebar-overlay lg:hidden" onclick="closeSidebarMobile()"></div>

        @include('layouts.sidebar')

        <!-- Main Content -->
        <div id="main-content" class="main-content flex-1 ml-0 lg:ml-64" style="background-color: #F5F5F5">
            @include('layouts.header')

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
