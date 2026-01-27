# Zona Integritas - AI Coding Instructions

## Project Overview
Laravel 12 CMS untuk pengisian kuesioner Zona Integritas (WBK/WBBM) Tangerang Selatan. Menggunakan Tailwind CSS via CDN dengan design minimalist modern.

## Tech Stack
- **Backend:** Laravel 12, PHP 8.2+, MySQL
- **Frontend:** Tailwind CSS (CDN), Inter font
- **Database:** MySQL (configured in `.env`)

## Design Guidelines
- **Primary color:** `#0164CA` (sidebar, buttons), `#0150A8` (hover state)
- **Secondary color:** `#F7D558` (accents)
- **NO shadows** on buttons/cards
- **NO gradients** - flat colors only
- Cards: `bg-white rounded-xl` tanpa border
- Form inputs: gunakan `border border-gray-300`
- Status values: gunakan `1` (aktif) dan `0` (tidak aktif), bukan string

## Development Commands
```bash
composer setup           # Full setup (deps, key, migrate, build)
composer dev             # Start server + queue + vite
php artisan migrate:fresh  # Reset database
```

## Project Structure

### Controllers
Controllers langsung di `app/Http/Controllers/`:
```php
namespace App\Http\Controllers;
```

### Database Tables
- `tm_opd` - Master OPD (n_opd, alamat, status as tinyInteger)
- `users` - Users dengan role enum (admin, operator, verifikator), linked via opd_id

### Views Structure
```
resources/views/
├── layouts/app.blade.php    # Main layout with sidebar + Tailwind config
└── page/
    ├── dashboard.blade.php
    └── [module]/            # index, create, edit views
```

### Layout Pattern
```blade
@extends('layouts.app')
@section('title', 'Page Title')
@section('page-title', 'Header Title')
@section('content')
    {{-- Content here --}}
@endsection
```

### Route Naming
Routes use `cms.` prefix: `cms.dashboard`, `cms.opd.index`, etc.

## UI Patterns

### Index Page (List View)
- Header dengan judul + tombol tambah di kanan
- Stats cards (Total, Aktif, Tidak Aktif)
- Filter section dengan search + dropdown status
- Table dengan desktop view dan mobile cards
- Pagination custom

### Create/Edit Page
- Header dengan icon, judul, subtitle + tombol "Kembali" (bg-white)
- Form card centered (`max-w-3xl mx-auto`)
- Form fields dengan label, input, error message

### Button Styles
```blade
{{-- Primary --}}
bg-primary text-white hover:bg-primary-dark

{{-- Secondary/Cancel --}}
bg-white border border-gray-300 text-gray-700 hover:bg-gray-50
```

### Status Badge
```blade
@if($item->status == 1)
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
    <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
    Aktif
</span>
@else
{{-- bg-gray-100 text-gray-600 untuk tidak aktif --}}
@endif
```

## Key Files
- [resources/views/layouts/app.blade.php](resources/views/layouts/app.blade.php) - Main layout + Tailwind config
- [routes/web.php](routes/web.php) - All routes (dashboard at `/`)
- [resources/views/page/opd/](resources/views/page/opd/) - Reference CRUD views
