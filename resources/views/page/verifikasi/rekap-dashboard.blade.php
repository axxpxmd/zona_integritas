@extends('layouts.app')

@section('title', 'Dashboard Verifikator')
@section('page-title', 'Dashboard Verifikator')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Rekapan Hasil Verifikasi OPD</h2>
                <p class="text-sm text-gray-500 mt-1">
                    Tabel ini menampilkan rangkuman nilai LKE OPD yang sudah disetujui atau terkirim oleh verifikator.
                </p>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4">
            <form action="{{ route('verifikasi.rekap') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
                <div class="w-full sm:w-64">
                    <label for="periode_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Periode</label>
                    <select name="periode_id" id="periode_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary text-sm"
                        onchange="this.form.submit()">
                        @if($periodes->isEmpty())
                            <option value="">Tidak ada periode aktif</option>
                        @else
                            @foreach($periodes as $p)
                                <option value="{{ $p->id }}" {{ $activePeriode && $activePeriode->id == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama_periode }} ({{ $p->tahun }})
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                @if($activePeriode)
                    <div class="w-full sm:w-auto text-sm text-gray-600 bg-gray-50 px-4 py-2 rounded-lg border border-gray-200">
                        <span class="font-medium text-gray-900">Periode Aktif:</span> {{ $activePeriode->nama_periode }}
                    </div>
                @endif
            </form>
        </div>

        @if($activePeriode)
            <div class="bg-white rounded-xl p-4">
                <p class="text-xs text-gray-500">
                    Ambang WBK: Total ZI >= {{ number_format($thresholds['total'], 2) }}, Total Pengungkit >= {{ number_format($thresholds['pengungkit_total'], 2) }},
                    Area Pengungkit >= 60%, Birokrasi Bersih dan Akuntabel >= 18.25, SPAK >= {{ number_format($thresholds['spak'], 2) }},
                    Capaian Kinerja >= {{ number_format($thresholds['capaian'], 2) }}, Pelayanan Publik Prima >= {{ number_format($thresholds['pelayanan'], 2) }}.
                </p>
            </div>

            @if($rekapRows->isEmpty())
                <div class="bg-white rounded-xl p-8 text-center border border-gray-200">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-medium text-gray-900">Belum Ada Rekapan</h3>
                    <p class="text-sm text-gray-500 mt-1">Belum ada OPD dengan status verifikasi disetujui atau terkirim pada periode ini.</p>
                </div>
            @else
                <div class="bg-white rounded-xl overflow-hidden border border-gray-200">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-xs text-gray-700">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th rowspan="2" class="px-3 py-3 text-center text-[11px] font-semibold uppercase tracking-wider">No</th>
                                    <th rowspan="2" class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider">Unit Kerja</th>
                                    <th colspan="7" class="px-3 py-3 text-center text-[11px] font-semibold uppercase tracking-wider">Pengungkit ({{ number_format($bobotMeta['pengungkit_total'], 2) }}%)</th>
                                    <th colspan="4" class="px-3 py-3 text-center text-[11px] font-semibold uppercase tracking-wider">Hasil ({{ number_format($bobotMeta['hasil_total'], 2) }}%)</th>
                                    <th rowspan="2" class="px-3 py-3 text-center text-[11px] font-semibold uppercase tracking-wider">Total (100%)</th>
                                    <th rowspan="2" class="px-3 py-3 text-center text-[11px] font-semibold uppercase tracking-wider">Simpulan</th>
                                </tr>
                                <tr class="bg-primary-dark">
                                    @foreach($areaOrder as $areaName)
                                        <th class="px-3 py-2 text-center text-[10px] font-semibold uppercase">
                                            <div>{{ $areaName }}</div>
                                            <div class="text-[9px] text-white/80">({{ number_format($bobotMeta['area'][$areaName], 2) }}%)</div>
                                        </th>
                                    @endforeach
                                    <th class="px-3 py-2 text-center text-[10px] font-semibold uppercase">
                                        Jumlah Pengungkit
                                        <div class="text-[9px] text-white/80">({{ number_format($bobotMeta['pengungkit_total'], 2) }}%)</div>
                                    </th>
                                    <th class="px-3 py-2 text-center text-[10px] font-semibold uppercase">
                                        SPAK
                                        <div class="text-[9px] text-white/80">({{ number_format($bobotMeta['hasil']['spak'], 2) }}%)</div>
                                    </th>
                                    <th class="px-3 py-2 text-center text-[10px] font-semibold uppercase">
                                        Capaian Kinerja
                                        <div class="text-[9px] text-white/80">({{ number_format($bobotMeta['hasil']['capaian'], 2) }}%)</div>
                                    </th>
                                    <th class="px-3 py-2 text-center text-[10px] font-semibold uppercase">
                                        SPP
                                        <div class="text-[9px] text-white/80">({{ number_format($bobotMeta['hasil']['pelayanan'], 2) }}%)</div>
                                    </th>
                                    <th class="px-3 py-2 text-center text-[10px] font-semibold uppercase">
                                        Jumlah Hasil
                                        <div class="text-[9px] text-white/80">({{ number_format($bobotMeta['hasil_total'], 2) }}%)</div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr class="bg-amber-50 text-[11px] font-semibold text-amber-900">
                                    <td colspan="2" class="px-4 py-2">Ambang WBK</td>
                                    @foreach($areaOrder as $areaName)
                                        <td class="px-3 py-2 text-center">{{ number_format($thresholds['area'][$areaName], 2) }}</td>
                                    @endforeach
                                    <td class="px-3 py-2 text-center">{{ number_format($thresholds['pengungkit_total'], 2) }}</td>
                                    <td class="px-3 py-2 text-center">{{ number_format($thresholds['spak'], 2) }}</td>
                                    <td class="px-3 py-2 text-center">{{ number_format($thresholds['capaian'], 2) }}</td>
                                    <td class="px-3 py-2 text-center">{{ number_format($thresholds['pelayanan'], 2) }}</td>
                                    <td class="px-3 py-2 text-center">{{ number_format($thresholds['hasil_total'], 2) }}</td>
                                    <td class="px-3 py-2 text-center">{{ number_format($thresholds['total'], 2) }}</td>
                                    <td class="px-3 py-2 text-center">-</td>
                                </tr>
                                @foreach($rekapRows as $index => $row)
                                    <tr class="hover:bg-gray-50/50 {{ $row['meets_wbk'] ? 'bg-green-50/40' : '' }}">
                                        <td class="px-3 py-3 text-center text-gray-500">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3 text-sm font-semibold text-gray-900">
                                            {{ $row['opd'] }}
                                        </td>
                                        @foreach($row['areas'] as $area)
                                            <td class="px-3 py-3 text-center">
                                                <div class="font-semibold text-gray-800">{{ number_format($area['nilai'], 2) }}</div>
                                                <div class="text-[10px] text-gray-500">({{ number_format($area['persen'], 2) }}%)</div>
                                            </td>
                                        @endforeach
                                        <td class="px-3 py-3 text-center">
                                            <div class="font-semibold text-gray-800">{{ number_format($row['pengungkit']['nilai'], 2) }}</div>
                                            <div class="text-[10px] text-gray-500">({{ number_format($row['pengungkit']['persen'], 2) }}%)</div>
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            <div class="font-semibold text-gray-800">{{ number_format($row['spak']['nilai'], 2) }}</div>
                                            <div class="text-[10px] text-gray-500">({{ number_format($row['spak']['persen'], 2) }}%)</div>
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            <div class="font-semibold text-gray-800">{{ number_format($row['capaian']['nilai'], 2) }}</div>
                                            <div class="text-[10px] text-gray-500">({{ number_format($row['capaian']['persen'], 2) }}%)</div>
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            <div class="font-semibold text-gray-800">{{ number_format($row['pelayanan']['nilai'], 2) }}</div>
                                            <div class="text-[10px] text-gray-500">({{ number_format($row['pelayanan']['persen'], 2) }}%)</div>
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            <div class="font-semibold text-gray-800">{{ number_format($row['hasil']['nilai'], 2) }}</div>
                                            <div class="text-[10px] text-gray-500">({{ number_format($row['hasil']['persen'], 2) }}%)</div>
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            <div class="font-semibold text-gray-900">{{ number_format($row['total']['nilai'], 2) }}</div>
                                            <div class="text-[10px] text-gray-500">({{ number_format($row['total']['persen'], 2) }}%)</div>
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            @if($row['meets_wbk'])
                                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] font-semibold bg-green-100 text-green-700">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Memenuhi
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] font-semibold bg-red-100 text-red-700">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                    Belum
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-8 text-center">
                <h3 class="text-sm font-medium text-yellow-800">Pilih Periode Terlebih Dahulu</h3>
                <p class="text-sm text-yellow-700 mt-1">Silakan pilih periode untuk menampilkan rekapan verifikasi.</p>
            </div>
        @endif
    </div>
@endsection
