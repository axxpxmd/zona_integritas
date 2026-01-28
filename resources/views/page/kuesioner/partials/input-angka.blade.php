{{-- Input Angka --}}
<div>
    <input type="number"
           step="0.01"
           class="jawaban-angka w-full max-w-xs px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"
           placeholder="Masukkan angka..."
           value="{{ $jawaban->jawaban_angka ?? '' }}"
           data-periode-id="{{ $periode->id }}"
           data-pertanyaan-id="{{ $pertanyaan->id }}"
           data-sub-pertanyaan-id="">
    @if($pertanyaan->satuan)
    <span class="text-xs text-gray-500 ml-2">{{ $pertanyaan->satuan }}</span>
    @endif
</div>
