@extends('layouts.app')

@section('title', 'Tambah Pengguna')
@section('page-title', 'Tambah Pengguna')

@section('content')
<form action="{{ route('cms.user.store') }}" method="POST">
    @csrf

    {{-- Header Section --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-primary rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Tambah User Baru</h2>
                <p class="text-sm text-gray-500">Buat akun pengguna baru untuk sistem</p>
            </div>
        </div>
        <a href="{{ route('cms.user.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    {{-- Two Column Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column (2/3) --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Informasi Dasar Card --}}
            <div class="bg-white rounded-xl p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900">Informasi Dasar</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Username --}}
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Username <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="username"
                               id="username"
                               value="{{ old('username') }}"
                               placeholder="Masukkan username..."
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors @error('username') border-red-500 @enderror">
                        @error('username')
                        <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email"
                               name="email"
                               id="email"
                               value="{{ old('email') }}"
                               placeholder="Masukkan email..."
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors @error('email') border-red-500 @enderror">
                        @error('email')
                        <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nama Instansi --}}
                    <div>
                        <label for="nama_instansi" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Nama Instansi <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="nama_instansi"
                               id="nama_instansi"
                               value="{{ old('nama_instansi') }}"
                               placeholder="Masukkan nama instansi..."
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors @error('nama_instansi') border-red-500 @enderror">
                        @error('nama_instansi')
                        <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- OPD --}}
                    <div>
                        <label for="opd_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                            OPD
                        </label>
                        <select name="opd_id"
                                id="opd_id"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors bg-white @error('opd_id') border-red-500 @enderror">
                            <option value="">Pilih OPD...</option>
                            @foreach($opds as $opd)
                            <option value="{{ $opd->id }}" {{ old('opd_id') == $opd->id ? 'selected' : '' }}>{{ $opd->n_opd }}</option>
                            @endforeach
                        </select>
                        @error('opd_id')
                        <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nama Kepala --}}
                    <div>
                        <label for="nama_kepala" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Nama Kepala
                        </label>
                        <input type="text"
                               name="nama_kepala"
                               id="nama_kepala"
                               value="{{ old('nama_kepala') }}"
                               placeholder="Masukkan nama kepala..."
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors @error('nama_kepala') border-red-500 @enderror">
                        @error('nama_kepala')
                        <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Jabatan Kepala --}}
                    <div>
                        <label for="jabatan_kepala" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Jabatan Kepala
                        </label>
                        <input type="text"
                               name="jabatan_kepala"
                               id="jabatan_kepala"
                               value="{{ old('jabatan_kepala') }}"
                               placeholder="Masukkan jabatan kepala..."
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors @error('jabatan_kepala') border-red-500 @enderror">
                        @error('jabatan_kepala')
                        <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nama Operator --}}
                    <div>
                        <label for="nama_operator" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Nama Operator
                        </label>
                        <input type="text"
                               name="nama_operator"
                               id="nama_operator"
                               value="{{ old('nama_operator') }}"
                               placeholder="Masukkan nama operator..."
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors @error('nama_operator') border-red-500 @enderror">
                        @error('nama_operator')
                        <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Jabatan Operator --}}
                    <div>
                        <label for="jabatan_operator" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Jabatan Operator
                        </label>
                        <input type="text"
                               name="jabatan_operator"
                               id="jabatan_operator"
                               value="{{ old('jabatan_operator') }}"
                               placeholder="Masukkan jabatan operator..."
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors @error('jabatan_operator') border-red-500 @enderror">
                        @error('jabatan_operator')
                        <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- No. Telepon --}}
                    <div>
                        <label for="telp" class="block text-sm font-medium text-gray-700 mb-1.5">
                            No. Telepon
                        </label>
                        <input type="text"
                               name="telp"
                               id="telp"
                               value="{{ old('telp') }}"
                               placeholder="Masukkan nomor telepon..."
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors @error('telp') border-red-500 @enderror">
                        @error('telp')
                        <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Alamat --}}
                    <div class="md:col-span-2">
                        <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Alamat <span class="text-red-500">*</span>
                        </label>
                        <textarea name="alamat"
                                  id="alamat"
                                  rows="3"
                                  placeholder="Masukkan alamat lengkap..."
                                  class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors resize-none @error('alamat') border-red-500 @enderror">{{ old('alamat') }}</textarea>
                        @error('alamat')
                        <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column (1/3) --}}
        <div class="space-y-6">
            {{-- Role & Akses Card --}}
            <div class="bg-white rounded-xl p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900">Role & Akses</h3>
                </div>

                <div class="space-y-4">
                    {{-- Role --}}
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Role User <span class="text-red-500">*</span>
                        </label>
                        <select name="role"
                                id="role"
                                onchange="updateRoleDescription()"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors bg-white @error('role') border-red-500 @enderror">
                            <option value="">Pilih Role</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="operator" {{ old('role') == 'operator' ? 'selected' : '' }}>Operator</option>
                            <option value="verifikator" {{ old('role') == 'verifikator' ? 'selected' : '' }}>Verifikator</option>
                        </select>
                        @error('role')
                        <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Role Description --}}
                    <div id="roleDescription" class="p-3 bg-blue-50 rounded-lg">
                        <p class="text-xs text-primary flex items-center gap-2">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span id="roleText">Pilih role untuk melihat deskripsi</span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- Password Card --}}
            <div class="bg-white rounded-xl p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900">Password</h3>
                </div>

                <div class="space-y-4">
                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password"
                                   name="password"
                                   id="password"
                                   placeholder="••••••••"
                                   oninput="checkPasswordStrength()"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors pr-10 @error('password') border-red-500 @enderror">
                            <button type="button" onclick="togglePassword('password')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Minimal 8 karakter</p>
                        @error('password')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password Confirmation --}}
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Konfirmasi Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password"
                                   name="password_confirmation"
                                   id="password_confirmation"
                                   placeholder="••••••••"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-colors pr-10">
                            <button type="button" onclick="togglePassword('password_confirmation')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Password Strength --}}
                    <div>
                        <div class="h-1 bg-gray-200 rounded-full overflow-hidden">
                            <div id="passwordStrengthBar" class="h-full w-0 transition-all duration-300"></div>
                        </div>
                        <p id="passwordStrengthText" class="mt-1 text-xs text-gray-500">Kekuatan password</p>
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="space-y-3">
                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary-dark transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    Buat User
                </button>
                <a href="{{ route('cms.user.index') }}"
                   class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Batal
                </a>
            </div>
        </div>
    </div>
</form>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    field.type = field.type === 'password' ? 'text' : 'password';
}

function updateRoleDescription() {
    const role = document.getElementById('role').value;
    const roleText = document.getElementById('roleText');
    const descriptions = {
        'admin': 'Admin memiliki akses penuh ke seluruh fitur sistem',
        'operator': 'Operator dapat mengelola data dan mengisi kuesioner',
        'verifikator': 'Verifikator dapat memverifikasi dan menyetujui data'
    };
    roleText.textContent = descriptions[role] || 'Pilih role untuk melihat deskripsi';
}

function checkPasswordStrength() {
    const password = document.getElementById('password').value;
    const strengthBar = document.getElementById('passwordStrengthBar');
    const strengthText = document.getElementById('passwordStrengthText');

    let strength = 0;
    if (password.length >= 8) strength++;
    if (password.match(/[a-z]+/)) strength++;
    if (password.match(/[A-Z]+/)) strength++;
    if (password.match(/[0-9]+/)) strength++;
    if (password.match(/[^a-zA-Z0-9]+/)) strength++;

    const widths = ['0%', '20%', '40%', '60%', '80%', '100%'];
    const colors = ['bg-gray-300', 'bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-lime-500', 'bg-green-500'];
    const texts = ['Kekuatan password', 'Sangat Lemah', 'Lemah', 'Cukup', 'Kuat', 'Sangat Kuat'];

    strengthBar.className = `h-full transition-all duration-300 ${colors[strength]}`;
    strengthBar.style.width = widths[strength];
    strengthText.textContent = texts[strength];
}

// Initialize role description on page load
document.addEventListener('DOMContentLoaded', updateRoleDescription);
</script>
@endsection
