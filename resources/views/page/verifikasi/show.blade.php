@extends('layouts.app')

@section('title', 'Verifikasi Jawaban OPD')
@section('page-title', 'Detail Verifikasi: ' . $opd->n_opd)

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('verifikasi.index') }}" class="p-2 rounded-lg hover:bg-gray-100 text-gray-500 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-900 border-l-4 border-primary pl-4 rounded">Evaluasi LKE: {{ $opd->n_opd }}</h2>
            <p class="text-sm text-gray-500 mt-1 ml-4">{{ $periode->nama_periode }} ({{ $periode->tahun }})</p>
        </div>
    </div>

    {{-- Status Ringkasan --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="text-sm font-medium text-gray-500">Total Jawaban</div>
            <div class="mt-1 text-2xl font-bold text-gray-900">{{ $verifikasiStats['total_jawaban'] }}</div>
        </div>
        <div class="bg-gray-50 rounded-xl border border-gray-200 p-4">
            <div class="text-sm font-medium text-gray-500">Belum Diverifikasi</div>
            <div class="mt-1 text-2xl font-bold text-gray-700">{{ $verifikasiStats['belum_diverifikasi'] }}</div>
        </div>
        <div class="bg-green-50 rounded-xl border border-green-200 p-4">
            <div class="text-sm font-medium text-green-700">Disetujui</div>
            <div class="mt-1 text-2xl font-bold text-green-800">{{ $verifikasiStats['disetujui'] }}</div>
        </div>
        <div class="bg-red-50 rounded-xl border border-red-200 p-4">
            <div class="text-sm font-medium text-red-700">Direvisi</div>
            <div class="mt-1 text-2xl font-bold text-red-800">{{ $verifikasiStats['direvisi'] }}</div>
        </div>
    </div>

    {{-- Kategori & Sub Kategori Struktur --}}
    @foreach($komponens as $komponen)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
            <div class="bg-primary border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-bold text-white">
                    Komponen: {{ $komponen->nama }}
                    <span class="text-sm font-normal text-white/80 block">{{ $komponen->bobot }}%</span>
                </h3>
            </div>

            <div class="divide-y divide-gray-100">
                @foreach($komponen->kategoris as $kategori)
                    <div class="px-6 py-4 bg-gray-50">
                        <h4 class="text-md font-semibold text-gray-900">Kategori: {{ $kategori->nama }}</h4>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($kategori->subKategoris as $sub)
                                <a href="{{ route('verifikasi.detail', [$periode->id, $opd->id, $sub->id]) }}"
                                   class="block bg-white rounded-xl border border-gray-200 p-5 hover:border-primary hover:shadow-md transition-all group">

                                    <div class="flex justify-between items-start mb-3">
                                        <h5 class="font-medium text-gray-900 group-hover:text-primary transition-colors line-clamp-2">
                                            {{ $sub->nama }}
                                        </h5>
                                    </div>

                                    @php
                                        // Hitung status dalam Sub Kategori ini
                                        $sub_total = 0;
                                        $sub_verified = 0;
                                        foreach($sub->indikators as $indikator) {
                                            foreach($indikator->pertanyaans as $pertanyaan) {
                                                if ($pertanyaan->subPertanyaans->count() > 0) {
                                                    foreach($pertanyaan->subPertanyaans as $sp) {
                                                        $sub_total++;
                                                        $key = "{$pertanyaan->id}_{$sp->id}";
                                                        if (isset($jawabanMap[$key]) && $jawabanMap[$key]->status_verifikasi != 'belum_diverifikasi') {
                                                            $sub_verified++;
                                                        }
                                                    }
                                                } else {
                                                    $sub_total++;
                                                    $key = $pertanyaan->id;
                                                    if (isset($jawabanMap[$key]) && $jawabanMap[$key]->status_verifikasi != 'belum_diverifikasi') {
                                                        $sub_verified++;
                                                    }
                                                }
                                            }
                                        }

                                        $isComplete = $sub_total > 0 && $sub_verified == $sub_total;
                                        $progressPercent = $sub_total > 0 ? round(($sub_verified / $sub_total) * 100) : 0;
                                    @endphp

                                    <div class="space-y-2">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-500">Progress</span>
                                            <span class="{{ $isComplete ? 'text-green-600 font-medium' : 'text-gray-700' }}">
                                                {{ $sub_verified }}/{{ $sub_total }} Diversifikasi
                                            </span>
                                        </div>
                                        <div class="w-full bg-gray-100 rounded-full h-2">
                                            <div class="h-2 rounded-full {{ $isComplete ? 'bg-green-500' : 'bg-primary' }}"
                                                 style="width: {{ $progressPercent }}%"></div>
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
