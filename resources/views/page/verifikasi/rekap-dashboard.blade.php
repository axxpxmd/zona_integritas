@extends('layouts.app')

@section('title', Auth::user()->role === 'operator' ? 'Rekapan WBK Unit Kerja' : 'Dashboard Verifikator')
@section('page-title', Auth::user()->role === 'operator' ? 'Rekapan WBK Unit Kerja' : 'Dashboard Verifikator')

@section('content')
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Rekapan Hasil Verifikasi Unit Kerja</h2>
                <p class="text-sm text-gray-500 mt-1">
                    Halaman ini merangkum nilai Lembar Kerja Evaluasi (LKE) Unit Kerja yang memenuhi kualifikasi menuju WBK
                    (Wilayah Bebas dari Korupsi).
                </p>
            </div>
        </div>

        <!-- Periode Selector -->
        <div class="bg-white rounded-xl p-5">
            <form action="{{ route('verifikasi.rekap') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
                <div class="w-full sm:w-64">
                    <label for="periode_id"
                        class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Pilih Periode
                        Aktif</label>
                    <select name="periode_id" id="periode_id"
                        class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary/45 focus:border-primary text-sm bg-gray-50/50 hover:bg-gray-50 transition-colors"
                        onchange="this.form.submit()">
                        @if ($periodes->isEmpty())
                            <option value="">Tidak ada periode aktif</option>
                        @else
                            @foreach ($periodes as $p)
                                <option value="{{ $p->id }}"
                                    {{ $activePeriode && $activePeriode->id == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama_periode }} ({{ $p->tahun }})
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                @if ($activePeriode)
                    <div
                        class="w-full sm:w-auto text-sm text-gray-600 bg-gray-50 px-4 py-2.5 rounded-lg border border-gray-200">
                        <span class="font-medium text-gray-900">Periode Aktif:</span> {{ $activePeriode->nama_periode }}
                        ({{ $activePeriode->tahun }})
                    </div>
                @endif
            </form>
        </div>

        @if ($activePeriode)
            <!-- Rules Summary Cards Section -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Card 1: Total Evaluasi ZI -->
                <div class="bg-white rounded-xl p-5 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">1. Total ZI</span>
                        <div class="p-2 rounded-lg bg-blue-50 text-primary">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                                    d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-2xl font-bold text-gray-900">&ge; 75.00</h3>
                        <p class="text-xs text-gray-500 mt-1">Total Nilai Evaluasi ZI</p>
                    </div>
                </div>

                <!-- Card 2: Total Pengungkit -->
                <div class="bg-white rounded-xl p-5 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">2. Total
                            Pengungkit</span>
                        <div class="p-2 rounded-lg bg-indigo-50 text-indigo-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                                    d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-2xl font-bold text-gray-900">&ge; 40.00</h3>
                        <p class="text-xs text-gray-500 mt-1">Skor dari 6 Area Pengungkit</p>
                    </div>
                </div>

                <!-- Card 3: Bobot Area Pengungkit -->
                <div class="bg-white rounded-xl p-5 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">3. Area
                            Pengungkit</span>
                        <div class="p-2 rounded-lg bg-teal-50 text-teal-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                                    d="M11 3.055A9.003 9.003 0 1020.945 13H11V3.055z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                                    d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-2xl font-bold text-gray-900">&ge; 60%</h3>
                        <p class="text-xs text-gray-500 mt-1">Bobot per masing-masing Area</p>
                    </div>
                </div>

                <!-- Card 4: Birokrasi Bersih & Akuntabel -->
                <div class="bg-white rounded-xl p-5 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">4. Birokrasi
                            Bersih</span>
                        <div class="p-2 rounded-lg bg-amber-50 text-amber-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-2xl font-bold text-gray-900">&ge; 18.25</h3>
                        <p class="text-[10px] text-gray-500 mt-1 leading-tight">SPAK &ge; 15.75 | Capaian &ge; 2.50</p>
                    </div>
                </div>

                <!-- Card 5: Pelayanan Publik Prima -->
                <div class="bg-white rounded-xl p-5 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">5. Pelayanan
                            Prima</span>
                        <div class="p-2 rounded-lg bg-rose-50 text-rose-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                                    d="M14 10h2m-2 4h2m7-4a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-2xl font-bold text-gray-900">&ge; 14.00</h3>
                        <p class="text-xs text-gray-500 mt-1">Hasil Pelayanan Publik Prima</p>
                    </div>
                </div>
            </div>

            @if ($rekapRows->isEmpty())
                <div class="bg-white rounded-xl p-12 text-center border border-gray-200/60 shadow-none">
                    <div
                        class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4 text-gray-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900">Belum Ada Rekapan Hasil</h3>
                    <p class="text-sm text-gray-500 mt-1 max-w-sm mx-auto">Belum ada Unit Kerja dengan yang selesai
                        diverifikasi pada periode ini.</p>
                </div>
            @else
                <div class="bg-white rounded-xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-[11px] text-gray-700 divide-y divide-gray-200">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th rowspan="2"
                                        class="px-3 py-3.5 text-center font-bold uppercase tracking-wider border-r border-white/10">
                                        No</th>
                                    <th rowspan="2"
                                        class="px-4 py-3.5 text-left font-bold uppercase tracking-wider border-r border-white/10 min-w-[200px]">
                                        Unit Kerja</th>
                                    <th colspan="7"
                                        class="px-3 py-2 text-center font-bold uppercase tracking-wider border-b border-white/10 border-r border-white/10">
                                        Pengungkit ({{ number_format($bobotMeta['pengungkit_total'], 2) }}%)</th>
                                    <th colspan="4"
                                        class="px-3 py-2 text-center font-bold uppercase tracking-wider border-b border-white/10 border-r border-white/10">
                                        Hasil ({{ number_format($bobotMeta['hasil_total'], 2) }}%)</th>
                                    <th rowspan="2"
                                        class="px-3 py-3.5 text-center font-bold uppercase tracking-wider border-r border-white/10">
                                        Total (100%)</th>
                                    <th rowspan="2" class="px-3 py-3.5 text-center font-bold uppercase tracking-wider">
                                        Simpulan</th>
                                </tr>
                                <tr class="bg-primary-dark text-white/95">
                                    @foreach ($areaOrder as $areaName)
                                        <th
                                            class="px-2 py-2 text-center text-[9px] font-bold uppercase border-r border-white/5 max-w-[90px] whitespace-normal">
                                            <div class="leading-tight">{{ $areaName }}</div>
                                            <div class="text-[8px] text-white/70 mt-0.5">
                                                ({{ number_format($bobotMeta['area'][$areaName], 2) }}%)</div>
                                        </th>
                                    @endforeach
                                    <th
                                        class="px-2 py-2 text-center text-[9px] font-bold uppercase border-r border-white/10">
                                        Jumlah Pengungkit
                                        <div class="text-[8px] text-white/70 mt-0.5">
                                            ({{ number_format($bobotMeta['pengungkit_total'], 2) }}%)</div>
                                    </th>
                                    <th
                                        class="px-2 py-2 text-center text-[9px] font-bold uppercase border-r border-white/5">
                                        SPAK
                                        <div class="text-[8px] text-white/70 mt-0.5">
                                            ({{ number_format($bobotMeta['hasil']['spak'], 2) }}%)</div>
                                    </th>
                                    <th
                                        class="px-2 py-2 text-center text-[9px] font-bold uppercase border-r border-white/5">
                                        Capaian Kinerja
                                        <div class="text-[8px] text-white/70 mt-0.5">
                                            ({{ number_format($bobotMeta['hasil']['capaian'], 2) }}%)</div>
                                    </th>
                                    <th
                                        class="px-2 py-2 text-center text-[9px] font-bold uppercase border-r border-white/5 bg-primary-dark/80">
                                        Birokrasi Bersih
                                        <div class="text-[8px] text-white/70 mt-0.5">
                                            ({{ number_format($bobotMeta['hasil']['birokrasi'], 2) }}%)</div>
                                    </th>
                                    <th
                                        class="px-2 py-2 text-center text-[9px] font-bold uppercase border-r border-white/10">
                                        SPP / Pelayanan
                                        <div class="text-[8px] text-white/70 mt-0.5">
                                            ({{ number_format($bobotMeta['hasil']['pelayanan'], 2) }}%)</div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <!-- Threshold Header Indicator row -->
                                <tr class="bg-amber-50/70 text-[10px] font-bold text-amber-900">
                                    <td colspan="2" class="px-4 py-2 border-r border-gray-200">Ambang Batas WBK</td>
                                    @foreach ($areaOrder as $areaName)
                                        <td class="px-2 py-2 text-center border-r border-gray-100">&ge;
                                            {{ number_format($thresholds['area'][$areaName], 2) }}</td>
                                    @endforeach
                                    <td class="px-2 py-2 text-center border-r border-gray-200">&ge;
                                        {{ number_format($thresholds['pengungkit_total'], 2) }}</td>
                                    <td class="px-2 py-2 text-center border-r border-gray-100">&ge;
                                        {{ number_format($thresholds['spak'], 2) }}</td>
                                    <td class="px-2 py-2 text-center border-r border-gray-100">&ge;
                                        {{ number_format($thresholds['capaian'], 2) }}</td>
                                    <td class="px-2 py-2 text-center border-r border-gray-100 bg-amber-100/40">&ge; 18.25
                                    </td>
                                    <td class="px-2 py-2 text-center border-r border-gray-200">&ge;
                                        {{ number_format($thresholds['pelayanan'], 2) }}</td>
                                    <td class="px-2 py-2 text-center border-r border-gray-200">&ge;
                                        {{ number_format($thresholds['total'], 2) }}</td>
                                    <td class="px-2 py-2 text-center">-</td>
                                </tr>

                                @foreach ($rekapRows as $index => $row)
                                    <tr
                                        class="hover:bg-gray-50/50 transition-colors {{ $row['meets_wbk'] ? 'bg-green-50/30' : '' }}">
                                        <td
                                            class="px-3 py-3.5 text-center text-gray-400 font-medium border-r border-gray-100">
                                            {{ $index + 1 }}</td>
                                        <td class="px-4 py-3.5 font-semibold text-gray-900 border-r border-gray-100">
                                            <div class="line-clamp-2" title="{{ $row['opd'] }}">{{ $row['opd'] }}
                                            </div>
                                        </td>

                                        <!-- Areas -->
                                        @foreach ($row['areas'] as $area)
                                            @php
                                                $areaName = $area['nama'];
                                                $complianceArea = $row['compliance']['areas'][$areaName] ?? null;
                                                $isAreaPassed = $complianceArea ? $complianceArea['is_passed'] : true;
                                            @endphp
                                            <td
                                                class="px-2 py-3.5 text-center border-r border-gray-100 transition-colors {{ !$isAreaPassed ? 'bg-red-50/50 text-red-700' : '' }}">
                                                <div
                                                    class="font-bold {{ !$isAreaPassed ? 'text-red-700' : 'text-gray-800' }}">
                                                    {{ number_format($area['nilai'], 2) }}
                                                </div>
                                                <div
                                                    class="text-[9px] {{ !$isAreaPassed ? 'text-red-500 font-semibold' : 'text-gray-400' }}">
                                                    {{ number_format($area['persen'], 1) }}%
                                                </div>
                                            </td>
                                        @endforeach

                                        <!-- Jumlah Pengungkit -->
                                        @php
                                            $isPengungkitPassed = $row['compliance']['total_pengungkit']['is_passed'];
                                        @endphp
                                        <td
                                            class="px-2 py-3.5 text-center border-r border-gray-200 transition-colors {{ !$isPengungkitPassed ? 'bg-red-50/50 text-red-700' : '' }}">
                                            <div
                                                class="font-extrabold {{ !$isPengungkitPassed ? 'text-red-700' : 'text-gray-950' }}">
                                                {{ number_format($row['pengungkit']['nilai'], 2) }}
                                            </div>
                                            <div
                                                class="text-[9px] {{ !$isPengungkitPassed ? 'text-red-500 font-semibold' : 'text-gray-400' }}">
                                                {{ number_format($row['pengungkit']['persen'], 1) }}%
                                            </div>
                                        </td>

                                        <!-- SPAK -->
                                        @php
                                            $isSpakPassed = $row['compliance']['spak']['is_passed'];
                                        @endphp
                                        <td
                                            class="px-2 py-3.5 text-center border-r border-gray-100 transition-colors {{ !$isSpakPassed ? 'bg-red-50/50 text-red-700' : '' }}">
                                            <div
                                                class="font-bold {{ !$isSpakPassed ? 'text-red-700' : 'text-gray-850' }}">
                                                {{ number_format($row['spak']['nilai'], 2) }}
                                            </div>
                                            <div
                                                class="text-[9px] {{ !$isSpakPassed ? 'text-red-500 font-semibold' : 'text-gray-400' }}">
                                                {{ number_format($row['spak']['persen'], 1) }}%
                                            </div>
                                        </td>

                                        <!-- Capaian Kinerja -->
                                        @php
                                            $isCapaianPassed = $row['compliance']['capaian']['is_passed'];
                                        @endphp
                                        <td
                                            class="px-2 py-3.5 text-center border-r border-gray-100 transition-colors {{ !$isCapaianPassed ? 'bg-red-50/50 text-red-700' : '' }}">
                                            <div
                                                class="font-bold {{ !$isCapaianPassed ? 'text-red-700' : 'text-gray-850' }}">
                                                {{ number_format($row['capaian']['nilai'], 2) }}
                                            </div>
                                            <div
                                                class="text-[9px] {{ !$isCapaianPassed ? 'text-red-500 font-semibold' : 'text-gray-400' }}">
                                                {{ number_format($row['capaian']['persen'], 1) }}%
                                            </div>
                                        </td>

                                        <!-- Birokrasi Bersih Total -->
                                        @php
                                            $isBirokrasiPassed = $row['compliance']['birokrasi_total']['is_passed'];
                                        @endphp
                                        <td
                                            class="px-2 py-3.5 text-center border-r border-gray-100 bg-gray-50/40 transition-colors {{ !$isBirokrasiPassed ? 'bg-red-50/50 text-red-700 font-bold' : '' }}">
                                            <div
                                                class="font-extrabold {{ !$isBirokrasiPassed ? 'text-red-700' : 'text-gray-900' }}">
                                                {{ number_format($row['birokrasi']['nilai'], 2) }}
                                            </div>
                                            <div
                                                class="text-[9px] {{ !$isBirokrasiPassed ? 'text-red-500 font-semibold' : 'text-gray-400' }}">
                                                {{ number_format($row['birokrasi']['persen'], 1) }}%
                                            </div>
                                        </td>

                                        <!-- SPP / Pelayanan Publik Prima -->
                                        @php
                                            $isPelayananPassed = $row['compliance']['pelayanan']['is_passed'];
                                        @endphp
                                        <td
                                            class="px-2 py-3.5 text-center border-r border-gray-200 transition-colors {{ !$isPelayananPassed ? 'bg-red-50/50 text-red-700' : '' }}">
                                            <div
                                                class="font-bold {{ !$isPelayananPassed ? 'text-red-700' : 'text-gray-850' }}">
                                                {{ number_format($row['pelayanan']['nilai'], 2) }}
                                            </div>
                                            <div
                                                class="text-[9px] {{ !$isPelayananPassed ? 'text-red-500 font-semibold' : 'text-gray-400' }}">
                                                {{ number_format($row['pelayanan']['persen'], 1) }}%
                                            </div>
                                        </td>

                                        <!-- Total ZI -->
                                        @php
                                            $isTotalPassed = $row['compliance']['total_zi']['is_passed'];
                                        @endphp
                                        <td
                                            class="px-2 py-3.5 text-center border-r border-gray-200 transition-colors {{ !$isTotalPassed ? 'bg-red-50/50 text-red-700' : '' }}">
                                            <div
                                                class="font-extrabold text-xs {{ !$isTotalPassed ? 'text-red-700' : 'text-gray-900' }}">
                                                {{ number_format($row['total']['nilai'], 2) }}
                                            </div>
                                            <div
                                                class="text-[9px] {{ !$isTotalPassed ? 'text-red-500 font-semibold' : 'text-gray-400' }}">
                                                {{ number_format($row['total']['persen'], 1) }}%
                                            </div>
                                        </td>

                                        <!-- Simpulan & Aksi -->
                                        <td class="px-3 py-3.5 text-center">
                                            <div class="flex flex-col items-center justify-center gap-1.5">
                                                @if ($row['meets_wbk'])
                                                    <span
                                                        class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-green-100 text-green-700">
                                                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                                        Memenuhi
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-red-100 text-red-700">
                                                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                                                        Belum
                                                    </span>
                                                @endif
                                                <button type="button" data-opd="{{ $row['opd'] }}"
                                                    data-compliance="{!! htmlspecialchars(json_encode($row['compliance']), ENT_QUOTES, 'UTF-8') !!}" onclick="openWbkModal(this)"
                                                    class="inline-flex items-center text-[9px] font-bold text-primary hover:text-primary-dark transition-colors underline cursor-pointer focus:outline-none">
                                                    Detail Syarat
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @else
            <div class="bg-amber-50/70 border border-amber-200/50 rounded-xl p-10 text-center">
                <h3 class="text-base font-semibold text-amber-950">Pilih Periode Terlebih Dahulu</h3>
                <p class="text-sm text-amber-700 mt-1 max-w-sm mx-auto">Silakan tentukan periode evaluasi untuk melihat
                    rekapan penilaian dan kelayakan menuju WBK.</p>
            </div>
        @endif
    </div>

    <!-- Interactive Qualification Details Modal -->
    <div id="wbkModal" class="fixed inset-0 z-50 hidden transition-all duration-300">
        <!-- Backdrop -->
        <div id="wbkModalBackdrop"
            class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity duration-300 opacity-0"
            onclick="closeWbkModal()"></div>

        <!-- Modal Content Container -->
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div id="wbkModalCard"
                class="rounded-2xl w-full max-w-4xl overflow-hidden flex flex-col max-h-[90vh] transition-all duration-300 scale-95 opacity-0 border border-gray-100/30 shadow-2xl">
                <!-- Modal Header -->
                <div class="bg-primary text-white px-6 py-3 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold tracking-wide">Detail Syarat Kelayakan WBK</h3>
                        <p id="modalOpdName" class="text-xs text-white/80 font-medium mt-0.5"></p>
                    </div>
                    <button type="button" onclick="closeWbkModal()"
                        class="text-white/80 hover:text-white transition-colors p-1.5 focus:outline-none rounded-lg hover:bg-white/10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Body (Scrollable) -->
                <div class="bg-white p-6 overflow-y-auto space-y-5 text-sm text-gray-700 bg-gray-50/50" id="modalBody">
                    <!-- Dynamic content will be injected by JavaScript -->
                </div>

                <!-- Modal Footer -->
                <div class="bg-white px-6 py-4 border-t border-gray-100 flex justify-end">
                    <button type="button" onclick="closeWbkModal()"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2.5 rounded-lg font-semibold text-xs transition-all focus:ring-2 focus:ring-gray-300 cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function openWbkModal(btn) {
            const opdName = btn.getAttribute('data-opd');
            const compliance = JSON.parse(btn.getAttribute('data-compliance'));

            document.getElementById('modalOpdName').innerText = opdName;

            const body = document.getElementById('modalBody');
            body.innerHTML = ''; // Clear previous content

            // Helper function to build custom styled badge
            function getStatusBadge(passed) {
                return passed ?
                    `<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    Memenuhi
                   </span>` :
                    `<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                    Belum
                   </span>`;
            }

            // 1. Total Evaluasi ZI
            const zi = compliance.total_zi;
            const ziDiff = zi.threshold - zi.nilai;
            const ziDiffText = zi.is_passed ? '' :
                `<p class="text-[11px] text-red-650 font-semibold mt-1">Kurang <strong>${ziDiff.toFixed(2)}</strong> poin untuk mencapai minimal 75.00</p>`;

            // 2. Total Pengungkit
            const pengungkit = compliance.total_pengungkit;
            const pengungkitDiff = pengungkit.threshold - pengungkit.nilai;
            const pengungkitDiffText = pengungkit.is_passed ? '' :
                `<p class="text-[11px] text-red-650 font-semibold mt-1">Kurang <strong>${pengungkitDiff.toFixed(2)}</strong> poin untuk mencapai minimal 40.00</p>`;

            // 3. Area Pengungkit (60% per area)
            let areasHtml = `
            <div class="mt-3 border border-gray-100 rounded-xl bg-gray-50/50 overflow-hidden divide-y divide-gray-100">
                <div class="grid grid-cols-12 bg-gray-100/50 text-[9px] font-bold text-gray-500 uppercase tracking-wider px-3.5 py-2">
                    <div class="col-span-6">Area Pengungkit</div>
                    <div class="col-span-3 text-center">Skor (Bobot)</div>
                    <div class="col-span-3 text-right">Capaian</div>
                </div>
        `;
            let allAreasPassed = true;
            for (const [name, area] of Object.entries(compliance.areas)) {
                const areaDiff = area.threshold - area.nilai;
                const diffText = area.is_passed ? '' :
                    `<span class="block text-[9px] font-bold text-red-500 mt-0.5">Kurang ${areaDiff.toFixed(2)} poin</span>`;
                const icon = area.is_passed ?
                    '<span class="text-green-600 mr-1.5 font-bold">✓</span>' :
                    '<span class="text-red-500 mr-1.5 font-bold">✗</span>';

                areasHtml += `
                <div class="grid grid-cols-12 items-center px-3.5 py-2.5 text-[11px] ${!area.is_passed ? 'bg-red-50/20' : ''}">
                    <div class="col-span-6 font-semibold text-gray-700 flex items-center leading-tight">
                        ${icon}
                        <span>${name}</span>
                    </div>
                    <div class="col-span-3 text-center font-medium text-gray-600">
                        ${area.nilai.toFixed(2)} <span class="text-[9px] text-gray-450">/ ${area.bobot.toFixed(2)}</span>
                    </div>
                    <div class="col-span-3 text-right font-bold ${area.is_passed ? 'text-green-600' : 'text-red-650'}">
                        ${area.persen.toFixed(1)}%
                        ${diffText}
                    </div>
                </div>
            `;
                if (!area.is_passed) allAreasPassed = false;
            }
            areasHtml += '</div>';

            // 4. Birokrasi Bersih (Total & Sub)
            const birokrasi = compliance.birokrasi_total;
            const spak = compliance.spak;
            const capaian = compliance.capaian;
            const birokrasiDiff = birokrasi.threshold - birokrasi.nilai;
            const spakDiff = spak.threshold - spak.nilai;
            const capaianDiff = capaian.threshold - capaian.nilai;
            const birokrasiDiffText = birokrasi.is_passed ? '' :
                `<p class="text-[11px] text-red-650 font-semibold mt-1">Kurang <strong>${birokrasiDiff.toFixed(2)}</strong> poin untuk mencapai minimal 18.25</p>`;

            let birokrasiHtml = `
            <div class="mt-3 border border-gray-100 rounded-xl bg-gray-50/50 overflow-hidden divide-y divide-gray-100">
                <div class="grid grid-cols-12 bg-gray-100/50 text-[9px] font-bold text-gray-500 uppercase tracking-wider px-3.5 py-2">
                    <div class="col-span-6">Sub-Komponen Hasil</div>
                    <div class="col-span-3 text-center">Batas Minimal</div>
                    <div class="col-span-3 text-right">Skor Riil</div>
                </div>
                <!-- SPAK Row -->
                <div class="grid grid-cols-12 items-center px-3.5 py-2.5 text-[11px] ${!spak.is_passed ? 'bg-red-50/20' : ''}">
                    <div class="col-span-6 font-semibold text-gray-700 flex items-center leading-tight">
                        ${spak.is_passed ? '<span class="text-green-600 mr-1.5 font-bold">✓</span>' : '<span class="text-red-500 mr-1.5 font-bold">✗</span>'}
                        <span>Survei Persepsi Korupsi (SPAK)</span>
                    </div>
                    <div class="col-span-3 text-center font-medium text-gray-500">&ge; 15.75</div>
                    <div class="col-span-3 text-right font-bold ${spak.is_passed ? 'text-green-600' : 'text-red-650'}">
                        ${spak.nilai.toFixed(2)} <span class="text-[9px] text-gray-400">/ 17.50</span>
                        ${!spak.is_passed ? `<span class="block text-[9px] text-red-500 font-bold mt-0.5">Kurang ${spakDiff.toFixed(2)}</span>` : ''}
                    </div>
                </div>
                <!-- Capaian Kinerja Row -->
                <div class="grid grid-cols-12 items-center px-3.5 py-2.5 text-[11px] ${!capaian.is_passed ? 'bg-red-50/20' : ''}">
                    <div class="col-span-6 font-semibold text-gray-700 flex items-center leading-tight">
                        ${capaian.is_passed ? '<span class="text-green-600 mr-1.5 font-bold">✓</span>' : '<span class="text-red-500 mr-1.5 font-bold">✗</span>'}
                        <span>Capaian Kinerja Lebih Baik</span>
                    </div>
                    <div class="col-span-3 text-center font-medium text-gray-500">&ge; 2.50</div>
                    <div class="col-span-3 text-right font-bold ${capaian.is_passed ? 'text-green-600' : 'text-red-650'}">
                        ${capaian.nilai.toFixed(2)} <span class="text-[9px] text-gray-400">/ 5.00</span>
                        ${!capaian.is_passed ? `<span class="block text-[9px] text-red-500 font-bold mt-0.5">Kurang ${capaianDiff.toFixed(2)}</span>` : ''}
                    </div>
                </div>
            </div>
        `;

            // 5. Pelayanan Publik Prima
            const pelayanan = compliance.pelayanan;
            const pelayananDiff = pelayanan.threshold - pelayanan.nilai;
            const pelayananDiffText = pelayanan.is_passed ? '' :
                `<p class="text-[11px] text-red-650 font-semibold mt-1">Kurang <strong>${pelayananDiff.toFixed(2)}</strong> poin untuk mencapai minimal 14.00</p>`;

            // Build entire modal body HTML
            body.innerHTML = `
            <!-- Requirement 1 -->
            <div class="bg-white p-4 rounded-xl border border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex-1">
                    <h4 class="font-bold text-gray-800 text-xs">1. Total Nilai Evaluasi ZI</h4>
                    <p class="text-[11px] text-gray-400 mt-0.5">Skor gabungan seluruh komponen pengungkit & hasil (Min. 75.00)</p>
                    ${ziDiffText}
                </div>
                <div class="flex items-center sm:flex-col sm:items-end justify-between sm:justify-center shrink-0 border-t sm:border-t-0 border-gray-50 pt-2 sm:pt-0">
                    <div class="sm:text-right pr-2 sm:pr-0">
                        <span class="text-[10px] text-gray-400 block sm:inline">Nilai:</span>
                        <span class="text-sm font-extrabold ${zi.is_passed ? 'text-green-600' : 'text-red-650'}">${zi.nilai.toFixed(2)}</span>
                    </div>
                    <div class="mt-1">${getStatusBadge(zi.is_passed)}</div>
                </div>
            </div>

            <!-- Requirement 2 -->
            <div class="bg-white p-4 rounded-xl border border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex-1">
                    <h4 class="font-bold text-gray-800 text-xs">2. Total Nilai Pengungkit</h4>
                    <p class="text-[11px] text-gray-400 mt-0.5">Jumlah skor dari ke-6 Area Pengungkit (Min. 40.00)</p>
                    ${pengungkitDiffText}
                </div>
                <div class="flex items-center sm:flex-col sm:items-end justify-between sm:justify-center shrink-0 border-t sm:border-t-0 border-gray-50 pt-2 sm:pt-0">
                    <div class="sm:text-right pr-2 sm:pr-0">
                        <span class="text-[10px] text-gray-400 block sm:inline">Nilai:</span>
                        <span class="text-sm font-extrabold ${pengungkit.is_passed ? 'text-green-600' : 'text-red-650'}">${pengungkit.nilai.toFixed(2)}</span>
                    </div>
                    <div class="mt-1">${getStatusBadge(pengungkit.is_passed)}</div>
                </div>
            </div>

            <!-- Requirement 3 -->
            <div class="bg-white p-4 rounded-xl border border-gray-100">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h4 class="font-bold text-gray-800 text-xs">3. Bobot Per Area Pengungkit (Min. 60%)</h4>
                        <p class="text-[11px] text-gray-400 mt-0.5">Setiap area pengungkit wajib memenuhi minimal 60% dari bobot total area</p>
                    </div>
                    ${getStatusBadge(allAreasPassed)}
                </div>
                ${areasHtml}
            </div>

            <!-- Requirement 4 -->
            <div class="bg-white p-4 rounded-xl border border-gray-100">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h4 class="font-bold text-gray-800 text-xs">4. Birokrasi yang Bersih dan Akuntabel</h4>
                        <p class="text-[11px] text-gray-400 mt-0.5">Minimal skor 18.25 dengan detail batas sub-komponen berikut</p>
                        ${birokrasiDiffText}
                    </div>
                    <div class="flex flex-col items-end shrink-0 justify-center">
                        <div class="text-right">
                            <span class="text-[10px] text-gray-400">Total:</span>
                            <span class="text-sm font-extrabold ${birokrasi.is_passed ? 'text-green-600' : 'text-red-650'}">${birokrasi.nilai.toFixed(2)}</span>
                        </div>
                        <div class="mt-1">${getStatusBadge(birokrasi.is_passed)}</div>
                    </div>
                </div>
                ${birokrasiHtml}
            </div>

            <!-- Requirement 5 -->
            <div class="bg-white p-4 rounded-xl border border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex-1">
                    <h4 class="font-bold text-gray-800 text-xs">5. Pelayanan Publik yang Prima</h4>
                    <p class="text-[11px] text-gray-400 mt-0.5">Skor Survei Kualitas Pelayanan (Eksternal) (Min. 14.00 | Bobot 17.50)</p>
                    ${pelayananDiffText}
                </div>
                <div class="flex items-center sm:flex-col sm:items-end justify-between sm:justify-center shrink-0 border-t sm:border-t-0 border-gray-50 pt-2 sm:pt-0">
                    <div class="sm:text-right pr-2 sm:pr-0">
                        <span class="text-[10px] text-gray-400 block sm:inline">Nilai:</span>
                        <span class="text-sm font-extrabold ${pelayanan.is_passed ? 'text-green-600' : 'text-red-650'}">${pelayanan.nilai.toFixed(2)}</span>
                    </div>
                    <div class="mt-1">${getStatusBadge(pelayanan.is_passed)}</div>
                </div>
            </div>
        `;

            // Open Animation
            const modal = document.getElementById('wbkModal');
            const backdrop = document.getElementById('wbkModalBackdrop');
            const card = document.getElementById('wbkModalCard');

            modal.classList.remove('hidden');
            // Force Reflow
            modal.offsetHeight;

            backdrop.classList.remove('opacity-0');
            backdrop.classList.add('opacity-100');

            card.classList.remove('scale-95', 'opacity-0');
            card.classList.add('scale-100', 'opacity-100');

            document.body.style.overflow = 'hidden';
        }

        function closeWbkModal() {
            const modal = document.getElementById('wbkModal');
            const backdrop = document.getElementById('wbkModalBackdrop');
            const card = document.getElementById('wbkModalCard');

            backdrop.classList.remove('opacity-100');
            backdrop.classList.add('opacity-0');

            card.classList.remove('scale-100', 'opacity-100');
            card.classList.add('scale-95', 'opacity-0');

            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }, 300);
        }
    </script>
@endpush
