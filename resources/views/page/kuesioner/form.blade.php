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

        {{-- Pagination Info --}}
        <div class="px-6 pt-4 pb-2 bg-gray-50 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    <span class="font-semibold text-gray-900">Indikator {{ $currentPage }}</span> dari {{ $totalIndikator }}
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-500">{{ $currentIndikator->kode }}. {{ $currentIndikator->nama }}</span>
                </div>
            </div>
        </div>

        {{-- Indikator Content --}}
        <form action="{{ route('kuesioner.submit') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="p-6">
                <div class="mb-6">
                    {{-- Indikator Header --}}
                    <div class="flex items-start gap-2 mb-4">
                        <span class="inline-flex items-center justify-center w-6 h-6 bg-primary/10 text-primary rounded text-sm font-bold flex-shrink-0 mt-0.5">
                            {{ $currentIndikator->kode }}
                        </span>
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <h6 class="text-base font-semibold text-gray-900">{{ $currentIndikator->nama }}</h6>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-accent/20 text-gray-700 rounded text-xs font-medium">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                                    </svg>
                                    Bobot: {{ $currentIndikator->bobot }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Pertanyaan Loop --}}
                    <div class="space-y-4">
                        @foreach($currentIndikator->pertanyaans as $pertanyaan)
                        <div class="bg-white rounded-lg p-4 border border-gray-200">
                            {{-- Pertanyaan --}}
                            <div class="flex items-start gap-3 mb-3">
                                <span class="inline-flex items-center justify-center min-w-[24px] h-6 bg-gray-100 text-gray-700 rounded text-xs font-semibold px-2">
                                    {{ $pertanyaan->urutan }}
                                </span>
                                <p class="text-sm text-gray-900 flex-1">{{ $pertanyaan->pertanyaan }}</p>
                                <input type="text" name="pertanyaan_id[]" value="{{ $pertanyaan->id }}">
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

            {{-- Submit Button --}}
            <div class="mt-6 pt-6 border-t border-gray-200">
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Simpan Jawaban</span>
                </button>
            </div>
        {{-- Pagination Navigation --}}
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <div class="flex items-center justify-between">
                {{-- Previous Button --}}
                @if($currentPage > 1)
                <a href="{{ route('kuesioner.fill', [$periode->id, $subKategori->id, 'indikator' => $currentPage - 1]) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    <span class="text-sm font-medium">Indikator Sebelumnya</span>
                </a>
                @else
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 border border-gray-200 text-gray-400 rounded-lg cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    <span class="text-sm font-medium">Indikator Sebelumnya</span>
                </div>
                @endif

                {{-- Page Indicator --}}
                <div class="flex items-center gap-2">
                    @for($i = 1; $i <= $totalIndikator; $i++)
                    <a href="{{ route('kuesioner.fill', [$periode->id, $subKategori->id, 'indikator' => $i]) }}"
                       class="w-8 h-8 flex items-center justify-center rounded-lg text-sm font-medium transition-colors {{ $i === $currentPage ? 'bg-primary text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                        {{ $i }}
                    </a>
                    @endfor
                </div>

                {{-- Next Button --}}
                @if($currentPage < $totalIndikator)
                <a href="{{ route('kuesioner.fill', [$periode->id, $subKategori->id, 'indikator' => $currentPage + 1]) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors">
                    <span class="text-sm font-medium">Indikator Selanjutnya</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                @else
                <a href="{{ route('kuesioner.show', $periode->id) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <span class="text-sm font-medium">Selesai</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </a>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
