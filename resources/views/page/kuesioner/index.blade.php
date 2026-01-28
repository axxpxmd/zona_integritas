@extends('layouts.app')

@section('title', 'Kuesioner Zona Integritas')
@section('page-title', 'Kuesioner Zona Integritas')

@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Header Section --}}
    <div class="text-center mb-8">
        <div class="w-20 h-20 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Kuesioner Zona Integritas</h2>
        <p class="text-gray-600">Pilih periode pengisian kuesioner yang tersedia</p>
    </div>

    @if(session('error'))
    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-start gap-3">
        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    {{-- Periode List --}}
    <div class="space-y-4">
        @forelse($periodes as $periode)
        <a href="{{ route('kuesioner.show', $periode->id) }}"
           class="block bg-white rounded-xl p-6 hover:shadow-lg transition-all border-2 border-transparent hover:border-primary/20 group">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-start gap-4 flex-1">
                    {{-- Icon --}}
                    <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-primary/20 transition-colors">
                        <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>

                    {{-- Info --}}
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-gray-900 mb-1 group-hover:text-primary transition-colors">
                            {{ $periode->nama_periode }}
                        </h3>
                        <div class="flex flex-wrap items-center gap-3 text-sm text-gray-600">
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $periode->tanggal_mulai->format('d M Y') }} - {{ $periode->tanggal_selesai->format('d M Y') }}
                            </div>
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $periode->tanggal_mulai->diffInDays($periode->tanggal_selesai) + 1 }} Hari
                            </div>
                        </div>
                        @if($periode->deskripsi)
                        <p class="text-sm text-gray-500 mt-2 line-clamp-2">{{ $periode->deskripsi }}</p>
                        @endif
                    </div>
                </div>

                {{-- Status & Arrow --}}
                <div class="flex items-center gap-3">
                    @php
                        $colors = [
                            'draft' => 'bg-gray-100 text-gray-700',
                            'aktif' => 'bg-green-100 text-green-700',
                            'selesai' => 'bg-blue-100 text-blue-700',
                            'ditutup' => 'bg-red-100 text-red-700',
                        ];
                    @endphp
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium {{ $colors[$periode->status] }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ str_replace('100', '500', $colors[$periode->status]) }}"></span>
                        {{ ucfirst($periode->status) }}
                    </span>
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-primary group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </div>
        </a>
        @empty
        <div class="bg-gray-50 rounded-xl p-12 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-gray-600 font-medium">Belum ada periode yang tersedia</p>
            <p class="text-sm text-gray-500 mt-1">Silakan hubungi administrator</p>
        </div>
        @endforelse
    </div>

    {{-- Info Box --}}
    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex gap-3">
            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="text-sm text-blue-800">
                <p class="font-medium mb-1">Informasi Penting:</p>
                <ul class="space-y-1 list-disc list-inside">
                    <li>Pilih periode yang tersedia untuk mulai mengisi kuesioner</li>
                    <li>Jawaban akan tersimpan otomatis saat Anda mengisi</li>
                    <li>Anda dapat melanjutkan pengisian kapan saja</li>
                    <li>Pastikan mengisi semua pertanyaan sebelum periode berakhir</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
