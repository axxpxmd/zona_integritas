@extends('layouts.app')

@section('title', 'Verifikasi Menpan Detail')
@section('page-title', 'Pemeriksaan Jawaban Menpan')

@section('content')
<div class="space-y-6">

    {{-- Alert Success --}}
    @if(session('success'))
    <div class="bg-green-100 border border-green-200 text-green-800 rounded-lg px-4 py-3 mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="font-semibold">{{ session('success') }}</span>
    </div>
    @endif

    {{-- Alert Error --}}
    @if(session('error'))
    <div class="bg-red-100 border border-red-200 text-red-800 rounded-lg px-4 py-3 mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <span class="font-semibold">{{ session('error') }}</span>
    </div>
    @endif

    {{-- Alert Waktu Verifikasi --}}
    @if(!$isCanVerify)
    <div class="bg-amber-50 border border-amber-300 text-amber-800 rounded-lg px-4 py-3 mb-4 flex items-center gap-3">
        <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <div class="text-sm">
            <p class="font-bold">Mode Lihat Saja - Waktu verifikasi tidak aktif!</p>
            <p>Anda hanya dapat melihat data.
            @if($startVerif && $endVerif)
                Waktu verifikasi: <span class="font-semibold">{{ $startVerif->format('d M Y') }} s/d {{ $endVerif->format('d M Y') }}</span>.
            @endif
            </p>
        </div>
    </div>
    @endif

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('verifikasi-menpan.show', [$periode->id, $opd->id]) }}"
               class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    {{ $subKategori->nama }}
                    @if($startVerif && $endVerif)
                        @if(\Carbon\Carbon::now()->lt($startVerif))
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">Belum Dimulai</span>
                        @elseif(\Carbon\Carbon::now()->gt($endVerif))
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">Waktu Habis</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">Aktif</span>
                        @endif
                    @endif
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ $periode->nama_periode }} - {{ $opd->n_opd }}
                    @if($startVerif && $endVerif)
                        - <span class="font-medium text-gray-700">Waktu Verifikasi: {{ $startVerif->format('d M Y') }} s/d {{ $endVerif->format('d M Y') }}</span>
                    @endif
                </p>
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
        <div class="bg-primary px-6 py-5">
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
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-600 mb-1">
                        <span class="font-semibold text-gray-900">Indikator {{ $currentPage }}</span> dari {{ $totalIndikator }}
                    </div>
                    <div class="text-xs text-gray-500">{{ $currentIndikator->kode }}. {{ $currentIndikator->nama }}</div>
                </div>

                {{-- Page Indicator --}}
                <div class="flex items-center gap-1.5 overflow-x-auto">
                    @for($i = 1; $i <= $totalIndikator; $i++)
                    <a href="{{ route('verifikasi-menpan.detail', ['periode' => $periode->id, 'opd' => $opd->id, 'subKategori' => $subKategori->id, 'indikator' => $i]) }}"
                       class="w-7 h-7 flex items-center justify-center rounded-lg text-sm font-medium transition-colors flex-shrink-0 {{ $i === $currentPage ? 'bg-primary text-white shadow-sm' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                        {{ $i }}
                    </a>
                    @endfor
                </div>
            </div>
        </div>

        {{-- Indikator Content --}}
        <form action="{{ route('verifikasi-menpan.store', [$periode->id, $opd->id, $subKategori->id]) }}" method="POST" id="verifikasiMenpanForm">
            @csrf
            <input type="hidden" name="current_page" value="{{ $currentPage }}">

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

                    {{-- Nilai Indikator Summary --}}
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-4 mb-4 border border-blue-100">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="text-center">
                                    <p class="text-xs text-gray-500 mb-1">Status Verifikasi Menpan</p>
                                    <p class="text-lg font-bold text-gray-900">{{ $nilaiIndikator['pertanyaan_terverifikasi'] }}/{{ $nilaiIndikator['total_pertanyaan'] }}</p>
                                </div>
                                <div class="h-10 w-px bg-gray-300"></div>
                                <div class="text-center">
                                    <p class="text-xs text-gray-500 mb-1">Rata-rata Nilai</p>
                                    <p class="text-lg font-bold text-primary">{{ number_format($nilaiIndikator['rata_rata_nilai'], 2) }}</p>
                                </div>
                                <div class="h-10 w-px bg-gray-300"></div>
                                <div class="text-center">
                                    <p class="text-xs text-gray-500 mb-1">Nilai Indikator</p>
                                    <p class="text-lg font-bold text-green-600">{{ number_format($nilaiIndikator['nilai_indikator'], 2) }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500 mb-1">Capaian</p>
                                <div class="flex items-center gap-2">
                                    <div class="w-24 h-2 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full {{ $nilaiIndikator['persen_capaian'] >= 80 ? 'bg-green-500' : ($nilaiIndikator['persen_capaian'] >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                             style="width: {{ min($nilaiIndikator['persen_capaian'], 100) }}%"></div>
                                    </div>
                                    <span class="text-sm font-bold {{ $nilaiIndikator['persen_capaian'] >= 80 ? 'text-green-600' : ($nilaiIndikator['persen_capaian'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ number_format($nilaiIndikator['persen_capaian'], 2) }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Pertanyaan Loop --}}
                    <div class="space-y-4">
                        @foreach($currentIndikator->pertanyaans as $pertanyaanIndex => $pertanyaan)
                        @php
                            $jawabanParent = $jawabanMap[$pertanyaan->id] ?? null;
                            $pertanyaanNilai = $nilaiIndikator['nilai_per_pertanyaan'][$pertanyaan->id] ?? null;
                            $nilaiTampil = $pertanyaanNilai && $pertanyaanNilai['nilai'] !== null ? $pertanyaanNilai['nilai'] : null;
                            $statusVerifikasiMenpan = $jawabanParent ? $jawabanParent->status_verifikasi_menpan : 'belum_diverifikasi';

                            $jawabansForForm = [];
                            $menpanSubValues = [];
                            if ($pertanyaan->has_sub_pertanyaan) {
                                foreach ($pertanyaan->subPertanyaans as $sp) {
                                    $spKey = "{$pertanyaan->id}_{$sp->id}";
                                    if (isset($jawabanMap[$spKey])) {
                                        $jawabansForForm[$pertanyaan->id . '-' . $sp->id] = $jawabanMap[$spKey];
                                        $jawabanSub = $jawabanMap[$spKey];
                                        $menpanSubValues[$sp->id] = $jawabanSub->menpan_jawaban_angka
                                            ?? $jawabanSub->verifikator_jawaban_angka
                                            ?? $jawabanSub->jawaban_angka;
                                    }
                                }
                            }

                            $menpanTextValue = $jawabanParent
                                ? ($jawabanParent->menpan_jawaban_text ?? $jawabanParent->verifikator_jawaban_text ?? $jawabanParent->jawaban_text)
                                : null;
                            $menpanAngkaValue = $jawabanParent
                                ? ($jawabanParent->menpan_jawaban_angka ?? $jawabanParent->verifikator_jawaban_angka ?? $jawabanParent->jawaban_angka)
                                : null;

                            $verifikatorTextValue = $jawabanParent
                                ? ($jawabanParent->verifikator_jawaban_text ?? $jawabanParent->jawaban_text)
                                : null;
                            $verifikatorAngkaValue = $jawabanParent
                                ? ($jawabanParent->verifikator_jawaban_angka ?? $jawabanParent->jawaban_angka)
                                : null;

                            $isMenpanBerbeda = false;
                            if ($statusVerifikasiMenpan !== 'belum_diverifikasi') {
                                if ($pertanyaan->has_sub_pertanyaan) {
                                    foreach ($pertanyaan->subPertanyaans as $sp) {
                                        $spKey = "{$pertanyaan->id}_{$sp->id}";
                                        if (isset($jawabanMap[$spKey])) {
                                            $jawabanSub = $jawabanMap[$spKey];
                                            $menpanVal = $jawabanSub->menpan_jawaban_angka ?? null;
                                            $verifikatorVal = $jawabanSub->verifikator_jawaban_angka ?? $jawabanSub->jawaban_angka ?? null;
                                            if ($menpanVal !== null && $menpanVal != $verifikatorVal) {
                                                $isMenpanBerbeda = true;
                                                break;
                                            }
                                        }
                                    }
                                } else if (in_array($pertanyaan->tipe_jawaban, ['ya_tidak', 'pilihan_ganda'])) {
                                    $menpanVal = $jawabanParent->menpan_jawaban_text ?? null;
                                    $verifikatorVal = $jawabanParent->verifikator_jawaban_text ?? $jawabanParent->jawaban_text ?? null;
                                    if ($menpanVal !== null && $menpanVal !== $verifikatorVal) {
                                        $isMenpanBerbeda = true;
                                    }
                                } else if ($pertanyaan->tipe_jawaban === 'angka') {
                                    $menpanVal = $jawabanParent->menpan_jawaban_angka ?? null;
                                    $verifikatorVal = $jawabanParent->verifikator_jawaban_angka ?? $jawabanParent->jawaban_angka ?? null;
                                    if ($menpanVal !== null && $menpanVal != $verifikatorVal) {
                                        $isMenpanBerbeda = true;
                                    }
                                }
                            }
                        @endphp
                        <div class="bg-white rounded-lg p-4 border border-gray-200">
                            <fieldset @if(!$isCanVerify || $statusVerifikasiMenpan !== 'belum_diverifikasi') disabled @endif>
                            <div class="flex items-start gap-3 mb-3">
                                <span class="inline-flex items-center justify-center min-w-[24px] h-6 bg-gray-100 text-gray-700 rounded text-xs font-semibold px-2">
                                    {{ $pertanyaan->urutan ?? ($pertanyaanIndex + 1) }}
                                </span>
                                <p class="text-sm text-gray-900 flex-1">{{ $pertanyaan->pertanyaan }}</p>
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-bold {{ $nilaiTampil !== null ? ($nilaiTampil >= 0.8 ? 'bg-green-100 text-green-700' : ($nilaiTampil >= 0.5 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700')) : '' }}" style="{{ $nilaiTampil === null ? 'display: none;' : '' }}">
                                    @if($nilaiTampil !== null)
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    @php
                                        $isPercenCapianFormat = isset($pertanyaan) && $pertanyaan->has_sub_pertanyaan;
                                    @endphp
                                    Nilai: {{ $isPercenCapianFormat ? number_format((float)$nilaiTampil * 100, 2) . '%' : number_format((float)$nilaiTampil, 2) }}
                                    @endif
                                </span>
                            </div>

                            {{-- Jawaban Verifikator (readonly) --}}
                            @if($isMenpanBerbeda)
                            <div class="mb-4 bg-red-50 rounded-lg p-4 border border-red-200">
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Jawaban Verifikator</p>
                                @if($pertanyaan->tipe_jawaban === 'ya_tidak')
                                    <p class="text-sm font-semibold text-gray-800">
                                        {{ $verifikatorTextValue ? ($verifikatorTextValue === 'ya' ? 'Ya' : 'Tidak') : '-' }}
                                    </p>
                                @elseif($pertanyaan->tipe_jawaban === 'pilihan_ganda')
                                    @php
                                        $opsiSaatIni = $verifikatorTextValue;
                                        $labelSaatIni = null;
                                        if ($opsiSaatIni && isset($pertanyaan->penjelasan_list)) {
                                            foreach ($pertanyaan->penjelasan_list as $opsi) {
                                                if (($opsi['opsi'] ?? null) === $opsiSaatIni) {
                                                    $labelSaatIni = $opsi['text'] ?? null;
                                                    break;
                                                }
                                            }
                                        }
                                    @endphp
                                    <p class="text-sm font-semibold text-gray-800">
                                        {{ $opsiSaatIni ? ($opsiSaatIni . '. ' . ($labelSaatIni ?? 'Pilihan ' . $opsiSaatIni)) : '-' }}
                                    </p>
                                @elseif($pertanyaan->has_sub_pertanyaan)
                                    <div class="space-y-1">
                                        @foreach($pertanyaan->subPertanyaans as $sp)
                                        @php $spJawaban = $jawabanMap[$pertanyaan->id . '_' . $sp->id] ?? null; @endphp
                                        <div class="flex items-center justify-between text-sm">
                                            <span class="text-gray-600">{{ $sp->label }}</span>
                                            <span class="font-semibold text-gray-800">{{ $spJawaban?->verifikator_jawaban_angka ?? $spJawaban?->jawaban_angka ?? '-' }}</span>
                                        </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm font-semibold text-gray-800">{{ $verifikatorAngkaValue ?? '-' }}</p>
                                @endif
                            </div>
                            @endif

                            {{-- Jawaban Menpan (ubah langsung di pilihan jawaban) --}}
                            <div class="space-y-3">
                                @if($pertanyaan->tipe_jawaban === 'ya_tidak')
                                    @include('page.kuesioner.partials.input-ya-tidak', [
                                        'pertanyaan' => $pertanyaan,
                                        'jawaban' => $jawabanParent,
                                        'periode' => $periode,
                                        'inputName' => 'menpan[' . $pertanyaan->id . '][menpan_jawaban_text][0]',
                                        'selectedValue' => $menpanTextValue,
                                    ])
                                @elseif($pertanyaan->tipe_jawaban === 'pilihan_ganda')
                                    @include('page.kuesioner.partials.input-pilihan-ganda', [
                                        'pertanyaan' => $pertanyaan,
                                        'jawaban' => $jawabanParent,
                                        'periode' => $periode,
                                        'inputName' => 'menpan[' . $pertanyaan->id . '][menpan_jawaban_text][0]',
                                        'selectedValue' => $menpanTextValue,
                                    ])
                                @elseif($pertanyaan->tipe_jawaban === 'angka')
                                    @if($pertanyaan->has_sub_pertanyaan)
                                        @include('page.kuesioner.partials.input-sub-pertanyaan', [
                                            'pertanyaan' => $pertanyaan,
                                            'jawabans' => $jawabansForForm,
                                            'periode' => $periode,
                                            'inputNamePrefix' => 'menpan[' . $pertanyaan->id . '][menpan_jawaban_angka]',
                                            'inputValues' => $menpanSubValues,
                                        ])
                                    @else
                                        @include('page.kuesioner.partials.input-angka', [
                                            'pertanyaan' => $pertanyaan,
                                            'jawaban' => $jawabanParent,
                                            'periode' => $periode,
                                            'inputName' => 'menpan[' . $pertanyaan->id . '][menpan_jawaban_angka][0]',
                                            'inputValue' => $menpanAngkaValue,
                                        ])
                                    @endif
                                @endif
                            </div>

                            {{-- Keterangan (optional) --}}
                            <div class="mt-3 pt-3 ml-9 border-t border-gray-100">
                                <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                    Keterangan (Opsional)
                                </label>
                                <div class="text-[13px] text-gray-800 rounded-lg px-3 py-2">
                                    {{ optional($jawabanParent)->keterangan ?: '-' }}
                                </div>
                            </div>

                            {{-- Upload Dokumen (optional) --}}
                            <div class="mt-3 pt-3 ml-9 border-t border-gray-100">
                                <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                    Dokumen Pendukung
                                </label>
                                <div>
                                    @php
                                        $uploadedFiles = $jawabanParent ? ($jawabanParent->files ?? collect()) : collect();
                                    @endphp
                                    @if($uploadedFiles->count())
                                        <ol class="list-decimal list-inside space-y-1 mt-1 text-[13px] ml-1">
                                            @foreach($uploadedFiles as $file)
                                            <li class="text-gray-600">
                                                <a href="{{ route('kuesioner.file.item.view', $file->id) }}" target="_blank" data-file-url="{{ route('kuesioner.file.item.view', $file->id) }}" class="js-view-file text-blue-600 hover:text-blue-800 hover:underline inline-flex items-center gap-1 align-middle">
                                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                                    </svg>
                                                    {{ $file->original_name ?? basename($file->file_path) }}
                                                </a>
                                                @if($file->size)
                                                <span class="text-xs text-gray-400 ml-0.5">({{ number_format($file->size / 1024, 0) }} KB)</span>
                                                @endif
                                                @if($file->revisi_ke)
                                                <span class="ml-1 text-[11px] font-semibold text-orange-600">Revisi ke-{{ $file->revisi_ke }}</span>
                                                @endif
                                            </li>
                                            @endforeach
                                        </ol>
                                    @elseif($jawabanParent && $jawabanParent->file_path)
                                        <ul class="list-disc list-inside mt-1 text-[13px] ml-1">
                                            <li class="text-gray-600">
                                                <a href="{{ route('kuesioner.file.view', $jawabanParent->id) }}" target="_blank" data-file-url="{{ route('kuesioner.file.view', $jawabanParent->id) }}" class="js-view-file text-blue-600 hover:text-blue-800 hover:underline inline-flex items-center gap-1 align-middle">
                                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                                    </svg>
                                                    {{ basename($jawabanParent->file_path) }}
                                                </a>
                                            </li>
                                        </ul>
                                    @else
                                        <div class="text-[12px] text-gray-500 italic mt-1">
                                            Tidak ada dokumen pendukung.
                                        </div>
                                    @endif
                                </div>
                            </div>

                            </fieldset>

                            {{-- Verifikasi Menpan --}}
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <fieldset @if(!$isCanVerify) disabled @endif>
                                <div class="p-4 rounded-xl border transition-all duration-200 {{ $statusVerifikasiMenpan === 'belum_diverifikasi' ? 'bg-yellow-50/50 border-yellow-200' : 'bg-gray-50 border-gray-200' }}">
                                    {{-- Header Status --}}
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                        <div>
                                            <div class="flex items-center gap-3 mb-1.5">
                                                <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Status Verifikasi Menpan</h4>
                                                @if($statusVerifikasiMenpan === 'belum_diverifikasi')
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-yellow-100 text-yellow-800 border border-yellow-200 shadow-sm animate-pulse">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                        </svg>
                                                        Perlu Diverifikasi
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-green-100 text-green-800 border border-green-200 shadow-sm">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                        Telah Diverifikasi Menpan
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-xs text-gray-500">
                                                @if($statusVerifikasiMenpan === 'belum_diverifikasi')
                                                    Mohon periksa jawaban dan dokumen pendukung sebelum menyetujui.
                                                @else
                                                    Jawaban telah diperiksa dan disetujui Menpan.
                                                @endif
                                            </p>
                                        </div>

                                        <div class="flex items-center gap-3 shrink-0">
                                            <div class="flex items-center bg-white px-4 py-2.5 rounded-lg border border-gray-200 shadow-sm">
                                                <input type="hidden" name="menpan[{{ $pertanyaan->id }}][status_verifikasi_menpan]" value="belum_diverifikasi">
                                                <label class="relative inline-flex items-center cursor-pointer group">
                                                    <input type="checkbox"
                                                           name="menpan[{{ $pertanyaan->id }}][status_verifikasi_menpan]"
                                                           value="disetujui"
                                                           {{ $statusVerifikasiMenpan == 'disetujui' ? 'checked' : '' }}
                                                           class="sr-only peer">
                                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                                    <span class="ml-3 text-sm font-bold text-gray-600 peer-checked:text-primary group-hover:text-gray-900 transition-colors">
                                                        Verifikasi
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                </fieldset>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Submit Button --}}
                @if($isCanVerify)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Simpan Verifikasi Menpan Halaman Ini
                    </button>
                </div>
                @else
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-gray-100 text-gray-400 rounded-lg font-medium border border-gray-200 cursor-not-allowed">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Verifikasi Dikunci - Di Luar Masa Verifikasi
                    </div>
                </div>
                @endif
            </div>
            </fieldset>
        </form>

        {{-- Pagination Navigation --}}
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <div class="flex items-center justify-between">
                {{-- Previous Button --}}
                @if($currentPage > 1)
                <a href="{{ route('verifikasi-menpan.detail', ['periode' => $periode->id, 'opd' => $opd->id, 'subKategori' => $subKategori->id, 'indikator' => $currentPage - 1]) }}"
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
                    <a href="{{ route('verifikasi-menpan.detail', ['periode' => $periode->id, 'opd' => $opd->id, 'subKategori' => $subKategori->id, 'indikator' => $i]) }}"
                       class="w-8 h-8 flex items-center justify-center rounded-lg text-sm font-medium transition-colors {{ $i === $currentPage ? 'bg-primary text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                        {{ $i }}
                    </a>
                    @endfor
                </div>

                {{-- Next Button --}}
                @if($currentPage < $totalIndikator)
                <a href="{{ route('verifikasi-menpan.detail', ['periode' => $periode->id, 'opd' => $opd->id, 'subKategori' => $subKategori->id, 'indikator' => $currentPage + 1]) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors">
                    <span class="text-sm font-medium">Indikator Selanjutnya</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                @else
                <a href="{{ route('verifikasi-menpan.show', [$periode->id, $opd->id]) }}"
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

{{-- Modal Preview File --}}
<div id="filePreviewModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50" data-file-preview-close></div>
    <div class="relative w-full max-w-4xl h-[80vh] bg-white rounded-xl overflow-hidden">
        <div class="flex items-center justify-between px-4 py-3 bg-gray-50">
            <p class="text-sm font-semibold text-gray-800">Preview Dokumen</p>
            <button type="button" id="closeFilePreview" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg" aria-label="Tutup">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <iframe id="filePreviewFrame" class="w-full h-full" src="" title="Preview Dokumen"></iframe>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('verifikasiMenpanForm');

    const filePreviewModal = document.getElementById('filePreviewModal');
    const filePreviewFrame = document.getElementById('filePreviewFrame');
    const closeFilePreview = document.getElementById('closeFilePreview');

    function openFilePreview(url) {
        if (!filePreviewModal || !filePreviewFrame) {
            window.open(url, '_blank');
            return;
        }
        filePreviewFrame.src = url;
        filePreviewModal.classList.remove('hidden');
        filePreviewModal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    function closeFilePreviewModal() {
        if (!filePreviewModal || !filePreviewFrame) return;
        filePreviewFrame.src = '';
        filePreviewModal.classList.add('hidden');
        filePreviewModal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }

    document.querySelectorAll('.js-view-file').forEach(link => {
        link.addEventListener('click', function(e) {
            const url = this.dataset.fileUrl || this.getAttribute('href');
            if (!url) return;
            e.preventDefault();
            openFilePreview(url);
        });
    });

    if (filePreviewModal) {
        filePreviewModal.addEventListener('click', function(e) {
            if (e.target.matches('[data-file-preview-close]')) {
                closeFilePreviewModal();
            }
        });
    }

    if (closeFilePreview) {
        closeFilePreview.addEventListener('click', closeFilePreviewModal);
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && filePreviewModal && !filePreviewModal.classList.contains('hidden')) {
            closeFilePreviewModal();
        }
    });

    if (!form) return;

    // Auto-sum logic
    function runAutoSum(pertanyaanId) {
        const targetInput = form.querySelector('.sum-target-' + pertanyaanId);
        if (!targetInput) return;

        let total = null;
        const parts = form.querySelectorAll('.sum-part-' + pertanyaanId);
        parts.forEach(function(part) {
            if (part.disabled) return;

            if (part.value !== '') {
                const val = parseFloat(part.value);
                if (!isNaN(val)) {
                    if (total === null) total = 0;
                    total += val;
                }
            }
        });

        targetInput.value = total !== null ? total : '';
    }

    form.querySelectorAll('input[class*="sum-part-"]').forEach(function(input) {
        input.addEventListener('input', function() {
            const pertanyaanId = this.dataset.pertanyaanId;
            runAutoSum(pertanyaanId);
        });
    });

    const seenIds = new Set();
    form.querySelectorAll('input[class*="sum-part-"]').forEach(function(input) {
        const id = input.dataset.pertanyaanId;
        if (id && !seenIds.has(id)) {
            seenIds.add(id);
            runAutoSum(id);
        }
    });
});
</script>
@endpush
