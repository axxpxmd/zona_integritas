@extends('layouts.app')

@section('title', 'Tambah Sub Pertanyaan')
@section('page-title', 'Tambah Sub Pertanyaan')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center gap-4 bg-white px-6 py-4 rounded-xl">
        <a href="{{ route('sub-pertanyaan.index') }}"
           class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
           title="Kembali">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div class="flex items-center gap-3 flex-1">
            <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Tambah Sub Pertanyaan</h1>
                <p class="text-sm text-gray-600">Tambah data sub pertanyaan untuk perhitungan detail</p>
            </div>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="max-w-3xl mx-auto">
        <form action="{{ route('sub-pertanyaan.store') }}" method="POST" class="bg-white rounded-xl p-6 space-y-6">
            @csrf

            {{-- Pertanyaan Utama --}}
            <div>
                <label for="pertanyaan_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Pertanyaan Utama <span class="text-red-500">*</span>
                </label>
                <select name="pertanyaan_id" id="pertanyaan_id" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none @error('pertanyaan_id') border-red-300 @enderror">
                    <option value="">Pilih Pertanyaan Utama</option>
                    @foreach($pertanyaans as $pertanyaan)
                        <option value="{{ $pertanyaan->id }}" {{ old('pertanyaan_id') == $pertanyaan->id ? 'selected' : '' }}>
                            {{ $pertanyaan->indikator->subKategori->kategori->komponen->kode }}.{{ $pertanyaan->indikator->subKategori->kategori->kode }}.{{ $pertanyaan->indikator->subKategori->kode }}.{{ $pertanyaan->indikator->kode }}.{{ $pertanyaan->kode }} - {{ $pertanyaan->pertanyaan }}
                        </option>
                    @endforeach
                </select>
                @error('pertanyaan_id')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Kode --}}
                <div>
                    <label for="kode" class="block text-sm font-medium text-gray-700 mb-2">
                        Kode <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="kode" id="kode" required maxlength="20"
                           value="{{ old('kode') }}"
                           placeholder="Contoh: A1"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none @error('kode') border-red-300 @enderror">
                    @error('kode')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Maksimal 20 karakter, unik per pertanyaan</p>
                </div>

                {{-- Tipe Input --}}
                <div>
                    <label for="tipe_input" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipe Input <span class="text-red-500">*</span>
                    </label>
                    <select name="tipe_input" id="tipe_input" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none @error('tipe_input') border-red-300 @enderror">
                        <option value="">Pilih Tipe Input</option>
                        <option value="jumlah" {{ old('tipe_input') == 'jumlah' ? 'selected' : '' }}>Jumlah</option>
                        <option value="persen" {{ old('tipe_input') == 'persen' ? 'selected' : '' }}>Persen (%)</option>
                        <option value="angka" {{ old('tipe_input') == 'angka' ? 'selected' : '' }}>Angka</option>
                        <option value="teks" {{ old('tipe_input') == 'teks' ? 'selected' : '' }}>Teks</option>
                    </select>
                    @error('tipe_input')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Sub Pertanyaan --}}
            <div>
                <label for="pertanyaan" class="block text-sm font-medium text-gray-700 mb-2">
                    Sub Pertanyaan <span class="text-red-500">*</span>
                </label>
                <textarea name="pertanyaan" id="pertanyaan" rows="3" required
                          placeholder="Masukkan teks sub pertanyaan"
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none @error('pertanyaan') border-red-300 @enderror">{{ old('pertanyaan') }}</textarea>
                @error('pertanyaan')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Satuan (conditional) --}}
                <div id="satuan_field" style="display: none;">
                    <label for="satuan" class="block text-sm font-medium text-gray-700 mb-2">
                        Satuan
                    </label>
                    <input type="text" name="satuan" id="satuan" maxlength="50"
                           value="{{ old('satuan') }}"
                           placeholder="Contoh: Orang, Unit, Dokumen"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none @error('satuan') border-red-300 @enderror">
                    @error('satuan')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Opsional, untuk tipe jumlah/persen</p>
                </div>

                {{-- Urutan --}}
                <div>
                    <label for="urutan" class="block text-sm font-medium text-gray-700 mb-2">
                        Urutan
                    </label>
                    <input type="number" name="urutan" id="urutan"
                           value="{{ old('urutan', 1) }}"
                           min="0"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none @error('urutan') border-red-300 @enderror">
                    @error('urutan')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Formula (conditional) --}}
            <div id="formula_field" style="display: none;">
                <label for="formula" class="block text-sm font-medium text-gray-700 mb-2">
                    Formula Perhitungan
                </label>
                <textarea name="formula" id="formula" rows="2"
                          placeholder="Contoh: A1 + A2 atau (A1 / A2) * 100"
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-mono focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none @error('formula') border-red-300 @enderror">{{ old('formula') }}</textarea>
                @error('formula')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-1">Opsional, gunakan kode sub pertanyaan untuk referensi</p>
            </div>

            {{-- Penjelasan --}}
            <div>
                <label for="penjelasan" class="block text-sm font-medium text-gray-700 mb-2">
                    Penjelasan
                </label>
                <textarea name="penjelasan" id="penjelasan" rows="3"
                          placeholder="Penjelasan detail mengenai sub pertanyaan ini (opsional)"
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none @error('penjelasan') border-red-300 @enderror">{{ old('penjelasan') }}</textarea>
                @error('penjelasan')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status --}}
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                    Status <span class="text-red-500">*</span>
                </label>
                <select name="status" id="status" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none @error('status') border-red-300 @enderror">
                    <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
                @error('status')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex gap-3 pt-4 border-t border-gray-200">
                <button type="submit"
                        class="flex-1 sm:flex-none px-6 py-2.5 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary-dark transition-colors">
                    Simpan
                </button>
                <a href="{{ route('sub-pertanyaan.index') }}"
                   class="flex-1 sm:flex-none px-6 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors text-center">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Dynamic field visibility based on tipe_input
const tipeInputSelect = document.getElementById('tipe_input');
const satuanField = document.getElementById('satuan_field');
const formulaField = document.getElementById('formula_field');

function updateFieldVisibility() {
    const tipeInput = tipeInputSelect.value;

    // Satuan: show for jumlah & persen
    if (tipeInput === 'jumlah' || tipeInput === 'persen') {
        satuanField.style.display = 'block';
    } else {
        satuanField.style.display = 'none';
    }

    // Formula: show for angka types (jumlah, persen, angka)
    if (tipeInput === 'jumlah' || tipeInput === 'persen' || tipeInput === 'angka') {
        formulaField.style.display = 'block';
    } else {
        formulaField.style.display = 'none';
    }
}

tipeInputSelect?.addEventListener('change', updateFieldVisibility);

// Initial check on page load
document.addEventListener('DOMContentLoaded', updateFieldVisibility);
</script>
@endpush
@endsection
