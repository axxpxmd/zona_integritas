# Dokumentasi Fitur Kuesioner

## Overview
Sistem kuesioner untuk pengisian Zona Integritas (WBK/WBBM) dengan fitur auto-save untuk mencegah kehilangan data saat pengisian.

## Fitur Utama

### 1. **Pilih Periode**
- User harus memilih periode/tahun terlebih dahulu sebelum mengisi kuesioner
- Hanya menampilkan periode dengan status `aktif` atau `selesai`
- Tidak menampilkan periode template

### 2. **Auto-Save**
- Jawaban tersimpan otomatis setiap kali user mengisi
- Delay 1 detik setelah user berhenti mengetik/memilih
- Indikator visual "Menyimpan..." dan "Tersimpan ✓"
- Data tidak hilang meskipun internet terputus sementara

### 3. **Accordion Navigation**
- Struktur hierarchy: Komponen → Kategori → Sub Kategori → Indikator → Pertanyaan
- Kategori bisa di-expand/collapse untuk navigasi lebih mudah
- User tidak perlu scroll terlalu jauh

### 4. **Tipe Input**
- **Ya/Tidak**: Radio button
- **Pilihan Ganda**: Parsed dari text dengan opsi A, B, C, D
- **Angka**: Number input (untuk pertanyaan tunggal)
- **Sub-Pertanyaan**: Multiple number inputs (untuk pertanyaan dengan formula)
- **Keterangan**: Optional textarea untuk catatan tambahan

## Struktur Database

### Tabel `jawaban`
```sql
- id (primary key)
- periode_id (FK ke tm_periode)
- opd_id (FK ke tm_opd)
- pertanyaan_id (FK ke tm_pertanyaan)
- sub_pertanyaan_id (FK ke tm_sub_pertanyaan, nullable)
- jawaban_text (untuk ya_tidak, pilihan_ganda)
- jawaban_angka (untuk angka, desimal)
- keterangan (catatan opsional)
- status (draft/final)
- created_by (FK ke users)
- updated_by (FK ke users)
- timestamps

UNIQUE KEY: periode_id + opd_id + pertanyaan_id + sub_pertanyaan_id
```

## Routes

```php
// Pilih Periode
GET /kuesioner - kuesioner.index
- Menampilkan daftar periode yang tersedia

// Form Kuesioner
GET /kuesioner/{periode} - kuesioner.show
- Menampilkan form kuesioner untuk periode tertentu
- Requires: user sudah login dan terhubung dengan OPD

// Auto-Save (AJAX)
POST /kuesioner/auto-save - kuesioner.auto-save
- Menyimpan jawaban secara otomatis
- Request: periode_id, pertanyaan_id, sub_pertanyaan_id, jawaban_text, jawaban_angka, keterangan
```

## Controllers

### `KuesionerController`

**Method `index()`**
- Menampilkan daftar periode yang available (aktif/selesai, bukan template)
- View: `page.kuesioner.index`

**Method `show($periode_id)`**
- Validasi user terhubung dengan OPD
- Load struktur lengkap: Komponen → Kategori → SubKategori → Indikator → Pertanyaan → SubPertanyaan
- Load jawaban yang sudah ada (untuk continue filling)
- View: `page.kuesioner.form`

**Method `autoSave(Request $request)`**
- Menerima AJAX request
- Validasi input
- UpdateOrCreate jawaban (upsert)
- Return JSON response

## Views Structure

```
resources/views/page/kuesioner/
├── index.blade.php                      # Pilih periode
├── form.blade.php                       # Form kuesioner utama
└── partials/
    ├── input-ya-tidak.blade.php        # Radio Ya/Tidak
    ├── input-pilihan-ganda.blade.php   # Radio multiple choice
    ├── input-angka.blade.php           # Number input single
    └── input-sub-pertanyaan.blade.php  # Number inputs multiple (formula)
```

## JavaScript Auto-Save Logic

```javascript
// Event listeners
- Radio buttons: change event
- Number inputs: input event (debounced 1 second)
- Textareas: input event (debounced 1 second)

// Flow
1. User mengisi input
2. Trigger auto-save function dengan delay 1 detik
3. Show "Menyimpan..." indicator
4. AJAX POST ke /kuesioner/auto-save
5. Success: Show "Tersimpan ✓" (hilang dalam 2 detik)
6. Error: Hide indicator, log to console
```

## Model `Jawaban`

```php
class Jawaban extends Model
{
    // Relationships
    - periode()
    - opd()
    - pertanyaan()
    - subPertanyaan()
    - createdBy()
    - updatedBy()
    
    // Casts
    - jawaban_angka: decimal:2
}
```

## Data Flow

1. **User Access**: `/kuesioner`
   - Pilih periode dari list yang tersedia

2. **Form Display**: `/kuesioner/{periode_id}`
   - Load pertanyaan 100+ dengan hierarchy
   - Load jawaban existing (jika ada)
   - Pre-fill form dengan jawaban existing

3. **User Fills**: Input data
   - Setiap input trigger auto-save (debounced 1s)
   - AJAX POST ke `/kuesioner/auto-save`
   - Update/Insert ke table `jawaban`

4. **Resume Session**:
   - User bisa close browser
   - Kembali lagi ke `/kuesioner/{periode_id}`
   - Jawaban sebelumnya sudah ter-load

## Testing Checklist

- [ ] Migrasi jawaban table berhasil
- [ ] Data master (Komponen, Kategori, dll) sudah di-seed
- [ ] Menu "Kuesioner" muncul di sidebar
- [ ] Halaman pilih periode menampilkan periode aktif
- [ ] Klik periode membuka form kuesioner
- [ ] Accordion kategori berfungsi (expand/collapse)
- [ ] Input Ya/Tidak bisa dipilih dan auto-save
- [ ] Input Pilihan Ganda menampilkan opsi A/B/C/D
- [ ] Input Angka bisa diisi dan auto-save
- [ ] Sub-pertanyaan (formula) menampilkan multiple inputs
- [ ] Keterangan bisa diisi dan auto-save
- [ ] Indicator "Menyimpan..." dan "Tersimpan" muncul
- [ ] Data tersimpan di database table `jawaban`
- [ ] Resume: Refresh page, jawaban tetap ada
- [ ] OPD user hanya bisa isi untuk OPD-nya sendiri

## Next Steps (Belum Implemented)

1. **Submit/Finalize Kuesioner**
   - Button "Submit" untuk finalisasi
   - Change status dari `draft` ke `final`
   - Validasi semua pertanyaan wajib sudah diisi

2. **Progress Indicator**
   - Show berapa persen sudah diisi
   - Per kategori atau per komponen

3. **Perhitungan Skor**
   - Formula untuk sub-pertanyaan
   - Total bobot per kategori
   - Total nilai akhir

4. **Review & Preview**
   - Halaman preview sebelum submit
   - Print/Export hasil

5. **Admin Review**
   - Admin bisa lihat jawaban per OPD
   - Approve/Reject jawaban
   - Comment/feedback

## Files Created

1. Migration: `2026_01_28_144626_create_jawaban_table.php`
2. Model: `app/Models/Jawaban.php`
3. Controller: `app/Http/Controllers/KuesionerController.php` (updated)
4. Routes: `routes/web.php` (added kuesioner routes)
5. Views:
   - `resources/views/page/kuesioner/index.blade.php`
   - `resources/views/page/kuesioner/form.blade.php`
   - `resources/views/page/kuesioner/partials/input-ya-tidak.blade.php`
   - `resources/views/page/kuesioner/partials/input-pilihan-ganda.blade.php`
   - `resources/views/page/kuesioner/partials/input-angka.blade.php`
   - `resources/views/page/kuesioner/partials/input-sub-pertanyaan.blade.php`
6. Layout: `resources/views/layouts/app.blade.php` (added menu)

## Usage Example

```php
// Untuk test, akses via browser:
1. Login sebagai user yang terhubung dengan OPD
2. Klik menu "Kuesioner" di sidebar
3. Pilih periode "Zona Integritas 2026"
4. Mulai isi kuesioner
5. Lihat indicator "Tersimpan" setelah isi input
6. Close browser, buka lagi - data masih ada
```

## Important Notes

⚠️ **User harus sudah terhubung dengan OPD**
- Validasi di controller: `$user->opd`
- Jika tidak ada OPD, redirect dengan error message

⚠️ **Data Seeding**
- Pastikan data master sudah di-seed:
  - KomponenSeeder
  - KategoriSeeder
  - SubKategoriSeeder
  - IndikatorSeeder
  - PertanyaanSeeder
  - SubPertanyaanSeeder
  - PeriodeSeeder

⚠️ **Auto-Save via AJAX**
- Memerlukan CSRF token
- Handle error saat network offline
- User experience: jangan block UI saat saving
