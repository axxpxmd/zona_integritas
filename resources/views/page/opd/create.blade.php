@extends('layouts.app')

@section('title', 'Tambah OPD')
@section('page-title', 'Tambah OPD')

@section('content')
<div class="max-w-3xl mx-auto">
    {{-- Header Section --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-primary rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Tambah OPD</h2>
                <p class="text-sm text-gray-500">Tambah data Organisasi Perangkat Daerah baru</p>
            </div>
        </div>
        <a href="{{ route('opd.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded-xl">
        <form action="{{ route('opd.store') }}" method="POST" class="p-4 sm:p-6 space-y-5">
            @csrf

            {{-- Nama OPD --}}
            <div>
                <label for="n_opd" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Nama OPD <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       name="n_opd"
                       id="n_opd"
                       value="{{ old('n_opd') }}"
                       placeholder="Masukkan nama OPD"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors @error('n_opd') border-red-500 @enderror">
                @error('n_opd')
                <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Alamat --}}
            <div>
                <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Alamat
                </label>
                <textarea name="alamat"
                          id="alamat"
                          rows="3"
                          placeholder="Masukkan alamat lengkap OPD"
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors resize-none @error('alamat') border-red-500 @enderror">{{ old('alamat') }}</textarea>
                @error('alamat')
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
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors bg-white @error('status') border-red-500 @enderror">
                    <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
                @error('status')
                <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Form Actions --}}
            <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('opd.index') }}"
                   class="w-full sm:w-auto px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors text-center">
                    Batal
                </a>
                <button type="submit"
                        class="w-full sm:w-auto px-6 py-2.5 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary-dark transition-colors">
                    Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
