@extends('layouts.app')

@section('title', 'Pilih Sub Kategori - ' . $periode->nama_periode)
@section('page-title', 'Pilih Sub Kategori')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('kuesioner.index') }}"
               class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $periode->nama_periode }}</h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ $opd->n_opd }}</p>
            </div>
        </div>
        <div class="text-sm text-gray-600">
            <span class="font-medium">Periode:</span> {{ $periode->tanggal_mulai->format('d M Y') }} - {{ $periode->tanggal_selesai->format('d M Y') }}
        </div>
    </div>

    {{-- Info --}}
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex gap-3">
            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="text-sm text-blue-800">
                <p class="font-medium">Pilih Sub Kategori untuk Mulai Mengisi</p>
                <p class="mt-1">Pilih salah satu sub kategori di bawah untuk mulai mengisi pertanyaan. Progress pengisian akan tersimpan otomatis.</p>
            </div>
        </div>
    </div>

    {{-- Komponen Loop --}}
    @foreach($komponens as $komponen)
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
                <div class="text-white/90 text-sm font-medium">
                    Bobot: {{ $komponen->bobot }}%
                </div>
            </div>
        </div>

        {{-- Kategori & Sub Kategori --}}
        <div class="divide-y divide-gray-200">
            @foreach($komponen->kategoris as $kategori)
            <div class="p-6">
                {{-- Kategori Header --}}
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                        <span class="text-sm font-bold text-gray-700">{{ $kategori->kode }}</span>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <h4 class="text-base font-semibold text-gray-900">{{ $kategori->nama }}</h4>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs font-medium">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                                </svg>
                                {{ $kategori->bobot }}
                            </span>
                        </div>
                        @if($kategori->deskripsi)
                        <p class="text-xs text-gray-500 mt-0.5">{{ $kategori->deskripsi }}</p>
                        @endif
                    </div>
                </div>

                {{-- Sub Kategori Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 ml-13">
                    @foreach($kategori->subKategoris as $subKategori)
                    @php
                        $prog = $progress[$subKategori->id] ?? ['total' => 0, 'terjawab' => 0, 'persen' => 0];
                    @endphp
                    <a href="{{ route('kuesioner.fill', [$periode->id, $subKategori->id]) }}"
                       class="block bg-white border-2 border-gray-200 rounded-xl p-4 hover:border-primary hover:shadow-lg transition-all group">
                        {{-- Sub Kategori Header --}}
                        <div class="flex items-start gap-3 mb-3">
                            <div class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-primary group-hover:text-white transition-colors">
                                <span class="text-xs font-bold text-primary group-hover:text-white">{{ $subKategori->kode }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h5 class="text-sm font-semibold text-gray-900 group-hover:text-primary transition-colors line-clamp-2">
                                    {{ $subKategori->nama }}
                                </h5>
                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-accent/20 text-gray-700 rounded text-xs font-medium mt-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                                    </svg>
                                    Bobot: {{ $subKategori->bobot }}
                                </span>
                            </div>
                        </div>

                        {{-- Progress Bar --}}
                        <div class="mb-2">
                            <div class="flex items-center justify-between text-xs text-gray-600 mb-1">
                                <span>Progress</span>
                                <span class="font-semibold">{{ $prog['terjawab'] }}/{{ $prog['total'] }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                <div class="h-full transition-all duration-300 rounded-full {{ $prog['persen'] == 100 ? 'bg-green-500' : 'bg-primary' }}"
                                     style="width: {{ $prog['persen'] }}%"></div>
                            </div>
                        </div>

                        {{-- Status Badge --}}
                        <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
                            @if($prog['persen'] == 100)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Selesai
                            </span>
                            @elseif($prog['persen'] > 0)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Dalam Progress
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Belum Mulai
                            </span>
                            @endif

                            <svg class="w-5 h-5 text-gray-400 group-hover:text-primary group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
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
