@extends('layouts.app')

@section('title', 'Isi Kuesioner - ' . $subKategori->nama)
@section('page-title', 'Isi Kuesioner')

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
            <input type="hidden" name="periode_id" value="{{ $periode->id }}">
            <input type="hidden" name="sub_kategori_id" value="{{ $subKategori->id }}">
            <input type="hidden" name="indikator_id" value="{{ $currentIndikator->id }}">
            <input type="hidden" name="current_page" value="{{ $currentPage }}">
            <input type="hidden" name="total_indikator" value="{{ $totalIndikator }}">
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
                        <input type="hidden" id="indikatorIdInput" value="{{ $currentIndikator->id }}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="text-center">
                                    <p class="text-xs text-gray-500 mb-1">Terjawab</p>
                                    <p id="terjawabCount" class="text-lg font-bold text-gray-900">{{ $nilaiIndikator['pertanyaan_terjawab'] }}/{{ $nilaiIndikator['total_pertanyaan'] }}</p>
                                </div>
                                <div class="h-10 w-px bg-gray-300"></div>
                                <div class="text-center">
                                    <p class="text-xs text-gray-500 mb-1">Rata-rata Nilai</p>
                                    <p id="rataRataNilai" class="text-lg font-bold text-primary">{{ number_format($nilaiIndikator['rata_rata_nilai'], 2) }}</p>
                                </div>
                                <div class="h-10 w-px bg-gray-300"></div>
                                <div class="text-center">
                                    <p class="text-xs text-gray-500 mb-1">Nilai Indikator</p>
                                    <p id="nilaiIndikator" class="text-lg font-bold text-green-600">{{ number_format($nilaiIndikator['nilai_indikator'], 2) }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500 mb-1">Capaian</p>
                                <div class="flex items-center gap-2">
                                    <div class="w-24 h-2 bg-gray-200 rounded-full overflow-hidden">
                                        <div id="progressBar" class="h-full rounded-full {{ $nilaiIndikator['persen_capaian'] >= 80 ? 'bg-green-500' : ($nilaiIndikator['persen_capaian'] >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                             style="width: {{ min($nilaiIndikator['persen_capaian'], 100) }}%"></div>
                                    </div>
                                    <span id="persenCapaian" class="text-sm font-bold {{ $nilaiIndikator['persen_capaian'] >= 80 ? 'text-green-600' : ($nilaiIndikator['persen_capaian'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ number_format($nilaiIndikator['persen_capaian'], 1) }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Pertanyaan Loop --}}
                    <div class="space-y-4">
                        @foreach($currentIndikator->pertanyaans as $pertanyaan)
                        @php
                            $pertanyaanNilai = $nilaiIndikator['nilai_per_pertanyaan'][$pertanyaan->id] ?? null;
                            $nilaiTampil = $pertanyaanNilai && $pertanyaanNilai['nilai'] !== null ? $pertanyaanNilai['nilai'] : null;
                        @endphp
                        <div class="bg-white rounded-lg p-4 border border-gray-200 {{ $nilaiTampil !== null ? 'border-l-4 border-l-green-500' : '' }}">
                            {{-- Pertanyaan --}}
                            <div class="flex items-start gap-3 mb-3">
                                <span class="inline-flex items-center justify-center min-w-[24px] h-6 bg-gray-100 text-gray-700 rounded text-xs font-semibold px-2">
                                    {{ $pertanyaan->urutan }}
                                </span>
                                <p class="text-sm text-gray-900 flex-1">{{ $pertanyaan->pertanyaan }}</p>
                                <span id="nilaiBadge-{{ $pertanyaan->id }}" class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-bold {{ $nilaiTampil !== null ? ($nilaiTampil >= 0.8 ? 'bg-green-100 text-green-700' : ($nilaiTampil >= 0.5 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700')) : '' }}" style="{{ $nilaiTampil === null ? 'display: none;' : '' }}">
                                    @if($nilaiTampil !== null)
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Nilai: {{ number_format($nilaiTampil, 2) }}
                                    @endif
                                </span>
                                <input type="hidden" name="pertanyaan_id[]" value="{{ $pertanyaan->id }}">
                            </div>

                            {{-- Input berdasarkan tipe --}}
                            @if($pertanyaan->tipe_jawaban === 'ya_tidak')
                                @include('page.kuesioner.partials.input-ya-tidak', [
                                    'pertanyaan' => $pertanyaan,
                                    'jawaban' => $jawabans[$pertanyaan->id] ?? null,
                                    'periode' => $periode
                                ])
                            @elseif($pertanyaan->tipe_jawaban === 'pilihan_ganda')
                                @include('page.kuesioner.partials.input-pilihan-ganda', [
                                    'pertanyaan' => $pertanyaan,
                                    'jawaban' => $jawabans[$pertanyaan->id] ?? null,
                                    'periode' => $periode
                                ])
                            @elseif($pertanyaan->tipe_jawaban === 'angka')
                                @if($pertanyaan->has_sub_pertanyaan)
                                    @include('page.kuesioner.partials.input-sub-pertanyaan', [
                                        'pertanyaan' => $pertanyaan,
                                        'jawabans' => $jawabans,
                                        'periode' => $periode
                                    ])
                                @else
                                    @include('page.kuesioner.partials.input-angka', [
                                        'pertanyaan' => $pertanyaan,
                                        'jawaban' => $jawabans[$pertanyaan->id] ?? null,
                                        'periode' => $periode
                                    ])
                                @endif
                            @endif

                            {{-- Keterangan (optional) --}}
                            <div class="mt-3 pt-3 border-t border-gray-100">
                                <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                    Keterangan (Opsional)
                                </label>
                                <textarea name="keterangan[{{ $pertanyaan->id }}]"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none resize-none"
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
                                    <input type="file"
                                            name="file[{{ $pertanyaan->id }}]"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"
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
            </div>
        </form>

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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('kuesionerForm');
    const indikatorId = document.getElementById('indikatorIdInput').value;

    // Function to collect all answers from form
    function collectAnswers() {
        const answers = {};

        // Collect radio button answers (ya/tidak, pilihan ganda)
        form.querySelectorAll('input[type="radio"]:checked').forEach(radio => {
            const match = radio.name.match(/jawaban\[(\d+)\]/);
            if (match) {
                answers[match[1]] = radio.value;
            }
        });

        // Collect number inputs
        form.querySelectorAll('input[type="number"][name^="jawaban"]').forEach(input => {
            const match = input.name.match(/jawaban\[(\d+)\]/);
            if (match && input.value) {
                answers[match[1]] = input.value;
            }
        });

        return answers;
    }

    // Function to update nilai display
    function updateNilaiDisplay(data) {
        // Update summary box
        document.getElementById('terjawabCount').textContent = data.pertanyaan_terjawab + '/' + data.total_pertanyaan;
        document.getElementById('rataRataNilai').textContent = data.rata_rata_nilai.toFixed(2);
        document.getElementById('nilaiIndikator').textContent = data.nilai_indikator.toFixed(2);
        document.getElementById('persenCapaian').textContent = data.persen_capaian.toFixed(1) + '%';

        // Update progress bar
        const progressBar = document.getElementById('progressBar');
        progressBar.style.width = Math.min(data.persen_capaian, 100) + '%';

        // Update progress bar color
        progressBar.className = progressBar.className.replace(/bg-(green|yellow|red)-500/g, '');
        if (data.persen_capaian >= 80) {
            progressBar.classList.add('bg-green-500');
        } else if (data.persen_capaian >= 50) {
            progressBar.classList.add('bg-yellow-500');
        } else {
            progressBar.classList.add('bg-red-500');
        }

        // Update persen text color
        const persenText = document.getElementById('persenCapaian');
        persenText.className = persenText.className.replace(/text-(green|yellow|red)-600/g, '');
        if (data.persen_capaian >= 80) {
            persenText.classList.add('text-green-600');
        } else if (data.persen_capaian >= 50) {
            persenText.classList.add('text-yellow-600');
        } else {
            persenText.classList.add('text-red-600');
        }

        // Update per-question nilai badges
        for (const [pertanyaanId, nilaiData] of Object.entries(data.nilai_per_pertanyaan)) {
            const badge = document.getElementById('nilaiBadge-' + pertanyaanId);
            if (badge) {
                if (nilaiData.nilai !== null) {
                    const nilai = parseFloat(nilaiData.nilai);
                    let badgeClass = 'bg-red-100 text-red-700';
                    if (nilai >= 0.8) {
                        badgeClass = 'bg-green-100 text-green-700';
                    } else if (nilai >= 0.5) {
                        badgeClass = 'bg-yellow-100 text-yellow-700';
                    }
                    badge.className = 'inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-bold ' + badgeClass;
                    badge.innerHTML = `
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Nilai: ${nilai.toFixed(2)}
                    `;
                    badge.style.display = 'inline-flex';
                } else {
                    badge.style.display = 'none';
                }
            }
        }
    }

    // Function to calculate nilai via AJAX
    function calculateNilai() {
        const answers = collectAnswers();

        fetch('{{ route("kuesioner.hitung-nilai") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                indikator_id: indikatorId,
                jawaban: answers
            })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                updateNilaiDisplay(result.data);
            }
        })
        .catch(error => console.error('Error calculating nilai:', error));
    }

    // Listen for changes on all form inputs
    form.querySelectorAll('input[type="radio"], input[type="number"]').forEach(input => {
        input.addEventListener('change', calculateNilai);
    });
});
</script>
@endpush
