@extends('layouts.app')

@section('title', 'Verifikasi Detail')
@section('page-title', 'Pemeriksaan Jawaban')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    {{-- Navigasi Atas --}}
    <div class="flex items-center gap-4 bg-white p-4 rounded-xl border border-gray-200">
        <a href="{{ route('verifikasi.show', [$periode->id, $opd->id]) }}" class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-gray-50 text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div class="flex-1">
            <div class="flex items-center gap-2 text-sm text-gray-500 font-medium mb-1">
                <span>{{ $subKategori->kategori->komponen->nama }}</span>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span>{{ $subKategori->kategori->nama }}</span>
            </div>
            <h2 class="text-xl font-bold text-gray-900">{{ $subKategori->nama }}</h2>
        </div>
    </div>

    {{-- Kumpulan Indikator & Pertanyaan --}}
    <div class="space-y-8">
        @foreach($subKategori->indikators as $indikatorIndex => $indikator)
            <div class="bg-white rounded-xl border border-primary/20 overflow-hidden shadow-sm">
                {{-- Header Indikator --}}
                <div class="bg-primary/5 border-b border-primary/10 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold text-sm">
                           {{ $indikatorIndex + 1 }}
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">{{ $indikator->nama }}</h3>
                    </div>
                    @if($indikator->deskripsi)
                        <p class="text-gray-600 text-sm mt-2 ml-11">{{ $indikator->deskripsi }}</p>
                    @endif
                </div>

                <div class="divide-y divide-gray-100">
                    @foreach($indikator->pertanyaans as $pertanyaanIndex => $pertanyaan)
                        {{-- Jika tidak ada sub pertanyaan --}}
                        @if($pertanyaan->subPertanyaans->count() === 0)
                            @php
                                $key = $pertanyaan->id;
                                $jawaban = $jawabanMap[$key] ?? null;
                            @endphp

                            <div class="px-6 py-6 md:pl-16">
                                <div class="flex gap-4">
                                    <div class="text-gray-400 font-medium">{{ $indikatorIndex + 1 }}.{{ $pertanyaanIndex + 1 }}</div>
                                    <div class="flex-1 space-y-4">
                                        <h4 class="text-gray-900 font-medium text-base">{{ $pertanyaan->pertanyaan }}</h4>

                                        {{-- Kotak Jawaban Operator --}}
                                        @if($jawaban)
                                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Jawaban Operator</div>
                                                <div class="grid grid-cols-2 gap-4 text-sm">
                                                    <div>
                                                        <span class="text-gray-500 block mb-1">Nilai / Pilihan:</span>
                                                        <span class="font-medium text-gray-900 bg-white px-3 py-1 border border-gray-200 rounded-md">
                                                            {{ $jawaban->jawaban_text ?? $jawaban->jawaban_angka ?? '-' }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-500 block mb-1">Keterangan / Dokumen:</span>
                                                        <span class="text-gray-800">{{ $jawaban->keterangan ?: 'Tidak ada keterangan/dokumen.' }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Form Verifikasi Simulas --}}
                                            <div class="mt-4 border-l-2 border-primary pl-4">
                                                <div class="text-sm font-medium text-primary mb-2">Status Verifikasi</div>
                                                <div class="flex flex-wrap gap-2 text-sm">
                                                    @if($jawaban->status_verifikasi == 'disetujui')
                                                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full font-medium flex items-center gap-1">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Disetujui
                                                        </span>
                                                    @elseif($jawaban->status_verifikasi == 'direvisi')
                                                        <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full font-medium flex items-center gap-1">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg> Direvisi
                                                        </span>
                                                        <div class="mt-2 w-full text-sm text-gray-700">
                                                            <span class="font-medium">Catatan Verifikator:</span> {{ $jawaban->catatan_verifikator }}
                                                        </div>
                                                    @else
                                                        <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full">Belum Diverifikasi</span>
                                                        <span class="text-xs text-gray-400 self-center italic">(Form verifikasi interaktif dapat ditambahkan disini di tahapan selanjutnya)</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <div class="text-sm text-red-500 italic bg-red-50 p-3 rounded-lg border border-red-100">
                                                Operator ini tidak mengisi jawaban untuk pertanyaan ini.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Punya Sub Pertanyaan --}}
                            <div class="px-6 py-5 md:pl-16">
                                <div class="flex gap-4">
                                    <div class="text-gray-400 font-medium">{{ $indikatorIndex + 1 }}.{{ $pertanyaanIndex + 1 }}</div>
                                    <div class="flex-1">
                                        <h4 class="text-gray-900 font-medium text-base mb-4">{{ $pertanyaan->pertanyaan }}</h4>

                                        <div class="space-y-4">
                                            @foreach($pertanyaan->subPertanyaans as $spIndex => $sp)
                                                @php
                                                    $key = "{$pertanyaan->id}_{$sp->id}";
                                                    $jawaban = $jawabanMap[$key] ?? null;
                                                @endphp
                                                <div class="bg-gray-50/50 p-4 rounded-lg border border-gray-100">
                                                    <div class="flex gap-3 mb-3">
                                                        <span class="text-gray-400 text-sm mt-0.5">{{ chr(97 + $spIndex) }}.</span>
                                                        <span class="text-gray-700 text-sm font-medium">{{ $sp->pertanyaan }}</span>
                                                    </div>

                                                    @if($jawaban)
                                                        <div class="ml-7 bg-white border border-gray-200 rounded-lg p-4">
                                                            <div class="grid grid-cols-2 gap-4 text-sm mb-3">
                                                                <div>
                                                                    <span class="text-gray-500 block mb-1">Jawaban:</span>
                                                                    <span class="font-medium text-gray-900">
                                                                        {{ $jawaban->jawaban_text ?? $jawaban->jawaban_angka ?? '-' }}
                                                                    </span>
                                                                </div>
                                                                <div>
                                                                    <span class="text-gray-500 block mb-1">Keterangan:</span>
                                                                    <span class="text-gray-800">{{ $jawaban->keterangan ?: '-' }}</span>
                                                                </div>
                                                            </div>

                                                            <div class="flex items-center gap-2 pt-3 border-t border-gray-100">
                                                                @if($jawaban->status_verifikasi == 'disetujui')
                                                                    <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded">Disetujui</span>
                                                                @elseif($jawaban->status_verifikasi == 'direvisi')
                                                                    <span class="text-xs font-medium text-red-600 bg-red-50 px-2 py-1 rounded">Direvisi</span>
                                                                    <span class="text-xs text-gray-600 line-clamp-1 flex-1">{{ $jawaban->catatan_verifikator }}</span>
                                                                @else
                                                                    <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-1 rounded">Belum Diverifikasi</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="ml-7 text-sm text-red-500 italic">Belum dijawab</div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection
