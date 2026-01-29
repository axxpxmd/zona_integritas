@extends('layouts.app')

@section('title', 'Detail Sub Pertanyaan')
@section('page-title', 'Detail Sub Pertanyaan')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center gap-4 bg-white px-6 py-4 rounded-xl">
        <a href="{{ route('sub-pertanyaan.index') }}"
           class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
           title="Kembali">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div class="flex items-center gap-3 flex-1">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Detail Sub Pertanyaan</h1>
                <p class="text-sm text-gray-600">Informasi lengkap sub pertanyaan</p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('sub-pertanyaan.edit', $subPertanyaan) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-50 text-yellow-700 rounded-lg text-sm font-medium hover:bg-yellow-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>
            <form action="{{ route('sub-pertanyaan.destroy', $subPertanyaan) }}"
                  method="POST"
                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus sub pertanyaan ini?')"
                  class="inline">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 text-red-700 rounded-lg text-sm font-medium hover:bg-red-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus
                </button>
            </form>
        </div>
    </div>

    {{-- Breadcrumb Hierarchy (6 levels) --}}
    <div class="bg-white rounded-xl p-5">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Hierarki</h3>
        <div class="flex flex-wrap items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-purple-100 text-purple-700">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                {{ $subPertanyaan->pertanyaanUtama->indikator->subKategori->kategori->komponen->kode }} - {{ $subPertanyaan->pertanyaanUtama->indikator->subKategori->kategori->komponen->nama }}
            </span>
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-blue-100 text-blue-700">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                </svg>
                {{ $subPertanyaan->pertanyaanUtama->indikator->subKategori->kategori->kode }} - {{ $subPertanyaan->pertanyaanUtama->indikator->subKategori->kategori->nama }}
            </span>
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-teal-100 text-teal-700">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                {{ $subPertanyaan->pertanyaanUtama->indikator->subKategori->kode }} - {{ $subPertanyaan->pertanyaanUtama->indikator->subKategori->nama }}
            </span>
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-amber-100 text-amber-700">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                {{ $subPertanyaan->pertanyaanUtama->indikator->kode }} - {{ $subPertanyaan->pertanyaanUtama->indikator->nama }}
            </span>
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-indigo-100 text-indigo-700">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ $subPertanyaan->pertanyaanUtama->kode }} - {{ Str::limit($subPertanyaan->pertanyaanUtama->pertanyaan, 40) }}
            </span>
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-primary/10 text-primary">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                {{ $subPertanyaan->kode }} - {{ Str::limit($subPertanyaan->pertanyaan, 40) }}
            </span>
        </div>
    </div>

    {{-- Main Info Card --}}
    <div class="bg-white rounded-xl p-6 space-y-5">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-3">
                    <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-lg font-bold bg-blue-100 text-blue-700">
                        {{ $subPertanyaan->kode }}
                    </span>
                    @if($subPertanyaan->tipe_input === 'jumlah')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-700">
                        Jumlah
                    </span>
                    @elseif($subPertanyaan->tipe_input === 'persen')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-700">
                        Persen (%)
                    </span>
                    @elseif($subPertanyaan->tipe_input === 'angka')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-700">
                        Angka
                    </span>
                    @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-700">
                        Teks
                    </span>
                    @endif
                    @if($subPertanyaan->satuan)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-700">
                        {{ $subPertanyaan->satuan }}
                    </span>
                    @endif
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $subPertanyaan->pertanyaan }}</h2>
                <p class="text-sm text-gray-600">
                    <span class="font-medium">Pertanyaan Utama:</span> {{ $subPertanyaan->pertanyaanUtama->pertanyaan }}
                </p>
            </div>
            @if($subPertanyaan->status == 1)
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

        @if($subPertanyaan->formula)
        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-orange-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                <div class="flex-1">
                    <h4 class="text-sm font-semibold text-orange-900 mb-1">Formula Perhitungan</h4>
                    <code class="block bg-white px-3 py-2 rounded border border-orange-300 text-sm font-mono text-orange-700">{{ $subPertanyaan->formula }}</code>
                </div>
            </div>
        </div>
        @endif

        @if($subPertanyaan->penjelasan)
        <div>
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Penjelasan</h3>
            <p class="text-gray-600 leading-relaxed">{{ $subPertanyaan->penjelasan }}</p>
        </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-gray-200">
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600">Urutan</p>
                        <p class="text-lg font-bold text-gray-900">{{ $subPertanyaan->urutan }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600">Dibuat</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $subPertanyaan->created_at->format('d M Y') }}</p>
                        <p class="text-xs text-gray-500">{{ $subPertanyaan->created_at->format('H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if($subPertanyaan->updated_at != $subPertanyaan->created_at)
        <div class="bg-gray-50 rounded-lg p-3 text-sm text-gray-600">
            <span class="font-medium">Terakhir diperbarui:</span> {{ $subPertanyaan->updated_at->format('d M Y H:i') }}
        </div>
        @endif
    </div>
</div>
@endsection
