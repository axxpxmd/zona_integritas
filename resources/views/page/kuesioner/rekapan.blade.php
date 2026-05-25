@extends('layouts.app')
@section('title', 'Rekapan Hasil Kuesioner')
@section('page-title', 'Rekapan Hasil LKE')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-xl font-semibold text-gray-800">Rekapan Hasil LKE</h2>
        <p class="text-sm text-gray-500 mt-1">
            Periode: <span class="font-medium text-gray-700">{{ $periode->nama_periode }}</span> |
            Unit Kerja: <span class="font-medium text-gray-700">{{ $opd->n_opd }}</span>
        </p>
    </div>
    <a href="{{ route('kuesioner.show', $periode->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Kembali
    </a>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left border-collapse">
            <thead class="bg-[#0164CA] text-white">
                <tr>
                    <th class="px-6 py-4 border-r border-[#0150A8] font-semibold">Area Perubahan</th>
                    <th class="px-6 py-4 border-r border-[#0150A8] font-semibold text-center w-24">Bobot</th>
                    <th class="px-6 py-4 border-r border-[#0150A8] font-semibold text-center w-32">Pemenuhan</th>
                    <th class="px-6 py-4 border-r border-[#0150A8] font-semibold text-center w-32">Reform</th>
                    <th class="px-6 py-4 border-r border-[#0150A8] font-semibold text-center w-32">Nilai</th>
                    <th class="px-6 py-4 font-semibold text-center w-24">%</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <tr class="bg-gray-100 font-medium">
                    <td colspan="6" class="px-6 py-3 font-bold text-gray-800">A. PENGUNGKIT</td>
                </tr>
                @php
                    $totalPengungkitBobot = 0;
                    $totalPengungkitNilai = 0;
                @endphp

                @foreach($rekapPengungkit as $area)
                    @php
                        $bobotArea = $area['pemenuhan_bobot'] + $area['reform_bobot'];
                        $nilaiArea = $area['pemenuhan_nilai'] + $area['reform_nilai'];
                        $persenArea = $bobotArea > 0 ? ($nilaiArea / $bobotArea) * 100 : 0;

                        $totalPengungkitBobot += $bobotArea;
                        $totalPengungkitNilai += $nilaiArea;
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 border-r border-gray-200 text-gray-700">
                            {{ $loop->iteration }}. {{ $area['nama'] }}
                        </td>
                        <td class="px-6 py-4 border-r border-gray-200 text-center font-medium">
                            {{ number_format($bobotArea, 2) }}
                        </td>
                        <td class="px-6 py-4 border-r border-gray-200 text-center text-[#0164CA]">
                            {{ number_format($area['pemenuhan_nilai'], 2) }}
                        </td>
                        <td class="px-6 py-4 border-r border-gray-200 text-center text-[#0164CA]">
                            {{ number_format($area['reform_nilai'], 2) }}
                        </td>
                        <td class="px-6 py-4 border-r border-gray-200 text-center font-semibold text-gray-900">
                            {{ number_format($nilaiArea, 2) }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($persenArea >= 100)
                                <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    {{ number_format($persenArea, 2) }}%
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-blue-50 text-[#0164CA]">
                                    {{ number_format($persenArea, 2) }}%
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach

                {{-- Subtotal Pengungkit --}}
                <tr class="bg-gray-100 font-semibold text-gray-900 border-t-2 border-gray-300">
                    <td class="px-6 py-4 border-r border-gray-200 text-right">TOTAL PENGUNGKIT</td>
                    <td class="px-6 py-4 border-r border-gray-200 text-center">{{ number_format($totalPengungkitBobot, 2) }}</td>
                    <td colspan="2" class="px-6 py-4 border-r border-gray-200 bg-gray-100"></td>
                    <td class="px-6 py-4 border-r border-gray-200 text-center">{{ number_format($totalPengungkitNilai, 2) }}</td>
                    <td class="px-6 py-4 text-center">
                        {{ number_format($totalPengungkitBobot > 0 ? ($totalPengungkitNilai / $totalPengungkitBobot) * 100 : 0, 2) }}%
                    </td>
                </tr>

                {{-- Komponen B. Hasil --}}
                <tr class="bg-gray-100 font-medium border-t-4 border-[#0164CA]">
                    <td colspan="6" class="px-6 py-3 font-bold text-gray-800">B. HASIL</td>
                </tr>
                @php
                    $totalHasilBobot = 0;
                    $totalHasilNilai = 0;
                @endphp
                @foreach($rekapHasil as $hasil)
                    @php
                        $persenHasil = $hasil['bobot'] > 0 ? ($hasil['nilai'] / $hasil['bobot']) * 100 : 0;
                        $totalHasilBobot += $hasil['bobot'];
                        $totalHasilNilai += $hasil['nilai'];
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors bg-gray-50 font-semibold">
                        <td class="px-6 py-4 border-r border-gray-200 text-gray-800">
                            {{ $hasil['kode'] ?? $loop->iteration }}. {{ $hasil['nama'] }}
                        </td>
                        <td class="px-6 py-4 border-r border-gray-200 text-center text-gray-800">
                            {{ number_format($hasil['bobot'], 2) }}
                        </td>
                        <td colspan="2" class="px-6 py-4 border-r border-gray-200 text-center"></td>
                        <td class="px-6 py-4 border-r border-gray-200 text-center text-gray-800">
                            {{ number_format($hasil['nilai'], 2) }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($persenHasil >= 100)
                                <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    {{ number_format($persenHasil, 2) }}%
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-blue-50 text-[#0164CA]">
                                    {{ number_format($persenHasil, 2) }}%
                                </span>
                            @endif
                        </td>
                    </tr>
                    @foreach($hasil['subs'] as $sub)
                        @php
                            $persenSub = $sub['bobot'] > 0 ? ($sub['nilai'] / $sub['bobot']) * 100 : 0;
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 border-r border-gray-200 text-gray-700 pl-10">
                                {{ $sub['kode'] }}. {{ $sub['nama'] }}
                            </td>
                            <td class="px-6 py-4 border-r border-gray-200 text-center font-medium">
                                {{ number_format($sub['bobot'], 2) }}
                            </td>
                            <td colspan="2" class="bg-gray-50 px-6 py-4 border-r border-gray-200 text-center text-gray-400 italic text-xs">
                            </td>
                            <td class="px-6 py-4 border-r border-gray-200 text-center font-semibold text-gray-900">
                                {{ number_format($sub['nilai'], 2) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($persenSub >= 100)
                                    <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        {{ number_format($persenSub, 2) }}%
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-blue-50 text-[#0164CA]">
                                        {{ number_format($persenSub, 2) }}%
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endforeach

                {{-- Subtotal Hasil --}}
                <tr class="bg-gray-100 font-semibold text-gray-900 border-t-2 border-gray-300">
                    <td class="px-6 py-4 border-r border-gray-200 text-right">TOTAL HASIL</td>
                    <td class="px-6 py-4 border-r border-gray-200 text-center">{{ number_format($totalHasilBobot, 2) }}</td>
                    <td colspan="2" class="px-6 py-4 border-r border-gray-200 bg-gray-100"></td>
                    <td class="px-6 py-4 border-r border-gray-200 text-center">{{ number_format($totalHasilNilai, 2) }}</td>
                    <td class="px-6 py-4 text-center">
                        {{ number_format($totalHasilBobot > 0 ? ($totalHasilNilai / $totalHasilBobot) * 100 : 0, 2) }}%
                    </td>
                </tr>

                {{-- TOTAL KESELURUHAN --}}
                @php
                    $grandTotalBobot = $totalPengungkitBobot + $totalHasilBobot;
                    $grandTotalNilai = $totalPengungkitNilai + $totalHasilNilai;
                    $grandTotalPersen = $grandTotalBobot > 0 ? ($grandTotalNilai / $grandTotalBobot) * 100 : 0;
                @endphp
                <tr class="bg-[#F7D558] text-gray-900 font-bold border-t-4 border-[#0164CA]">
                    <td class="px-6 py-5 border-r border-[#E0C040] text-right uppercase tracking-wide">NILAI EVALUASI ZONA INTEGRITAS</td>
                    <td class="px-6 py-5 border-r border-[#E0C040] text-center text-lg">{{ number_format($grandTotalBobot, 2) }}</td>
                    <td colspan="2" class="px-6 py-5 border-r border-[#E0C040]"></td>
                    <td class="px-6 py-5 border-r border-[#E0C040] text-center text-lg">{{ number_format($grandTotalNilai, 2) }}</td>
                    <td class="px-6 py-5 text-center text-lg">{{ number_format($grandTotalPersen, 2) }}%</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
