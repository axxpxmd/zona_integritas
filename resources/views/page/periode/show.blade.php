@extends('layouts.app')

@section('title', 'Detail Periode')
@section('page-title', 'Detail Periode')

@section('content')
<div class="space-y-6">
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('periode.index') }}"
               class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-gray-900">Detail Periode</h2>
                <p class="text-sm text-gray-500 mt-1">Informasi lengkap periode pengisian kuesioner</p>
            </div>
        </div>
        <a href="{{ route('periode.edit', $periode) }}"
           class="inline-flex items-center justify-center gap-2 bg-primary text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-primary-dark transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit Periode
        </a>
    </div>

    {{-- Main Info Card --}}
    <div class="bg-white rounded-xl p-6 space-y-6">
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-primary/10 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900">{{ $periode->tahun }}</h3>
                    <p class="text-base text-gray-600 mt-0.5">{{ $periode->nama_periode }}</p>
                </div>
            </div>
            <div class="flex gap-2">
                @php
                    $colors = [
                        'draft' => 'bg-gray-100 text-gray-700',
                        'aktif' => 'bg-green-100 text-green-700',
                        'selesai' => 'bg-blue-100 text-blue-700',
                        'ditutup' => 'bg-red-100 text-red-700',
                    ];
                @endphp
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium {{ $colors[$periode->status] }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ str_replace('100', '500', $colors[$periode->status]) }}"></span>
                    {{ $periode->status_label }}
                </span>
                @if($periode->is_template)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium bg-yellow-100 text-yellow-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Template
                </span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-gray-100">
            {{-- Tanggal Mulai --}}
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Tanggal Mulai</p>
                    <p class="text-base font-semibold text-gray-900 mt-0.5">
                        {{ $periode->tanggal_mulai->format('d F Y') }}
                    </p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        {{ $periode->tanggal_mulai->diffForHumans() }}
                    </p>
                </div>
            </div>

            {{-- Tanggal Selesai --}}
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Tanggal Selesai</p>
                    <p class="text-base font-semibold text-gray-900 mt-0.5">
                        {{ $periode->tanggal_selesai->format('d F Y') }}
                    </p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        {{ $periode->tanggal_selesai->diffForHumans() }}
                    </p>
                </div>
            </div>

            {{-- Durasi --}}
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Durasi Periode</p>
                    <p class="text-base font-semibold text-gray-900 mt-0.5">
                        {{ $periode->tanggal_mulai->diffInDays($periode->tanggal_selesai) + 1 }} Hari
                    </p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        {{ $periode->tanggal_mulai->format('d/m/Y') }} - {{ $periode->tanggal_selesai->format('d/m/Y') }}
                    </p>
                </div>
            </div>

            {{-- Copy From --}}
            @if($periode->copiedFrom)
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Di-copy dari</p>
                    <a href="{{ route('periode.show', $periode->copiedFrom) }}"
                       class="text-base font-semibold text-primary hover:text-primary-dark mt-0.5 inline-block">
                        {{ $periode->copiedFrom->nama_periode }}
                    </a>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Tahun {{ $periode->copiedFrom->tahun }}
                    </p>
                </div>
            </div>
            @endif
        </div>

        {{-- Deskripsi --}}
        @if($periode->deskripsi)
        <div class="pt-6 border-t border-gray-100">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Deskripsi</h4>
            <p class="text-sm text-gray-600 leading-relaxed">{{ $periode->deskripsi }}</p>
        </div>
        @endif

        {{-- Meta Info --}}
        <div class="pt-6 border-t border-gray-100 grid grid-cols-1 md:grid-cols-2 gap-4 text-xs text-gray-500">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <span>Dibuat: {{ $periode->created_at->format('d F Y H:i') }}</span>
            </div>
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span>Diperbarui: {{ $periode->updated_at->format('d F Y H:i') }}</span>
            </div>
        </div>
    </div>

    {{-- Periode yang Di-copy dari periode ini --}}
    @if($periode->copiedPeriodes->count() > 0)
    <div class="bg-white rounded-xl p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Periode yang Di-copy dari Periode Ini</h3>
        <div class="space-y-3">
            @foreach($periode->copiedPeriodes as $copied)
            <a href="{{ route('periode.show', $copied) }}"
               class="flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $copied->nama_periode }}</p>
                        <p class="text-xs text-gray-500">Tahun {{ $copied->tahun }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @php $colors = ['draft' => 'bg-gray-100 text-gray-700', 'aktif' => 'bg-green-100 text-green-700', 'selesai' => 'bg-blue-100 text-blue-700', 'ditutup' => 'bg-red-100 text-red-700']; @endphp
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $colors[$copied->status] }}">
                        {{ $copied->status_label }}
                    </span>
                    <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
