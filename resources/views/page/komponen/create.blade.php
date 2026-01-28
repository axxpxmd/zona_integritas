@extends('layouts.app')

@section('title', 'Tambah Komponen')
@section('page-title', 'Tambah Komponen')

@section('content')
<div class="max-w-3xl mx-auto">
    {{-- Header Section --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-primary rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Tambah Komponen</h2>
                <p class="text-sm text-gray-500">Tambah data komponen Zona Integritas baru</p>
            </div>
        </div>
        <a href="{{ route('komponen.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded-xl">
        <form action="{{ route('komponen.store') }}" method="POST" class="p-4 sm:p-6 space-y-5">
            @csrf

            {{-- Kode --}}
            <div>
                <label for="kode" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Kode <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       name="kode"
                       id="kode"
                       value="{{ old('kode') }}"
                       placeholder="Contoh: A, B, C"
                       maxlength="10"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors @error('kode') border-red-500 @enderror">
                @error('kode')
                <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Nama --}}
            <div>
                <label for="nama" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Nama Komponen <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       name="nama"
                       id="nama"
                       value="{{ old('nama') }}"
                       placeholder="Masukkan nama komponen"
                       maxlength="100"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors @error('nama') border-red-500 @enderror">
                @error('nama')
                <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Bobot & Urutan --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="bobot" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Bobot (%) <span class="text-red-500">*</span>
                    </label>
                    <input type="number"
                           name="bobot"
                           id="bobot"
                           value="{{ old('bobot') }}"
                           placeholder="0-100"
                           step="0.01"
                           min="0"
                           max="100"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors @error('bobot') border-red-500 @enderror">
                    @error('bobot')
                    <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="urutan" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Urutan <span class="text-red-500">*</span>
                    </label>
                    <input type="number"
                           name="urutan"
                           id="urutan"
                           value="{{ old('urutan', 1) }}"
                           placeholder="1, 2, 3, ..."
                           min="0"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors @error('urutan') border-red-500 @enderror">
                    @error('urutan')
                    <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Deskripsi --}}
            <div>
                <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Deskripsi
                </label>
                <textarea name="deskripsi"
                          id="deskripsi"
                          rows="4"
                          placeholder="Deskripsi komponen (opsional)"
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors resize-none @error('deskripsi') border-red-500 @enderror">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status --}}
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Status <span class="text-red-500">*</span>
                </label>
                <select name="status"
                        id="status"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors @error('status') border-red-500 @enderror">
                    <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
                @error('status')
                <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-100">
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary-dark transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan
                </button>
                <a href="{{ route('komponen.index') }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
