# Parser Penjelasan Pilihan Ganda - Dokumentasi

## Overview
Fitur ini memungkinkan penjelasan pilihan ganda yang tersimpan dalam format teks plain (misal: "a. Opsi A b. Opsi B c. Opsi C") untuk di-parse menjadi array terstruktur sehingga mudah ditampilkan sebagai list di front-end.

## Kenapa Pakai Parser Method?

✅ **Keuntungan:**
- Tidak perlu ubah data yang sudah ada (100+ pertanyaan)
- Backward compatible dengan format apapun
- Logic terpusat di Model
- Fleksibel & mudah di-maintain
- Bisa diperbaiki kapan saja tanpa migration

❌ **Tanpa parser, harus:**
- Update 100+ pertanyaan manual
- Buat migration untuk data existing
- Ribet maintenance di masa depan

---

## Implementasi

### 1. Model Pertanyaan.php
File: `app/Models/Pertanyaan.php`

```php
/**
 * Accessor: Parse penjelasan pilihan ganda menjadi array terstruktur
 */
public function getPenjelasanListAttribute()
{
    if (!$this->penjelasan || $this->tipe_jawaban !== 'pilihan_ganda') {
        return [];
    }
    
    $result = [];
    $text = $this->penjelasan;
    
    // Regex untuk match pattern: "a. Text..." sampai sebelum "b. Text..."
    preg_match_all('/([a-z])\.\s*(.+?)(?=\s+[a-z]\.|$)/is', $text, $matches, PREG_SET_ORDER);
    
    foreach ($matches as $match) {
        $result[] = [
            'opsi' => strtoupper($match[1]),  // A, B, C, dst
            'text' => trim($match[2])          // Text penjelasan
        ];
    }
    
    return $result;
}
```

**Regex Explanation:**
- `([a-z])\.` - Match huruf a-z diikuti titik (a., b., c.)
- `\s*` - Optional whitespace setelah titik
- `(.+?)` - Capture text penjelasan (non-greedy)
- `(?=\s+[a-z]\.|$)` - Lookahead: berhenti sebelum opsi berikutnya atau akhir string

### 2. Penggunaan di Controller
File: `app/Http/Controllers/KuesionerController.php`

```php
public function show($indikator_id)
{
    $pertanyaans = Pertanyaan::where('indikator_id', $indikator_id)
        ->where('status', 1)
        ->orderBy('urutan')
        ->get();
    
    // Accessor penjelasan_list otomatis tersedia!
    return view('kuesioner.form', compact('pertanyaans'));
}
```

### 3. Blade View
File: `resources/views/kuesioner/form-example.blade.php`

**Ya/Tidak:**
```blade
@if($pertanyaan->tipe_jawaban === 'ya_tidak')
    <div class="space-y-2">
        @foreach(['Ya', 'Tidak'] as $option)
            <label class="flex items-center gap-3 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                <input type="radio" name="jawaban[{{ $pertanyaan->id }}]" value="{{ $option }}" required>
                <span>{{ $option }}</span>
            </label>
        @endforeach
    </div>
@endif
```

**Pilihan Ganda (Dengan Parser):**
```blade
@if($pertanyaan->tipe_jawaban === 'pilihan_ganda')
    <div class="space-y-2">
        @foreach($pertanyaan->penjelasan_list as $item)
            <label class="flex items-start gap-3 p-4 border rounded-lg hover:bg-gray-50 cursor-pointer">
                <input 
                    type="radio" 
                    name="jawaban[{{ $pertanyaan->id }}]" 
                    value="{{ $item['opsi'] }}" 
                    required
                >
                <div class="flex-1">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-primary text-white text-xs font-semibold mr-2">
                        {{ $item['opsi'] }}
                    </span>
                    <span class="text-gray-700">{{ $item['text'] }}</span>
                </div>
            </label>
        @endforeach
    </div>
@endif
```

---

## Testing

### Test Script
File: `test_parser.php` (root folder)

```bash
php test_parser.php
```

**Output:**
```
✅ Berhasil di-parse: 3 opsi
  [A] Jika dengan prosedur/mekanisme yang jelas...
  [B] Jika sebagian menggunakan prosedur...
  [C] Jika tidak di seleksi.
```

### Unit Test Example
```php
// Test case 1: Format dengan spasi
$pertanyaan = Pertanyaan::find(2);
$parsed = $pertanyaan->penjelasan_list;

// Expected: [
//   ['opsi' => 'A', 'text' => 'Jika dengan prosedur...'],
//   ['opsi' => 'B', 'text' => 'Jika sebagian...'],
//   ['opsi' => 'C', 'text' => 'Jika tidak di seleksi.']
// ]

// Test case 2: Bukan pilihan ganda
$pertanyaan = Pertanyaan::where('tipe_jawaban', 'ya_tidak')->first();
$parsed = $pertanyaan->penjelasan_list;
// Expected: []
```

---

## Format Data yang Didukung

Parser dapat handle berbagai format:

**Format 1 - Spasi:**
```
a. Opsi pertama b. Opsi kedua c. Opsi ketiga
```

**Format 2 - Newline:**
```
a. Opsi pertama
b. Opsi kedua
c. Opsi ketiga
```

**Format 3 - Mixed:**
```
a. Opsi pertama dengan text panjang
b. Opsi kedua c. Opsi ketiga
```

**Format 4 - Capital (auto normalized):**
```
A. Opsi pertama B. Opsi kedua
→ Output tetap: ['opsi' => 'A', ...]
```

---

## Struktur Output

```php
$pertanyaan->penjelasan_list
// Returns:
[
    [
        'opsi' => 'A',                              // String: A, B, C, D, E
        'text' => 'Jika dengan prosedur yang jelas' // String: penjelasan lengkap
    ],
    [
        'opsi' => 'B',
        'text' => 'Jika sebagian menggunakan prosedur'
    ],
    // ...
]
```

---

## Tips & Best Practices

### 1. Validation di Controller
```php
public function submit(Request $request)
{
    $validated = $request->validate([
        'jawaban' => 'required|array',
        'jawaban.*' => 'required|in:A,B,C,D,E,Ya,Tidak',
    ]);
}
```

### 2. Dynamic Styling
```blade
{{-- Highlight selected option --}}
<label class="... @if(old('jawaban.'.$pertanyaan->id) == $item['opsi']) bg-blue-50 border-primary @endif">
```

### 3. Error Display
```blade
@error('jawaban.'.$pertanyaan->id)
    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
@enderror
```

### 4. Progress Tracking
```blade
{{-- Hitung pertanyaan terjawab --}}
@php
    $total = $pertanyaans->count();
    $answered = count(old('jawaban', []));
    $progress = $total > 0 ? round(($answered / $total) * 100) : 0;
@endphp

<div class="bg-gray-200 rounded-full h-2">
    <div class="bg-primary h-2 rounded-full" style="width: {{ $progress }}%"></div>
</div>
<p class="text-sm text-gray-600 mt-1">{{ $answered }}/{{ $total }} pertanyaan terjawab</p>
```

---

## FAQ

**Q: Bagaimana jika ada typo di data?**
A: Parser akan skip opsi yang tidak match pattern "a. ", "b. ", dst. Bisa di-fix di seeder lalu re-seed.

**Q: Apakah bisa format "1. ", "2. " instead of "a. ", "b. "?**
A: Ya, tinggal ubah regex di method: `/(\d+)\.\s*(.+?)(?=\s+\d+\.|$)/is`

**Q: Performance impact?**
A: Minimal. Parsing hanya saat accessor dipanggil, bukan di setiap query. Bisa di-cache jika perlu.

**Q: Bagaimana jika mau update format di masa depan?**
A: Cukup update method `getPenjelasanListAttribute()` di Model. Tidak perlu touch database!

---

## File-file Terkait

```
app/
├── Models/
│   └── Pertanyaan.php                    # Model dengan parser method
└── Http/
    └── Controllers/
        └── KuesionerController.php       # Controller example

resources/
└── views/
    └── kuesioner/
        └── form-example.blade.php        # View example dengan styling

test_parser.php                           # Test script untuk verifikasi

database/
└── seeders/
    └── PertanyaanSeeder.php              # Data source (tidak perlu diubah)
```

---

## Kesimpulan

✅ Parser method adalah solusi **paling efisien** untuk case ini
✅ **Tidak perlu** update 100+ pertanyaan di database
✅ **Backward compatible** dengan format apapun
✅ **Mudah maintain** - logic terpusat di Model
✅ **Fleksibel** - bisa diperbaiki/ditambah fitur kapan saja

Implementasi sudah selesai dan siap digunakan! 🚀
