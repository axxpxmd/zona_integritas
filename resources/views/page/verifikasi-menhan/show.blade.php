@extends('layouts.app')

@section('title', 'Verifikasi Menhan Jawaban OPD')
@section('page-title', 'Detail Verifikasi Menhan: ' . $opd->n_opd)

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
                <a href="{{ route('verifikasi-menhan.index') }}"
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
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
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

            {{-- Card 2: Terverifikasi Menhan --}}
            <div class="bg-white rounded-xl p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-gray-900 font-bold text-base">Terverifikasi Menhan</h3>
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

            {{-- Card 3: Belum Diverifikasi --}}
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
                        <span class="font-bold text-gray-700 text-lg">{{ $verifikasiStats['belum_terverifikasi'] ?? 0 }}</span>
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
                            <p class="font-medium">Pilih Sub Kategori untuk Verifikasi Menhan</p>
                            <p class="mt-1">Pilih salah satu sub kategori di bawah untuk mulai melakukan verifikasi.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

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
                <div class="bg-primary px-6 py-4">
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
                                <div>
                                    <h4 class="text-base font-bold text-gray-900">{{ $kategori->kode }}. {{ $kategori->nama }}</h4>
                                    @if($kategori->deskripsi)
                                        <p class="text-xs text-gray-500 mt-1">{{ $kategori->deskripsi }}</p>
                                    @endif
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="text-right">
                                        <div class="text-gray-500 text-xs uppercase font-semibold tracking-wider">Bobot</div>
                                        <div class="text-gray-800 font-bold text-sm">{{ number_format($kategori->bobot, 2) }}</div>
                                    </div>
                                    <div class="w-px h-8 bg-gray-200"></div>
                                    <div class="text-right">
                                        <div class="text-gray-500 text-xs uppercase font-semibold tracking-wider">Nilai</div>
                                        <div class="text-gray-800 font-bold text-sm">{{ number_format($kategoriNilai, 2) }}</div>
                                    </div>
                                    <div class="w-px h-8 bg-gray-200"></div>
                                    <div class="text-right">
                                        <div class="text-gray-500 text-xs uppercase font-semibold tracking-wider">Capaian</div>
                                        <div class="text-gray-800 font-bold text-sm">{{ number_format($kategoriCapaian, 2) }}%</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Sub Kategori List --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($kategori->subKategoris as $subKategori)
                                    @php
                                        $subProgress = $progress[$subKategori->id] ?? null;
                                    @endphp
                                    <a href="{{ route('verifikasi-menhan.detail', [$periode->id, $opd->id, $subKategori->id]) }}"
                                        class="border border-gray-200 rounded-xl p-4 hover:border-primary transition">
                                        <div class="flex items-start justify-between gap-3">
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900">{{ $subKategori->kode }}. {{ $subKategori->nama }}</p>
                                                <p class="text-xs text-gray-500 mt-0.5">{{ $subKategori->deskripsi ?? '-' }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-xs text-gray-500">Terverifikasi</p>
                                                <p class="text-sm font-bold text-gray-900">{{ $subProgress['terverifikasi'] ?? 0 }}/{{ $subProgress['total'] ?? 0 }}</p>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                                                <div class="h-2 rounded-full {{ ($subProgress['persen'] ?? 0) >= 100 ? 'bg-green-500' : 'bg-teal-500' }}"
                                                    style="width: {{ $subProgress['persen'] ?? 0 }}%"></div>
                                            </div>
                                            <div class="mt-2 flex items-center justify-between text-xs text-gray-500">
                                                <span>Progress</span>
                                                <span class="font-semibold text-gray-700">{{ $subProgress['persen'] ?? 0 }}%</span>
                                            </div>
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
