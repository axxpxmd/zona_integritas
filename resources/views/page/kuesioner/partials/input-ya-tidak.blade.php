{{-- Input Ya/Tidak --}}
<div class="flex gap-4">
    <label class="flex items-center gap-2 cursor-pointer">
        <input type="radio"
               name="jawaban_{{ $pertanyaan->id }}"
               value="ya"
               {{ ($jawaban && $jawaban->jawaban_text === 'ya') ? 'checked' : '' }}
               class="jawaban-radio w-4 h-4 text-primary border-gray-300 focus:ring-2 focus:ring-primary/20"
               data-periode-id="{{ $periode->id }}"
               data-pertanyaan-id="{{ $pertanyaan->id }}">
        <span class="text-sm text-gray-700">Ya</span>
    </label>
    <label class="flex items-center gap-2 cursor-pointer">
        <input type="radio"
               name="jawaban_{{ $pertanyaan->id }}"
               value="tidak"
               {{ ($jawaban && $jawaban->jawaban_text === 'tidak') ? 'checked' : '' }}
               class="jawaban-radio w-4 h-4 text-primary border-gray-300 focus:ring-2 focus:ring-primary/20"
               data-periode-id="{{ $periode->id }}"
               data-pertanyaan-id="{{ $pertanyaan->id }}">
        <span class="text-sm text-gray-700">Tidak</span>
    </label>
</div>
