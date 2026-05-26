<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Zona Integritas Tangerang Selatan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: '#0164CA',
                            dark: '#0150A8',
                            light: '#3383d5',
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
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        .bg-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
</head>

<body class="min-h-screen bg-gray-50 flex">

    {{-- Left Side: Branding/Banner (Hidden on mobile) --}}
    <div
        class="hidden lg:flex lg:w-1/2 relative bg-gradient-to-br from-primary-dark via-primary to-blue-600 bg-pattern items-center justify-center p-12 overflow-hidden">
        {{-- Decorative Circles --}}
        <div
            class="absolute top-0 left-0 w-64 h-64 bg-white/10 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2">
        </div>
        <div
            class="absolute bottom-0 right-0 w-96 h-96 bg-secondary/20 rounded-full blur-3xl translate-x-1/3 translate-y-1/3">
        </div>

        <div class="relative z-10 w-full max-w-lg text-white">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-20 h-20 bg-white p-2 rounded-2xl shadow-xl flex items-center justify-center">
                    <img src="{{ asset('images/tangsel.png') }}" alt="Logo Tangsel" class="w-14 h-14 object-contain">
                </div>
            </div>
            <h1 class="text-4xl font-bold mb-4 leading-tight">Sistem Penilaian<br><span class="text-secondary">Zona
                    Integritas</span></h1>
            <p class="text-lg text-blue-100 mb-8 leading-relaxed">
                Pemerintah Kota Tangerang Selatan berkomitmen untuk mewujudkan wilayah bebas korupsi (WBK) dan wilayah
                birokrasi bersih dan melayani (WBBM) melalui tata kelola pemerintahan yang baik.
            </p>

            <div class="grid grid-cols-2 gap-6 mt-12 border-t border-white/20 pt-8">
                <div>
                    <div class="text-3xl font-bold mb-1">Transparan</div>
                    <div class="text-blue-200 text-sm">Penilaian yang objektif</div>
                </div>
                <div>
                    <div class="text-3xl font-bold mb-1">Akuntabel</div>
                    <div class="text-blue-200 text-sm">Dapat dipertanggungjawabkan</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Side: Login Form --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-12">
        <div class="w-full max-w-md">
            {{-- Mobile Logo (Only visible on small screens) --}}
            <div class="lg:hidden text-center mb-10">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-white shadow-md rounded-2xl mb-4">
                    <img src="{{ asset('images/tangsel.png') }}" alt="Logo Tangsel" class="w-10 h-10 object-contain">
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Zona Integritas</h1>
                <p class="text-primary font-medium text-sm mt-1">Kota Tangerang Selatan</p>
            </div>

            <div class="mb-10 text-center lg:text-left">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Selamat Datang</h2>
                <p class="text-gray-500">Silakan masuk ke akun Anda untuk melanjutkan</p>
            </div>

            {{-- Error Alert --}}
            @if($errors->any())
                <div class="mb-8 bg-red-50 border border-red-200 rounded-xl p-4 flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="text-sm text-red-700 font-medium">
                        {{ $errors->first() }}
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                {{-- Username --}}
                <div>
                    <label for="username" class="block text-sm font-semibold text-gray-700 mb-2">Username</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400 group-focus-within:text-primary transition-colors"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <input type="text" id="username" name="username" value="{{ old('username') }}"
                            class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none transition-all font-medium text-gray-900 placeholder-gray-400"
                            placeholder="Masukkan username Anda" required autofocus>
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400 group-focus-within:text-primary transition-colors"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input type="password" id="password" name="password"
                            class="w-full pl-11 pr-12 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none transition-all font-medium text-gray-900 placeholder-gray-400"
                            placeholder="Masukkan password Anda" required>
                        <button type="button" onclick="togglePassword()"
                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-700 transition-colors focus:outline-none">
                            <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg id="eye-off-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Submit Button --}}
                <button type="submit"
                    class="w-full bg-primary text-white py-3.5 px-4 rounded-xl text-sm font-bold hover:bg-primary-dark hover:shadow-primary/40 focus:ring-4 focus:ring-primary/20 active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                    Masuk ke Sistem
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </form>

            {{-- Footer --}}
            <p class="text-center text-gray-400 text-sm mt-12 font-medium">
                &copy; {{ date('Y') }} Zona Integritas<br>Pemerintah Kota Tangerang Selatan
            </p>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            const eyeOffIcon = document.getElementById('eye-off-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeOffIcon.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeOffIcon.classList.add('hidden');
            }
        }
    </script>
</body>

</html>
