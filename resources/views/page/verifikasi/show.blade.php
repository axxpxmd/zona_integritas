@extends('layouts.app')

@section('title', 'Verifikasi Jawaban OPD')
@section('page-title', 'Detail Verifikasi: ' . $opd->n_opd)

@section('content')
    @php
        $now = \Carbon\Carbon::now()->startOfDay();
        $start = \Carbon\Carbon::parse($periode->tanggal_mulai_verifikasi)->startOfDay();
        $end = \Carbon\Carbon::parse($periode->tanggal_selesai_verifikasi)->endOfDay();
        $isCanVerify = $now->between($start, $end);
    @endphp
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('verifikasi.index') }}"
                    class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $periode->nama_periode }}</h2>
                    <p class="text-sm text-gray-500 mt-0.5">{{ $opd->n_opd }}</p>
                </div>
            </div>
            <div class="flex flex-col items-end gap-1">
                <div class="text-sm text-gray-600">
                    <span class="font-medium">Waktu Verifikasi:</span> {{ $start->format('d M Y') }} -
                    {{ $end->format('d M Y') }}
                </div>
                @if($now->lt($start))
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Belum
                        Dimulai</span>
                @elseif($now->gt($end))
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Waktu
                        Habis</span>
                @else
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Sedang
                        Berjalan</span>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <p class="text-sm text-green-800">{{ session('success') }}</p>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <p class="text-sm text-red-800">{{ session('error') }}</p>
            </div>
        @endif

        {{-- Stats Dashboard --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-5">

            {{-- Card 1: Total Pertanyaan --}}
            <div class="bg-white rounded-xl p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-gray-900 font-bold text-base">Total Pertanyaan</h3>
                </div>
                <div class="flex justify-between items-end mb-2">
                    <span class="text-3xl font-bold text-gray-900">{{ $verifikasiStats['total_pertanyaan'] ?? 0 }}</span>
                    <span
                        class="text-sm font-semibold text-blue-600">{{ $verifikasiStats['total_pertanyaan'] > 0 ? round(($verifikasiStats['terverifikasi'] / $verifikasiStats['total_pertanyaan']) * 100) : 0 }}%
                        terverifikasi</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2">
                    <div class="bg-blue-500 h-2 rounded-full"
                        style="width: {{ $verifikasiStats['total_pertanyaan'] > 0 ? round(($verifikasiStats['terverifikasi'] / $verifikasiStats['total_pertanyaan']) * 100) : 0 }}%">
                    </div>
                </div>
            </div>

            {{-- Card 2: Terverifikasi --}}
            <div class="bg-white rounded-xl p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-gray-900 font-bold text-base">Terverifikasi</h3>
                </div>
                <div class="space-y-2">
                    <div class="flex items-center justify-between p-2.5 bg-green-50 rounded-lg border border-green-100">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-green-500"></div>
                            <span class="text-sm font-medium text-green-800">Disetujui</span>
                        </div>
                        <span class="font-bold text-green-700 text-lg">{{ $verifikasiStats['terverifikasi'] ?? 0 }}</span>
                    </div>
                </div>
            </div>

            {{-- Card 3: Direvisi --}}
            <div class="bg-white rounded-xl p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-lg bg-orange-50 flex items-center justify-center text-orange-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <h3 class="text-gray-900 font-bold text-base">Perlu Revisi</h3>
                </div>
                <div class="space-y-2">
                    <div class="flex items-center justify-between p-2.5 bg-orange-50 rounded-lg border border-orange-100">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-orange-500"></div>
                            <span class="text-sm font-medium text-orange-800">Dikirim ke Operator</span>
                        </div>
                        <span class="font-bold text-orange-600 text-lg">{{ $verifikasiStats['direvisi'] ?? 0 }}</span>
                    </div>
                </div>
            </div>

            {{-- Card 4: Belum Diverifikasi --}}
            <div class="bg-white rounded-xl p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-gray-900 font-bold text-base">Belum Diverifikasi</h3>
                </div>
                <div class="space-y-2">
                    <div class="flex items-center justify-between p-2.5 bg-gray-50 rounded-lg border border-gray-100">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-gray-400"></div>
                            <span class="text-sm font-medium text-gray-700">Menunggu Dicek</span>
                        </div>
                        <span
                            class="font-bold text-gray-700 text-lg">{{ $verifikasiStats['belum_terverifikasi'] ?? 0 }}</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- Info --}}
        @if($isSent)
            {{-- --}}
        @elseif(!$isCanVerify)
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div class="text-sm text-red-800">
                        <p class="font-medium">Waktu Verifikasi Tidak Aktif</p>
                        <p class="mt-1">Anda hanya dapat melihat data karena berada di luar waktu verifikasi.</p>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="text-sm text-blue-800">
                            <p class="font-medium">Pilih Sub Kategori untuk Verifikasi</p>
                            <p class="mt-1">Pilih salah satu sub kategori di bawah untuk mulai melakukan verifikasi secara
                                spesifik.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        {{-- @if(config('app.debug') || env('APP_ENV') === 'local')
            <form action="{{ route('verifikasi.verify-all-dev', [$periode->id, $opd->id]) }}" method="POST"
                onsubmit="return confirm('Yakin ingin verifikasi semua pertanyaan untuk OPD ini secara otomatis (DEV ONLY)?');">
                @csrf
                <button type="submit"
                    class="shrink-0 inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors shadow-sm gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    [DEV] Verifikasi Semua
                </button>
            </form>
        @endif --}}

        {{-- Komponen Loop --}}
        @foreach($komponens as $komponen)
            @php
                $komponenNilai = 0;
                foreach ($komponen->kategoris as $cat) {
                    foreach ($cat->subKategoris as $subCat) {
                        if (isset($progress[$subCat->id])) {
                            $komponenNilai += $progress[$subCat->id]['nilai'];
                        }
                    }
                }
                $komponenCapaian = $komponen->bobot > 0 ? ($komponenNilai / $komponen->bobot * 100) : 0;
            @endphp
            <div class="bg-white rounded-xl overflow-hidden">
                {{-- Komponen Header --}}
                <div class="bg-[#0E7C7B] px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-white">{{ $komponen->kode }}. {{ $komponen->nama }}</h3>
                            @if($komponen->deskripsi)
                                <p class="text-sm text-white/80 mt-1">{{ $komponen->deskripsi }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                <div class="text-white/80 text-xs uppercase font-semibold tracking-wider">Bobot</div>
                                <div class="text-white font-bold text-sm">{{ number_format($komponen->bobot, 2) }}</div>
                            </div>
                            <div class="w-px h-8 bg-white/20"></div>
                            <div class="text-right">
                                <div class="text-white/80 text-xs uppercase font-semibold tracking-wider">Nilai</div>
                                <div class="text-white font-bold text-sm">{{ number_format($komponenNilai, 2) }}</div>
                            </div>
                            <div class="w-px h-8 bg-white/20"></div>
                            <div class="text-right">
                                <div class="text-white/80 text-xs uppercase font-semibold tracking-wider">Capaian</div>
                                <div class="text-white font-bold text-sm">{{ number_format($komponenCapaian, 2) }}%</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kategori & Sub Kategori --}}
                <div class="divide-y divide-gray-200">
                    @foreach($komponen->kategoris as $kategori)
                        @php
                            $kategoriNilai = 0;
                            foreach ($kategori->subKategoris as $subCat) {
                                if (isset($progress[$subCat->id])) {
                                    $kategoriNilai += $progress[$subCat->id]['nilai'];
                                }
                            }
                            $kategoriCapaian = $kategori->bobot > 0 ? ($kategoriNilai / $kategori->bobot * 100) : 0;
                        @endphp
                        <div class="p-6">
                            {{-- Kategori Header --}}
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <span class="text-sm font-bold text-gray-700">{{ $kategori->kode }}</span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <h4 class="text-base font-semibold text-gray-900">{{ $kategori->nama }}</h4>
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs font-medium">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                                                </svg>
                                                {{ number_format($kategori->bobot, 2) }}
                                            </span>
                                        </div>
                                        @if($kategori->deskripsi)
                                            <p class="text-xs text-gray-500 mt-0.5">{{ $kategori->deskripsi }}</p>
                                        @endif
                                    </div>
                                </div>

                                {{-- Nilai & Capaian Kategori --}}
                                <div class="flex items-center gap-3 ml-4">
                                    <div
                                        class="bg-gray-50 rounded-lg px-3 py-2 border border-gray-100 flex flex-col justify-center items-end min-w-[70px]">
                                        <span class="text-[10px] text-gray-500 uppercase font-semibold tracking-wider">Nilai</span>
                                        <span class="text-sm font-bold text-gray-900">{{ number_format($kategoriNilai, 2) }}</span>
                                    </div>
                                    <div
                                        class="bg-gray-50 rounded-lg px-3 py-2 border border-gray-100 flex flex-col justify-center items-end min-w-[70px]">
                                        <span
                                            class="text-[10px] text-gray-500 uppercase font-semibold tracking-wider">Capaian</span>
                                        <span
                                            class="text-sm font-bold {{ $kategoriCapaian >= 80 ? 'text-green-600' : ($kategoriCapaian >= 50 ? 'text-yellow-600' : 'text-red-600') }}">{{ number_format($kategoriCapaian, 2) }}%</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Sub Kategori Grid --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 ml-13">
                                @foreach($kategori->subKategoris as $subKategori)
                                    @php
                                        $prog = $progress[$subKategori->id] ?? ['total' => 0, 'terverifikasi' => 0, 'persen' => 0, 'nilai' => 0, 'capaian' => 0];
                                    @endphp
                                    <a href="{{ route('verifikasi.detail', [$periode->id, $opd->id, $subKategori->id]) }}"
                                        class="flex flex-col h-full bg-white border-2 border-gray-200 rounded-xl p-4 hover:border-[#0E7C7B] hover:shadow-lg transition-all group">
                                        {{-- Sub Kategori Header --}}
                                        <div class="flex items-start gap-3 mb-3">
                                            <div
                                                class="w-8 h-8 bg-[#0E7C7B]/10 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-[#0E7C7B] group-hover:text-white transition-colors">
                                                <span
                                                    class="text-xs font-bold text-[#0E7C7B] group-hover:text-white">{{ $subKategori->kode }}</span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h5
                                                    class="text-sm font-semibold text-gray-900 group-hover:text-[#0E7C7B] transition-colors line-clamp-2">
                                                    {{ $subKategori->nama }}
                                                </h5>
                                                <span
                                                    class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-accent/20 text-gray-700 rounded text-xs font-medium mt-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                                                    </svg>
                                                    Bobot: {{ $subKategori->bobot }}
                                                </span>
                                            </div>
                                        </div>

                                        {{-- Nilai & Capaian --}}
                                        <div class="grid grid-cols-2 gap-2 mb-3">
                                            <div
                                                class="bg-gray-50 rounded-lg p-2.5 border border-gray-100 flex flex-col justify-center">
                                                <p class="text-[10px] text-gray-500 uppercase font-semibold tracking-wider mb-0.5">Nilai
                                                </p>
                                                <p class="text-sm font-bold text-gray-900">{{ number_format($prog['nilai'], 2) }}</p>
                                            </div>
                                            <div
                                                class="bg-gray-50 rounded-lg p-2.5 border border-gray-100 flex flex-col justify-center">
                                                <p class="text-[10px] text-gray-500 uppercase font-semibold tracking-wider mb-0.5">
                                                    Capaian</p>
                                                <p
                                                    class="text-sm font-bold {{ $prog['capaian'] >= 80 ? 'text-green-600' : ($prog['capaian'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                                    {{ number_format($prog['capaian'], 2) }}%
                                                </p>
                                            </div>
                                        </div>

                                        {{-- Progress Bar --}}
                                        <div class="mt-auto mb-2">
                                            <div class="flex items-center justify-between text-xs text-gray-600 mb-1">
                                                <span>Progress Verifikasi</span>
                                                <span class="font-semibold">{{ $prog['terverifikasi'] }}/{{ $prog['total'] }}</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                                <div class="h-full transition-all duration-300 rounded-full {{ $prog['persen'] == 100 ? 'bg-green-500' : 'bg-[#0E7C7B]' }}"
                                                    style="width: {{ $prog['persen'] }}%"></div>
                                            </div>
                                        </div>

                                        {{-- Status Badge --}}
                                        <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
                                            @if($prog['persen'] == 100)
                                                <span
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Selesai
                                                </span>
                                            @elseif($prog['persen'] > 0)
                                                <span
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Dalam Progress
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M12 4v16m8-8H4" />
                                                    </svg>
                                                    Belum Mulai
                                                </span>
                                            @endif

                                            <svg class="w-5 h-5 text-gray-400 group-hover:text-[#0E7C7B] group-hover:translate-x-1 transition-all"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
@endsection
