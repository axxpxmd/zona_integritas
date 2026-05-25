@extends('layouts.app')
@section('title', 'Profil Pengguna')
@section('page-title', 'Profil Pengguna')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Success Message -->
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    <!-- Error Messages -->
    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Profile Info Card -->
        <div class="col-span-1 md:col-span-1">
            <div class="bg-white rounded-xl p-6 border border-gray-200 h-full">
                <div class="text-center mb-6">
                    <div class="w-24 h-24 bg-primary-light text-primary rounded-full flex items-center justify-center mx-auto mb-4 text-3xl font-bold">
                        {{ strtoupper(substr(Auth::user()->username, 0, 1)) }}
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">{{ Auth::user()->nama_instansi }}</h3>
                    <p class="text-sm text-gray-500">{{ Auth::user()->username }}</p>
                    <div class="mt-2 inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 capitalize">
                        {{ Auth::user()->role }}
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="border-t border-gray-100 pt-4">
                        <p class="text-sm text-gray-500 mb-1">Email</p>
                        <p class="text-sm font-medium text-gray-900">{{ Auth::user()->email }}</p>
                    </div>
                    @if(Auth::user()->telp)
                    <div class="border-t border-gray-100 pt-4">
                        <p class="text-sm text-gray-500 mb-1">Telepon</p>
                        <p class="text-sm font-medium text-gray-900">{{ Auth::user()->telp }}</p>
                    </div>
                    @endif
                    @if(Auth::user()->alamat)
                    <div class="border-t border-gray-100 pt-4">
                        <p class="text-sm text-gray-500 mb-1">Alamat</p>
                        <p class="text-sm font-medium text-gray-900">{{ Auth::user()->alamat }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Detail Info & Change Password Column -->
        <div class="col-span-1 md:col-span-2 space-y-6">
            <!-- Informasi Instansi Card -->
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-primary-light text-primary rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Detail Informasi Instansi</h2>
                            <p class="text-sm text-gray-500">Informasi detail mengenai penanggung jawab dan operator instansi.</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Info Kepala -->
                        <div class="space-y-4">
                            <h3 class="font-semibold text-gray-900 border-b border-gray-100 pb-2">Penanggung Jawab (Kepala)</h3>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Nama Kepala</p>
                                <p class="text-sm font-medium text-gray-900 {{ !Auth::user()->nama_kepala ? 'italic text-gray-400' : '' }}">{{ Auth::user()->nama_kepala ?? 'Belum diatur' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Jabatan Kepala</p>
                                <p class="text-sm font-medium text-gray-900 {{ !Auth::user()->jabatan_kepala ? 'italic text-gray-400' : '' }}">{{ Auth::user()->jabatan_kepala ?? 'Belum diatur' }}</p>
                            </div>
                        </div>

                        <!-- Info Operator -->
                        <div class="space-y-4">
                            <h3 class="font-semibold text-gray-900 border-b border-gray-100 pb-2">Informasi Operator</h3>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Nama Operator</p>
                                <p class="text-sm font-medium text-gray-900 {{ !Auth::user()->nama_operator ? 'italic text-gray-400' : '' }}">{{ Auth::user()->nama_operator ?? 'Belum diatur' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Jabatan Operator</p>
                                <p class="text-sm font-medium text-gray-900 {{ !Auth::user()->jabatan_operator ? 'italic text-gray-400' : '' }}">{{ Auth::user()->jabatan_operator ?? 'Belum diatur' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Change Password Card -->
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-primary-light text-primary rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Ubah Password</h2>
                            <p class="text-sm text-gray-500">Pastikan akun Anda menggunakan password yang panjang dan acak untuk tetap aman.</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('profile.password.update') }}" method="POST" class="p-6">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Password Saat Ini <span class="text-red-500">*</span></label>
                            <input type="password" name="current_password" id="current_password" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-0.5 focus:ring-primary focus:border-primary" required>
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru <span class="text-red-500">*</span></label>
                            <input type="password" name="password" id="password" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-0.5 focus:ring-primary focus:border-primary" required>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru <span class="text-red-500">*</span></label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-0.5 focus:ring-primary focus:border-primary" required>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors border border-transparent shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Simpan Password Baru
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
