@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
	<!-- Welcome Card -->
	<div class="bg-white rounded-xl p-6 md:p-8">
		<div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
			<div class="flex items-center gap-5">
				<div class="hidden sm:flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-full bg-primary-light text-primary">
					<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7">
						<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
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

			<div class="inline-flex items-center gap-2 rounded-full bg-primary-light px-4 py-2 text-sm font-semibold text-primary">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
					<path fill-rule="evenodd" d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.057-1.35-.166-1.998A11.954 11.954 0 0110 1.944zM8.504 12.39L6.101 9.987a.75.75 0 011.06-1.061l1.343 1.344 3.125-3.126a.75.75 0 011.06 1.061l-3.656 3.655a.75.75 0 01-1.06 0z" clip-rule="evenodd" />
				</svg>
				Role Aktif: {{ $roleLabel }}
			</div>
		</div>
	</div>

	@if($activePeriode)
		@if(auth()->user()->role === 'admin')
		<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
			<!-- Total OPD -->
			<div class="bg-white rounded-xl p-6 flex items-center justify-between">
				<div>
					<p class="text-sm font-medium text-gray-500">Total OPD</p>
					<h3 class="mt-2 text-3xl font-bold text-gray-900">{{ $totalOpd }}</h3>
				</div>
				<div class="h-12 w-12 flex items-center justify-center rounded-full bg-blue-100 text-blue-600">
					<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
				</div>
			</div>

			<!-- Selesai -->
			<div class="bg-white rounded-xl p-6 flex items-center justify-between">
				<div>
					<p class="text-sm font-medium text-gray-500">Selesai (100%)</p>
					<h3 class="mt-2 text-3xl font-bold text-gray-900">{{ $opdCompleted }}</h3>
				</div>
				<div class="h-12 w-12 flex items-center justify-center rounded-full bg-green-100 text-green-600">
					<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
				</div>
			</div>

			<!-- Proses -->
			<div class="bg-white rounded-xl p-6 flex items-center justify-between">
				<div>
					<p class="text-sm font-medium text-gray-500">Dalam Proses</p>
					<h3 class="mt-2 text-3xl font-bold text-gray-900">{{ $opdInProgress }}</h3>
				</div>
				<div class="h-12 w-12 flex items-center justify-center rounded-full bg-yellow-100 text-yellow-600">
					<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
				</div>
			</div>

			<!-- Belum -->
			<div class="bg-white rounded-xl p-6 flex items-center justify-between">
				<div>
					<p class="text-sm font-medium text-gray-500">Belum Mengisi</p>
					<h3 class="mt-2 text-3xl font-bold text-gray-900">{{ $opdNotStarted }}</h3>
				</div>
				<div class="h-12 w-12 flex items-center justify-center rounded-full bg-gray-100 text-gray-600">
					<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
				</div>
			</div>
		</div>

		<!-- Progress OPD Table -->
		<div class="bg-white rounded-xl overflow-hidden mt-6">
			<div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
				<h3 class="text-lg font-semibold text-gray-900">Progress Pengisian Kuesioner (Periode: {{ $activePeriode->nama_periode }})</h3>
				<span class="text-sm text-gray-500">Total Pertanyaan: {{ $totalRequired }}</span>
			</div>

			<div class="overflow-x-auto">
				<table class="w-full text-left text-sm text-gray-600">
					<thead class="bg-gray-50 border-b border-gray-100">
						<tr>
							<th class="px-6 py-4 font-medium text-gray-700">Nama OPD</th>
							<th class="px-6 py-4 font-medium text-gray-700">Terisi / Total</th>
							<th class="px-6 py-4 font-medium text-gray-700">Persentase</th>
							<th class="px-6 py-4 font-medium text-gray-700">Status</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-100">
						@forelse($opdProgress as $progress)
							<tr class="hover:bg-gray-50/50 transition-colors">
								<td class="px-6 py-4">
									<p class="font-medium text-gray-900">{{ $progress->opd->n_opd }}</p>
								</td>
								<td class="px-6 py-4">{{ $progress->terisi }} / {{ $progress->total }}</td>
								<td class="px-6 py-4">
									<div class="flex items-center gap-3">
										<div class="w-full bg-gray-200 rounded-full h-2 max-w-[150px]">
											<div class="bg-{{ $progress->color }}-500 h-2 rounded-full" style="width: {{ $progress->persentase }}%"></div>
										</div>
										<span class="text-sm font-medium text-gray-700">{{ $progress->persentase }}%</span>
									</div>
								</td>
								<td class="px-6 py-4">
									<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-{{ $progress->color }}-100 text-{{ $progress->color }}-700 border border-{{ $progress->color }}-200">
										{{ $progress->status }}
									</span>
								</td>
							</tr>
						@empty
							<tr>
								<td colspan="4" class="px-6 py-8 text-center text-gray-500">
									Belum ada data OPD yang aktif.
								</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>
		</div>
		@endif
	@else
		<div class="bg-yellow-50 text-yellow-800 rounded-xl p-6 border border-yellow-200">
			<div class="flex items-center gap-3">
				<svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
				<h3 class="font-semibold text-lg">Peringatan</h3>
			</div>
			<p class="mt-2">Belum ada periode evaluasi yang aktif. Silahkan atur periode aktif terlebih dahulu di menu manajemen periode.</p>
		</div>
	@endif
</div>
@endsection
