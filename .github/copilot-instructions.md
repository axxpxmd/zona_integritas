# Zona Integritas - AI Coding Instructions

## Project Overview
Laravel 12 untuk pengisian kuesioner Zona Integritas (WBK/WBBM) Tangerang Selatan. Portal manajemen OPD (Organisasi Perangkat Daerah) dengan multi-role user system.

## Tech Stack & Architecture
- **Backend:** Laravel 12, PHP 8.2+, Eloquent ORM
- **Frontend:** Tailwind CSS via CDN (no build step for styles), Inter font
- **Database:** MySQL - table prefix `tm_` untuk master data
- **Auth:** Username-based login (bukan email), roles: admin/operator/verifikator

## Development Commands
```bash
composer setup           # Full setup (install, key, migrate, npm build)
composer dev             # Start server + queue + vite concurrently
php artisan migrate:fresh --seed  # Reset database with seeders
```

## Design System (STRICT)
- **Colors:** Primary `#0164CA`, hover `#0150A8`, accent `#F7D558`
- **Cards:** `bg-white rounded-xl` - NO borders, NO shadows
- **Buttons:** Flat colors only - NO gradients, NO shadows
- **Inputs:** `border border-gray-300 rounded-lg`
- **Status:** Integer `1` (aktif) / `0` (tidak aktif) - never strings

## Database Conventions
| Table | Prefix | Key Columns |
|-------|--------|-------------|
| `tm_opd` | tm_ | n_opd, alamat, status (tinyInt) |
| `users` | - | username (unique), opd_id (FK), role (enum) |

Model relationships: User `belongsTo` Opd, Opd `hasMany` Users

## Project Structure
```
app/Http/Controllers/     # Flat structure, no subfolders for main controllers
app/Models/               # Opd.php (table: tm_opd), User.php
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
