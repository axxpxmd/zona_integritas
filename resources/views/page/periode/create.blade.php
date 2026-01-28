@extends('layouts.app')

@section('title', 'Tambah Periode')
@section('page-title', 'Tambah Periode')

@section('content')
<div class="max-w-3xl mx-auto">
    {{-- Header Section --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-primary rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Tambah Periode Baru</h2>
                <p class="text-sm text-gray-500">Buat periode pengisian kuesioner baru</p>
            </div>
        </div>
        <a href="{{ route('periode.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded-xl">
        <form action="{{ route('periode.store') }}" method="POST" class="p-4 sm:p-6 space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Tahun --}}
                <div>
                    <label for="tahun" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Tahun <span class="text-red-500">*</span>
                    </label>
                    <input type="number"
                           name="tahun"
                           id="tahun"
                           value="{{ old('tahun', date('Y')) }}"
                           min="2024"
                           max="2100"
                           required
                           class="w-full px-4 py-2.5 bg-white border @error('tahun') border-red-300 @else border-gray-300 @enderror rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                    @error('tahun')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nama Periode --}}
                <div>
                    <label for="nama_periode" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Nama Periode <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="nama_periode"
                           id="nama_periode"
                           value="{{ old('nama_periode') }}"
                           maxlength="100"
                           required
                           placeholder="Contoh: Zona Integritas 2024"
                           class="w-full px-4 py-2.5 bg-white border @error('nama_periode') border-red-300 @else border-gray-300 @enderror rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                    @error('nama_periode')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tanggal Mulai --}}
                <div>
                    <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Tanggal Mulai <span class="text-red-500">*</span>
                    </label>
                    <input type="date"
                           name="tanggal_mulai"
                           id="tanggal_mulai"
                           value="{{ old('tanggal_mulai') }}"
                           required
                           class="w-full px-4 py-2.5 bg-white border @error('tanggal_mulai') border-red-300 @else border-gray-300 @enderror rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                    @error('tanggal_mulai')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tanggal Selesai --}}
                <div>
                    <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Tanggal Selesai <span class="text-red-500">*</span>
                    </label>
                    <input type="date"
                           name="tanggal_selesai"
                           id="tanggal_selesai"
                           value="{{ old('tanggal_selesai') }}"
                           required
                           class="w-full px-4 py-2.5 bg-white border @error('tanggal_selesai') border-red-300 @else border-gray-300 @enderror rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                    @error('tanggal_selesai')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status"
                            id="status"
                            required
                            class="w-full px-4 py-2.5 bg-white border @error('status') border-red-300 @else border-gray-300 @enderror rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all appearance-none cursor-pointer">
                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="selesai" {{ old('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="ditutup" {{ old('status') == 'ditutup' ? 'selected' : '' }}>Ditutup</option>
                    </select>
                    @error('status')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Copy dari Periode --}}
                <div>
                    <label for="copied_from_periode_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Copy dari Periode (Opsional)
                    </label>
                    <select name="copied_from_periode_id"
                            id="copied_from_periode_id"
                            class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all appearance-none cursor-pointer">
                        <option value="">-- Tidak Di-copy --</option>
                        <optgroup label="Template">
                            @foreach($templates as $template)
                            <option value="{{ $template->id }}" {{ old('copied_from_periode_id') == $template->id ? 'selected' : '' }}>
                                {{ $template->nama_periode }} ({{ $template->tahun }})
                            </option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Periode Normal">
                            @foreach($periodes as $periode)
                            <option value="{{ $periode->id }}" {{ old('copied_from_periode_id') == $periode->id ? 'selected' : '' }}>
                                {{ $periode->nama_periode }} ({{ $periode->tahun }})
                            </option>
                            @endforeach
                        </optgroup>
                    </select>
                    <p class="mt-1.5 text-xs text-gray-500">
                        <svg class="w-3.5 h-3.5 inline mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Semua data master akan di-copy dari periode yang dipilih
                    </p>
                </div>
            </div>

            {{-- Deskripsi --}}
            <div>
                <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Deskripsi
                </label>
                <textarea name="deskripsi"
                          id="deskripsi"
                          rows="3"
                          placeholder="Deskripsi periode..."
                          class="w-full px-4 py-2.5 bg-white border @error('deskripsi') border-red-300 @else border-gray-300 @enderror rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all resize-none">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Is Template --}}
            <div class="bg-gray-50 rounded-lg p-4">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox"
                           name="is_template"
                           id="is_template"
                           value="1"
                           {{ old('is_template') ? 'checked' : '' }}
                           class="mt-1 w-4 h-4 text-primary border-gray-300 rounded focus:ring-2 focus:ring-primary/20">
                    <div class="flex-1">
                        <span class="text-sm font-medium text-gray-900">Jadikan Template</span>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Centang jika periode ini akan dijadikan master template untuk di-copy ke periode baru. Template tidak digunakan untuk pengisian real oleh OPD.
                        </p>
                    </div>
                </label>
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
                <a href="{{ route('periode.index') }}"
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
