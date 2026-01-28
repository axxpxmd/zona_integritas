{{-- Input Pilihan Ganda --}}
<div class="space-y-2">
    @foreach($pertanyaan->penjelasan_list as $opsi)
    <label class="flex items-start gap-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
        <input type="radio"
               name="jawaban_{{ $pertanyaan->id }}"
               value="{{ $opsi['opsi'] }}"
               {{ ($jawaban && $jawaban->jawaban_text === $opsi['opsi']) ? 'checked' : '' }}
               class="jawaban-radio mt-0.5 w-4 h-4 text-primary border-gray-300 focus:ring-2 focus:ring-primary/20"
               data-periode-id="{{ $periode->id }}"
               data-pertanyaan-id="{{ $pertanyaan->id }}">
        <div class="flex-1">
            <span class="inline-flex items-center justify-center w-5 h-5 bg-primary/10 text-primary rounded text-xs font-bold mr-2">
                {{ $opsi['opsi'] }}
            </span>
            <span class="text-sm text-gray-700">{{ $opsi['text'] }}</span>
        </div>
    </label>
    @endforeach
</div>
