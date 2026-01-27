<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Zona Integritas</title>
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
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-primary text-white flex-shrink-0 fixed h-full">
            <!-- Logo Header -->
            <div class="px-4 py-4">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/tangsel.png') }}" alt="Logo Tangsel" class="w-10 h-10 object-contain">
                    <div>
                        <h1 class="text-lg font-bold leading-tight">Portal CMS</h1>
                        <p class="text-xs text-white/70">Tangerang Selatan</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="mt-4 px-3">
                <div class="space-y-1">
                    <a href="{{ route('cms.dashboard') }}"
                       class="flex items-center gap-3 px-4 py-2.5 rounded-full text-sm font-medium transition-colors
                              {{ request()->routeIs('cms.dashboard') ? 'bg-white/20 text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Beranda
                    </a>
                    <a href="{{ route('cms.opd.index') }}"
                       class="flex items-center gap-3 px-4 py-2.5 rounded-full text-sm font-medium transition-colors
                              {{ request()->routeIs('cms.opd.*') ? 'bg-white/20 text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Data OPD
                    </a>
                    <a href="{{ route('cms.user.index') }}"
                       class="flex items-center gap-3 px-4 py-2.5 rounded-full text-sm font-medium transition-colors
                              {{ request()->routeIs('cms.user.*') ? 'bg-white/20 text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Data Pengguna
                    </a>
                </div>
            </nav>

            <!-- User Section at Bottom -->
            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-white/10">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-secondary flex items-center justify-center">
                        <span class="text-primary font-semibold text-sm">A</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate">Administrator</p>
                        <p class="text-xs text-white/60 truncate">admin@example.com</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 ml-64" style="background-color: #F5F5F5">
            <!-- Top Header -->
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 sticky top-0 z-10">
                <div>
                    <h1 class="text-lg font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h1>
                </div>
                <div class="flex items-center gap-4">
                    <!-- Notification -->
                    <button class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </button>
                    <!-- Logout -->
                    <button class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-colors">
                        Keluar
                    </button>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-6">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
