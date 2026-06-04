@extends('layouts.app')

@section('title', 'Log Sistem')
@section('page-title', 'Log Sistem')

@section('content')
<div class="space-y-6">
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900 font-sans">Log Aktivitas Sistem</h2>
            <p class="text-sm text-gray-500 mt-1">Pantau dan kelola berkas catatan log aktivitas harian aplikasi</p>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
    <div class="bg-green-50 rounded-xl p-4 flex items-center gap-3 shadow-sm border border-green-100">
        <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-sm text-green-800 font-medium">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 rounded-xl p-4 flex items-center gap-3 shadow-sm border border-red-100">
        <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-sm text-red-800 font-medium">{{ session('error') }}</p>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">
        {{-- Left Sidebar: Log Files List --}}
        <div class="lg:col-span-1 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col max-h-[calc(100vh-220px)]">
            <div class="p-4 bg-gray-50/50 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                    Berkas Log Harian
                </h3>
            </div>
            
            <div class="overflow-y-auto flex-1 divide-y divide-gray-100 p-2 space-y-1">
                @forelse($logFiles as $file)
                    <a href="{{ route('logs.index', ['file' => $file['name']]) }}" 
                       class="flex flex-col p-3 rounded-xl transition-all duration-200 group {{ $activeFile === $file['name'] ? 'bg-primary-light border-primary/20 border text-primary' : 'hover:bg-gray-50 text-gray-700' }}">
                        <div class="flex items-center justify-between w-full">
                            <span class="text-xs font-semibold truncate {{ $activeFile === $file['name'] ? 'text-primary' : 'text-gray-900 group-hover:text-primary' }}">
                                {{ $file['name'] }}
                            </span>
                            <span class="text-[10px] bg-gray-100 group-hover:bg-white text-gray-600 px-1.5 py-0.5 rounded font-mono font-medium">
                                {{ $file['size'] }}
                            </span>
                        </div>
                        <span class="text-[10px] text-gray-400 mt-1">
                            Diperbarui: {{ $file['modified'] }}
                        </span>
                    </a>
                @empty
                    <div class="p-6 text-center text-gray-400 text-sm">
                        Belum ada berkas log.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Right Side: Log Contents & Search --}}
        <div class="lg:col-span-3 space-y-6">
            @if($selectedFileExists)
                {{-- Active File Details & Filters --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-5">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-gray-100 gap-4">
                        <div class="flex items-start gap-3">
                            <div class="p-2.5 bg-primary/10 text-primary rounded-xl mt-1">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900">{{ $activeFile }}</h3>
                                <p class="text-xs text-gray-500 mt-0.5 font-mono">
                                    Path: storage/logs/{{ $activeFile }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2.5">
                            <a href="{{ route('logs.download', $activeFile) }}" 
                               class="inline-flex items-center gap-2 px-4 py-2 border border-gray-200 hover:border-gray-300 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Unduh
                            </a>
                            <form action="{{ route('logs.destroy', $activeFile) }}" 
                                  method="POST" 
                                  class="inline" 
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus berkas log ini secara permanen?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl text-sm font-medium transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Search & Level Filters --}}
                    <form action="{{ route('logs.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
                        <input type="hidden" name="file" value="{{ $activeFile }}">
                        
                        <div class="flex-1 w-full">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Cari Kata Kunci</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </span>
                                <input type="text" 
                                       name="search" 
                                       value="{{ request('search') }}" 
                                       placeholder="Cari pesan log atau timestamp..." 
                                       class="w-full pl-10 pr-4 py-2.5 bg-gray-50/50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                            </div>
                        </div>

                        <div class="w-full sm:w-48">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Tingkat Log (Level)</label>
                            <select name="level" 
                                    class="w-full px-4 py-2.5 bg-gray-50/50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all cursor-pointer">
                                <option value="">Semua Level</option>
                                @foreach(['EMERGENCY', 'ALERT', 'CRITICAL', 'ERROR', 'WARNING', 'NOTICE', 'INFO', 'DEBUG'] as $lvl)
                                    <option value="{{ $lvl }}" {{ request('level') === $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <button type="submit" 
                                    class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-primary text-white rounded-xl text-sm font-semibold hover:bg-primary-dark transition-colors shadow-sm shadow-primary/10">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                                </svg>
                                Filter
                            </button>
                            @if(request('search') || request('level'))
                                <a href="{{ route('logs.index', ['file' => $activeFile]) }}" 
                                   class="p-2.5 border border-gray-200 rounded-xl text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition-colors" 
                                   title="Reset Filter">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                {{-- Log Entries List --}}
                <div class="space-y-4 max-h-[calc(100vh-340px)] overflow-y-auto pr-2">
                    @forelse($parsedLogs as $log)
                        @php
                            $level = $log['level'];
                            $isError = in_array($level, ['EMERGENCY', 'ALERT', 'CRITICAL', 'ERROR']);
                            $isWarning = $level === 'WARNING';
                            $isNotice = $level === 'NOTICE';
                            $isInfo = $level === 'INFO';
                            
                            $badgeColor = 'bg-gray-100 text-gray-700 border-gray-200/50';
                            $borderColor = 'border-gray-200';
                            
                            if ($isError) {
                                $badgeColor = 'bg-red-50 text-red-700 border-red-200/50';
                                $borderColor = 'border-l-4 border-l-red-500 border-y-gray-100 border-r-gray-100';
                            } elseif ($isWarning) {
                                $badgeColor = 'bg-amber-50 text-amber-700 border-amber-200/50';
                                $borderColor = 'border-l-4 border-l-amber-500 border-y-gray-100 border-r-gray-100';
                            } elseif ($isNotice) {
                                $badgeColor = 'bg-yellow-50 text-yellow-800 border-yellow-200/50';
                                $borderColor = 'border-l-4 border-l-yellow-400 border-y-gray-100 border-r-gray-100';
                            } elseif ($isInfo) {
                                $badgeColor = 'bg-blue-50 text-blue-700 border-blue-200/50';
                                $borderColor = 'border-l-4 border-l-primary border-y-gray-100 border-r-gray-100';
                            }
                        @endphp

                        <div class="bg-white rounded-xl border {{ $borderColor }} shadow-sm p-4 space-y-2.5 transition-all hover:shadow-md">
                            <div class="flex flex-wrap items-center justify-between gap-2.5">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wider border {{ $badgeColor }}">
                                        {{ $level }}
                                    </span>
                                    <span class="text-xs font-mono font-medium text-gray-400 bg-gray-50 px-2 py-0.5 rounded border border-gray-100">
                                        {{ $log['env'] }}
                                    </span>
                                </div>
                                <span class="text-xs text-gray-400 font-mono font-semibold">
                                    {{ $log['timestamp'] }}
                                </span>
                            </div>

                            @php
                                $hasStackTrace = strpos($log['message'], "\n") !== false;
                                $firstLine = $hasStackTrace ? explode("\n", $log['message'])[0] : $log['message'];
                            @endphp

                            <div class="text-sm font-mono text-gray-800 whitespace-pre-wrap break-words leading-relaxed select-all">
                                {{ $firstLine }}
                            </div>

                            @if($hasStackTrace)
                                <details class="group mt-2 border-t border-gray-100 pt-2.5">
                                    <summary class="text-xs font-semibold text-gray-500 cursor-pointer list-none flex items-center gap-1.5 select-none hover:text-primary transition-colors">
                                        <svg class="w-3.5 h-3.5 transition-transform duration-200 group-open:rotate-90 text-gray-400 group-hover:text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                        </svg>
                                        Lihat Stack Trace
                                    </summary>
                                    <div class="mt-2 text-xs font-mono bg-gray-950 text-gray-200 p-4 rounded-xl overflow-x-auto leading-relaxed border border-gray-900 max-h-[350px] scrollbar-thin select-all">
                                        {{ $log['message'] }}
                                    </div>
                                </details>
                            @endif
                        </div>
                    @empty
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 text-center text-gray-500">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-sm font-semibold text-gray-900">Tidak ada log ditemukan</p>
                            <p class="text-xs text-gray-400 mt-1">
                                @if(request('search') || request('level'))
                                    Coba ubah kata kunci pencarian atau filter tingkat log.
                                @else
                                    Berkas log ini kosong.
                                @endif
                            </p>
                        </div>
                    @endforelse
                </div>
            @else
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center text-gray-500">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h3 class="text-base font-bold text-gray-900">Tidak Ada Berkas Log</h3>
                    <p class="text-sm text-gray-500 mt-1">Silakan pilih berkas log di panel sebelah kiri atau buat aktivitas baru untuk memicu pencatatan log.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
