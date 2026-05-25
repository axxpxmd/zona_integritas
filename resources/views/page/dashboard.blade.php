@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
	<div class="space-y-6">
		<!-- Welcome Card -->
		<div class="bg-white rounded-xl p-6 md:p-8">
			<div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
				<div class="flex items-center gap-5">
					<div
						class="hidden sm:flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-full bg-primary-light text-primary">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
							stroke="currentColor" class="h-7 w-7">
							<path stroke-linecap="round" stroke-linejoin="round"
								d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
						</svg>
					</div>
					<div>
						<p class="text-sm font-medium text-primary">Selamat Datang 👋</p>
						<h2 class="mt-1 text-2xl font-bold text-gray-900">{{ $displayName }}</h2>
						<p class="mt-2 text-sm text-gray-600">
							Anda login sebagai
							<span class="font-semibold text-gray-800">{{ $roleLabel }}</span>
							dengan username
							<span class="font-semibold text-gray-800">{{ $username }}</span>.
						</p>
					</div>
				</div>

				<div
					class="inline-flex items-center gap-2 rounded-full bg-primary-light px-4 py-2 text-sm font-semibold text-primary">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
						<path fill-rule="evenodd"
							d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.057-1.35-.166-1.998A11.954 11.954 0 0110 1.944zM8.504 12.39L6.101 9.987a.75.75 0 011.06-1.061l1.343 1.344 3.125-3.126a.75.75 0 011.06 1.061l-3.656 3.655a.75.75 0 01-1.06 0z"
							clip-rule="evenodd" />
					</svg>
					Role Aktif: {{ $roleLabel }}
				</div>
			</div>
		</div>

		@if($activePeriode)
			{{-- ========== OPERATOR STATS ========== --}}
			@if(auth()->user()->role === 'operator' && !empty($operatorStats))
				@php $s = $operatorStats; @endphp

				{{-- Status Banner --}}
				@php
					$bannerColorMap = [
						'green' => ['bg' => 'bg-green-50', 'border' => 'border-green-200', 'text' => 'text-green-800', 'icon' => 'text-green-500', 'badge_bg' => 'bg-green-100', 'badge_text' => 'text-green-800'],
						'red' => ['bg' => 'bg-red-50', 'border' => 'border-red-200', 'text' => 'text-red-800', 'icon' => 'text-red-500', 'badge_bg' => 'bg-red-100', 'badge_text' => 'text-red-800'],
						'blue' => ['bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'text' => 'text-blue-800', 'icon' => 'text-blue-500', 'badge_bg' => 'bg-blue-100', 'badge_text' => 'text-blue-800'],
						'yellow' => ['bg' => 'bg-yellow-50', 'border' => 'border-yellow-200', 'text' => 'text-yellow-800', 'icon' => 'text-yellow-500', 'badge_bg' => 'bg-yellow-100', 'badge_text' => 'text-yellow-800'],
						'indigo' => ['bg' => 'bg-indigo-50', 'border' => 'border-indigo-200', 'text' => 'text-indigo-800', 'icon' => 'text-indigo-500', 'badge_bg' => 'bg-indigo-100', 'badge_text' => 'text-indigo-800'],
						'gray' => ['bg' => 'bg-gray-50', 'border' => 'border-gray-200', 'text' => 'text-gray-700', 'icon' => 'text-gray-400', 'badge_bg' => 'bg-gray-100', 'badge_text' => 'text-gray-700'],
					];
					$bc = $bannerColorMap[$s['statusColor']] ?? $bannerColorMap['gray'];
				@endphp
				<div
					class="rounded-xl border px-5 py-4 flex items-center justify-between gap-4 {{ $bc['bg'] }} {{ $bc['border'] }}">
					<div class="flex items-center gap-3">
						<div class="flex-shrink-0">
							@if($s['statusColor'] === 'green')
								<svg class="w-6 h-6 {{ $bc['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
								</svg>
							@elseif($s['statusColor'] === 'red')
								<svg class="w-6 h-6 {{ $bc['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
								</svg>
							@elseif($s['statusColor'] === 'blue')
								<svg class="w-6 h-6 {{ $bc['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
								</svg>
							@else
								<svg class="w-6 h-6 {{ $bc['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
								</svg>
							@endif
						</div>
						<div>
							<p class="text-sm font-bold {{ $bc['text'] }}">Status LKE: {{ $s['statusLabel'] }}</p>
							<p class="text-xs {{ $bc['text'] }} opacity-80 mt-0.5">Periode: <span
									class="font-semibold">{{ $activePeriode->nama_periode }}</span> • Pengisian:
								{{ $s['tanggalMulai']->format('d M Y') }} s/d {{ $s['tanggalSelesai']->format('d M Y') }}
							</p>
						</div>
					</div>
					<div class="flex items-center gap-2 flex-shrink-0">
						@if($s['isCanFill'])
							<span
								class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-200">
								<span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span> Sedang Berjalan
							</span>
						@elseif($s['isCanRevisi'])
							<span
								class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800 border border-orange-200">
								<span class="w-1.5 h-1.5 bg-orange-500 rounded-full animate-pulse"></span> Masa Revisi
							</span>
						@else
							<span
								class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
								<span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span> Di Luar Jadwal
							</span>
						@endif
						<a href="{{ route('kuesioner.show', $activePeriode->id) }}"
							class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary text-white rounded-lg text-xs font-semibold hover:bg-primary-dark transition-colors">
							Buka LKE
							<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
							</svg>
						</a>
					</div>
				</div>

				{{-- Stats Grid --}}
				<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
					{{-- Progress Pengisian --}}
					<div class="bg-white rounded-xl p-5 col-span-2 md:col-span-2">
						<div class="flex items-start justify-between mb-4">
							<div>
								<p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Progress Pengisian LKE</p>
								<div class="flex items-end gap-2 mt-1">
									<span class="text-3xl font-bold text-gray-900">{{ $s['persenPengisian'] }}%</span>
									<span class="text-sm text-gray-500 mb-0.5">{{ $s['totalDiisi'] }} / {{ $s['totalRequired'] }}
										jawaban</span>
								</div>
							</div>
							<div
								class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary flex-shrink-0">
								<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
								</svg>
							</div>
						</div>
						<div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
							<div class="h-3 rounded-full transition-all duration-500 {{ $s['persenPengisian'] >= 100 ? 'bg-green-500' : ($s['persenPengisian'] >= 50 ? 'bg-primary' : 'bg-yellow-400') }}"
								style="width: {{ $s['persenPengisian'] }}%"></div>
						</div>
						<div class="flex items-center justify-between mt-2">
							<span class="text-xs text-gray-500">Belum diisi: {{ $s['totalRequired'] - $s['totalDiisi'] }}</span>
							@if($s['isKirimFinal'])
								<span class="inline-flex items-center gap-1 text-xs font-semibold text-blue-700">
									<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
									</svg>
									Sudah Dikirim ke Verifikator
								</span>
							@else
								<span
									class="text-xs font-semibold {{ $s['persenPengisian'] >= 100 ? 'text-green-600' : 'text-gray-400' }}">
									{{ $s['persenPengisian'] >= 100 ? 'Siap Dikirim!' : 'Draft' }}
								</span>
							@endif
						</div>
					</div>

					{{-- Status Verifikasi --}}
					<div class="bg-white rounded-xl p-5">
						<div class="flex items-center justify-between mb-3">
							<p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Verifikasi</p>
							<div class="w-8 h-8 rounded-lg bg-teal-50 flex items-center justify-center text-teal-600">
								<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
								</svg>
							</div>
						</div>
						<div class="space-y-2">
							<div class="flex items-center justify-between">
								<div class="flex items-center gap-1.5">
									<div class="w-2 h-2 rounded-full bg-green-500"></div>
									<span class="text-xs text-gray-600">Disetujui</span>
								</div>
								<span class="text-sm font-bold text-green-700">{{ $s['totalDisetujui'] }}</span>
							</div>
							<div class="flex items-center justify-between">
								<div class="flex items-center gap-1.5">
									<div class="w-2 h-2 rounded-full bg-gray-300"></div>
									<span class="text-xs text-gray-600">Belum Dicek</span>
								</div>
								<span class="text-sm font-bold text-gray-600">{{ $s['totalBelumDiverifikasi'] }}</span>
							</div>
						</div>
					</div>

					{{-- Revisi --}}
					<div class="bg-white rounded-xl p-5">
						<div class="flex items-center justify-between mb-3">
							<p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Revisi</p>
							<div
								class="w-8 h-8 rounded-lg {{ $s['totalDirevisi'] > 0 ? 'bg-red-50 text-red-600' : 'bg-gray-50 text-gray-400' }} flex items-center justify-center">
								<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
								</svg>
							</div>
						</div>
						<div class="space-y-2">
							<div class="flex items-center justify-between">
								<div class="flex items-center gap-1.5">
									<div class="w-2 h-2 rounded-full bg-red-500"></div>
									<span class="text-xs text-gray-600">Perlu Revisi</span>
								</div>
								<span
									class="text-sm font-bold {{ $s['totalDirevisi'] > 0 ? 'text-red-700' : 'text-gray-400' }}">{{ $s['totalDirevisi'] }}</span>
							</div>
							<div class="flex items-center justify-between">
								<div class="flex items-center gap-1.5">
									<div class="w-2 h-2 rounded-full bg-orange-400"></div>
									<span class="text-xs text-gray-600">Menunggu Cek</span>
								</div>
								<span
									class="text-sm font-bold {{ $s['totalMenungguDicekUlang'] > 0 ? 'text-orange-600' : 'text-gray-400' }}">{{ $s['totalMenungguDicekUlang'] }}</span>
							</div>
						</div>
						@if($s['totalDirevisi'] > 0 && $s['isCanRevisi'])
							<a href="{{ route('kuesioner.revisi.index', $activePeriode->id) }}"
								class="mt-3 w-full inline-flex items-center justify-center gap-1 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-semibold transition-colors">
								Lihat Revisi
								<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
								</svg>
							</a>
						@endif
					</div>
				</div>

				{{-- Info Dokumen & Jadwal --}}
				<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
					{{-- Dokumen Terupload --}}
					<div class="bg-white rounded-xl p-5 flex items-center gap-4">
						<div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 flex-shrink-0">
							<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
							</svg>
						</div>
						<div>
							<p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Dokumen Pendukung</p>
							<p class="text-2xl font-bold text-gray-900 mt-0.5">{{ $s['totalDokumen'] }} <span
									class="text-sm font-normal text-gray-500">file terupload</span></p>
						</div>
					</div>

					{{-- Jadwal Periode --}}
					<div class="bg-white rounded-xl p-5">
						<p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Jadwal Periode</p>
						<div class="space-y-2 text-sm">
							<div class="flex items-center justify-between">
								<span class="text-gray-600 flex items-center gap-2">
									<span
										class="w-1.5 h-1.5 rounded-full {{ $s['isCanFill'] ? 'bg-green-500' : 'bg-gray-300' }}"></span>
									Pengisian
								</span>
								<span class="font-medium text-gray-800">{{ $s['tanggalMulai']->format('d M Y') }} —
									{{ $s['tanggalSelesai']->format('d M Y') }}</span>
							</div>
							@if($s['tanggalMulaiRevisi'])
								<div class="flex items-center justify-between">
									<span class="text-gray-600 flex items-center gap-2">
										<span
											class="w-1.5 h-1.5 rounded-full {{ $s['isCanRevisi'] ? 'bg-orange-500' : 'bg-gray-300' }}"></span>
										Revisi
									</span>
									<span class="font-medium text-gray-800">{{ $s['tanggalMulaiRevisi']->format('d M Y') }} —
										{{ $s['tanggalSelesaiRevisi']->format('d M Y') }}</span>
								</div>
							@endif
						</div>
					</div>
				</div>
			@endif
			{{-- ========== END OPERATOR STATS ========== --}}

			{{-- ========== VERIFIKATOR MENHAN STATS ========== --}}
			@if(in_array(auth()->user()->role, ['verifikator_menhan']) && !empty($menhanStats))
				@php $m = $menhanStats; @endphp

				{{-- Status Banner --}}
				<div
					class="rounded-xl border px-5 py-4 flex items-center justify-between gap-4 {{ $m['isVerifActive'] ? 'bg-indigo-50 border-indigo-200' : 'bg-gray-50 border-gray-200' }}">
					<div class="flex items-center gap-3">
						<div class="flex-shrink-0">
							<svg class="w-6 h-6 {{ $m['isVerifActive'] ? 'text-indigo-500' : 'text-gray-400' }}" fill="none"
								stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
							</svg>
						</div>
						<div>
							<p class="text-sm font-bold {{ $m['isVerifActive'] ? 'text-indigo-800' : 'text-gray-700' }}">
								Dashboard Verifikasi Menhan — Periode: {{ $activePeriode->nama_periode }}
							</p>
							<p class="text-xs {{ $m['isVerifActive'] ? 'text-indigo-700' : 'text-gray-500' }} mt-0.5">
								@if($m['startVerif'] && $m['endVerif'])
									Jadwal verifikasi: {{ $m['startVerif']->format('d M Y') }} s/d {{ $m['endVerif']->format('d M Y') }}
								@else
									Jadwal verifikasi belum ditentukan
								@endif
							</p>
						</div>
					</div>
					<div class="flex items-center gap-2 flex-shrink-0">
						@if($m['isVerifActive'])
							<span
								class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 border border-indigo-200">
								<span class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-pulse"></span> Verifikasi Aktif
							</span>
						@else
							<span
								class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
								<span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span> Di Luar Jadwal
							</span>
						@endif
					</div>
				</div>

				{{-- Data akan ditampilkan sama dengan tabel verifikator namun mengambil dari variabel $m --}}
				{{-- Stats Grid Menhan --}}
				<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
					{{-- Status OPD (Menhan) --}}
					<div class="bg-white rounded-xl p-5 flex items-center justify-between">
						<div>
							<p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Status OPD Ter-Assign</p>
							<div class="flex gap-4 mt-2">
								<div>
									<p class="text-2xl font-bold text-gray-900">{{ $m['opdSiapMenhan'] }}</p>
									<p class="text-xs text-green-600 font-medium">Siap Verif</p>
								</div>
								<div>
									<p class="text-2xl font-bold text-gray-900">{{ $m['opdBelumSiapMenhan'] }}</p>
									<p class="text-xs text-orange-500 font-medium">Belum Siap</p>
								</div>
							</div>
						</div>
						<div
							class="w-14 h-14 rounded-full bg-blue-50 flex items-center justify-center border-4 border-blue-100 text-blue-600 flex-shrink-0">
							<span class="text-lg font-bold">{{ $m['totalOpdAssigned'] }}</span>
						</div>
					</div>

					{{-- Progress Verifikasi Keseluruhan Menhan --}}
					<div class="bg-white rounded-xl p-5 col-span-2">
						<div class="flex items-start justify-between mb-4">
							<div>
								<p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Progress Menhan (Keseluruhan)
								</p>
								<div class="flex items-end gap-2 mt-1">
									<span class="text-3xl font-bold text-gray-900">{{ $m['persenVerifikasi'] }}%</span>
									<span class="text-sm text-gray-500 mb-0.5">{{ $m['totalDisetujui'] }} /
										{{ $m['totalJawaban'] }} disetujui</span>
								</div>
							</div>
						</div>
						<div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
							<div class="h-3 rounded-full bg-indigo-500 transition-all duration-500"
								style="width: {{ $m['persenVerifikasi'] }}%"></div>
						</div>
					</div>
				</div>

				{{-- Table OPD List Menhan --}}
				<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
					<div class="px-5 py-4 border-b border-gray-200 bg-gray-50/50">
						<h3 class="text-base font-bold text-gray-900">Daftar OPD untuk Diverifikasi Menhan</h3>
					</div>
					<div class="overflow-x-auto">
						<table class="w-full text-left text-sm whitespace-nowrap">
							<thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider border-b border-gray-200">
								<tr>
									<th class="px-5 py-3 font-semibold">Nama Unit Kerja</th>
									<th class="px-5 py-3 font-semibold text-center w-32">Status Persiapan</th>
									<th class="px-5 py-3 font-semibold text-center w-32">Disetujui</th>
									<th class="px-5 py-3 font-semibold text-center w-32">Belum Dicek</th>
									<th class="px-5 py-3 font-semibold w-40">Progress</th>
									<th class="px-5 py-3 font-semibold text-right w-24">Aksi</th>
								</tr>
							</thead>
							<tbody class="divide-y divide-gray-100">
								@forelse($m['opdProgressMenhan'] as $p)
									<tr class="hover:bg-gray-50 transition-colors group">
										<td class="px-5 py-3">
											<div class="font-medium text-gray-900">{{ $p->opd->n_opd }}</div>
											@if($p->total == 0)
												<div class="text-xs text-gray-500 mt-0.5">Belum ada jawaban sama sekali</div>
											@endif
										</td>
										<td class="px-5 py-3 text-center">
											@if($p->isSiap)
												<span
													class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-green-50 text-green-700 border border-green-200">
													Siap
												</span>
											@else
												<span
													class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-orange-50 text-orange-700 border border-orange-200">
													Belum Siap
												</span>
											@endif
										</td>
										<td class="px-5 py-3 text-center font-bold text-green-600">
											{{ $p->disetujui }}
										</td>
										<td class="px-5 py-3 text-center font-bold text-gray-500">
											{{ $p->belum }}
										</td>
										<td class="px-5 py-3">
											<div class="flex items-center gap-2">
												<div class="w-full bg-gray-100 rounded-full h-1.5 flex-grow">
													<div class="h-1.5 rounded-full bg-indigo-500" style="width: {{ $p->persen }}%">
													</div>
												</div>
												<span class="text-xs font-semibold text-gray-700 w-8 text-right">{{ $p->persen }}%</span>
											</div>
										</td>
										<td class="px-5 py-3 text-right">
											@if($p->isSiap)
												<a href="{{ route('verifikasi-menhan.show', ['periode' => $activePeriode->id, 'opd' => $p->opd->id]) }}"
													class="inline-flex items-center justify-center px-3 py-1.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-600 hover:text-white border border-indigo-200 hover:border-transparent rounded-lg text-xs font-semibold transition-all">
													Lakukan Verifikasi
												</a>
											@else
												<span class="text-xs text-gray-400 italic">Belum bisa ditindak</span>
											@endif
										</td>
									</tr>
								@empty
									<tr>
										<td colspan="6" class="px-5 py-8 text-center text-gray-500">
											Belum ada OPD yang di-assign ke Anda.
										</td>
									</tr>
								@endforelse
							</tbody>
						</table>
					</div>
				</div>
			@endif
			{{-- ========== END VERIFIKATOR MENHAN STATS ========== --}}

			{{-- ========== VERIFIKATOR STATS ========== --}}
			@if(auth()->user()->role === 'verifikator' && !empty($verifikatorStats))
				@php $v = $verifikatorStats; @endphp

				{{-- Status Banner --}}
				<div
					class="rounded-xl border px-5 py-4 flex items-center justify-between gap-4 {{ $v['isVerifActive'] ? 'bg-teal-50 border-teal-200' : 'bg-gray-50 border-gray-200' }}">
					<div class="flex items-center gap-3">
						<div class="flex-shrink-0">
							<svg class="w-6 h-6 {{ $v['isVerifActive'] ? 'text-teal-500' : 'text-gray-400' }}" fill="none"
								stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
							</svg>
						</div>
						<div>
							<p class="text-sm font-bold {{ $v['isVerifActive'] ? 'text-teal-800' : 'text-gray-700' }}">
								Dashboard Verifikasi — Periode: {{ $activePeriode->nama_periode }}
							</p>
							<p class="text-xs {{ $v['isVerifActive'] ? 'text-teal-700' : 'text-gray-500' }} mt-0.5">
								@if($v['startVerif'] && $v['endVerif'])
									Jadwal verifikasi: {{ $v['startVerif']->format('d M Y') }} s/d {{ $v['endVerif']->format('d M Y') }}
								@else
									Jadwal verifikasi belum ditentukan
								@endif
							</p>
						</div>
					</div>
					<div class="flex items-center gap-2 flex-shrink-0">
						@if($v['isVerifActive'])
							<span
								class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-teal-100 text-teal-800 border border-teal-200">
								<span class="w-1.5 h-1.5 bg-teal-500 rounded-full animate-pulse"></span> Verifikasi Aktif
							</span>
						@else
							<span
								class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
								<span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span> Di Luar Jadwal
							</span>
						@endif
						<a href="{{ route('verifikasi.index') }}"
							class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-teal-600 text-white rounded-lg text-xs font-semibold hover:bg-teal-700 transition-colors">
							Buka Verifikasi
							<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
							</svg>
						</a>
					</div>
				</div>

				{{-- Stats Cards --}}
				<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
					{{-- OPD Ditangani --}}
					<div class="bg-white rounded-xl p-5">
						<div class="flex items-center justify-between mb-3">
							<p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">OPD Ditangani</p>
							<div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
								<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
								</svg>
							</div>
						</div>
						<p class="text-3xl font-bold text-gray-900">{{ $v['totalOpdAssigned'] }}</p>
						<div class="flex items-center gap-3 mt-2 text-xs">
							<span class="text-green-600 font-semibold">{{ $v['opdSudahKirim'] }} sudah kirim</span>
							<span class="text-gray-400">·</span>
							<span class="text-gray-500">{{ $v['opdBelumKirim'] }} belum</span>
						</div>
					</div>

					{{-- Progress Verifikasi --}}
					<div class="bg-white rounded-xl p-5 col-span-1 md:col-span-1">
						<div class="flex items-center justify-between mb-3">
							<p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Progress Verifikasi</p>
							<div class="w-8 h-8 rounded-lg bg-teal-50 flex items-center justify-center text-teal-600">
								<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
								</svg>
							</div>
						</div>
						<p class="text-3xl font-bold text-gray-900">{{ $v['persenVerifikasi'] }}%</p>
						<div class="mt-2 w-full bg-gray-100 rounded-full h-2 overflow-hidden">
							<div class="h-2 rounded-full {{ $v['persenVerifikasi'] >= 100 ? 'bg-green-500' : 'bg-teal-500' }}"
								style="width: {{ $v['persenVerifikasi'] }}%"></div>
						</div>
						<p class="text-xs text-gray-400 mt-1.5">{{ $v['totalDisetujui'] + $v['totalDirevisi'] }} /
							{{ $v['totalJawaban'] }} ditindaklanjuti
						</p>
					</div>

					{{-- Status Jawaban --}}
					<div class="bg-white rounded-xl p-5">
						<p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Status Jawaban</p>
						<div class="space-y-2">
							<div class="flex items-center justify-between">
								<div class="flex items-center gap-1.5">
									<div class="w-2 h-2 rounded-full bg-green-500"></div>
									<span class="text-xs text-gray-600">Disetujui</span>
								</div>
								<span class="text-sm font-bold text-green-700">{{ $v['totalDisetujui'] }}</span>
							</div>
							<div class="flex items-center justify-between">
								<div class="flex items-center gap-1.5">
									<div class="w-2 h-2 rounded-full bg-red-400"></div>
									<span class="text-xs text-gray-600">Direvisi</span>
								</div>
								<span class="text-sm font-bold text-red-600">{{ $v['totalDirevisi'] }}</span>
							</div>
							<div class="flex items-center justify-between">
								<div class="flex items-center gap-1.5">
									<div class="w-2 h-2 rounded-full bg-gray-300"></div>
									<span class="text-xs text-gray-600">Belum Dicek</span>
								</div>
								<span class="text-sm font-bold text-gray-600">{{ $v['totalBelumDiverifikasi'] }}</span>
							</div>
						</div>
					</div>

					{{-- Menunggu Cek Ulang --}}
					<div class="bg-white rounded-xl p-5">
						<div class="flex items-center justify-between mb-3">
							<p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Perlu Cek Ulang</p>
							<div
								class="w-8 h-8 rounded-lg {{ $v['totalMenungguDicekUlang'] > 0 ? 'bg-orange-50 text-orange-500' : 'bg-gray-50 text-gray-300' }} flex items-center justify-center">
								<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
								</svg>
							</div>
						</div>
						<p class="text-3xl font-bold {{ $v['totalMenungguDicekUlang'] > 0 ? 'text-orange-600' : 'text-gray-300' }}">
							{{ $v['totalMenungguDicekUlang'] }}
						</p>
						<p class="text-xs text-gray-400 mt-1">operator sudah merevisi</p>
					</div>
				</div>

				{{-- Tabel per-OPD --}}
				@if($v['opdProgressVerif']->isNotEmpty())
					<div class="bg-white rounded-xl overflow-hidden">
						<div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
							<h3 class="text-sm font-semibold text-gray-900">Progress Verifikasi per OPD</h3>
							<span class="text-xs text-gray-400">{{ $activePeriode->nama_periode }}</span>
						</div>
						<div class="overflow-x-auto">
							<table class="w-full text-sm text-left text-gray-600">
								<thead class="bg-gray-50 border-b border-gray-100">
									<tr>
										<th class="px-5 py-3 font-medium text-gray-700">Nama OPD</th>
										<th class="px-5 py-3 font-medium text-gray-700 text-center">Status Pengisian</th>
										<th class="px-5 py-3 font-medium text-gray-700 text-center">Disetujui</th>
										<th class="px-5 py-3 font-medium text-gray-700 text-center">Direvisi</th>
										<th class="px-5 py-3 font-medium text-gray-700 text-center">Belum</th>
										<th class="px-5 py-3 font-medium text-gray-700 text-center">Menunggu</th>
										<th class="px-5 py-3 font-medium text-gray-700">Progress</th>
										<th class="px-5 py-3 font-medium text-gray-700"></th>
									</tr>
								</thead>
								<tbody class="divide-y divide-gray-100">
									@foreach($v['opdProgressVerif'] as $row)
										<tr class="hover:bg-gray-50/50 transition-colors">
											<td class="px-5 py-3">
												<p class="font-medium text-gray-900 text-sm">{{ $row->opd->n_opd }}</p>
											</td>
											<td class="px-5 py-3 text-center">
												<div class="flex flex-col items-center justify-center gap-1.5">
													@if($row->isFinal)
														<span
															class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-100 text-blue-700">
															<span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span> Sudah Kirim
														</span>
													@else
														<span
															class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-500">
															Belum Kirim
														</span>
													@endif
													<span class="text-[10px] text-gray-500 font-medium">
														Terisi: {{ $row->totalDiisi }}/{{ $row->totalRequired }}
													</span>
												</div>
											</td>
											<td class="px-5 py-3 text-center"><span
													class="font-semibold text-green-700">{{ $row->disetujui }}</span></td>
											<td class="px-5 py-3 text-center"><span
													class="font-semibold {{ $row->direvisi > 0 ? 'text-red-600' : 'text-gray-400' }}">{{ $row->direvisi }}</span>
											</td>
											<td class="px-5 py-3 text-center"><span
													class="font-semibold text-gray-500">{{ $row->belum }}</span></td>
											<td class="px-5 py-3 text-center">
												@if($row->menunggu > 0)
													<span
														class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-orange-100 text-orange-700 text-xs font-bold">{{ $row->menunggu }}</span>
												@else
													<span class="text-gray-300">—</span>
												@endif
											</td>
											<td class="px-5 py-3">
												<div class="flex items-center gap-2">
													<div class="w-24 bg-gray-100 rounded-full h-1.5 overflow-hidden">
														<div class="h-1.5 rounded-full {{ $row->persen >= 100 ? 'bg-green-500' : 'bg-teal-500' }}"
															style="width: {{ $row->persen }}%"></div>
													</div>
													<span class="text-xs font-semibold text-gray-600">{{ $row->persen }}%</span>
												</div>
											</td>
											<td class="px-5 py-3">
												@if($row->isFinal)
													<a href="{{ route('verifikasi.show', ['periode' => $activePeriode->id, 'opd' => $row->opd->id]) }}"
														class="inline-flex items-center gap-1 text-xs text-teal-600 hover:text-teal-800 font-medium">
														Verifikasi
														<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
																d="M9 5l7 7-7 7" />
														</svg>
													</a>
												@endif
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
				@endif

			{{-- ========== VERIFIKATOR MENHAN STATS ========== --}}
			@if(in_array(auth()->user()->role, ['verifikator_menhan', 'admin']) && !empty($menhanStats))
				@php $m = $menhanStats; @endphp

				<div
					class="rounded-xl border px-5 py-4 flex items-center justify-between gap-4 {{ $m['isVerifActive'] ? 'bg-teal-50 border-teal-200' : 'bg-gray-50 border-gray-200' }}">
					<div class="flex items-center gap-3">
						<div class="flex-shrink-0">
							<svg class="w-6 h-6 {{ $m['isVerifActive'] ? 'text-teal-500' : 'text-gray-400' }}" fill="none"
								stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
							</svg>
						</div>
						<div>
							<p class="text-sm font-bold {{ $m['isVerifActive'] ? 'text-teal-800' : 'text-gray-700' }}">
								Dashboard Verifikasi Menhan — Periode: {{ $activePeriode->nama_periode }}
							</p>
							<p class="text-xs {{ $m['isVerifActive'] ? 'text-teal-700' : 'text-gray-500' }} mt-0.5">
								@if($m['startVerif'] && $m['endVerif'])
									Jadwal verifikasi: {{ $m['startVerif']->format('d M Y') }} s/d {{ $m['endVerif']->format('d M Y') }}
								@else
									Jadwal verifikasi belum ditentukan
								@endif
							</p>
						</div>
					</div>
					<div class="flex items-center gap-2 flex-shrink-0">
						@if($m['isVerifActive'])
							<span
								class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-teal-100 text-teal-800 border border-teal-200">
								<span class="w-1.5 h-1.5 bg-teal-500 rounded-full animate-pulse"></span> Verifikasi Aktif
							</span>
						@else
							<span
								class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
								<span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span> Di Luar Jadwal
							</span>
						@endif
						<a href="{{ route('verifikasi-menhan.index') }}"
							class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-teal-600 text-white rounded-lg text-xs font-semibold hover:bg-teal-700 transition-colors">
							Buka Verifikasi Menhan
							<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
							</svg>
						</a>
					</div>
				</div>

				<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
					<div class="bg-white rounded-xl p-5">
						<div class="flex items-center justify-between mb-3">
							<p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">OPD Siap Menhan</p>
							<div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
								<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
								</svg>
							</div>
						</div>
						<p class="text-3xl font-bold text-gray-900">{{ $m['opdSiapMenhan'] }}</p>
						<div class="flex items-center gap-3 mt-2 text-xs">
							<span class="text-green-600 font-semibold">{{ $m['opdSiapMenhan'] }} siap</span>
							<span class="text-gray-400">·</span>
							<span class="text-gray-500">{{ $m['opdBelumSiapMenhan'] }} belum</span>
						</div>
					</div>

					<div class="bg-white rounded-xl p-5 col-span-1 md:col-span-1">
						<div class="flex items-center justify-between mb-3">
							<p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Progress Menhan</p>
							<div class="w-8 h-8 rounded-lg bg-teal-50 flex items-center justify-center text-teal-600">
								<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
								</svg>
							</div>
						</div>
						<p class="text-3xl font-bold text-gray-900">{{ $m['persenVerifikasi'] }}%</p>
						<div class="mt-2 w-full bg-gray-100 rounded-full h-2 overflow-hidden">
							<div class="h-2 rounded-full {{ $m['persenVerifikasi'] >= 100 ? 'bg-green-500' : 'bg-teal-500' }}"
								style="width: {{ $m['persenVerifikasi'] }}%"></div>
						</div>
						<p class="text-xs text-gray-400 mt-1.5">{{ $m['totalDisetujui'] }} /
							{{ $m['totalJawaban'] }} disetujui
						</p>
					</div>

					<div class="bg-white rounded-xl p-5">
						<p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Status Menhan</p>
						<div class="space-y-2">
							<div class="flex items-center justify-between">
								<div class="flex items-center gap-1.5">
									<div class="w-2 h-2 rounded-full bg-green-500"></div>
									<span class="text-xs text-gray-600">Disetujui</span>
								</div>
								<span class="text-sm font-bold text-green-700">{{ $m['totalDisetujui'] }}</span>
							</div>
							<div class="flex items-center justify-between">
								<div class="flex items-center gap-1.5">
									<div class="w-2 h-2 rounded-full bg-gray-300"></div>
									<span class="text-xs text-gray-600">Belum Dicek</span>
								</div>
								<span class="text-sm font-bold text-gray-600">{{ $m['totalBelumDiverifikasi'] }}</span>
							</div>
						</div>
					</div>

					<div class="bg-white rounded-xl p-5">
						<div class="flex items-center justify-between mb-3">
							<p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">OPD Belum Siap</p>
							<div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-300">
								<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
								</svg>
							</div>
						</div>
						<p class="text-3xl font-bold text-gray-300">{{ $m['opdBelumSiapMenhan'] }}</p>
						<p class="text-xs text-gray-400 mt-1">menunggu verifikator</p>
					</div>
				</div>

				@if($m['opdProgressMenhan']->isNotEmpty())
					<div class="bg-white rounded-xl overflow-hidden">
						<div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
							<h3 class="text-sm font-semibold text-gray-900">Progress Verifikasi Menhan per OPD</h3>
							<span class="text-xs text-gray-400">{{ $activePeriode->nama_periode }}</span>
						</div>
						<div class="overflow-x-auto">
							<table class="w-full text-sm text-left text-gray-600">
								<thead class="bg-gray-50 border-b border-gray-100">
									<tr>
										<th class="px-5 py-3 font-medium text-gray-700">Nama OPD</th>
										<th class="px-5 py-3 font-medium text-gray-700 text-center">Status Verifikator</th>
										<th class="px-5 py-3 font-medium text-gray-700 text-center">Disetujui</th>
										<th class="px-5 py-3 font-medium text-gray-700 text-center">Belum</th>
										<th class="px-5 py-3 font-medium text-gray-700">Progress</th>
										<th class="px-5 py-3 font-medium text-gray-700"></th>
									</tr>
								</thead>
								<tbody class="divide-y divide-gray-100">
									@foreach($m['opdProgressMenhan'] as $row)
										<tr class="hover:bg-gray-50/50 transition-colors">
											<td class="px-5 py-3">
												<p class="font-medium text-gray-900 text-sm">{{ $row->opd->n_opd }}</p>
											</td>
											<td class="px-5 py-3 text-center">
												@if($row->isSiap)
													<span
														class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-green-100 text-green-700">
														<span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Siap
													</span>
												@else
													<span
														class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-500">
														Belum Siap
													</span>
												@endif
											</td>
											<td class="px-5 py-3 text-center"><span
													class="font-semibold text-green-700">{{ $row->disetujui }}</span></td>
											<td class="px-5 py-3 text-center"><span
													class="font-semibold text-gray-500">{{ $row->belum }}</span></td>
											<td class="px-5 py-3">
												<div class="flex items-center gap-2">
													<div class="w-24 bg-gray-100 rounded-full h-1.5 overflow-hidden">
														<div class="h-1.5 rounded-full {{ $row->persen >= 100 ? 'bg-green-500' : 'bg-teal-500' }}"
															style="width: {{ $row->persen }}%"></div>
													</div>
													<span class="text-xs font-semibold text-gray-600">{{ $row->persen }}%</span>
												</div>
											</td>
											<td class="px-5 py-3">
												@if($row->isSiap)
													<a href="{{ route('verifikasi-menhan.show', ['periode' => $activePeriode->id, 'opd' => $row->opd->id]) }}"
														class="inline-flex items-center gap-1 text-xs text-teal-600 hover:text-teal-800 font-medium">
														Verifikasi Menhan
														<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
																d="M9 5l7 7-7 7" />
														</svg>
													</a>
												@endif
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
				@endif
			@endif
			@endif
		@else
			<div class="bg-yellow-50 text-yellow-800 rounded-xl p-6 border border-yellow-200">
				<div class="flex items-center gap-3">
					<svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
						</path>
					</svg>
					<h3 class="font-semibold text-lg">Peringatan</h3>
				</div>
				<p class="mt-2">Belum ada periode evaluasi yang aktif. Silahkan atur periode aktif terlebih dahulu di menu
					manajemen periode.</p>
			</div>
		@endif
	</div>
@endsection
