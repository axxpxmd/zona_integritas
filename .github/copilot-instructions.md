# Zona Integritas - AI Coding Instructions

## Project Overview
Laravel 12 CMS untuk pengisian kuesioner Zona Integritas (WBK/WBBM) Tangerang Selatan. Menggunakan Tailwind CSS via CDN dengan design minimalist modern.

## Tech Stack
- **Backend:** Laravel 12, PHP 8.2+, MySQL
- **Frontend:** Tailwind CSS (CDN), Poppins font
- **Database:** MySQL (configured in `.env`)

## Design Guidelines
- **Primary color:** `#0164CA` (sidebar, buttons)
- **Secondary color:** `#F7D558` (accents)
- **NO shadows** on buttons/cards - use borders instead
- **NO gradients** - flat colors only
- Rounded-full for navigation items, rounded-lg for cards

## Development Commands
```bash
composer setup    # Full setup (deps, key, migrate, build)
composer dev      # Start server + queue + vite
composer test     # Run tests
php artisan migrate:fresh  # Reset database
```

## Project Structure

### Controllers
CMS controllers in `app/Http/Controllers/Cms/`:
```php
namespace App\Http\Controllers\Cms;
use App\Http\Controllers\Controller;
```

### Database Tables
- `tm_opd` - Master OPD (n_opd, alamat, status)
- `users` - Users with role enum (admin, operator, verifikator), linked to tm_opd via opd_id

### Views Structure
```
resources/views/
├── layouts/app.blade.php    # Main CMS layout with sidebar
└── cms/
    ├── dashboard.blade.php
    └── [module]/            # index, create, edit views
```

### Layout Pattern
All CMS views extend `layouts.app`:
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

## Key Files
- [resources/views/layouts/app.blade.php](resources/views/layouts/app.blade.php) - Main layout with sidebar
- [routes/web.php](routes/web.php) - All routes (dashboard at `/`)
- [database/migrations/](database/migrations/) - tm_opd and users tables

## UI Components Pattern
- Cards: `bg-white border border-gray-200 rounded-lg p-6`
- Buttons primary: `bg-primary text-white px-4 py-2 rounded-lg`
- Form inputs: `border border-gray-300 rounded-lg px-4 py-2 w-full`
- Active nav: `bg-white/20 text-white rounded-full`
