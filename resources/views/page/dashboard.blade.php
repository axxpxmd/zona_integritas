@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Selamat Datang, <span class="text-primary">{{ Auth::user()->nama_instansi }}!</span> 👋
            </h1>
            <p class="text-gray-500 mt-1">Kelola seluruh sistem dan pantau performa website</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-lg">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-sm font-medium text-gray-700">{{ now()->translatedFormat('d F Y') }}</span>
            </div>
            <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-lg">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="text-sm font-medium text-gray-700 capitalize">{{ Auth::user()->role }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
