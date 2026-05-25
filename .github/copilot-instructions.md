# Zona Integritas - AI Coding Instructions

## Project Overview
Aplikasi Zona Integritas (WBK/WBBM) digunakan untuk mengakomodir pengisian dan verifikasi Lembar Kerja Evaluasi (LKE) / Kuesioner untuk OPD (Organisasi Perangkat Daerah). Sistem ini memiliki 3 role utama:
1. **Admin:** Mengelola master data, periode, struktur kuesioner, dan user.
2. **Operator:** Mengisi kuesioner evaluasi pada rentang waktu yang ditentukan di `tm_periode`. Jika sudah selesai, akan dikirimkan ke Verifikator.
3. **Verifikator:** Memverifikasi kuesioner yang dikirim Operator. Bisa menyetujui, merevisi jawaban secara langsung (tetap menyimpan jawaban asli operator sebagai histori), atau mengembalikan pertanyaan/kuesioner ke operator dengan status revisi agar diperbaiki oleh operator. Setelah selesai memverifikasi, bisa mengirimkan hasil verifikasi ke Verifikator Menpan untuk tahap finalisasi.
4. **Verifikator Menpan:** Role khusus untuk memverifikasi kuesioner yang sudah diverifikasi oleh Verifikator biasa. Hanya bisa mengubah jawaban yang sudah disetujui Verifikator biasa, tidak bisa merevisi. Merevisi jawaban Verifikator biasa berarti mengubah jawaban yang sudah disetujui tersebut, sehingga tetap menyimpan histori jawaban operator dan verifikator biasa.

## Tech Stack & Architecture
- **Backend:** Laravel 12, PHP 8.2+, Eloquent ORM
- **Frontend:** Tailwind CSS via CDN (no build step for styles), Inter font
- **Database:** MySQL - table prefix `tm_` untuk master data
- **Auth:** Username-based login (bukan email), roles: `admin`, `operator`, `verifikator`

## Development Commands
```bash
composer setup           # Full setup (install, key, migrate, npm build)
composer dev             # Start server + queue + vite concurrently
php artisan migrate:fresh --seed  # Reset database with seeders
```

## Jawaban tiap rolenya
- **Operator:** `jawaban_text`, `jawaban_angka` (input asli operator)
- **Verifikator:** `verifikator_jawaban_text`, `verifikator_jawaban_angka` (input verifikator, tetap simpan jawaban asli operator)
- **Verifikator Menpan:** `menpan_jawaban_text`, `menpan_jawaban_angka` (input verifikator menpan, tetap simpan jawaban asli operator dan verifikator biasa)

## Status sudah mengisi / sudah selesai mengisi jawaban pada tiap role
- **Operator:** status == "final"
- **Verifikator:** status_verifikasi == terkirim
- **Verifikator Menpan:** status_verifikasi_menpan == disetujui
note: kolom tersebut ada di tabel jawaban. pastikan pada setiap pertanyaan sudah berubah valuenya seperti diatas, jika ada 1 yg belum berarti belum selesai mengisinya.

## Business Logic & Workflow (STRICT)
- **Struktur Kuesioner (Hierarkis):** `tm_komponen` -> `tm_kategori` -> `tm_sub_kategori` -> `tm_indikator` -> `tm_pertanyaan` -> `tm_sub_pertanyaan` (opsional).
- **Periode (`tm_periode`):** Memiliki rentang waktu pengerjaan. Operator dan Verifikator hanya bisa bekerja jika tanggal saat ini masuk dalam rentang waktu sesi/periode tersebut.
- **Histori Jawaban (`jawaban`):** Tabel jawaban menyimpan input asli operator (`jawaban_text`, `jawaban_angka`). Ketika verifikator mengubah, jawaban verifikator disimpan di field terpisah (misal `verifikator_jawaban_text`) sehingga riwayat asli operator tetap aman. Terdapat *flag/status* revisi atau disetujui.

## Design System (STRICT)
- **Colors:** Primary `#0164CA`, hover `#0150A8`, accent `#F7D558`
- **Cards:** `bg-white rounded-xl` - NO borders, NO shadows
- **Buttons:** Flat colors only - NO gradients, NO shadows
- **Inputs:** `border border-gray-300 rounded-lg`
- **Status:** Integer `1` (aktif) / `0` (tidak aktif) - never strings

## Database Conventions
| Table | Prefix | Key Columns / Usage |
|-------|--------|---------------------|
| `tm_opd` | tm_ | n_opd, alamat, status (tinyInt) |
| `users` | - | username (unique), opd_id (FK), role (enum) |
| Kuesioner | tm_ | `komponen`, `kategori`, `sub_kategori`, `indikator`, `pertanyaan`, `sub_pertanyaan` |

Model relationships: User `belongsTo` Opd, Opd `hasMany` Users. Kuesioner berelasi linear (One to Many).

## Project Structure
```
app/Http/Controllers/     # Flat structure, no subfolders for main controllers
app/Models/               # Opd.php (table: tm_opd), User.php, dst
resources/views/
├── layouts/app.blade.php # Main layout with sidebar + Tailwind config
├── auth/                 # Login pages
└── page/
    ├── dashboard.blade.php
    └── [module]/         # index, create, edit, show views
```

## Route Conventions
- **No prefix** - routes use simple names: `dashboard`, `opd.index`, `user.show`
- Resource routes: `Route::resource('opd', OpdController::class)->names('opd')`
- Dashboard at root: `Route::get('/', [DashboardController::class, 'index'])->name('dashboard')`

## View Patterns

### Layout Usage
```blade
@extends('layouts.app')
@section('title', 'Page Title')
@section('page-title', 'Header Title')
@section('content')
    {{-- Content here --}}
@endsection
```

### Index Page Structure
1. Header: judul + subtitle + tombol "Tambah" (kanan)
2. Stats cards row: Total, Aktif, Tidak Aktif
3. Filter card: search input + status dropdown + tombol filter
4. Data table (desktop) + mobile cards (responsive)
5. Custom pagination

### Form Page Structure
- Header dengan icon, judul, subtitle, tombol "Kembali" (`bg-white`)
- Form card: `max-w-3xl mx-auto bg-white rounded-xl p-6`
- Grid layout: `grid grid-cols-1 md:grid-cols-2 gap-6`

### Button Classes
```blade
{{-- Primary --}} bg-primary text-white hover:bg-primary-dark
{{-- Secondary --}} bg-white border border-gray-300 text-gray-700 hover:bg-gray-50
```

### Status Badge
```blade
@if($item->status == 1)
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
    <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>Aktif
</span>
@else
<span class="... bg-gray-100 text-gray-600">Tidak Aktif</span>
@endif
```

## Controller Patterns
- Validation dengan custom Indonesian messages
- `withQueryString()` untuk pagination dengan filters
- Hash password: `Hash::make()` on store, conditional on update

## Key Reference Files
- [resources/views/layouts/app.blade.php](resources/views/layouts/app.blade.php) - Layout + Tailwind config
- [resources/views/page/opd/index.blade.php](resources/views/page/opd/index.blade.php) - Index page template
- [app/Http/Controllers/UserController.php](app/Http/Controllers/UserController.php) - CRUD with validation
