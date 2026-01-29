@extends('layouts.app')

@section('title', 'Data Sub Pertanyaan')
@section('page-title', 'Data Sub Pertanyaan')

@section('content')
<div class="space-y-6">
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Data Sub Pertanyaan</h2>
            <p class="text-sm text-gray-600 mt-1">Kelola data sub pertanyaan untuk perhitungan detail</p>
        </div>
        <a href="{{ route('sub-pertanyaan.create') }}"
           class="inline-flex items-center justify-center gap-2 bg-primary text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-primary-dark transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Sub Pertanyaan
        </a>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-start gap-3">
        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-start gap-3">
        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Sub Pertanyaan</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $total }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Aktif</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $aktif }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Tidak Aktif</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $tidakAktif }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="bg-white rounded-xl p-5">
        <form method="GET" action="{{ route('sub-pertanyaan.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                <div class="lg:col-span-3">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari sub pertanyaan, kode, penjelasan, atau formula..."
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                </div>
                <select name="komponen_id" id="komponen_filter"
                        class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                    <option value="">Semua Komponen</option>
                    @foreach($komponens as $komp)
                        <option value="{{ $komp->id }}" {{ request('komponen_id') == $komp->id ? 'selected' : '' }}>
                            {{ $komp->kode }} - {{ $komp->nama }}
                        </option>
                    @endforeach
                </select>
                <select name="kategori_id" id="kategori_filter"
                        class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $kat)
                        <option value="{{ $kat->id }}"
                                data-komponen="{{ $kat->komponen_id }}"
                                {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>
                            {{ $kat->komponen->kode }}.{{ $kat->kode }} - {{ $kat->nama }}
                        </option>
                    @endforeach
                </select>
                <select name="sub_kategori_id" id="sub_kategori_filter"
                        class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                    <option value="">Semua Sub Kategori</option>
                    @foreach($subKategoris as $subKat)
                        <option value="{{ $subKat->id }}"
                                data-komponen="{{ $subKat->kategori->komponen_id }}"
                                data-kategori="{{ $subKat->kategori_id }}"
                                {{ request('sub_kategori_id') == $subKat->id ? 'selected' : '' }}>
                            {{ $subKat->kategori->komponen->kode }}.{{ $subKat->kategori->kode }}.{{ $subKat->kode }} - {{ $subKat->nama }}
                        </option>
                    @endforeach
                </select>
                <select name="indikator_id" id="indikator_filter"
                        class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                    <option value="">Semua Indikator</option>
                    @foreach($indikators as $ind)
                        <option value="{{ $ind->id }}"
                                data-komponen="{{ $ind->subKategori->kategori->komponen_id }}"
                                data-kategori="{{ $ind->subKategori->kategori_id }}"
                                data-subkategori="{{ $ind->sub_kategori_id }}"
                                {{ request('indikator_id') == $ind->id ? 'selected' : '' }}>
                            {{ $ind->kode }} - {{ $ind->nama }}
                        </option>
                    @endforeach
                </select>
                <select name="pertanyaan_id" id="pertanyaan_filter"
                        class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                    <option value="">Semua Pertanyaan</option>
                    @foreach($pertanyaans as $pert)
                        <option value="{{ $pert->id }}"
                                data-komponen="{{ $pert->indikator->subKategori->kategori->komponen_id }}"
                                data-kategori="{{ $pert->indikator->subKategori->kategori_id }}"
                                data-subkategori="{{ $pert->indikator->sub_kategori_id }}"
                                data-indikator="{{ $pert->indikator_id }}"
                                {{ request('pertanyaan_id') == $pert->id ? 'selected' : '' }}>
                            {{ $pert->kode }} - {{ Str::limit($pert->pertanyaan, 50) }}
                        </option>
                    @endforeach
                </select>
                <select name="tipe_input"
                        class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                    <option value="">Semua Tipe Input</option>
                    <option value="jumlah" {{ request('tipe_input') == 'jumlah' ? 'selected' : '' }}>Jumlah</option>
                    <option value="persen" {{ request('tipe_input') == 'persen' ? 'selected' : '' }}>Persen (%)</option>
                    <option value="angka" {{ request('tipe_input') == 'angka' ? 'selected' : '' }}>Angka</option>
                    <option value="teks" {{ request('tipe_input') == 'teks' ? 'selected' : '' }}>Teks</option>
                </select>
                <select name="status"
                        class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                    <option value="">Semua Status</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
            </div>
            <div class="flex flex-wrap gap-3">
                <button type="submit"
                        class="px-5 py-2.5 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary-dark transition-colors">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'komponen_id', 'kategori_id', 'sub_kategori_id', 'indikator_id', 'pertanyaan_id', 'tipe_input', 'status']))
                <a href="{{ route('sub-pertanyaan.index') }}"
                   class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Data Table (Desktop) --}}
    <div class="bg-white rounded-xl overflow-hidden hidden lg:block">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Kode</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Sub Pertanyaan</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Pertanyaan Utama</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Tipe</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Satuan</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($subPertanyaans as $subPertanyaan)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-100 text-blue-700">
                                {{ $subPertanyaan->kode }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <p class="font-medium text-gray-900 text-sm line-clamp-2">{{ $subPertanyaan->pertanyaan }}</p>
                            @if($subPertanyaan->formula)
                            <p class="text-xs text-orange-600 mt-1">
                                <span class="font-mono">{{ $subPertanyaan->formula }}</span>
                            </p>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <p class="text-sm font-medium text-gray-900 line-clamp-1">{{ $subPertanyaan->pertanyaanUtama->pertanyaan }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $subPertanyaan->pertanyaanUtama->indikator->nama }}</p>
                        </td>
                        <td class="px-4 py-4 text-center">
                            @if($subPertanyaan->tipe_input === 'jumlah')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                Jumlah
                            </span>
                            @elseif($subPertanyaan->tipe_input === 'persen')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                Persen
                            </span>
                            @elseif($subPertanyaan->tipe_input === 'angka')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                                Angka
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                Teks
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-center">
                            @if($subPertanyaan->satuan)
                            <span class="text-sm font-medium text-gray-700">{{ $subPertanyaan->satuan }}</span>
                            @else
                            <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-center">
                            @if($subPertanyaan->status == 1)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                Aktif
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>
                                Tidak Aktif
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('sub-pertanyaan.show', $subPertanyaan) }}"
                                   class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                   title="Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('sub-pertanyaan.edit', $subPertanyaan) }}"
                                   class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors"
                                   title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('sub-pertanyaan.destroy', $subPertanyaan) }}"
                                      method="POST"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus sub pertanyaan ini?')"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                            title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center">
                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="text-gray-500 font-medium">Tidak ada data sub pertanyaan</p>
                            <p class="text-sm text-gray-400 mt-1">Silakan tambah data sub pertanyaan baru</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Data Cards (Mobile) --}}
    <div class="grid grid-cols-1 gap-4 lg:hidden">
        @forelse($subPertanyaans as $subPertanyaan)
        <div class="bg-white rounded-xl p-4 space-y-3">
            <div class="flex items-start justify-between gap-3">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700">
                            {{ $subPertanyaan->kode }}
                        </span>
                        @if($subPertanyaan->tipe_input === 'jumlah')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">Jumlah</span>
                        @elseif($subPertanyaan->tipe_input === 'persen')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Persen</span>
                        @elseif($subPertanyaan->tipe_input === 'angka')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700">Angka</span>
                        @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">Teks</span>
                        @endif
                        @if($subPertanyaan->satuan)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">
                            {{ $subPertanyaan->satuan }}
                        </span>
                        @endif
                    </div>
                    <h3 class="font-semibold text-gray-900 line-clamp-2">{{ $subPertanyaan->pertanyaan }}</h3>
                    <p class="text-xs text-gray-500 mt-1 line-clamp-1">{{ $subPertanyaan->pertanyaanUtama->pertanyaan }}</p>
                    @if($subPertanyaan->formula)
                    <p class="text-xs text-orange-600 mt-1 font-mono">{{ $subPertanyaan->formula }}</p>
                    @endif
                </div>
                @if($subPertanyaan->status == 1)
                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                    Aktif
                </span>
                @else
                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>
                    Tidak Aktif
                </span>
                @endif
            </div>

            <div class="flex items-center gap-2 pt-2">
                <a href="{{ route('sub-pertanyaan.show', $subPertanyaan) }}"
                   class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-blue-50 text-blue-700 rounded-lg text-sm font-medium hover:bg-blue-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Detail
                </a>
                <a href="{{ route('sub-pertanyaan.edit', $subPertanyaan) }}"
                   class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-yellow-50 text-yellow-700 rounded-lg text-sm font-medium hover:bg-yellow-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
                <form action="{{ route('sub-pertanyaan.destroy', $subPertanyaan) }}"
                      method="POST"
                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus sub pertanyaan ini?')"
                      class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="p-2 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl p-8 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-gray-500 font-medium">Tidak ada data sub pertanyaan</p>
            <p class="text-sm text-gray-400 mt-1">Silakan tambah data sub pertanyaan baru</p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($subPertanyaans->hasPages())
    <div class="bg-white rounded-xl p-4">
        {{ $subPertanyaans->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
// 5-level cascading filter: Komponen -> Kategori -> Sub Kategori -> Indikator -> Pertanyaan
const komponenFilter = document.getElementById('komponen_filter');
const kategoriFilter = document.getElementById('kategori_filter');
const subKategoriFilter = document.getElementById('sub_kategori_filter');
const indikatorFilter = document.getElementById('indikator_filter');
const pertanyaanFilter = document.getElementById('pertanyaan_filter');

// Filter kategori berdasarkan komponen
komponenFilter?.addEventListener('change', function() {
    const komponenId = this.value;
    filterCascade(komponenId, null, null, null);
});

// Filter sub kategori & indikator berdasarkan kategori
kategoriFilter?.addEventListener('change', function() {
    const komponenId = komponenFilter.value;
    const kategoriId = this.value;
    filterCascade(komponenId, kategoriId, null, null);
});

// Filter indikator & pertanyaan berdasarkan sub kategori
subKategoriFilter?.addEventListener('change', function() {
    const komponenId = komponenFilter.value;
    const kategoriId = kategoriFilter.value;
    const subKategoriId = this.value;
    filterCascade(komponenId, kategoriId, subKategoriId, null);
});

// Filter pertanyaan berdasarkan indikator
indikatorFilter?.addEventListener('change', function() {
    const komponenId = komponenFilter.value;
    const kategoriId = kategoriFilter.value;
    const subKategoriId = subKategoriFilter.value;
    const indikatorId = this.value;
    filterCascade(komponenId, kategoriId, subKategoriId, indikatorId);
});

function filterCascade(komponenId, kategoriId, subKategoriId, indikatorId) {
    // Filter Kategori
    if (komponenId !== null) {
        filterOptions(kategoriFilter, komponenId, 'data-komponen');
        if (komponenId) kategoriFilter.value = '';
    }

    // Filter Sub Kategori
    if (kategoriId !== null) {
        const subKatOptions = subKategoriFilter.querySelectorAll('option');
        subKatOptions.forEach(option => {
            if (option.value === '') {
                option.style.display = 'block';
                return;
            }
            const optKomp = option.getAttribute('data-komponen');
            const optKat = option.getAttribute('data-kategori');
            let show = true;
            if (komponenId && optKomp !== komponenId) show = false;
            if (kategoriId && optKat !== kategoriId) show = false;
            option.style.display = show ? 'block' : 'none';
        });
        if (kategoriId || komponenId) subKategoriFilter.value = '';
    }

    // Filter Indikator
    if (subKategoriId !== null) {
        const indOptions = indikatorFilter.querySelectorAll('option');
        indOptions.forEach(option => {
            if (option.value === '') {
                option.style.display = 'block';
                return;
            }
            const optKomp = option.getAttribute('data-komponen');
            const optKat = option.getAttribute('data-kategori');
            const optSubKat = option.getAttribute('data-subkategori');
            let show = true;
            if (komponenId && optKomp !== komponenId) show = false;
            if (kategoriId && optKat !== kategoriId) show = false;
            if (subKategoriId && optSubKat !== subKategoriId) show = false;
            option.style.display = show ? 'block' : 'none';
        });
        if (subKategoriId || kategoriId || komponenId) indikatorFilter.value = '';
    }

    // Filter Pertanyaan
    if (indikatorId !== null || subKategoriId !== null) {
        const pertOptions = pertanyaanFilter.querySelectorAll('option');
        pertOptions.forEach(option => {
            if (option.value === '') {
                option.style.display = 'block';
                return;
            }
            const optKomp = option.getAttribute('data-komponen');
            const optKat = option.getAttribute('data-kategori');
            const optSubKat = option.getAttribute('data-subkategori');
            const optInd = option.getAttribute('data-indikator');
            let show = true;
            if (komponenId && optKomp !== komponenId) show = false;
            if (kategoriId && optKat !== kategoriId) show = false;
            if (subKategoriId && optSubKat !== subKategoriId) show = false;
            if (indikatorId && optInd !== indikatorId) show = false;
            option.style.display = show ? 'block' : 'none';
        });
        if (indikatorId || subKategoriId || kategoriId || komponenId) pertanyaanFilter.value = '';
    }
}

function filterOptions(selectElement, value, attribute) {
    const options = selectElement.querySelectorAll('option');
    options.forEach(option => {
        if (option.value === '') {
            option.style.display = 'block';
            return;
        }
        const optionValue = option.getAttribute(attribute);
        option.style.display = (value === '' || optionValue === value) ? 'block' : 'none';
    });
}
</script>
@endpush
@endsection
