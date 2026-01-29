@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Selamat Datang, <span class="text-primary">{{ Auth::user()->nama_instansi }}!</span> 👋
            </h1>
            <p class="text-gray-500 mt-1">Kelola seluruh sistem dan pantau performa kuesioner Zona Integritas</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-lg">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-sm font-medium text-gray-700">{{ now()->translatedFormat('d F Y') }}</span>
            </div>
            <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-lg">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="text-sm font-medium text-gray-700 capitalize">{{ Auth::user()->role }}</span>
            </div>
        </div>
    </div>

    @if(Auth::user()->role === 'admin')
    <!-- Admin Statistics -->
    <div>
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Statistik Master Data</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Periode -->
            <div class="bg-white rounded-xl p-5 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900">{{ $stats['periode'] }}</h3>
                <p class="text-sm text-gray-600 mt-1">Total Periode</p>
                <p class="text-xs text-green-600 mt-2">{{ $stats['periodeAktif'] }} aktif</p>
            </div>

            <!-- Komponen -->
            <div class="bg-white rounded-xl p-5 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900">{{ $stats['komponen'] }}</h3>
                <p class="text-sm text-gray-600 mt-1">Total Komponen</p>
                <p class="text-xs text-green-600 mt-2">{{ $stats['komponenAktif'] }} aktif</p>
            </div>

            <!-- Kategori -->
            <div class="bg-white rounded-xl p-5 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900">{{ $stats['kategori'] }}</h3>
                <p class="text-sm text-gray-600 mt-1">Total Kategori</p>
                <p class="text-xs text-green-600 mt-2">{{ $stats['kategoriAktif'] }} aktif</p>
            </div>

            <!-- Sub Kategori -->
            <div class="bg-white rounded-xl p-5 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-cyan-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900">{{ $stats['subKategori'] }}</h3>
                <p class="text-sm text-gray-600 mt-1">Total Sub Kategori</p>
                <p class="text-xs text-green-600 mt-2">{{ $stats['subKategoriAktif'] }} aktif</p>
            </div>

            <!-- Indikator -->
            <div class="bg-white rounded-xl p-5 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900">{{ $stats['indikator'] }}</h3>
                <p class="text-sm text-gray-600 mt-1">Total Indikator</p>
                <p class="text-xs text-green-600 mt-2">{{ $stats['indikatorAktif'] }} aktif</p>
            </div>

            <!-- Pertanyaan -->
            <div class="bg-white rounded-xl p-5 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900">{{ $stats['pertanyaan'] }}</h3>
                <p class="text-sm text-gray-600 mt-1">Total Pertanyaan</p>
                <p class="text-xs text-green-600 mt-2">{{ $stats['pertanyaanAktif'] }} aktif</p>
            </div>

            <!-- Sub Pertanyaan -->
            <div class="bg-white rounded-xl p-5 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-violet-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900">{{ $stats['subPertanyaan'] }}</h3>
                <p class="text-sm text-gray-600 mt-1">Total Sub Pertanyaan</p>
                <p class="text-xs text-green-600 mt-2">{{ $stats['subPertanyaanAktif'] }} aktif</p>
            </div>

            <!-- OPD -->
            <div class="bg-white rounded-xl p-5 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-rose-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900">{{ $stats['opd'] }}</h3>
                <p class="text-sm text-gray-600 mt-1">Total OPD</p>
                <p class="text-xs text-green-600 mt-2">{{ $stats['opdAktif'] }} aktif</p>
            </div>

            <!-- User -->
            <div class="bg-white rounded-xl p-5 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900">{{ $stats['user'] }}</h3>
                <p class="text-sm text-gray-600 mt-1">Total Pengguna</p>
            </div>
        </div>
    </div>

    <!-- Statistik Jawaban -->
    <div>
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Statistik Kuesioner</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="bg-gradient-to-br from-primary to-primary-dark rounded-xl p-6 text-white hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-3xl font-bold">{{ $stats['jawaban'] }}</h3>
                <p class="text-sm text-white/80 mt-1">Total Jawaban Kuesioner</p>
                <p class="text-xs text-secondary mt-2">Dari seluruh OPD</p>
            </div>
        </div>
    </div>
    @else
    <!-- Operator/Verifikator Statistics -->
    <div>
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Statistik Kuesioner</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Periode Aktif -->
            <div class="bg-white rounded-xl p-5 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900">{{ $stats['periodeAktif'] }}</h3>
                <p class="text-sm text-gray-600 mt-1">Periode Aktif</p>
            </div>

            <!-- Pertanyaan -->
            <div class="bg-white rounded-xl p-5 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900">{{ $stats['pertanyaanTotal'] }}</h3>
                <p class="text-sm text-gray-600 mt-1">Total Pertanyaan</p>
            </div>

            <!-- Sub Pertanyaan -->
            <div class="bg-white rounded-xl p-5 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-violet-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900">{{ $stats['subPertanyaanTotal'] }}</h3>
                <p class="text-sm text-gray-600 mt-1">Total Sub Pertanyaan</p>
            </div>

            <!-- Jawaban Saya -->
            <div class="bg-gradient-to-br from-primary to-primary-dark rounded-xl p-5 text-white hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold">{{ $stats['jawabanSaya'] }}</h3>
                <p class="text-sm text-white/80 mt-1">Jawaban Saya</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Quick Actions -->
    <div>
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Aksi Cepat</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <a href="{{ route('kuesioner.index') }}" class="bg-white rounded-xl p-5 hover:shadow-lg transition-all group">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center group-hover:bg-primary group-hover:scale-110 transition-all">
                        <svg class="w-6 h-6 text-primary group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Isi Kuesioner</h3>
                        <p class="text-sm text-gray-500">Mulai mengisi kuesioner</p>
                    </div>
                </div>
            </a>

            @if(Auth::user()->role === 'admin')
            <a href="{{ route('periode.index') }}" class="bg-white rounded-xl p-5 hover:shadow-lg transition-all group">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center group-hover:bg-purple-600 group-hover:scale-110 transition-all">
                        <svg class="w-6 h-6 text-purple-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Kelola Periode</h3>
                        <p class="text-sm text-gray-500">Atur periode kuesioner</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('pertanyaan.index') }}" class="bg-white rounded-xl p-5 hover:shadow-lg transition-all group">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center group-hover:bg-indigo-600 group-hover:scale-110 transition-all">
                        <svg class="w-6 h-6 text-indigo-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Kelola Pertanyaan</h3>
                        <p class="text-sm text-gray-500">Atur master pertanyaan</p>
                    </div>
                </div>
            </a>
            @endif
        </div>
    </div>
</div>
@endsection
