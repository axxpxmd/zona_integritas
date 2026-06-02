<!-- Sidebar -->
<aside id="sidebar"
    class="sidebar w-64 bg-gradient-to-b from-primary to-primary-dark text-white flex-shrink-0 fixed h-full flex flex-col z-20">
    <!-- Logo Header -->
    <div class="logo-header px-5 py-5 border-b border-white/10">
        <div class="logo-wrapper flex items-center gap-3 transition-all duration-300">
            <div
                class="w-10 h-10 bg-white rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-black/10">
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
            <a href="{{ route('dashboard') }}" data-tooltip="Beranda"
                class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                      {{ request()->routeIs('dashboard') ? 'bg-white text-primary shadow-lg shadow-black/10' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="sidebar-text transition-all duration-300 whitespace-nowrap">Beranda</span>
            </a>
            <a href="{{ route('profile.index') }}" data-tooltip="Profil"
                class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                      {{ request()->routeIs('profile.*') ? 'bg-white text-primary shadow-lg shadow-black/10' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M5.121 17.804A8.966 8.966 0 0112 15a8.966 8.966 0 016.879 2.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M12 21a9 9 0 100-18 9 9 0 000 18z" />
                </svg>
                <span class="sidebar-text transition-all duration-300 whitespace-nowrap">Profil</span>
            </a>
        </div>

        @if (Auth::user()->role === 'operator')
            <p class="section-label text-[10px] font-semibold text-white/40 uppercase tracking-wider mb-3 mt-6">
                Lembar Kerja 
            </p>
            <div class="space-y-1.5">
                <a href="{{ route('kuesioner.index') }}" data-tooltip="Kuesioner"
                    class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                                  {{ request()->routeIs('kuesioner.*') && !request()->routeIs('kuesioner.revisi.*') ? 'bg-white text-primary shadow-lg shadow-black/10' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="sidebar-text transition-all duration-300 whitespace-nowrap">Lembar Kerja Evaluasi</span>
                </a>
                @php
                    $opd = Auth::user()->opd;
                    $periodeAktif = $opd ? \App\Models\Periode::where('status', 'aktif')->where('is_template', false)->orderByDesc('tahun')->first() : null;
                    $totalRevisiSidebar = 0;
                    if ($periodeAktif && $opd) {
                        $totalRevisiSidebar = \App\Models\Jawaban::where('periode_id', $periodeAktif->id)
                            ->where('opd_id', $opd->id)
                            ->where('status_verifikasi', 'direvisi')
                            ->whereNull('sub_pertanyaan_id')
                            ->count();
                    }
                @endphp
                @if($periodeAktif)
                <a href="{{ route('kuesioner.revisi.index', $periodeAktif->id) }}" data-tooltip="Revisi Jawaban"
                    class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                                  {{ request()->routeIs('kuesioner.revisi.*') ? 'bg-white text-primary shadow-lg shadow-black/10' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    <span class="sidebar-text transition-all duration-300 whitespace-nowrap flex items-center gap-2">
                        Revisi Jawaban
                        @if($totalRevisiSidebar > 0)
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full text-[10px] font-bold bg-orange-400 text-white">{{ $totalRevisiSidebar }}</span>
                        @endif
                    </span>
                </a>
                @endif
            </div>
        @endif

        @if (in_array(Auth::user()->role, ['admin', 'verifikator', 'verifikator_menpan']))
            <p class="section-label text-[10px] font-semibold text-white/40 uppercase tracking-wider mb-3 mt-6">
                Verifikasi
            </p>
            <div class="space-y-1.5">
                @if (in_array(Auth::user()->role, ['admin', 'verifikator']))
                    <a href="{{ route('verifikasi.index') }}" data-tooltip="Verifikasi"
                        class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                                      {{ request()->routeIs('verifikasi.*') ? 'bg-white text-primary shadow-lg shadow-black/10' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        <span class="sidebar-text transition-all duration-300 whitespace-nowrap">Verifikasi LKE</span>
                    </a>
                @endif
                @if (in_array(Auth::user()->role, ['admin', 'verifikator_menpan']))
                    <a href="{{ route('verifikasi-menpan.index') }}" data-tooltip="Verifikasi Menpan"
                        class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                                      {{ request()->routeIs('verifikasi-menpan.*') ? 'bg-white text-primary shadow-lg shadow-black/10' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span class="sidebar-text transition-all duration-300 whitespace-nowrap">Verifikasi Menpan</span>
                    </a>
                @endif
            </div>
        @endif

        @if(Auth::user()->role === 'admin')
            <!-- Grup Pengaturan Sistem -->
            <p class="section-label text-[10px] font-semibold text-white/40 uppercase tracking-wider mb-3 mt-6">Pengaturan
                Sistem</p>
            <div class="space-y-1.5">
                <a href="{{ route('periode.index') }}" data-tooltip="Data Periode"
                    class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                                  {{ request()->routeIs('periode.*') ? 'bg-white text-primary shadow-lg shadow-black/10' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="sidebar-text transition-all duration-300 whitespace-nowrap">Data Periode</span>
                </a>
                <a href="{{ route('opd.index') }}" data-tooltip="Data OPD"
                    class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                                  {{ request()->routeIs('opd.*') ? 'bg-white text-primary shadow-lg shadow-black/10' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span class="sidebar-text transition-all duration-300 whitespace-nowrap">Data Unit Kerja</span>
                </a>
                <a href="{{ route('user.index') }}" data-tooltip="Data Pengguna"
                    class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                                  {{ request()->routeIs('user.*') ? 'bg-white text-primary shadow-lg shadow-black/10' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span class="sidebar-text transition-all duration-300 whitespace-nowrap">Data Pengguna</span>
                </a>
            </div>

            <!-- Grup Struktur Kuesioner -->
            <p class="section-label text-[10px] font-semibold text-white/40 uppercase tracking-wider mb-3 mt-6">Struktur
                Kuesioner</p>
            <div class="space-y-1.5">
                <a href="{{ route('komponen.index') }}" data-tooltip="Data Komponen"
                    class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                                  {{ request()->routeIs('komponen.*') ? 'bg-white text-primary shadow-lg shadow-black/10' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="sidebar-text transition-all duration-300 whitespace-nowrap">Data Komponen</span>
                </a>
                <a href="{{ route('kategori.index') }}" data-tooltip="Data Kategori"
                    class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                                  {{ request()->routeIs('kategori.*') ? 'bg-white text-primary shadow-lg shadow-black/10' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <span class="sidebar-text transition-all duration-300 whitespace-nowrap">Data Kategori</span>
                </a>
                <a href="{{ route('sub-kategori.index') }}" data-tooltip="Data Sub Kategori"
                    class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                                  {{ request()->routeIs('sub-kategori.*') ? 'bg-white text-primary shadow-lg shadow-black/10' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                    <span class="sidebar-text transition-all duration-300 whitespace-nowrap">Data Sub Kategori</span>
                </a>
                <a href="{{ route('indikator.index') }}" data-tooltip="Data Indikator"
                    class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                                  {{ request()->routeIs('indikator.*') ? 'bg-white text-primary shadow-lg shadow-black/10' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span class="sidebar-text transition-all duration-300 whitespace-nowrap">Data Indikator</span>
                </a>
                <a href="{{ route('pertanyaan.index') }}" data-tooltip="Data Pertanyaan"
                    class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                                  {{ request()->routeIs('pertanyaan.*') ? 'bg-white text-primary shadow-lg shadow-black/10' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="sidebar-text transition-all duration-300 whitespace-nowrap">Data Pertanyaan</span>
                </a>
                <a href="{{ route('sub-pertanyaan.index') }}" data-tooltip="Data Sub Pertanyaan"
                    class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                                  {{ request()->routeIs('sub-pertanyaan.*') ? 'bg-white text-primary shadow-lg shadow-black/10' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <span class="sidebar-text transition-all duration-300 whitespace-nowrap">Data Sub Pertanyaan</span>
                </a>
            </div>
        @endif
    </nav>

    <!-- User Section at Bottom -->
    <div
        class="user-section p-4 border-t border-white/10 bg-black/10 flex items-center gap-3 transition-all duration-300">
        <div
            class="user-avatar w-10 h-10 rounded-xl bg-secondary flex items-center justify-center flex-shrink-0 shadow-lg shadow-black/10">
            <span class="text-primary font-bold text-sm">{{ strtoupper(substr(Auth::user()->username, 0, 1)) }}</span>
        </div>
        <div class="sidebar-user-info flex-1 min-w-0 overflow-hidden">
            <p class="text-sm font-semibold truncate">{{ Auth::user()->nama_instansi }}</p>
            <p class="text-xs text-white/50 truncate capitalize">{{ Auth::user()->role }}</p>
        </div>
    </div>
</aside>
