@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
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
</div>
@endsection
