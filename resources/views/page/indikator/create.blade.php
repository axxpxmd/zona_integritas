@extends('layouts.app')

@section('title', 'Tambah Indikator')
@section('page-title', 'Tambah Indikator')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('indikator.index') }}"
           class="p-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div class="flex-1">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Tambah Indikator</h2>
                    <p class="text-sm text-gray-600 mt-0.5">Tambah data indikator baru</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Form --}}
    <div class="max-w-3xl mx-auto">
        <form action="{{ route('indikator.store') }}" method="POST" class="bg-white rounded-xl p-6 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Sub Kategori --}}
                <div class="md:col-span-2">
                    <label for="sub_kategori_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Sub Kategori <span class="text-red-500">*</span>
                    </label>
                    <select name="sub_kategori_id" id="sub_kategori_id"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none @error('sub_kategori_id') border-red-500 @enderror"
                            required>
                        <option value="">Pilih Sub Kategori</option>
                        @foreach($subKategoris as $subKat)
                            <option value="{{ $subKat->id }}" {{ old('sub_kategori_id') == $subKat->id ? 'selected' : '' }}>
                                {{ $subKat->kategori->komponen->kode }}.{{ $subKat->kategori->kode }}.{{ $subKat->kode }} - {{ $subKat->nama }} ({{ number_format($subKat->bobot, 2) }}%)
                            </option>
                        @endforeach
                    </select>
                    @error('sub_kategori_id')
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
                           value="{{ old('kode') }}"
                           maxlength="10"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none @error('kode') border-red-500 @enderror"
                           placeholder="Contoh: IND01"
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
                           value="{{ old('urutan', 1) }}"
                           min="1"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none @error('urutan') border-red-500 @enderror"
                           required>
                    @error('urutan')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nama --}}
                <div class="md:col-span-2">
                    <label for="nama" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Indikator <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="nama"
                           id="nama"
                           value="{{ old('nama') }}"
                           maxlength="250"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none @error('nama') border-red-500 @enderror"
                           placeholder="Masukkan nama indikator"
                           required>
                    @error('nama')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Bobot --}}
                <div>
                    <label for="bobot" class="block text-sm font-medium text-gray-700 mb-2">
                        Bobot (%) <span class="text-red-500">*</span>
                    </label>
                    <input type="number"
                           name="bobot"
                           id="bobot"
                           value="{{ old('bobot') }}"
                           min="0"
                           max="100"
                           step="0.01"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none @error('bobot') border-red-500 @enderror"
                           placeholder="0.00"
                           required>
                    @error('bobot')
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
                        <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                    @error('status')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Deskripsi --}}
                <div class="md:col-span-2">
                    <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi
                    </label>
                    <textarea name="deskripsi"
                              id="deskripsi"
                              rows="4"
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none resize-none @error('deskripsi') border-red-500 @enderror"
                              placeholder="Masukkan deskripsi indikator (opsional)">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('indikator.index') }}"
                   class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="px-5 py-2.5 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary-dark transition-colors">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
