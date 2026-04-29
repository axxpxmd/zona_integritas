{{-- Input Ya/Tidak --}}
@php
    $inputName = $inputName ?? ('jawaban[' . $pertanyaan->id . ']');
    $selectedValue = $selectedValue ?? ($jawaban->jawaban_text ?? null);
@endphp
<div class="flex gap-4">
    <label class="flex items-center ml-9 gap-2 cursor-pointer">
        <input type="radio"
               name="{{ $inputName }}"
               value="ya"
               {{ ($selectedValue === 'ya') ? 'checked' : '' }}
             @if(!empty($isReadonly)) disabled @endif
               class="jawaban-radio w-4 h-4 text-primary border-gray-300 focus:ring-2 focus:ring-primary/20"
               data-periode-id="{{ $periode->id }}"
               data-pertanyaan-id="{{ $pertanyaan->id }}">
        <span class="text-sm text-gray-700">Ya</span>
    </label>
    <label class="flex items-center gap-2 cursor-pointer">
        <input type="radio"
               name="{{ $inputName }}"
               value="tidak"
               {{ ($selectedValue === 'tidak') ? 'checked' : '' }}
             @if(!empty($isReadonly)) disabled @endif
               class="jawaban-radio w-4 h-4 text-primary border-gray-300 focus:ring-2 focus:ring-primary/20"
               data-periode-id="{{ $periode->id }}"
               data-pertanyaan-id="{{ $pertanyaan->id }}">
        <span class="text-sm text-gray-700">Tidak</span>
    </label>
</div>
