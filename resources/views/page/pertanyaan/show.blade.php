@extends('layouts.app')

@section('title', 'Detail Pertanyaan')
@section('page-title', 'Detail Pertanyaan')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('pertanyaan.index') }}"
           class="p-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div class="flex-1">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Detail Pertanyaan</h2>
                    <p class="text-sm text-gray-600 mt-0.5">Informasi lengkap pertanyaan</p>
                </div>
            </div>
        </div>
        <a href="{{ route('pertanyaan.edit', $pertanyaan) }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-yellow-50 text-yellow-700 border border-yellow-200 rounded-lg text-sm font-medium hover:bg-yellow-100 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit
        </a>
    </div>

    {{-- Breadcrumb Hierarchy --}}
    <div class="bg-white rounded-xl p-4">
        <div class="flex items-center gap-2 flex-wrap text-sm">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-purple-100 text-purple-700 font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                {{ $pertanyaan->indikator->subKategori->kategori->komponen->kode }} - {{ $pertanyaan->indikator->subKategori->kategori->komponen->nama }}
            </span>
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-100 text-indigo-700 font-medium">
                {{ $pertanyaan->indikator->subKategori->kategori->kode }} - {{ $pertanyaan->indikator->subKategori->kategori->nama }}
            </span>
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-cyan-100 text-cyan-700 font-medium">
                {{ $pertanyaan->indikator->subKategori->kode }} - {{ $pertanyaan->indikator->subKategori->nama }}
            </span>
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-100 text-blue-700 font-medium">
                {{ $pertanyaan->indikator->kode }} - {{ $pertanyaan->indikator->nama }}
            </span>
        </div>
    </div>

    {{-- Main Info Card --}}
    <div class="bg-white rounded-xl p-6">
        <div class="flex items-start justify-between gap-4 mb-6">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-3">
                    <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-2xl font-bold bg-blue-100 text-blue-700">
                        {{ $pertanyaan->kode }}
                    </span>
                    @if($pertanyaan->tipe_jawaban === 'ya_tidak')
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-green-100 text-green-700">
                        Ya/Tidak
                    </span>
                    @elseif($pertanyaan->tipe_jawaban === 'pilihan_ganda')
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-purple-100 text-purple-700">
                        Pilihan Ganda
                    </span>
                    @elseif($pertanyaan->tipe_jawaban === 'angka')
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-orange-100 text-orange-700">
                        Angka
                    </span>
                    @else
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-gray-100 text-gray-700">
                        Teks
                    </span>
                    @endif
                    @if($pertanyaan->status == 1)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium bg-green-100 text-green-700">
                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                        Aktif
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium bg-gray-100 text-gray-600">
                        <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                        Tidak Aktif
                    </span>
                    @endif
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $pertanyaan->pertanyaan }}</h3>
            </div>
        </div>

        {{-- Penjelasan --}}
        @if($pertanyaan->penjelasan)
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <h4 class="text-sm font-semibold text-blue-900 mb-2 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Penjelasan/Petunjuk Jawaban
            </h4>
            <p class="text-sm text-blue-800 leading-relaxed">{{ $pertanyaan->penjelasan }}</p>
        </div>
        @endif

        {{-- Pilihan Jawaban --}}
        @if($pertanyaan->pilihan_jawaban && is_array($pertanyaan->pilihan_jawaban) && count($pertanyaan->pilihan_jawaban) > 0)
        <div class="mb-6">
            <h4 class="text-sm font-semibold text-gray-700 mb-3">Pilihan Jawaban</h4>
            <div class="flex flex-wrap gap-2">
                @foreach($pertanyaan->pilihan_jawaban as $pilihan)
                <span class="inline-flex items-center px-3 py-1.5 rounded-lg bg-purple-50 text-purple-700 border border-purple-200 font-medium text-sm">
                    {{ $pilihan }}
                </span>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Stats Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <div class="bg-purple-50 rounded-xl p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-purple-600 font-medium">Urutan</p>
                        <p class="text-xl font-bold text-purple-700">{{ $pertanyaan->urutan }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-green-50 rounded-xl p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-green-600 font-medium">Jumlah Sub Pertanyaan</p>
                        <p class="text-xl font-bold text-green-700">{{ $pertanyaan->subPertanyaans->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Meta Info --}}
        <div class="border-t border-gray-200 pt-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div class="flex items-center gap-2 text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span>Dibuat: {{ $pertanyaan->created_at->format('d M Y H:i') }}</span>
                </div>
                <div class="flex items-center gap-2 text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span>Diubah: {{ $pertanyaan->updated_at->format('d M Y H:i') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Sub Pertanyaan List --}}
    @if($pertanyaan->subPertanyaans->count() > 0)
    <div class="bg-white rounded-xl p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Daftar Sub Pertanyaan</h3>
            <span class="text-sm text-gray-600">{{ $pertanyaan->subPertanyaans->count() }} sub pertanyaan</span>
        </div>

        <div class="space-y-3">
            @foreach($pertanyaan->subPertanyaans as $subPertanyaan)
            <div class="border border-gray-200 rounded-lg p-4 hover:border-primary/50 transition-colors">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700">
                                {{ $subPertanyaan->kode }}
                            </span>
                            @if($subPertanyaan->status == 1)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                Aktif
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>
                                Tidak Aktif
                            </span>
                            @endif
                        </div>
                        <p class="font-medium text-gray-900 text-sm">{{ $subPertanyaan->pertanyaan }}</p>
                    </div>
                    <span class="text-xs text-gray-600">Urutan: <strong>{{ $subPertanyaan->urutan }}</strong></span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
