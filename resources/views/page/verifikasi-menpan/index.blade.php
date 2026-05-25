@extends('layouts.app')

@section('title', 'Verifikasi Menpan')
@section('page-title', 'Verifikasi Menpan')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Daftar Unit Kerja Siap Verifikasi Menpan</h2>
                <p class="text-sm text-gray-500 mt-1">Hanya Unit Kerja yang sudah dikirim oleh verifikator akan muncul di sini.</p>
            </div>
        </div>

        {{-- Filter --}}
        <div class="bg-white rounded-xl p-4">
            <form action="{{ route('verifikasi-menpan.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
                <div class="w-full sm:w-64">
                    <label for="periode_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Periode</label>
                    <select name="periode_id" id="periode_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary text-sm"
                        onchange="this.form.submit()">
                        @if($periodes->isEmpty())
                            <option value="">Tidak ada periode aktif</option>
                        @else
                            @foreach($periodes as $p)
                                <option value="{{ $p->id }}" {{ $activePeriode && $activePeriode->id == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama_periode }} ({{ $p->tahun }})
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                @if($activePeriode)
                    <div class="w-full sm:w-auto text-sm text-gray-600 bg-gray-50 px-4 py-2 rounded-lg border border-gray-200 mb-0">
                        <span class="font-medium text-gray-900">Total OPD:</span> {{ $submittedOpds->count() }}
                    </div>
                @endif
            </form>
        </div>

        {{-- Daftar OPD --}}
        @if($activePeriode)
            @if($submittedOpds->isEmpty())
                <div class="bg-white rounded-xl p-8 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-medium text-gray-900">Belum Ada Data</h3>
                    <p class="text-sm text-gray-500 mt-1">Belum ada Unit Kerja yang siap diverifikasi oleh Menpan.</p>
                </div>
            @else
                <div class="bg-white rounded-xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">No</th>
                                    <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Kerja</th>
                                    <th scope="col" class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Terverifikasi</th>
                                    <th scope="col" class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Belum</th>
                                    <th scope="col" class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                                    <th scope="col" class="px-5 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($submittedOpds as $index => $opd)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-5 py-3 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                                        <td class="px-5 py-3">
                                            <div class="text-sm font-medium text-gray-900">{{ $opd->n_opd }}</div>
                                            <div class="text-xs text-gray-500 mt-0.5">Dikirim: {{ \Carbon\Carbon::parse($opd->submitted_at)->format('d M Y, H:i') }} WIB</div>
                                        </td>
                                        <td class="px-5 py-3 text-center">
                                            <div class="flex flex-col items-center justify-center gap-1.5">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-100 text-blue-800">
                                                    Terkirim ke Menpan
                                                </span>
                                                <span class="text-[10px] text-gray-500 font-medium">Terisi: {{ $opd->total_jawaban }}/{{ $opd->total_jawaban }}</span>
                                            </div>
                                        </td>
                                        <td class="px-5 py-3 text-center"><span class="font-semibold text-green-700">{{ $opd->terverifikasi }}</span></td>
                                        <td class="px-5 py-3 text-center"><span class="font-semibold text-gray-500">{{ $opd->belum_terverifikasi }}</span></td>
                                        <td class="px-5 py-3">
                                            <div class="flex items-center gap-2">
                                                <div class="w-24 bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                                    <div class="h-1.5 rounded-full {{ $opd->persen >= 100 ? 'bg-green-500' : 'bg-teal-500' }}" style="width: {{ $opd->persen }}%"></div>
                                                </div>
                                                <span class="text-xs font-semibold text-gray-600">{{ $opd->persen }}%</span>
                                            </div>
                                        </td>
                                        <td class="px-5 py-3 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('verifikasi-menpan.show', [$activePeriode->id, $opd->id]) }}"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors text-xs">
                                                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                Verifikasi Menpan
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-8 text-center">
                <h3 class="text-sm font-medium text-yellow-800">Pilih Periode Terlebih Dahulu</h3>
            </div>
        @endif
    </div>
@endsection
