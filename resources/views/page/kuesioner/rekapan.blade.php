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

<!-- Nav Tabs -->
<div class="mb-6 bg-gray-100 p-1.5 rounded-xl flex flex-wrap sm:inline-flex gap-1.5 border border-gray-200 shadow-sm">
    <button type="button" onclick="switchTab('operator')" id="tab-operator" class="px-5 py-2.5 rounded-lg text-sm font-semibold transition-all flex items-center gap-2 bg-white text-[#0164CA] shadow-sm border border-gray-200">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
        Jawaban Operator
    </button>
    <button type="button" onclick="switchTab('verifikator')" id="tab-verifikator" class="px-5 py-2.5 rounded-lg text-sm font-medium transition-all flex items-center gap-2 text-gray-500 hover:text-gray-700 hover:bg-gray-200 hover:shadow-sm border border-transparent">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
        Jawaban Verifikator
    </button>
    <button type="button" onclick="switchTab('menpan')" id="tab-menpan" class="px-5 py-2.5 rounded-lg text-sm font-medium transition-all flex items-center gap-2 text-gray-500 hover:text-gray-700 hover:bg-gray-200 hover:shadow-sm border border-transparent">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
        Jawaban Menpan
    </button>
</div>

@foreach(['operator', 'verifikator', 'menpan'] as $role)
@php
    $rekapPengungkit = $rekapData[$role]['rekapPengungkit'];
    $rekapHasil = $rekapData[$role]['rekapHasil'];
@endphp
<div id="content-{{ $role }}" class="{{ $role == 'operator' ? 'block' : 'hidden' }}">
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
                    <tr class="bg-gray-100 font-medium border-t-2">
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
                        <td class="px-6 py-4 border-r border-gray-200 text-right uppercase">Total Pengungkit (A)</td>
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
                        <td class="px-6 py-4 border-r border-gray-200 text-right uppercase">Total Hasil (B)</td>
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
                        <td class="px-6 py-5 border-r border-[#E0C040] text-right uppercase tracking-wide">Nilai Evaluasi Zona Integritas (A+B)</td>
                        <td class="px-6 py-5 border-r border-[#E0C040] text-center text-lg">{{ number_format($grandTotalBobot, 2) }}</td>
                        <td colspan="2" class="px-6 py-5 border-r border-[#E0C040]"></td>
                        <td class="px-6 py-5 border-r border-[#E0C040] text-center text-lg">{{ number_format($grandTotalNilai, 2) }}</td>
                        <td class="px-6 py-5 text-center text-lg">{{ number_format($grandTotalPersen, 2) }}%</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endforeach

<script>
    function switchTab(role) {
        const roles = ['operator', 'verifikator', 'menpan'];
        roles.forEach(r => {
            document.getElementById('content-' + r).classList.add('hidden');
            document.getElementById('content-' + r).classList.remove('block');

            let tab = document.getElementById('tab-' + r);
            tab.classList.remove('bg-white', 'text-[#0164CA]', 'shadow-sm', 'font-semibold', 'border-gray-200');
            tab.classList.add('text-gray-500', 'font-medium', 'hover:bg-gray-200', 'hover:shadow-sm', 'border-transparent');
        });

        document.getElementById('content-' + role).classList.remove('hidden');
        document.getElementById('content-' + role).classList.add('block');

        let activeTab = document.getElementById('tab-' + role);
        activeTab.classList.remove('text-gray-500', 'font-medium', 'hover:bg-gray-200', 'hover:shadow-sm', 'border-transparent');
        activeTab.classList.add('bg-white', 'text-[#0164CA]', 'shadow-sm', 'font-semibold', 'border-gray-200');
    }
</script>
@endsection
