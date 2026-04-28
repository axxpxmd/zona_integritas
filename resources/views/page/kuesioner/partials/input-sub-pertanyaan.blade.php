{{-- Input Sub Pertanyaan (untuk pertanyaan dengan formula) --}}
<div class="space-y-3">
    @foreach($pertanyaan->subPertanyaans as $index => $subPertanyaan)
    @php
        // Auto sum jika element ini memiliki formula
        $isAutoSum = !empty($subPertanyaan->formula);
        // Element lain dianggap part dari sum jika ada element yang memiliki formula di pertanyaan ini
        $hasAnyFormula = $pertanyaan->subPertanyaans->where('formula', '!=', null)->count() > 0;
        $isPartSum = !$isAutoSum && $hasAnyFormula && !$loop->last;
    @endphp
    <div class="flex items-center gap-4 ml-9">
        <label class="text-sm text-gray-700 w-48 flex-shrink-0">
            {{ $subPertanyaan->pertanyaan }}
        </label>
        <input type="number"
               name="jawaban_sub[{{ $pertanyaan->id }}][{{ $subPertanyaan->id }}]"
               step="{{ $subPertanyaan->tipe_input === 'desimal' ? '0.01' : '1' }}"
               class="jawaban-angka-sub flex-1 max-w-xs px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none {{ $isAutoSum ? 'bg-gray-100 cursor-not-allowed sum-target-' . $pertanyaan->id : '' }} {{ $isPartSum ? 'sum-part-' . $pertanyaan->id : '' }}"
               placeholder="{{ $isAutoSum ? 'Otomatis' : 'Masukkan ' . $subPertanyaan->tipe_input . '...' }}"
               value="{{ $jawabans[$pertanyaan->id . '-' . $subPertanyaan->id]->jawaban_angka ?? '' }}"
               data-periode-id="{{ $periode->id }}"
               data-pertanyaan-id="{{ $pertanyaan->id }}"
               data-sub-pertanyaan-id="{{ $subPertanyaan->id }}"
               {{ $isAutoSum ? 'readonly tabindex="-1"' : '' }}>
        @if($subPertanyaan->satuan)
        <span class="text-xs text-gray-500">{{ $subPertanyaan->satuan }}</span>
        @endif
    </div>
    @endforeach

    @if($pertanyaan->formula)
    <div class="mt-3 pt-3 border-t border-gray-200">
        <p class="text-xs text-gray-600">
            <span class="font-medium">Formula:</span> {{ $pertanyaan->formula }}
        </p>
    </div>
    @endif
</div>
