@extends('layouts.app')

@section('title', 'Isi Kuesioner - ' . $subKategori->nama)
@section('page-title', 'Isi Kuesioner')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('kuesioner.show', $periode->id) }}"
               class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $subKategori->nama }}</h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ $periode->nama_periode }} • {{ $opd->n_opd }}</p>
            </div>
        </div>
    </div>

    {{-- Breadcrumb --}}
    <div class="bg-white rounded-lg p-4">
        <div class="flex flex-wrap items-center gap-2 text-sm">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-purple-100 text-purple-700 font-medium">
                {{ $subKategori->kategori->komponen->kode }}. {{ $subKategori->kategori->komponen->nama }}
            </span>
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-100 text-blue-700 font-medium">
                {{ $subKategori->kategori->kode }}. {{ $subKategori->kategori->nama }}
            </span>
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary text-white font-medium">
                {{ $subKategori->kode }}. {{ $subKategori->nama }}
            </span>
        </div>
    </div>

    {{-- Sub Kategori Content --}}
    <div class="bg-white rounded-xl overflow-hidden">
        {{-- Sub Kategori Header --}}
        <div class="bg-gradient-to-r from-primary to-primary-dark px-6 py-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <span class="text-lg font-bold text-white">{{ $subKategori->kode }}</span>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white">{{ $subKategori->nama }}</h3>
                    @if($subKategori->deskripsi)
                    <p class="text-sm text-white/80 mt-1">{{ $subKategori->deskripsi }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Indikator Loop --}}
        <div class="p-6 space-y-6">
            @foreach($subKategori->indikators as $indikator)
            <div class="ml-9 mb-6 last:mb-0">
                <div class="flex items-start gap-2 mb-3">
                    <span class="inline-flex items-center justify-center w-5 h-5 bg-primary/10 text-primary rounded text-xs font-bold flex-shrink-0 mt-0.5">
                        {{ $indikator->kode }}
                    </span>
                    <div>
                        <h6 class="text-sm font-medium text-gray-900">{{ $indikator->nama }}</h6>
                        @if($indikator->deskripsi)
                        <p class="text-xs text-gray-600 mt-1">{{ $indikator->deskripsi }}</p>
                        @endif
                    </div>
                </div>

                {{-- Pertanyaan Loop --}}
                <div class="space-y-4 ml-7">
                    @foreach($indikator->pertanyaans as $pertanyaan)
                    <div class="bg-white rounded-lg p-4 border border-gray-200">
                        {{-- Pertanyaan --}}
                        <div class="flex items-start gap-3 mb-3">
                            <span class="inline-flex items-center justify-center min-w-[24px] h-6 bg-gray-100 text-gray-700 rounded text-xs font-semibold px-2">
                                {{ $pertanyaan->urutan }}
                            </span>
                            <p class="text-sm text-gray-900 flex-1">{{ $pertanyaan->pertanyaan }}</p>
                        </div>

                        {{-- Input berdasarkan tipe --}}
                        @if($pertanyaan->tipe_jawaban === 'ya_tidak')
                            @include('page.kuesioner.partials.input-ya-tidak', [
                                'pertanyaan' => $pertanyaan,
                                'jawaban' => $jawabans[$pertanyaan->id] ?? null
                            ])
                        @elseif($pertanyaan->tipe_jawaban === 'pilihan_ganda')
                            @include('page.kuesioner.partials.input-pilihan-ganda', [
                                'pertanyaan' => $pertanyaan,
                                'jawaban' => $jawabans[$pertanyaan->id] ?? null
                            ])
                        @elseif($pertanyaan->tipe_jawaban === 'angka')
                            @if($pertanyaan->has_sub_pertanyaan)
                                @include('page.kuesioner.partials.input-sub-pertanyaan', [
                                    'pertanyaan' => $pertanyaan,
                                    'jawabans' => $jawabans
                                ])
                            @else
                                @include('page.kuesioner.partials.input-angka', [
                                    'pertanyaan' => $pertanyaan,
                                    'jawaban' => $jawabans[$pertanyaan->id] ?? null
                                ])
                            @endif
                        @endif

                        {{-- Keterangan (optional) --}}
                        <div class="mt-3 pt-3 border-t border-gray-100">
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                Keterangan (Opsional)
                            </label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none resize-none"
                                        rows="2"
                                        placeholder="Tambahkan catatan atau keterangan jika diperlukan...">{{ $jawabans[$pertanyaan->id]->keterangan ?? '' }}</textarea>
                        </div>

                        {{-- Upload Dokumen (optional) --}}
                        <div class="mt-3 pt-3 border-t border-gray-100">
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                Upload Dokumen Pendukung (Opsional)
                            </label>
                            <div class="space-y-2">
                                @if(isset($jawabans[$pertanyaan->id]) && $jawabans[$pertanyaan->id]->file_path)
                                <div class="flex items-center gap-2 p-2 bg-green-50 border border-green-200 rounded-lg">
                                    <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span class="text-xs text-green-700 flex-1 truncate">
                                        {{ basename($jawabans[$pertanyaan->id]->file_path) }}
                                    </span>
                                </div>
                                @endif
                                <input type="file" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"
                                        accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                                <p class="text-xs text-gray-500">Format: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG. Maksimal 5MB.</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection
