@extends('layouts.app')

@section('title', 'Edit Pertanyaan')
@section('page-title', 'Edit Pertanyaan')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('pertanyaan.index') }}"
           class="p-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div class="flex-1">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Edit Pertanyaan</h2>
                    <p class="text-sm text-gray-600 mt-0.5">Ubah data pertanyaan</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Form --}}
    <div class="max-w-4xl mx-auto">
        <form action="{{ route('pertanyaan.update', $pertanyaan) }}" method="POST" class="bg-white rounded-xl p-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Indikator --}}
                <div class="md:col-span-2">
                    <label for="indikator_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Indikator <span class="text-red-500">*</span>
                    </label>
                    <select name="indikator_id" id="indikator_id"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none @error('indikator_id') border-red-500 @enderror"
                            required>
                        <option value="">Pilih Indikator</option>
                        @foreach($indikators as $ind)
                            <option value="{{ $ind->id }}" {{ old('indikator_id', $pertanyaan->indikator_id) == $ind->id ? 'selected' : '' }}>
                                {{ $ind->subKategori->kategori->komponen->kode }}.{{ $ind->subKategori->kategori->kode }}.{{ $ind->subKategori->kode }}.{{ $ind->kode }} - {{ $ind->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('indikator_id')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kode --}}
                <div>
                    <label for="kode" class="block text-sm font-medium text-gray-700 mb-2">
                        Kode <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="kode"
                           id="kode"
                           value="{{ old('kode', $pertanyaan->kode) }}"
                           maxlength="10"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none @error('kode') border-red-500 @enderror"
                           placeholder="Contoh: a, b, c"
                           required>
                    @error('kode')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Urutan --}}
                <div>
                    <label for="urutan" class="block text-sm font-medium text-gray-700 mb-2">
                        Urutan <span class="text-red-500">*</span>
                    </label>
                    <input type="number"
                           name="urutan"
                           id="urutan"
                           value="{{ old('urutan', $pertanyaan->urutan) }}"
                           min="0"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none @error('urutan') border-red-500 @enderror"
                           required>
                    @error('urutan')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Pertanyaan --}}
                <div class="md:col-span-2">
                    <label for="pertanyaan" class="block text-sm font-medium text-gray-700 mb-2">
                        Pertanyaan <span class="text-red-500">*</span>
                    </label>
                    <textarea name="pertanyaan"
                              id="pertanyaan"
                              rows="3"
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none resize-none @error('pertanyaan') border-red-500 @enderror"
                              placeholder="Masukkan teks pertanyaan"
                              required>{{ old('pertanyaan', $pertanyaan->pertanyaan) }}</textarea>
                    @error('pertanyaan')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Penjelasan --}}
                <div class="md:col-span-2">
                    <label for="penjelasan" class="block text-sm font-medium text-gray-700 mb-2">
                        Penjelasan/Petunjuk Jawaban
                    </label>
                    <textarea name="penjelasan"
                              id="penjelasan"
                              rows="3"
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none resize-none @error('penjelasan') border-red-500 @enderror"
                              placeholder="Masukkan penjelasan atau petunjuk jawaban (opsional)">{{ old('penjelasan', $pertanyaan->penjelasan) }}</textarea>
                    @error('penjelasan')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tipe Jawaban --}}
                <div>
                    <label for="tipe_jawaban" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipe Jawaban <span class="text-red-500">*</span>
                    </label>
                    <select name="tipe_jawaban" id="tipe_jawaban"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none @error('tipe_jawaban') border-red-500 @enderror"
                            required>
                        <option value="">Pilih Tipe Jawaban</option>
                        <option value="ya_tidak" {{ old('tipe_jawaban', $pertanyaan->tipe_jawaban) == 'ya_tidak' ? 'selected' : '' }}>Ya/Tidak</option>
                        <option value="pilihan_ganda" {{ old('tipe_jawaban', $pertanyaan->tipe_jawaban) == 'pilihan_ganda' ? 'selected' : '' }}>Pilihan Ganda</option>
                        <option value="angka" {{ old('tipe_jawaban', $pertanyaan->tipe_jawaban) == 'angka' ? 'selected' : '' }}>Angka</option>
                        <option value="teks" {{ old('tipe_jawaban', $pertanyaan->tipe_jawaban) == 'teks' ? 'selected' : '' }}>Teks</option>
                    </select>
                    @error('tipe_jawaban')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" id="status"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none @error('status') border-red-500 @enderror"
                            required>
                        <option value="1" {{ old('status', $pertanyaan->status) == 1 ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('status', $pertanyaan->status) == 0 ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                    @error('status')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Pilihan Jawaban (conditional) --}}
                <div id="pilihan_jawaban_wrapper" class="md:col-span-2" style="display: none;">
                    <label for="pilihan_jawaban" class="block text-sm font-medium text-gray-700 mb-2">
                        Pilihan Jawaban <span class="text-red-500">*</span>
                    </label>
                    <div id="ya_tidak_info" class="hidden p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
                        Pilihan jawaban otomatis: <strong>Ya, Tidak</strong>
                    </div>
                    <div id="pilihan_ganda_input" class="hidden">
                        <input type="text"
                               name="pilihan_jawaban"
                               id="pilihan_jawaban"
                               value="{{ old('pilihan_jawaban', is_array($pertanyaan->pilihan_jawaban) ? implode(', ', $pertanyaan->pilihan_jawaban) : '') }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none @error('pilihan_jawaban') border-red-500 @enderror"
                               placeholder="Pisahkan dengan koma. Contoh: A, B, C atau A, B, C, D, E">
                        <p class="mt-1.5 text-xs text-gray-500">Masukkan pilihan jawaban dipisahkan dengan koma</p>
                    </div>
                    <div id="angka_info" class="hidden p-3 bg-orange-50 border border-orange-200 rounded-lg text-sm text-orange-700">
                        Responden akan mengisi dengan <strong>angka/numerik</strong>
                    </div>
                    <div id="teks_info" class="hidden p-3 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700">
                        Responden akan mengisi dengan <strong>teks bebas</strong>
                    </div>
                    @error('pilihan_jawaban')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('pertanyaan.index') }}"
                   class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="px-5 py-2.5 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary-dark transition-colors">
                    Perbarui
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Dynamic pilihan jawaban based on tipe_jawaban
const tipeJawabanSelect = document.getElementById('tipe_jawaban');
const pilihanWrapper = document.getElementById('pilihan_jawaban_wrapper');
const yaTidakInfo = document.getElementById('ya_tidak_info');
const pilihanGandaInput = document.getElementById('pilihan_ganda_input');
const angkaInfo = document.getElementById('angka_info');
const teksInfo = document.getElementById('teks_info');
const pilihanInput = document.getElementById('pilihan_jawaban');

tipeJawabanSelect?.addEventListener('change', function() {
    const tipe = this.value;

    // Hide all
    pilihanWrapper.style.display = 'none';
    yaTidakInfo.classList.add('hidden');
    pilihanGandaInput.classList.add('hidden');
    angkaInfo.classList.add('hidden');
    teksInfo.classList.add('hidden');
    pilihanInput.required = false;

    if (tipe === 'ya_tidak') {
        pilihanWrapper.style.display = 'block';
        yaTidakInfo.classList.remove('hidden');
        pilihanInput.value = '';
    } else if (tipe === 'pilihan_ganda') {
        pilihanWrapper.style.display = 'block';
        pilihanGandaInput.classList.remove('hidden');
        pilihanInput.required = true;
    } else if (tipe === 'angka') {
        pilihanWrapper.style.display = 'block';
        angkaInfo.classList.remove('hidden');
        pilihanInput.value = '';
    } else if (tipe === 'teks') {
        pilihanWrapper.style.display = 'block';
        teksInfo.classList.remove('hidden');
        pilihanInput.value = '';
    }
});

// Trigger on load
if (tipeJawabanSelect?.value) {
    tipeJawabanSelect.dispatchEvent(new Event('change'));
}
</script>
@endpush
@endsection
