@extends('layouts.app')

@section('title', 'Daftar Revisi - ' . $periode->nama_periode)
@section('page-title', 'Revisi Jawaban')

@section('content')
@php
    $now       = \Carbon\Carbon::now()->startOfDay();
    $startRevisi = $periode->tanggal_mulai_revisi ? \Carbon\Carbon::parse($periode->tanggal_mulai_revisi)->startOfDay() : null;
    $endRevisi   = $periode->tanggal_selesai_revisi ? \Carbon\Carbon::parse($periode->tanggal_selesai_revisi)->endOfDay() : null;
    // Cek apakah dalam masa revisi
    $isCanRevisi = true; // Default bisa revisi jika tidak ada rentang waktu revisi
    if ($startRevisi && $endRevisi) {
        $isCanRevisi = $now->between($startRevisi, $endRevisi);
    }
@endphp
<div class="space-y-6">

    {{-- Alert Success --}}
    @if(session('success'))
    <div class="bg-green-100 border border-green-200 text-green-800 rounded-lg px-4 py-3 flex items-center gap-2">
        <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="font-semibold">{{ session('success') }}</span>
    </div>
    @endif

    {{-- Alert Waktu Revisi --}}
    @if($startRevisi && $endRevisi && !$isCanRevisi)
    <div class="bg-amber-50 border border-amber-300 text-amber-800 rounded-lg px-4 py-3 flex items-center gap-3">
        <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <div class="text-sm">
            <p class="font-bold">Mode Lihat Saja — Waktu revisi tidak aktif!</p>
            <p>Waktu revisi: <span class="font-semibold">{{ $startRevisi->format('d M Y') }} s/d {{ $endRevisi->format('d M Y') }}</span>.</p>
        </div>
    </div>
    @endif

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
                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    Daftar Revisi
                    <span class="px-2.5 py-0.5 rounded-full text-sm font-bold bg-orange-100 text-orange-700 border border-orange-200">
                        {{ $totalRevisi }} Pertanyaan
                    </span>
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ $periode->nama_periode }} • {{ $opd->n_opd }}</p>
            </div>
        </div>
    </div>

    {{-- Empty State --}}
    @if($totalRevisi === 0)
    <div class="bg-white rounded-xl p-12 text-center border border-gray-200">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-2">Tidak ada revisi</h3>
        <p class="text-gray-500 text-sm">Semua jawaban Anda sudah disetujui atau belum diverifikasi.</p>
        <a href="{{ route('kuesioner.show', $periode->id) }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors text-sm font-medium">
            Kembali ke Kuesioner
        </a>
    </div>
    @else

    {{-- Instruction Banner --}}
    <div class="bg-orange-50 border border-orange-200 rounded-xl px-5 py-4 flex items-start gap-3">
        <svg class="w-5 h-5 text-orange-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        <div class="text-sm">
            <p class="font-bold text-orange-800 mb-0.5">Perbaiki jawaban sesuai catatan verifikator</p>
            <p class="text-orange-700">Setelah selesai memperbaiki semua pertanyaan, klik <strong>Kirim Revisi</strong> agar verifikator dapat memeriksa kembali.</p>
        </div>
    </div>

    {{-- Form Revisi --}}
    <form action="{{ route('kuesioner.revisi.submit') }}" method="POST" enctype="multipart/form-data" id="revisiForm">
        @csrf
        <input type="hidden" name="periode_id" value="{{ $periode->id }}">

        <div class="space-y-8">
            @foreach($pertanyaanRevisi as $subKategoriId => $pertanyaans)
            @php $subKategori = $subKategoris[$subKategoriId] ?? null; @endphp

            {{-- Sub Kategori Group --}}
            <div class="bg-white rounded-xl overflow-hidden border border-gray-200">
                {{-- Sub Kategori Header --}}
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                            <span class="text-sm font-bold text-white">{{ $subKategori?->kode ?? '?' }}</span>
                        </div>
                        <div>
                            <p class="text-xs text-white/70 font-medium">
                                {{ $subKategori?->kategori?->komponen?->nama ?? '' }} → {{ $subKategori?->kategori?->nama ?? '' }}
                            </p>
                            <h3 class="text-base font-bold text-white">{{ $subKategori?->nama ?? 'Sub Kategori' }}</h3>
                        </div>
                    </div>
                </div>

                {{-- Pertanyaan dikelompokkan per Indikator --}}
                <div class="divide-y divide-gray-200">
                    @foreach($pertanyaans as $indikatorId => $indikatorPertanyaans)
                    @php $indikator = $indikators[$indikatorId] ?? null; @endphp

                    {{-- Indikator Header --}}
                    <div class="bg-orange-50/60 px-6 py-3 border-b border-orange-100 flex items-center gap-3">
                        <div class="w-7 h-7 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="text-[11px] font-bold text-orange-600">{{ $indikator?->kode ?? $indikatorId }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-orange-900 leading-snug">{{ $indikator?->nama ?? 'Indikator' }}</p>
                        </div>
                        @if($indikator?->bobot)
                        <span class="text-xs font-medium text-orange-600 bg-orange-100 px-2 py-0.5 rounded-full flex-shrink-0">Bobot: {{ $indikator->bobot }}</span>
                        @endif
                    </div>

                    {{-- Daftar Pertanyaan dalam Indikator ini --}}
                    <div class="divide-y divide-gray-100">
                    @foreach($indikatorPertanyaans as $pertanyaan)
                    @php
                        $jawaban = $jawabanRevisis[$pertanyaan->id] ?? null;
                    @endphp

                    <div class="p-6">
                        <input type="hidden" name="pertanyaan_id[]" value="{{ $pertanyaan->id }}">

                        {{-- Catatan Revisi Verifikator --}}
                        @if($jawaban?->catatan_revisi)
                        <div class="mb-5 bg-red-50 border border-red-200 rounded-xl px-4 py-3.5 flex items-start gap-3">
                            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                            </svg>
                            <div>
                                <p class="text-xs font-bold text-red-700 uppercase tracking-wide mb-1">Catatan Revisi dari Verifikator</p>
                                <p class="text-sm text-red-900">{{ $jawaban->catatan_revisi }}</p>
                                @if($jawaban->revised_at || $jawaban->revisi_count > 0)
                                <p class="text-xs text-red-600 mt-1.5">
                                    @if($jawaban->revisi_count > 0) Revisi ke-{{ $jawaban->revisi_count + 1 }}. @endif
                                    @if($jawaban->verified_at) Dikirim: {{ \Carbon\Carbon::parse($jawaban->verified_at)->translatedFormat('d M Y H:i') }}. @endif
                                </p>
                                @endif
                            </div>
                        </div>
                        @endif

                        {{-- Nomor & Teks Pertanyaan --}}
                        <div class="flex items-start gap-3 mb-4">
                            <span class="inline-flex items-center justify-center min-w-[28px] h-7 bg-orange-100 text-orange-700 rounded-lg text-xs font-bold px-2 flex-shrink-0">
                                {{ $pertanyaan->urutan }}
                            </span>
                            <p class="text-sm text-gray-900 leading-relaxed flex-1">{{ $pertanyaan->pertanyaan }}</p>
                        </div>

                        {{-- Jawaban Saat Ini (readonly) --}}
                        <div class="mb-4 bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Jawaban Anda Saat Ini</p>
                            @if($pertanyaan->tipe_jawaban === 'ya_tidak')
                                <p class="text-sm font-semibold text-gray-800">
                                    {{ $jawaban?->jawaban_text ? ($jawaban->jawaban_text === 'ya' ? '✓ Ya' : '✗ Tidak') : '-' }}
                                </p>
                            @elseif($pertanyaan->tipe_jawaban === 'pilihan_ganda')
                                @php
                                    $opsiSaatIni = $jawaban?->jawaban_text ?? null;
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
                                    @php $spJawaban = $subJawabansRevisi[$pertanyaan->id . '-' . $sp->id] ?? null; @endphp
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600">{{ $sp->label }}</span>
                                        <span class="font-semibold text-gray-800">{{ $spJawaban?->jawaban_angka ?? '-' }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm font-semibold text-gray-800">{{ $jawaban?->jawaban_angka ?? '-' }}</p>
                            @endif
                        </div>

                        {{-- Input Jawaban Baru --}}
                        <div class="space-y-3">
                            <p class="text-xs font-bold text-gray-700 uppercase tracking-wide">Perbaiki Jawaban</p>

                            @if($pertanyaan->tipe_jawaban === 'ya_tidak')
                                @include('page.kuesioner.partials.input-ya-tidak', [
                                    'pertanyaan' => $pertanyaan,
                                    'jawaban'    => $jawaban,
                                    'periode'    => $periode,
                                    'isReadonly' => !$isCanRevisi,
                                ])
                            @elseif($pertanyaan->tipe_jawaban === 'pilihan_ganda')
                                @include('page.kuesioner.partials.input-pilihan-ganda', [
                                    'pertanyaan' => $pertanyaan,
                                    'jawaban'    => $jawaban,
                                    'periode'    => $periode,
                                    'isReadonly' => !$isCanRevisi,
                                ])
                            @elseif($pertanyaan->tipe_jawaban === 'angka')
                                @if($pertanyaan->has_sub_pertanyaan)
                                    @include('page.kuesioner.partials.input-sub-pertanyaan', [
                                        'pertanyaan' => $pertanyaan,
                                        'jawabans'   => $subJawabansRevisi,
                                        'periode'    => $periode,
                                        'isReadonly' => !$isCanRevisi,
                                    ])
                                @else
                                    @include('page.kuesioner.partials.input-angka', [
                                        'pertanyaan' => $pertanyaan,
                                        'jawaban'    => $jawaban,
                                        'periode'    => $periode,
                                        'isReadonly' => !$isCanRevisi,
                                    ])
                                @endif
                            @endif

                            {{-- Keterangan --}}
                            <div class="mt-3 pt-3 ml-9 border-t border-gray-100">
                                <label class="block text-xs font-medium text-gray-700 mb-1.5">Keterangan (Opsional)</label>
                                <textarea name="keterangan[{{ $pertanyaan->id }}]"
                                          rows="4"
                                          @if(!$isCanRevisi) readonly @endif
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-[14px] focus:ring-2 focus:ring-orange-400/20 focus:border-orange-400 outline-none resize-none {{ !$isCanRevisi ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                                          placeholder="Tambahkan catatan atau keterangan jika diperlukan...">{{ $jawaban?->keterangan ?? '' }}</textarea>
                            </div>

                            {{-- Upload Dokumen --}}
                            <div class="mt-3 pt-3 ml-9 border-t border-gray-100">
                                <label class="block text-xs font-medium text-gray-700 mb-1.5">Dokumen Pendukung</label>
                                @php $uploadedFiles = $jawaban ? ($jawaban->files ?? collect()) : collect(); @endphp
                                <div class="space-y-2">
                                    @if($uploadedFiles->count())
                                    <ol class="list-decimal list-inside space-y-1 text-[13px] ml-1">
                                        @foreach($uploadedFiles as $file)
                                        <li class="text-gray-600 flex items-center justify-between gap-3 p-1.5 hover:bg-gray-50 rounded-lg transition-colors group">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <div class="w-7 h-7 bg-blue-50 text-blue-600 rounded flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                                    </svg>
                                                </div>
                                                <div class="flex flex-col min-w-0">
                                                    <a href="{{ route('kuesioner.file.item.view', $file->id) }}" target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline font-medium truncate">
                                                        {{ $file->original_name ?? basename($file->file_path) }}
                                                    </a>
                                                    @if($file->size)
                                                    <span class="text-[10px] text-gray-400">{{ number_format($file->size / 1024, 0) }} KB</span>
                                                    @endif
                                                </div>
                                            </div>
                                            @if($isCanRevisi)
                                            <button type="button" onclick="deleteFile({{ $file->id }}, this)" class="flex-shrink-0 p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Hapus File">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                            @endif
                                        </li>
                                        @endforeach
                                    </ol>
                                    @endif

                                    @if($isCanRevisi)
                                    <label for="file-{{ $pertanyaan->id }}"
                                           class="flex items-center justify-between gap-4 p-2 border-2 border-dashed border-orange-300 rounded-lg bg-orange-50 hover:bg-white hover:border-orange-400 transition-colors cursor-pointer">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-orange-100 text-orange-500 rounded-lg flex items-center justify-center">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M12 12v7m0 0l-3-3m3 3l3-3M12 5v7"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-800">Upload dokumen baru</p>
                                                <p class="text-xs text-gray-500">PDF, DOC, DOCX, XLS, XLSX, JPG, PNG — Maks 5MB</p>
                                            </div>
                                        </div>
                                        <span class="px-3 py-1.5 text-xs font-medium bg-orange-500 text-white rounded-lg">Pilih File</span>
                                    </label>
                                    <input id="file-{{ $pertanyaan->id }}" type="file"
                                           name="file[{{ $pertanyaan->id }}][]"
                                           class="hidden"
                                           accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                                           multiple>
                                    <div id="selected-files-{{ $pertanyaan->id }}" class="hidden text-xs text-gray-600"></div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    </div>{{-- /div.divide-y pertanyaan --}}
                    @endforeach
                </div>{{-- /div.divide-y indikator --}}
            </div>{{-- /sub-kategori card --}}
            @endforeach
        </div>

        {{-- Submit Button --}}
        @if($isCanRevisi)
        <div class="mt-6 flex items-center justify-end gap-3">
            <a href="{{ route('kuesioner.show', $periode->id) }}"
               class="px-5 py-2.5 text-sm font-medium text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Batal
            </a>
            <button type="submit" id="btnSubmitRevisi"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors font-semibold text-sm">
                <svg id="revisiIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
                <svg id="revisiSpinner" class="hidden w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span id="revisiText">Kirim Revisi ke Verifikator</span>
            </button>
        </div>
        @endif
    </form>
    @endif

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('revisiForm');
    if (!form) return;

    // Show selected file names
    form.querySelectorAll('input[type="file"][id^="file-"]').forEach(input => {
        input.addEventListener('change', function() {
            const id = this.id.replace('file-', '');
            const listEl = document.getElementById('selected-files-' + id);
            if (!listEl) return;
            const files = Array.from(this.files || []);
            if (files.length === 0) {
                listEl.classList.add('hidden');
                listEl.innerHTML = '';
                return;
            }
            const items = files.map((f, i) => `<div class="truncate">${i + 1}. ${f.name}</div>`).join('');
            listEl.innerHTML = `<div class="text-orange-600 ml-4 space-y-0.5">${items}</div>`;
            listEl.classList.remove('hidden');
        });
    });

    // Auto-sum untuk sub pertanyaan
    form.querySelectorAll('input[class*="sum-part-"]').forEach(input => {
        input.addEventListener('input', function() {
            const pid = this.dataset.pertanyaanId;
            const targetInput = form.querySelector('.sum-target-' + pid);
            if (targetInput) {
                let total = null;
                form.querySelectorAll('.sum-part-' + pid).forEach(part => {
                    if (part.value !== '') {
                        const val = parseFloat(part.value);
                        if (!isNaN(val)) { if (total === null) total = 0; total += val; }
                    }
                });
                targetInput.value = total !== null ? total : '';
            }
        });
    });

    const btn = document.getElementById('btnSubmitRevisi');
    const icon = document.getElementById('revisiIcon');
    const spinner = document.getElementById('revisiSpinner');
    const text = document.getElementById('revisiText');

    if (btn) {
        form.addEventListener('submit', function() {
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            if (spinner) spinner.classList.remove('hidden');
            if (icon) icon.classList.add('hidden');
            if (text) text.textContent = 'Mengirim...';
        });
    }

    // Function to delete file via AJAX
    window.deleteFile = function(fileId, element) {
        if (!confirm('Apakah Anda yakin ingin menghapus file ini?')) return;

        const li = element.closest('li');
        const originalContent = li.innerHTML;
        li.innerHTML = '<span class="text-xs text-gray-400">Menghapus...</span>';

        fetch('{{ route("kuesioner.file.item.delete", ":id") }}'.replace(':id', fileId), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                const list = li.parentElement;
                li.remove();
                // Check if list is empty
                if (list && list.children.length === 0) {
                    const container = list.parentElement;
                    list.remove();
                    // Optional: show "Belum ada dokumen" if needed, but in revisi we might just leave it empty
                }
            } else {
                alert(result.message || 'Gagal menghapus file');
                li.innerHTML = originalContent;
            }
        })
        .catch(error => {
            console.error('Error deleting file:', error);
            alert('Terjadi kesalahan saat menghapus file');
            li.innerHTML = originalContent;
        });
    }
});
</script>
@endpush
