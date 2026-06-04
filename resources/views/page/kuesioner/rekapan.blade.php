@extends('layouts.app')
@section('title', 'Rekapan Hasil Kuesioner')
@section('page-title', 'Rekapan Hasil LKE')

@section('content')
    <!-- Header Section -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Rekapan Hasil LKE</h2>
            <p class="text-sm text-gray-500 mt-1 flex items-center flex-wrap gap-2">
                <span>Periode:</span>
                <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-primary border border-blue-100">
                    {{ $periode->nama_periode }}
                </span>
                <span class="text-gray-300">|</span>
                <span>Unit Kerja:</span>
                <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                    {{ $opd->n_opd }}
                </span>
            </p>
        </div>
        <div class="flex gap-2 self-start sm:self-auto">
            <a id="btn-export-pdf" href="{{ route('kuesioner.rekap.pdf', $periode->id) }}?role=operator"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-600 text-white rounded-xl text-sm font-semibold hover:bg-red-700 shadow-sm hover:shadow transition-all duration-200"
                target="_blank">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export PDF
            </a>
            <a href="{{ route('kuesioner.show', $periode->id) }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:text-gray-900 hover:bg-gray-50 shadow-sm hover:shadow transition-all duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <!-- Nav Tabs -->
    <div class="mb-8 border-b border-gray-200 flex flex-wrap gap-6">
        <button type="button" onclick="switchTab('operator')" id="tab-operator"
            class="pb-3 text-sm font-semibold transition-all duration-200 flex items-center gap-2 border-b-2 border-primary text-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            Nilai Unit Kerja
        </button>
        <button type="button" onclick="switchTab('verifikator')" id="tab-verifikator"
            class="pb-3 text-sm font-medium transition-all duration-200 flex items-center gap-2 border-b-2 border-transparent text-gray-500 hover:text-gray-800 hover:border-gray-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
            Nilai TPI
        </button>
        <button type="button" onclick="switchTab('menpan')" id="tab-menpan"
            class="pb-3 text-sm font-medium transition-all duration-200 flex items-center gap-2 border-b-2 border-transparent text-gray-500 hover:text-gray-800 hover:border-gray-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            Nilai TPE
        </button>
    </div>

    @foreach (['operator', 'verifikator', 'menpan'] as $role)
        @php
            $rekapPengungkit = $rekapData[$role]['rekapPengungkit'];
            $rekapHasil = $rekapData[$role]['rekapHasil'];

            // Pre-calculate values for cards
            $totalPengungkitBobot = 0;
            $totalPengungkitNilai = 0;
            foreach ($rekapPengungkit as $area) {
                $totalPengungkitBobot += $area['pemenuhan_bobot'] + $area['reform_bobot'];
                $totalPengungkitNilai += $area['pemenuhan_nilai'] + $area['reform_nilai'];
            }

            $totalHasilBobot = 0;
            $totalHasilNilai = 0;
            foreach ($rekapHasil as $hasil) {
                $totalHasilBobot += $hasil['bobot'];
                $totalHasilNilai += $hasil['nilai'];
            }

            $grandTotalBobot = $totalPengungkitBobot + $totalHasilBobot;
            $grandTotalNilai = $totalPengungkitNilai + $totalHasilNilai;
            $grandTotalPersen = $grandTotalBobot > 0 ? ($grandTotalNilai / $grandTotalBobot) * 100 : 0;
            $pengungkitPersen = $totalPengungkitBobot > 0 ? ($totalPengungkitNilai / $totalPengungkitBobot) * 100 : 0;
            $hasilPersen = $totalHasilBobot > 0 ? ($totalHasilNilai / $totalHasilBobot) * 100 : 0;
        @endphp
        <div id="content-{{ $role }}" class="{{ $role == 'operator' ? 'block' : 'hidden' }} space-y-6">
            @if (!$isFinished[$role])
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-8 text-center mt-4">
                    <div class="mx-auto w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-primary" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Evaluasi Belum Selesai</h3>
                    <p class="text-gray-600 max-w-lg mx-auto">
                        Proses pengisian Lembar Kerja Evaluasi untuk peran <strong>{{ strtoupper($role) }}</strong> belum
                        divalidasi/diselesaikan seluruhnya. Rekapan hanya akan tampil ketika semua pertanyaan sudah selesai
                        diverifikasi atau dikirim.
                    </p>
                </div>
            @else
                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <!-- Grand Total Card -->
                    <div
                        class="relative overflow-hidden bg-[#0164CA] text-white rounded-2xl p-6 shadow-md hover:shadow-lg transition-all duration-300">
                        <!-- Decorative Light Glow -->
                        <div class="absolute -right-10 -top-10 w-36 h-36 bg-white/10 rounded-full blur-3xl"></div>
                        <div class="absolute -left-10 -bottom-10 w-36 h-36 bg-white/10 rounded-full blur-2xl"></div>

                        <div class="relative flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-blue-100">Total Nilai
                                    Evaluasi ZI</span>
                                <div class="flex items-baseline gap-1.5 mt-1.5">
                                    <span
                                        class="text-4xl font-black text-[#F7D558] tracking-tight">{{ number_format($grandTotalNilai, 2) }}</span>
                                    <span class="text-sm text-blue-100 font-medium">/
                                        {{ number_format($grandTotalBobot, 2) }}</span>
                                </div>
                            </div>
                            <div class="p-3 bg-[#0150A8] rounded-xl text-[#F7D558] shadow-inner">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                </svg>
                            </div>
                        </div>

                        <div class="mt-5 relative">
                            <div class="flex items-center justify-between text-xs text-blue-100 mb-1.5">
                                <span>Persentase Capaian</span>
                                <span class="font-bold text-[#F7D558]">{{ number_format($grandTotalPersen, 2) }}%</span>
                            </div>
                            <div class="w-full bg-[#0150A8] rounded-full h-2">
                                <div class="bg-[#F7D558] h-2 rounded-full transition-all duration-500"
                                    style="width: {{ $grandTotalPersen }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Pengungkit Card -->
                    <div
                        class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200/50 hover:shadow-md hover:border-gray-200 transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Total Pengungkit
                                    (A)</span>
                                <div class="flex items-baseline gap-1.5 mt-1.5">
                                    <span
                                        class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ number_format($totalPengungkitNilai, 2) }}</span>
                                    <span class="text-xs text-gray-400 font-medium">/
                                        {{ number_format($totalPengungkitBobot, 2) }}</span>
                                </div>
                            </div>
                            <div class="p-3 bg-blue-50 text-primary rounded-xl border border-blue-100/50 shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                </svg>
                            </div>
                        </div>

                        <div class="mt-6">
                            <div class="flex items-center justify-between text-xs text-gray-500 mb-1.5">
                                <span>Persentase Capaian</span>
                                <span class="font-bold text-primary">{{ number_format($pengungkitPersen, 2) }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="bg-primary h-2 rounded-full transition-all duration-500"
                                    style="width: {{ $pengungkitPersen }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Hasil Card -->
                    <div
                        class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200/50 hover:shadow-md hover:border-gray-200 transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Total Hasil
                                    (B)</span>
                                <div class="flex items-baseline gap-1.5 mt-1.5">
                                    <span
                                        class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ number_format($totalHasilNilai, 2) }}</span>
                                    <span class="text-xs text-gray-400 font-medium">/
                                        {{ number_format($totalHasilBobot, 2) }}</span>
                                </div>
                            </div>
                            <div
                                class="p-3 bg-emerald-50 text-emerald-600 rounded-xl border border-emerald-100/50 shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                        </div>

                        <div class="mt-6">
                            <div class="flex items-center justify-between text-xs text-gray-500 mb-1.5">
                                <span>Persentase Capaian</span>
                                <span class="font-bold text-emerald-600">{{ number_format($hasilPersen, 2) }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="bg-emerald-500 h-2 rounded-full transition-all duration-500"
                                    style="width: {{ $hasilPersen }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table Section -->
                <div class="bg-white rounded-2xl border border-gray-200/70 overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left border-collapse">
                            <thead class="bg-[#0164CA] text-white">
                                <tr>
                                    <th
                                        class="px-6 py-5 border-r border-white/10 font-bold tracking-wider text-medium uppercase">
                                        Area Perubahan</th>
                                    <th
                                        class="px-6 py-5 border-r border-white/10 font-bold tracking-wider text-medium uppercase text-center w-24">
                                        Bobot</th>
                                    <th
                                        class="px-6 py-5 border-r border-white/10 font-bold tracking-wider text-medium uppercase text-center w-32">
                                        Pemenuhan</th>
                                    <th
                                        class="px-6 py-5 border-r border-white/10 font-bold tracking-wider text-medium uppercase text-center w-32">
                                        Reform</th>
                                    <th
                                        class="px-6 py-5 border-r border-white/10 font-bold tracking-wider text-medium uppercase text-center w-32">
                                        Nilai</th>
                                    <th class="px-6 py-5 font-bold tracking-wider text-xs uppercase text-center w-24">%
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <!-- A. PENGUNGKIT -->
                                <tr class="bg-slate-50">
                                    <td colspan="6" class="px-6 py-3.5 font-bold text-gray-800">
                                        <div class="flex items-center gap-2.5">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-primary"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                            </svg>
                                            <span class="tracking-wide text-medium font-bold uppercase text-gray-700">A.
                                                Pengungkit</span>
                                        </div>
                                    </td>
                                </tr>

                                @foreach ($rekapPengungkit as $area)
                                    @php
                                        $bobotArea = $area['pemenuhan_bobot'] + $area['reform_bobot'];
                                        $nilaiArea = $area['pemenuhan_nilai'] + $area['reform_nilai'];
                                        $persenArea = $bobotArea > 0 ? ($nilaiArea / $bobotArea) * 100 : 0;
                                    @endphp
                                    <tr class="hover:bg-slate-50/50 transition-colors duration-150">
                                        <td class="px-6 py-4 border-r border-gray-100 text-gray-700 font-medium">
                                            <span class="text-gray-400 font-normal mr-1">{{ $loop->iteration }}.</span>
                                            {{ $area['nama'] }}
                                        </td>
                                        <td
                                            class="px-6 py-4 border-r border-gray-100 text-center font-semibold text-gray-600">
                                            {{ number_format($bobotArea, 2) }}
                                        </td>
                                        <td
                                            class="px-6 py-4 border-r border-gray-100 text-center text-primary font-medium">
                                            {{ number_format($area['pemenuhan_nilai'], 2) }}
                                        </td>
                                        <td
                                            class="px-6 py-4 border-r border-gray-100 text-center text-primary font-medium">
                                            {{ number_format($area['reform_nilai'], 2) }}
                                        </td>
                                        <td class="px-6 py-4 border-r border-gray-100 text-center font-bold text-gray-800">
                                            {{ number_format($nilaiArea, 2) }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @php
                                                $badgeColor = 'bg-red-50 text-red-700 border-red-100';
                                                if ($persenArea >= 90) {
                                                    $badgeColor = 'bg-emerald-50 text-emerald-700 border-emerald-100';
                                                } elseif ($persenArea >= 75) {
                                                    $badgeColor = 'bg-blue-50 text-blue-700 border-blue-100';
                                                } elseif ($persenArea >= 50) {
                                                    $badgeColor = 'bg-amber-50 text-amber-700 border-amber-100';
                                                }
                                            @endphp
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border {{ $badgeColor }} shadow-sm">
                                                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                                {{ number_format($persenArea, 2) }}%
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach

                                {{-- Subtotal Pengungkit --}}
                                <tr class="bg-slate-50/50 font-bold text-gray-900 border-t border-b border-gray-200">
                                    <td
                                        class="px-6 py-4 border-r border-gray-200 text-right uppercase tracking-wider text-xs text-gray-500 font-semibold">
                                        Total Pengungkit (A)</td>
                                    <td class="px-6 py-4 border-r border-gray-200 text-center text-gray-800">
                                        {{ number_format($totalPengungkitBobot, 2) }}</td>
                                    <td colspan="2"
                                        class="px-6 py-4 border-r border-gray-200 bg-slate-50/20 text-center text-gray-400 italic text-xs font-normal">
                                        -</td>
                                    <td class="px-6 py-4 border-r border-gray-200 text-center text-primary">
                                        {{ number_format($totalPengungkitNilai, 2) }}</td>
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $badgeColor = 'bg-red-50 text-red-700 border-red-100';
                                            if ($pengungkitPersen >= 90) {
                                                $badgeColor = 'bg-emerald-50 text-emerald-700 border-emerald-100';
                                            } elseif ($pengungkitPersen >= 75) {
                                                $badgeColor = 'bg-blue-50 text-blue-700 border-blue-100';
                                            } elseif ($pengungkitPersen >= 50) {
                                                $badgeColor = 'bg-amber-50 text-amber-700 border-amber-100';
                                            }
                                        @endphp
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold border {{ $badgeColor }} shadow-sm">
                                            <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                            {{ number_format($pengungkitPersen, 2) }}%
                                        </span>
                                    </td>
                                </tr>

                                {{-- Komponen B. Hasil --}}
                                <tr class="bg-slate-50 border-l-4 border-emerald-500 border-t border-gray-200">
                                    <td colspan="6" class="px-6 py-3.5 font-bold text-gray-800">
                                        <div class="flex items-center gap-2.5">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-emerald-500"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                            </svg>
                                            <span class="tracking-wide text-medium font-bold uppercase text-gray-700">B.
                                                Hasil</span>
                                        </div>
                                    </td>
                                </tr>

                                @foreach ($rekapHasil as $hasil)
                                    @php
                                        $persenHasil =
                                            $hasil['bobot'] > 0 ? ($hasil['nilai'] / $hasil['bobot']) * 100 : 0;
                                    @endphp
                                    <tr
                                        class="hover:bg-slate-50/50 transition-colors duration-150 bg-slate-50/10 font-bold border-t border-gray-200">
                                        <td class="px-6 py-4 border-r border-gray-100 text-gray-800 font-bold">
                                            <span
                                                class="text-gray-400 font-semibold mr-1">{{ $hasil['kode'] ?? $loop->iteration }}.</span>
                                            {{ $hasil['nama'] }}
                                        </td>
                                        <td
                                            class="px-6 py-4 border-r border-gray-100 text-center text-gray-800 font-extrabold">
                                            {{ number_format($hasil['bobot'], 2) }}
                                        </td>
                                        <td colspan="2"
                                            class="px-6 py-4 border-r border-gray-100 text-center text-gray-400 italic text-xs font-normal bg-slate-50/20">
                                            -
                                        </td>
                                        <td
                                            class="px-6 py-4 border-r border-gray-100 text-center text-gray-800 font-extrabold">
                                            {{ number_format($hasil['nilai'], 2) }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @php
                                                $badgeColor = 'bg-red-50 text-red-700 border-red-100';
                                                if ($persenHasil >= 90) {
                                                    $badgeColor = 'bg-emerald-50 text-emerald-700 border-emerald-100';
                                                } elseif ($persenHasil >= 75) {
                                                    $badgeColor = 'bg-blue-50 text-blue-700 border-blue-100';
                                                } elseif ($persenHasil >= 50) {
                                                    $badgeColor = 'bg-amber-50 text-amber-700 border-amber-100';
                                                }
                                            @endphp
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold border {{ $badgeColor }} shadow-sm">
                                                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                                {{ number_format($persenHasil, 2) }}%
                                            </span>
                                        </td>
                                    </tr>
                                    @foreach ($hasil['subs'] as $sub)
                                        @php
                                            $persenSub = $sub['bobot'] > 0 ? ($sub['nilai'] / $sub['bobot']) * 100 : 0;
                                        @endphp
                                        <tr class="hover:bg-slate-50/50 transition-colors duration-150">
                                            <td class="px-6 py-4 border-r border-gray-100 text-gray-700 pl-12 font-medium">
                                                <span class="text-gray-400 font-normal mr-1">{{ $sub['kode'] }}.</span>
                                                {{ $sub['nama'] }}
                                            </td>
                                            <td
                                                class="px-6 py-4 border-r border-gray-100 text-center font-semibold text-gray-600">
                                                {{ number_format($sub['bobot'], 2) }}
                                            </td>
                                            <td colspan="2"
                                                class="bg-slate-50/10 px-6 py-4 border-r border-gray-100 text-center text-gray-400 italic text-xs font-normal">
                                                -
                                            </td>
                                            <td
                                                class="px-6 py-4 border-r border-gray-100 text-center font-bold text-gray-800">
                                                {{ number_format($sub['nilai'], 2) }}
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                @php
                                                    $badgeColor = 'bg-red-50 text-red-700 border-red-100';
                                                    if ($persenSub >= 90) {
                                                        $badgeColor =
                                                            'bg-emerald-50 text-emerald-700 border-emerald-100';
                                                    } elseif ($persenSub >= 75) {
                                                        $badgeColor = 'bg-blue-50 text-blue-700 border-blue-100';
                                                    } elseif ($persenSub >= 50) {
                                                        $badgeColor = 'bg-amber-50 text-amber-700 border-amber-100';
                                                    }
                                                @endphp
                                                <span
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border {{ $badgeColor }} shadow-sm">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                                    {{ number_format($persenSub, 2) }}%
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach

                                {{-- Subtotal Hasil --}}
                                <tr class="bg-slate-50/50 font-bold text-gray-900 border-t border-b border-gray-200">
                                    <td
                                        class="px-6 py-4 border-r border-gray-200 text-right uppercase tracking-wider text-xs text-gray-500 font-semibold">
                                        Total Hasil (B)</td>
                                    <td class="px-6 py-4 border-r border-gray-200 text-center text-gray-800">
                                        {{ number_format($totalHasilBobot, 2) }}</td>
                                    <td colspan="2"
                                        class="px-6 py-4 border-r border-gray-200 bg-slate-50/20 text-center text-gray-400 italic text-xs font-normal">
                                        -</td>
                                    <td class="px-6 py-4 border-r border-gray-200 text-center text-emerald-600">
                                        {{ number_format($totalHasilNilai, 2) }}</td>
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $badgeColor = 'bg-red-50 text-red-700 border-red-100';
                                            if ($hasilPersen >= 90) {
                                                $badgeColor = 'bg-emerald-50 text-emerald-700 border-emerald-100';
                                            } elseif ($hasilPersen >= 75) {
                                                $badgeColor = 'bg-blue-50 text-blue-700 border-blue-100';
                                            } elseif ($hasilPersen >= 50) {
                                                $badgeColor = 'bg-amber-50 text-amber-700 border-amber-100';
                                            }
                                        @endphp
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold border {{ $badgeColor }} shadow-sm">
                                            <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                            {{ number_format($hasilPersen, 2) }}%
                                        </span>
                                    </td>
                                </tr>

                                {{-- TOTAL KESELURUHAN --}}
                                <tr class="bg-[#0164CA] text-white font-bold border-t-2 border-[#0150A8]">
                                    <td
                                        class="px-6 py-4 border-r border-[#0150A8] text-right uppercase tracking-wider text-xs">
                                        Nilai Evaluasi Zona Integritas (A+B)</td>
                                    <td
                                        class="px-6 py-4 border-r border-[#0150A8] text-center text-lg font-extrabold text-secondary">
                                        {{ number_format($grandTotalBobot, 2) }}</td>
                                    <td colspan="2" class="px-6 py-4 border-r border-[#0164CA] bg-[#0164CA]"></td>
                                    <td
                                        class="px-6 py-4 border-r border-[#0150A8] text-center text-lg font-extrabold text-secondary">
                                        {{ number_format($grandTotalNilai, 2) }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class=" text-secondary text-base font-black">
                                            {{ number_format($grandTotalPersen, 2) }}%
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    @endforeach

    <script>
        function switchTab(role) {
            const roles = ['operator', 'verifikator', 'menpan'];
            roles.forEach(r => {
                document.getElementById('content-' + r).classList.add('hidden');
                document.getElementById('content-' + r).classList.remove('block');

                let tab = document.getElementById('tab-' + r);
                tab.classList.remove('border-primary', 'text-primary', 'font-semibold');
                tab.classList.add('border-transparent', 'text-gray-500', 'font-medium', 'hover:text-gray-800',
                    'hover:border-gray-300');
            });

            document.getElementById('content-' + role).classList.remove('hidden');
            document.getElementById('content-' + role).classList.add('block');

            let activeTab = document.getElementById('tab-' + role);
            activeTab.classList.remove('border-transparent', 'text-gray-500', 'font-medium', 'hover:text-gray-800',
                'hover:border-gray-300');
            activeTab.classList.add('border-primary', 'text-primary', 'font-semibold');

            // Update Export PDF URL to include the selected role
            let exportBtn = document.getElementById('btn-export-pdf');
            if (exportBtn) {
                exportBtn.href = "{{ route('kuesioner.rekap.pdf', $periode->id) }}?role=" + role;
            }
        }
    </script>
@endsection
