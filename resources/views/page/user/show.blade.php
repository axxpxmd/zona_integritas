@extends('layouts.app')

@section('title', 'Detail Pengguna')
@section('page-title', 'Detail Pengguna')

@section('content')
{{-- Header Section --}}
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-primary rounded-xl flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
        </div>
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Detail Pengguna</h2>
            <p class="text-sm text-gray-500">Informasi lengkap data pengguna</p>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('cms.user.edit', $user) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary-dark transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit
        </a>
        <a href="{{ route('cms.user.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>
</div>

{{-- Two Column Layout --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Left Column (2/3) --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- Informasi Akun Card --}}
        <div class="bg-white rounded-xl p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-gray-900">Informasi Akun</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <p class="text-xs text-gray-500">Username</p>
                    <p class="text-sm font-medium text-gray-900">{{ $user->username }}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-xs text-gray-500">Email</p>
                    <p class="text-sm font-medium text-gray-900">{{ $user->email }}</p>
                </div>
            </div>
        </div>

        {{-- Informasi Instansi Card --}}
        <div class="bg-white rounded-xl p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-gray-900">Informasi Instansi</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <p class="text-xs text-gray-500">Nama Instansi</p>
                    <p class="text-sm font-medium text-gray-900">{{ $user->nama_instansi }}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-xs text-gray-500">OPD</p>
                    <p class="text-sm font-medium text-gray-900">{{ $user->opd?->n_opd ?? '-' }}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-xs text-gray-500">Nama Kepala</p>
                    <p class="text-sm font-medium text-gray-900">{{ $user->nama_kepala ?? '-' }}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-xs text-gray-500">Jabatan Kepala</p>
                    <p class="text-sm font-medium text-gray-900">{{ $user->jabatan_kepala ?? '-' }}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-xs text-gray-500">Nama Operator</p>
                    <p class="text-sm font-medium text-gray-900">{{ $user->nama_operator ?? '-' }}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-xs text-gray-500">Jabatan Operator</p>
                    <p class="text-sm font-medium text-gray-900">{{ $user->jabatan_operator ?? '-' }}</p>
                </div>
            </div>
        </div>

        {{-- Informasi Kontak Card --}}
        <div class="bg-white rounded-xl p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-gray-900">Informasi Kontak</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <p class="text-xs text-gray-500">No. Telepon</p>
                    <p class="text-sm font-medium text-gray-900">{{ $user->telp ?? '-' }}</p>
                </div>
                <div class="space-y-1 md:col-span-2">
                    <p class="text-xs text-gray-500">Alamat</p>
                    <p class="text-sm font-medium text-gray-900">{{ $user->alamat ?? '-' }}</p>
                </div>
            </div>
        </div>

        {{-- Meta Info Card --}}
        <div class="bg-white rounded-xl p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-gray-900">Informasi Tambahan</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1">Dibuat pada</p>
                    <p class="text-sm font-medium text-gray-900">{{ $user->created_at->format('d M Y, H:i') }}</p>
                </div>
                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1">Terakhir diperbarui</p>
                    <p class="text-sm font-medium text-gray-900">{{ $user->updated_at->format('d M Y, H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Column (1/3) --}}
    <div class="space-y-6">
        {{-- Role & Status Card --}}
        <div class="bg-white rounded-xl p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-gray-900">Role & Status</h3>
            </div>

            <div class="space-y-4">
                <div class="space-y-1">
                    <p class="text-xs text-gray-500">Role User</p>
                    <div class="flex items-center gap-2">
                        @if($user->role === 'admin')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-purple-100 text-purple-700">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Admin
                        </span>
                        @elseif($user->role === 'operator')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-blue-100 text-blue-700">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Operator
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-green-100 text-green-700">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Verifikator
                        </span>
                        @endif
                    </div>
                </div>

                <div class="p-3 bg-blue-50 rounded-lg">
                    <p class="text-xs text-primary">
                        @if($user->role === 'admin')
                        Admin memiliki akses penuh ke seluruh fitur sistem
                        @elseif($user->role === 'operator')
                        Operator dapat mengelola data dan mengisi kuesioner
                        @else
                        Verifikator dapat memverifikasi dan menyetujui data
                        @endif
                    </p>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="bg-white rounded-xl p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-gray-900">Aksi Cepat</h3>
            </div>

            <div class="space-y-2">
                <a href="{{ route('cms.user.edit', $user) }}"
                   class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary-dark transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit Pengguna
                </a>
                <form action="{{ route('cms.user.destroy', $user) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-red-300 text-red-600 rounded-lg text-sm font-medium hover:bg-red-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Hapus Pengguna
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
