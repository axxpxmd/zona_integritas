@extends('layouts.app')

@section('title', 'Isi Kuesioner - ' . $periode->nama_periode)
@section('page-title', 'Isi Kuesioner')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('kuesioner.index') }}"
               class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $periode->nama_periode }}</h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ $opd->n_opd }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <div id="autoSaveIndicator" class="hidden items-center gap-2 text-sm text-gray-500">
                <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span>Menyimpan...</span>
            </div>
            <div id="savedIndicator" class="hidden items-center gap-2 text-sm text-green-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Tersimpan</span>
            </div>
        </div>
    </div>

    {{-- Info Bar --}}
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex gap-3">
            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="text-sm text-blue-800">
                <p class="font-medium">Jawaban Anda akan tersimpan otomatis setiap kali Anda mengisi.</p>
                <p class="mt-1">Anda dapat meninggalkan halaman ini dan melanjutkan nanti tanpa kehilangan data.</p>
            </div>
        </div>
    </div>

    {{-- Komponen Loop --}}
    @foreach($komponens as $komponen)
    <div class="bg-white rounded-xl overflow-hidden">
        {{-- Komponen Header --}}
        <div class="bg-primary px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-white">{{ $komponen->kode }}. {{ $komponen->nama }}</h3>
                    @if($komponen->deskripsi)
                    <p class="text-sm text-white/80 mt-1">{{ $komponen->deskripsi }}</p>
                    @endif
                </div>
                <div class="text-white/90 text-sm font-medium">
                    Bobot: {{ $komponen->bobot }}%
                </div>
            </div>
        </div>

        {{-- Kategori Accordion --}}
        <div class="divide-y divide-gray-200">
            @foreach($komponen->kategoris as $kategori)
            <div class="kategori-section">
                {{-- Kategori Header (Clickable) --}}
                <button type="button"
                        class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors kategori-toggle"
                        data-target="kategori-{{ $kategori->id }}">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                            <span class="text-sm font-bold text-gray-700">{{ $kategori->kode }}</span>
                        </div>
                        <div class="text-left">
                            <h4 class="text-base font-semibold text-gray-900">{{ $kategori->nama }}</h4>
                            @if($kategori->deskripsi)
                            <p class="text-xs text-gray-500 mt-0.5">{{ $kategori->deskripsi }}</p>
                            @endif
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 transition-transform kategori-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                {{-- Kategori Content (Collapsible) --}}
                <div id="kategori-{{ $kategori->id }}" class="kategori-content hidden">
                    @foreach($kategori->subKategoris as $subKategori)
                    <div class="px-6 py-4 bg-gray-50">
                        {{-- Sub Kategori Header --}}
                        <div class="flex items-start gap-3 mb-4">
                            <div class="w-6 h-6 bg-white rounded flex items-center justify-center flex-shrink-0 mt-0.5">
                                <span class="text-xs font-bold text-primary">{{ $subKategori->kode }}</span>
                            </div>
                            <div>
                                <h5 class="text-sm font-semibold text-gray-900">{{ $subKategori->nama }}</h5>
                                @if($subKategori->deskripsi)
                                <p class="text-xs text-gray-600 mt-1">{{ $subKategori->deskripsi }}</p>
                                @endif
                            </div>
                        </div>

                        {{-- Indikator Loop --}}
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
                                        <textarea class="keterangan-input w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none resize-none"
                                                  rows="2"
                                                  placeholder="Tambahkan catatan atau keterangan jika diperlukan..."
                                                  data-periode-id="{{ $periode->id }}"
                                                  data-pertanyaan-id="{{ $pertanyaan->id }}"
                                                  data-sub-pertanyaan-id="">{{ $jawabans[$pertanyaan->id]->keterangan ?? '' }}</textarea>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>

@push('scripts')
<script>
// Accordion functionality
document.querySelectorAll('.kategori-toggle').forEach(button => {
    button.addEventListener('click', function() {
        const target = this.getAttribute('data-target');
        const content = document.getElementById(target);
        const icon = this.querySelector('.kategori-icon');

        // Toggle content
        content.classList.toggle('hidden');

        // Rotate icon
        if (content.classList.contains('hidden')) {
            icon.style.transform = 'rotate(0deg)';
        } else {
            icon.style.transform = 'rotate(180deg)';
        }
    });
});

// Auto-save functionality
let saveTimeout;
const autoSaveIndicator = document.getElementById('autoSaveIndicator');
const savedIndicator = document.getElementById('savedIndicator');

function showSaving() {
    autoSaveIndicator.classList.remove('hidden');
    autoSaveIndicator.classList.add('flex');
    savedIndicator.classList.remove('flex');
    savedIndicator.classList.add('hidden');
}

function showSaved() {
    autoSaveIndicator.classList.remove('flex');
    autoSaveIndicator.classList.add('hidden');
    savedIndicator.classList.remove('hidden');
    savedIndicator.classList.add('flex');

    setTimeout(() => {
        savedIndicator.classList.remove('flex');
        savedIndicator.classList.add('hidden');
    }, 2000);
}

function autoSave(periodeId, pertanyaanId, subPertanyaanId, jawabanText, jawabanAngka, keterangan) {
    clearTimeout(saveTimeout);

    saveTimeout = setTimeout(() => {
        showSaving();

        fetch('{{ route("kuesioner.auto-save") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                periode_id: periodeId,
                pertanyaan_id: pertanyaanId,
                sub_pertanyaan_id: subPertanyaanId || null,
                jawaban_text: jawabanText || null,
                jawaban_angka: jawabanAngka || null,
                keterangan: keterangan || null
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSaved();
            }
        })
        .catch(error => {
            console.error('Auto-save error:', error);
            autoSaveIndicator.classList.add('hidden');
        });
    }, 1000); // Delay 1 detik setelah user berhenti mengetik
}

// Attach auto-save to all inputs
document.addEventListener('DOMContentLoaded', function() {
    // Radio buttons (ya_tidak, pilihan_ganda)
    document.querySelectorAll('.jawaban-radio').forEach(radio => {
        radio.addEventListener('change', function() {
            const periodeId = this.getAttribute('data-periode-id');
            const pertanyaanId = this.getAttribute('data-pertanyaan-id');
            const value = this.value;

            autoSave(periodeId, pertanyaanId, null, value, null, null);
        });
    });

    // Number inputs (angka)
    document.querySelectorAll('.jawaban-angka').forEach(input => {
        input.addEventListener('input', function() {
            const periodeId = this.getAttribute('data-periode-id');
            const pertanyaanId = this.getAttribute('data-pertanyaan-id');
            const subPertanyaanId = this.getAttribute('data-sub-pertanyaan-id');
            const value = this.value;

            autoSave(periodeId, pertanyaanId, subPertanyaanId, null, value, null);
        });
    });

    // Keterangan textareas
    document.querySelectorAll('.keterangan-input').forEach(textarea => {
        textarea.addEventListener('input', function() {
            const periodeId = this.getAttribute('data-periode-id');
            const pertanyaanId = this.getAttribute('data-pertanyaan-id');
            const subPertanyaanId = this.getAttribute('data-sub-pertanyaan-id');
            const value = this.value;

            autoSave(periodeId, pertanyaanId, subPertanyaanId, null, null, value);
        });
    });
});
</script>
@endpush
@endsection
