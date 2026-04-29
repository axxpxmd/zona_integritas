{{-- Input Pilihan Ganda --}}
@php
        $inputName = $inputName ?? ('jawaban[' . $pertanyaan->id . ']');
        $selectedValue = $selectedValue ?? ($jawaban->jawaban_text ?? null);
@endphp
<div class="space-y-0">
    @foreach($pertanyaan->penjelasan_list as $opsi)
    <label class="flex items-start ml-6 gap-3 p-3 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
        <input type="radio"
                             name="{{ $inputName }}"
               value="{{ $opsi['opsi'] }}"
                             {{ ($selectedValue === $opsi['opsi']) ? 'checked' : '' }}
             @if(!empty($isReadonly)) disabled @endif
               class="jawaban-radio mt-0.5 w-4 h-4 text-primary border-gray-300 focus:ring-2 focus:ring-primary/40"
               data-periode-id="{{ $periode->id }}"
               data-pertanyaan-id="{{ $pertanyaan->id }}">
        <div class="flex-1" style="margin-top: -4px">
            <span class="inline-flex items-center justify-center w-5 h-5 bg-primary/10 text-primary rounded text-xs font-bold mr-2">
                {{ $opsi['opsi'] }}
            </span>
            <span class="text-sm text-gray-700">{{ $opsi['text'] }}</span>
        </div>
    </label>
    @endforeach
</div>
