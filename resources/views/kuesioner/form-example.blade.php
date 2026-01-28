{{--
    Contoh Implementasi Form Kuesioner dengan Parser Penjelasan
    Path: resources/views/kuesioner/form-example.blade.php
--}}

@extends('layouts.app')

@section('title', 'Form Kuesioner')
@section('page-title', 'Kuesioner Zona Integritas')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="bg-white rounded-xl p-6">
        <h2 class="text-xl font-semibold text-gray-800">Form Kuesioner WBK/WBBM</h2>
        <p class="text-sm text-gray-600 mt-1">Silakan jawab pertanyaan di bawah ini</p>
    </div>

    <form action="{{ route('kuesioner.submit') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Loop Through Pertanyaan --}}
        @foreach($pertanyaans as $pertanyaan)
            <div class="bg-white rounded-xl p-6">
                {{-- Nomor & Pertanyaan --}}
                <div class="mb-4">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-primary text-white text-sm font-semibold mr-3">
                        {{ $loop->iteration }}
                    </span>
                    <span class="text-base font-medium text-gray-800">{{ $pertanyaan->pertanyaan }}</span>
                </div>

                {{-- Ya/Tidak --}}
                @if($pertanyaan->tipe_jawaban === 'ya_tidak')
                    <div class="space-y-2 ml-11">
                        @foreach(['Ya', 'Tidak'] as $option)
                            <label class="flex items-center gap-3 p-3 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer transition">
                                <input
                                    type="radio"
                                    name="jawaban[{{ $pertanyaan->id }}]"
                                    value="{{ $option }}"
                                    class="w-4 h-4 text-primary focus:ring-primary"
                                    required
                                >
                                <span class="text-gray-700">{{ $option }}</span>
                            </label>
                        @endforeach
                    </div>
                @endif

                {{-- Pilihan Ganda dengan Parser --}}
                @if($pertanyaan->tipe_jawaban === 'pilihan_ganda')
                    <div class="space-y-2 ml-11">
                        @foreach($pertanyaan->penjelasan_list as $item)
                            <label class="flex items-start gap-3 p-4 border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-primary cursor-pointer transition group">
                                <input
                                    type="radio"
                                    name="jawaban[{{ $pertanyaan->id }}]"
                                    value="{{ $item['opsi'] }}"
                                    class="mt-1 w-4 h-4 text-primary focus:ring-primary"
                                    required
                                >
                                <div class="flex-1">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-primary text-white text-xs font-semibold mr-2 group-hover:bg-primary-dark transition">
                                        {{ $item['opsi'] }}
                                    </span>
                                    <span class="text-gray-700">{{ $item['text'] }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                @endif

                {{-- Penjelasan tambahan jika ada --}}
                @if($pertanyaan->tipe_jawaban === 'ya_tidak' && $pertanyaan->penjelasan)
                    <div class="mt-3 ml-11 p-3 bg-blue-50 rounded-lg">
                        <p class="text-xs text-blue-700">
                            <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            {{ $pertanyaan->penjelasan }}
                        </p>
                    </div>
                @endif
            </div>
        @endforeach

        {{-- Submit Button --}}
        <div class="flex gap-3 justify-end">
            <a href="{{ route('dashboard') }}" class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-primary text-white rounded-lg hover:bg-primary-dark font-medium transition">
                Simpan Jawaban
            </button>
        </div>
    </form>
</div>
@endsection
