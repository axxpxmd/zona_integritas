@extends('layouts.app')

@section('title', 'Pilih Sub Kategori - ' . $periode->nama_periode)
@section('page-title', 'Pilih Sub Kategori')

@section('content')
@php
    $now = \Carbon\Carbon::now()->startOfDay();
    $start = \Carbon\Carbon::parse($periode->tanggal_mulai)->startOfDay();
    $end = \Carbon\Carbon::parse($periode->tanggal_selesai)->endOfDay();
    $isCanFill = $now->between($start, $end);
@endphp
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
        <div class="flex flex-col items-end gap-1">
            <div class="text-sm text-gray-600">
                <span class="font-medium">Waktu Pengisian:</span> {{ $start->format('d M Y') }} - {{ $end->format('d M Y') }}
            </div>
            @if($now->lt($start))
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Belum Dimulai</span>
            @elseif($now->gt($end))
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Waktu Habis</span>
            @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Sedang Berjalan</span>
            @endif
        </div>
    </div>

    {{-- Info --}}
    @if(!$isCanFill)
    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex gap-3">
            <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div class="text-sm text-red-800">
                <p class="font-medium">Waktu Pengisian Tidak Aktif</p>
                <p class="mt-1">Anda hanya dapat melihat isian Lembar Kerja Evaluasi Anda saat ini. Anda tidak dapat mengisi atau memperbarui jawaban karena di luar waktu pengisian.</p>
            </div>
        </div>
    </div>
    @else
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
    @endif

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
                        $prog = $progress[$subKategori->id] ?? ['total' => 0, 'terjawab' => 0, 'persen' => 0, 'nilai' => 0, 'capaian' => 0];
                    @endphp
                    <a href="{{ route('kuesioner.fill', [$periode->id, $subKategori->id]) }}"
                       class="flex flex-col h-full bg-white border-2 border-gray-200 rounded-xl p-4 hover:border-primary hover:shadow-lg transition-all group">
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

                        {{-- Nilai & Capaian --}}
                        <div class="grid grid-cols-2 gap-2 mb-3">
                            <div class="bg-gray-50 rounded-lg p-2.5 border border-gray-100 flex flex-col justify-center">
                                <p class="text-[10px] text-gray-500 uppercase font-semibold tracking-wider mb-0.5">Nilai</p>
                                <p class="text-sm font-bold text-gray-900">{{ number_format($prog['nilai'], 2) }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-2.5 border border-gray-100 flex flex-col justify-center">
                                <p class="text-[10px] text-gray-500 uppercase font-semibold tracking-wider mb-0.5">Capaian</p>
                                <p class="text-sm font-bold {{ $prog['capaian'] >= 80 ? 'text-green-600' : ($prog['capaian'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">{{ number_format($prog['capaian'], 2) }}%</p>
                            </div>
                        </div>

                        {{-- Progress Bar --}}
                        <div class="mt-auto mb-2">
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
