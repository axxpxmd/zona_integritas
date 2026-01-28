@extends('layouts.app')

@section('title', 'Detail Komponen')
@section('page-title', 'Detail Komponen')

@section('content')
<div class="space-y-6">
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('komponen.index') }}"
               class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-gray-900">Detail Komponen</h2>
                <p class="text-sm text-gray-500 mt-1">Informasi lengkap komponen Zona Integritas</p>
            </div>
        </div>
        <a href="{{ route('komponen.edit', $komponen) }}"
           class="inline-flex items-center justify-center gap-2 bg-primary text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-primary-dark transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit Komponen
        </a>
    </div>

    {{-- Main Info Card --}}
    <div class="bg-white rounded-xl p-6 space-y-6">
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-primary/10 rounded-xl flex items-center justify-center flex-shrink-0">
                    <span class="text-2xl font-bold text-primary">{{ $komponen->kode }}</span>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900">{{ $komponen->nama }}</h3>
                    <p class="text-base text-gray-600 mt-0.5">Komponen {{ $komponen->urutan }}</p>
                </div>
            </div>
            <div class="flex gap-2">
                @if($komponen->status == 1)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium bg-green-100 text-green-700">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                    Aktif
                </span>
                @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium bg-gray-100 text-gray-600">
                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>
                    Tidak Aktif
                </span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-gray-100">
            {{-- Bobot --}}
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Bobot</p>
                    <p class="text-base font-semibold text-gray-900 mt-0.5">{{ $komponen->bobot }}%</p>
                </div>
            </div>

            {{-- Urutan --}}
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Urutan</p>
                    <p class="text-base font-semibold text-gray-900 mt-0.5">{{ $komponen->urutan }}</p>
                </div>
            </div>

            {{-- Jumlah Kategori --}}
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Jumlah Kategori</p>
                    <p class="text-base font-semibold text-gray-900 mt-0.5">{{ $komponen->kategoris->count() }} Kategori</p>
                </div>
            </div>
        </div>

        {{-- Deskripsi --}}
        @if($komponen->deskripsi)
        <div class="pt-6 border-t border-gray-100">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Deskripsi</h4>
            <p class="text-sm text-gray-600 leading-relaxed">{{ $komponen->deskripsi }}</p>
        </div>
        @endif

        {{-- Meta Info --}}
        <div class="pt-6 border-t border-gray-100 grid grid-cols-1 md:grid-cols-2 gap-4 text-xs text-gray-500">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <span>Dibuat: {{ $komponen->created_at->format('d F Y H:i') }}</span>
            </div>
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span>Diperbarui: {{ $komponen->updated_at->format('d F Y H:i') }}</span>
            </div>
        </div>
    </div>

    {{-- Kategori List --}}
    @if($komponen->kategoris->count() > 0)
    <div class="bg-white rounded-xl p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Daftar Kategori ({{ $komponen->kategoris->count() }})</h3>
        <div class="space-y-3">
            @foreach($komponen->kategoris as $kategori)
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center border border-gray-200">
                        <span class="text-sm font-bold text-primary">{{ $kategori->kode }}</span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $kategori->nama }}</p>
                        <p class="text-xs text-gray-500">Bobot: {{ $kategori->bobot }}% | Urutan: {{ $kategori->urutan }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @if($kategori->status == 1)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                        Aktif
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                        Tidak Aktif
                    </span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
