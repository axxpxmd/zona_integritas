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
                @if ($now->lt($start))
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

        @if (session('success'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <p class="text-sm text-green-800">{{ session('success') }}</p>
            </div>
        @endif
        @if (session('error'))
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

            {{-- Card 2: Disetujui --}}
            <div class="bg-white rounded-xl p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-gray-900 font-bold text-base">Disetujui</h3>
                </div>
                <div class="space-y-2">
                    <div class="flex items-center justify-between p-2.5 bg-green-50 rounded-lg border border-green-100">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-green-500"></div>
                            <span class="text-sm font-medium text-green-800">Disetujui</span>
                        </div>
                        <span class="font-bold text-green-700 text-lg">{{ $verifikasiStats['disetujui'] ?? 0 }}</span>
                    </div>
                </div>
            </div>

            {{-- Card 4: Direvisi --}}
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

            {{-- Card 5: Belum Diverifikasi --}}
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
        @if ($isSent)
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
                            <p class="mt-1">Pilih salah satu sub kategori di bawah untuk mulai melakukan verifikasi
                                secara
                                spesifik.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Kirim ke Menpan Card --}}
        <div
            class="bg-white rounded-xl p-6 border {{ $isSentToMenpan ? 'border-green-200 bg-green-50/30' : ($isReadySendMenpan ? 'border-blue-200 bg-blue-50/30' : 'border-gray-200') }}">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-start gap-4">
                    @if ($isSentToMenpan)
                        <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Validasi Selesai</h3>
                            <p class="text-sm text-gray-600 mt-1">Hasil verifikasi Unit Kerja ini sudah berhasil dikirim ke
                                Verifikator Menpan.</p>
                        </div>
                    @elseif($isReadySendMenpan)
                        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div>
                            @if ($meetsWbk)
                                <h3 class="text-lg font-bold text-gray-900">Siap Dikirim</h3>
                                <p class="text-sm text-gray-600 mt-1">Semua pertanyaan sudah diverifikasi. Anda dapat
                                    mengirimkan hasilnya ke Verifikator Menpan sekarang.</p>
                            @else
                                <h3 class="text-lg font-bold text-gray-900">Verifikasi Selesai</h3>
                                <p class="text-sm text-gray-600 mt-1">Klik Selesai jika seluruh pengisian telah selesai
                                    diverifikasi. Hasil kuesioner ini tidak memenuhi kualifikasi WBK, sehingga tidak dapat
                                    dikirimkan ke Menpan.</p>
                            @endif
                        </div>
                    @else
                        <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Belum Siap Dikirim</h3>
                            <p class="text-sm text-gray-600 mt-1">Selesaikan verifikasi seluruh pertanyaan untuk dapat
                                mengirimkan hasil ke Verifikator Menpan.</p>
                        </div>
                    @endif
                </div>

                @if ($isReadySendMenpan && !$isSentToMenpan)
                    <div class="flex-shrink-0">
                        <form action="{{ route('verifikasi.kirim-menpan', [$periode->id, $opd->id]) }}" method="POST"
                            onsubmit="return confirm('Kirim hasil verifikasi ke Verifikator Menpan? Tindakan ini tidak dapat dibatalkan.');">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary-dark transition-colors border-none shadow-none">
                                @if ($meetsWbk)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                    Kirim ke Menpan
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    Selesai
                                @endif
                            </button>
                        </form>
                    </div>
                @elseif($isSentToMenpan)
                    <div class="flex-shrink-0">
                        <span
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Terkirim
                        </span>
                    </div>
                @endif
            </div>
        </div>

        {{-- WBK Compliance Status Card --}}
        @if ($isReadySendMenpan || $isSentToMenpan)
            <div
                class="bg-white rounded-xl p-6 {{ $meetsWbk ? 'border border-green-200 bg-green-50/10' : 'border border-red-200 bg-red-50/10' }}">
                <!-- Header (Always Visible) -->
                <div class="flex items-center justify-between cursor-pointer select-none" onclick="toggleWbkCollapse()">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0 {{ $meetsWbk ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            @if ($meetsWbk)
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                </svg>
                            @else
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            @endif
                        </div>
                        <div>
                            <div class="flex items-center gap-3">
                                <h3 class="text-base font-bold text-gray-900">Kelayakan Kualifikasi WBK</h3>
                                @if ($meetsWbk)
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-green-200 text-green-800">
                                        MEMENUHI SYARAT
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-200 text-red-800">
                                        BELUM MEMENUHI SYARAT
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-400 mt-0.5">Klik untuk menampilkan/menyembunyikan detail kelayakan
                            </p>
                        </div>
                    </div>
                    <button type="button"
                        class="text-gray-400 hover:text-gray-600 transition-colors p-1 focus:outline-none">
                        <svg id="wbkCollapseIcon" class="w-5 h-5 transform transition-transform duration-200 rotate-180"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7" />
                        </svg>
                    </button>
                </div>

                <!-- Collapsible Content Wrapper -->
                <div id="wbkCollapseContent"
                    class="transition-all duration-500 ease-in-out max-h-0 opacity-0 overflow-hidden">
                    <div class="mt-5 pl-0 sm:pl-16">
                        <p class="text-xs text-gray-500">
                            @if ($meetsWbk)
                                Unit kerja ini telah berhasil memenuhi seluruh batas minimal kriteria penilaian LKE untuk
                                diajukan menuju Wilayah Bebas dari Korupsi (WBK).
                            @else
                                Unit kerja ini belum dapat diajukan menuju WBK karena terdapat beberapa kriteria batas
                                minimal penilaian LKE yang belum terpenuhi. Silakan lihat rincian checklist di bawah ini.
                            @endif
                        </p>

                        {{-- Checklist Kriteria WBK --}}
                        <div
                            class="mt-4 border border-gray-200 rounded-xl bg-white overflow-hidden divide-y divide-gray-100">
                            <!-- Header Grid -->
                            <div
                                class="grid grid-cols-12 bg-gray-50/80 text-[10px] font-bold text-gray-400 uppercase tracking-wider px-4 py-2 border-b border-gray-100">
                                <div class="col-span-6">Kriteria Kelayakan WBK</div>
                                <div class="col-span-3 text-center">Ambang Batas</div>
                                <div class="col-span-3 text-right">Nilai Hasil</div>
                            </div>

                            <!-- Rule 1: Total Nilai Evaluasi ZI -->
                            @php
                                $r1 = $compliance['total_zi'];
                            @endphp
                            <div
                                class="grid grid-cols-12 items-center px-4 py-3 text-xs {{ !$r1['is_passed'] ? 'bg-red-50/20' : '' }}">
                                <div class="col-span-6 font-semibold text-gray-700 flex items-center gap-2">
                                    {!! $r1['is_passed']
                                        ? '<span class="text-green-600 font-bold">✓</span>'
                                        : '<span class="text-red-500 font-bold">✗</span>' !!}
                                    <span>Total Nilai Evaluasi ZI</span>
                                </div>
                                <div class="col-span-3 text-center font-medium text-gray-500">&ge; 75.00</div>
                                <div
                                    class="col-span-3 text-right font-bold {{ $r1['is_passed'] ? 'text-green-600' : 'text-red-650' }}">
                                    {{ number_format($r1['nilai'], 2) }}
                                    @if (!$r1['is_passed'])
                                        <span class="block text-[9px] text-red-500 font-bold mt-0.5">Kurang
                                            {{ number_format($r1['threshold'] - $r1['nilai'], 2) }}</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Rule 2: Total Nilai Pengungkit -->
                            @php
                                $r2 = $compliance['total_pengungkit'];
                            @endphp
                            <div
                                class="grid grid-cols-12 items-center px-4 py-3 text-xs {{ !$r2['is_passed'] ? 'bg-red-50/20' : '' }}">
                                <div class="col-span-6 font-semibold text-gray-700 flex items-center gap-2">
                                    {!! $r2['is_passed']
                                        ? '<span class="text-green-600 font-bold">✓</span>'
                                        : '<span class="text-red-500 font-bold">✗</span>' !!}
                                    <span>Total Nilai Komponen Pengungkit</span>
                                </div>
                                <div class="col-span-3 text-center font-medium text-gray-500">&ge; 40.00</div>
                                <div
                                    class="col-span-3 text-right font-bold {{ $r2['is_passed'] ? 'text-green-600' : 'text-red-650' }}">
                                    {{ number_format($r2['nilai'], 2) }}
                                    @if (!$r2['is_passed'])
                                        <span class="block text-[9px] text-red-500 font-bold mt-0.5">Kurang
                                            {{ number_format($r2['threshold'] - $r2['nilai'], 2) }}</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Rule 3: Kepatuhan Area Pengungkit (min 60% per area) -->
                            @php
                                $allAreasPassed = collect($compliance['areas'])->every(fn($a) => $a['is_passed']);
                            @endphp
                            <div
                                class="grid grid-cols-12 items-start px-4 py-3 text-xs {{ !$allAreasPassed ? 'bg-red-50/20' : '' }}">
                                <div class="col-span-6 font-semibold text-gray-700">
                                    <div class="flex items-center gap-2">
                                        {!! $allAreasPassed
                                            ? '<span class="text-green-600 font-bold">✓</span>'
                                            : '<span class="text-red-500 font-bold">✗</span>' !!}
                                        <span>Bobot Minimal Per Area Pengungkit</span>
                                    </div>
                                    <div class="pl-4 mt-2 grid grid-cols-1 sm:grid-cols-2 gap-2 text-[10px]">
                                        @foreach ($compliance['areas'] as $name => $area)
                                            <div
                                                class="flex items-center justify-between p-1.5 rounded {{ $area['is_passed'] ? 'bg-green-50/40 text-green-700' : 'bg-red-50/50 text-red-700 ring-1 ring-red-100/50' }}">
                                                <span
                                                    class="font-medium truncate max-w-[120px]">{{ $name }}</span>
                                                <span class="font-bold ml-1">{{ number_format($area['persen'], 1) }}%
                                                    (min. 60%)
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-span-3 text-center font-medium text-gray-500">&ge; 60% per Area</div>
                                <div
                                    class="col-span-3 text-right font-bold {{ $allAreasPassed ? 'text-green-600' : 'text-red-650' }}">
                                    {{ $allAreasPassed ? 'Semua Lolos' : 'Ada Yang Kurang' }}
                                </div>
                            </div>

                            <!-- Rule 4: Birokrasi Bersih dan Akuntabel (Total & Subs) -->
                            @php
                                $r4 = $compliance['birokrasi_total'];
                                $spak = $compliance['spak'];
                                $capaian = $compliance['capaian'];
                            @endphp
                            <div
                                class="grid grid-cols-12 items-start px-4 py-3 text-xs {{ !$r4['is_passed'] || !$spak['is_passed'] || !$capaian['is_passed'] ? 'bg-red-50/20' : '' }}">
                                <div class="col-span-6 font-semibold text-gray-700">
                                    <div class="flex items-center gap-2">
                                        {!! $r4['is_passed'] && $spak['is_passed'] && $capaian['is_passed']
                                            ? '<span class="text-green-600 font-bold">✓</span>'
                                            : '<span class="text-red-500 font-bold">✗</span>' !!}
                                        <span>Birokrasi yang Bersih dan Akuntabel</span>
                                    </div>
                                    <div class="pl-4 mt-2 space-y-1.5 text-[10px]">
                                        <!-- SPAK Subrow -->
                                        <div
                                            class="flex items-center justify-between p-1.5 rounded {{ $spak['is_passed'] ? 'bg-green-50/40 text-green-700' : 'bg-red-50/50 text-red-700 ring-1 ring-red-100/50' }}">
                                            <span class="font-medium">Survei Persepsi Korupsi (SPAK)</span>
                                            <span class="font-bold">{{ number_format($spak['nilai'], 2) }} / 17.50 (min.
                                                15.75)</span>
                                        </div>
                                        <!-- Capaian Subrow -->
                                        <div
                                            class="flex items-center justify-between p-1.5 rounded {{ $capaian['is_passed'] ? 'bg-green-50/40 text-green-700' : 'bg-red-50/50 text-red-700 ring-1 ring-red-100/50' }}">
                                            <span class="font-medium">Capaian Kinerja Lebih Baik</span>
                                            <span class="font-bold">{{ number_format($capaian['nilai'], 2) }} / 5.00 (min.
                                                2.50)</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-span-3 text-center font-medium text-gray-500">&ge; 18.25</div>
                                <div
                                    class="col-span-3 text-right font-bold {{ $r4['is_passed'] ? 'text-green-600' : 'text-red-650' }}">
                                    {{ number_format($r4['nilai'], 2) }}
                                    @if (!$r4['is_passed'])
                                        <span class="block text-[9px] text-red-500 font-bold mt-0.5">Kurang
                                            {{ number_format($r4['threshold'] - $r4['nilai'], 2) }}</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Rule 5: Pelayanan Publik Prima -->
                            @php
                                $r5 = $compliance['pelayanan'];
                            @endphp
                            <div
                                class="grid grid-cols-12 items-center px-4 py-3 text-xs {{ !$r5['is_passed'] ? 'bg-red-50/20' : '' }}">
                                <div class="col-span-6 font-semibold text-gray-700 flex items-center gap-2">
                                    {!! $r5['is_passed']
                                        ? '<span class="text-green-600 font-bold">✓</span>'
                                        : '<span class="text-red-500 font-bold">✗</span>' !!}
                                    <span>Pelayanan Publik yang Prima (Hasil II)</span>
                                </div>
                                <div class="col-span-3 text-center font-medium text-gray-500">&ge; 14.00</div>
                                <div
                                    class="col-span-3 text-right font-bold {{ $r5['is_passed'] ? 'text-green-600' : 'text-red-650' }}">
                                    {{ number_format($r5['nilai'], 2) }}
                                    @if (!$r5['is_passed'])
                                        <span class="block text-[9px] text-red-500 font-bold mt-0.5">Kurang
                                            {{ number_format($r5['threshold'] - $r5['nilai'], 2) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                function toggleWbkCollapse() {
                    const content = document.getElementById('wbkCollapseContent');
                    const icon = document.getElementById('wbkCollapseIcon');
                    if (content.classList.contains('max-h-0')) {
                        content.classList.remove('max-h-0', 'opacity-0');
                        content.classList.add('max-h-[1500px]', 'opacity-100');
                        icon.classList.remove('rotate-180');
                    } else {
                        content.classList.remove('max-h-[1500px]', 'opacity-100');
                        content.classList.add('max-h-0', 'opacity-0');
                        icon.classList.add('rotate-180');
                    }
                }
            </script>
        @endif

        @if (config('app.debug') || env('APP_ENV') === 'local')
            <form action="{{ route('verifikasi.verify-all-dev', [$periode->id, $opd->id]) }}" method="POST"
                onsubmit="return confirm('Yakin ingin verifikasi semua pertanyaan untuk OPD ini secara otomatis (DEV ONLY)?');">
                @csrf
                <button type="submit"
                    class="shrink-0 inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors shadow-sm gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    [DEV] Verifikasi Semua
                </button>
            </form>
        @endif

        {{-- Komponen Loop --}}
        @foreach ($komponens as $komponen)
            @php
                $komponenNilai = 0;
                foreach ($komponen->kategoris as $cat) {
                    foreach ($cat->subKategoris as $subCat) {
                        if (isset($progress[$subCat->id])) {
                            $komponenNilai += $progress[$subCat->id]['nilai'];
                        }
                    }
                }
                $komponenCapaian = $komponen->bobot > 0 ? ($komponenNilai / $komponen->bobot) * 100 : 0;
            @endphp
            <div class="bg-white rounded-xl overflow-hidden">
                {{-- Komponen Header --}}
                <div class="bg-[#0E7C7B] px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-white">{{ $komponen->kode }}. {{ $komponen->nama }}</h3>
                            @if ($komponen->deskripsi)
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
                    @foreach ($komponen->kategoris as $kategori)
                        @php
                            $kategoriNilai = 0;
                            foreach ($kategori->subKategoris as $subCat) {
                                if (isset($progress[$subCat->id])) {
                                    $kategoriNilai += $progress[$subCat->id]['nilai'];
                                }
                            }
                            $kategoriCapaian = $kategori->bobot > 0 ? ($kategoriNilai / $kategori->bobot) * 100 : 0;
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
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                                                </svg>
                                                {{ number_format($kategori->bobot, 2) }}
                                            </span>
                                        </div>
                                        @if ($kategori->deskripsi)
                                            <p class="text-xs text-gray-500 mt-0.5">{{ $kategori->deskripsi }}</p>
                                        @endif
                                    </div>
                                </div>

                                {{-- Nilai & Capaian Kategori --}}
                                <div class="flex items-center gap-3 ml-4">
                                    <div
                                        class="bg-gray-50 rounded-lg px-3 py-2 border border-gray-100 flex flex-col justify-center items-end min-w-[70px]">
                                        <span
                                            class="text-[10px] text-gray-500 uppercase font-semibold tracking-wider">Nilai</span>
                                        <span
                                            class="text-sm font-bold text-gray-900">{{ number_format($kategoriNilai, 2) }}</span>
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
                                @foreach ($kategori->subKategoris as $subKategori)
                                    @php
                                        $prog = $progress[$subKategori->id] ?? [
                                            'total' => 0,
                                            'terverifikasi' => 0,
                                            'persen' => 0,
                                            'nilai' => 0,
                                            'capaian' => 0,
                                        ];
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
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
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
                                                <p
                                                    class="text-[10px] text-gray-500 uppercase font-semibold tracking-wider mb-0.5">
                                                    Nilai
                                                </p>
                                                <p class="text-sm font-bold text-gray-900">
                                                    {{ number_format($prog['nilai'], 2) }}</p>
                                            </div>
                                            <div
                                                class="bg-gray-50 rounded-lg p-2.5 border border-gray-100 flex flex-col justify-center">
                                                <p
                                                    class="text-[10px] text-gray-500 uppercase font-semibold tracking-wider mb-0.5">
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
                                                <span
                                                    class="font-semibold">{{ $prog['terverifikasi'] }}/{{ $prog['total'] }}</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                                <div class="h-full transition-all duration-300 rounded-full {{ $prog['persen'] == 100 ? 'bg-green-500' : 'bg-[#0E7C7B]' }}"
                                                    style="width: {{ $prog['persen'] }}%"></div>
                                            </div>
                                        </div>

                                        {{-- Status Badge --}}
                                        <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
                                            @if ($prog['persen'] == 100)
                                                <span
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Selesai
                                                </span>
                                            @elseif($prog['persen'] > 0)
                                                <span
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Dalam Progress
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M12 4v16m8-8H4" />
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
