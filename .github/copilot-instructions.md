# Zona Integritas - AI Coding Instructions

## Project Overview
Laravel 12 web application running on PHP 8.2+ with Tailwind CSS 4 and Vite 7. The project is in early development stage with standard Laravel scaffolding.

## Tech Stack
- **Backend:** Laravel 12, PHP 8.2+
- **Frontend:** Tailwind CSS 4 (via `@tailwindcss/vite`), Vite 7
- **Database:** SQLite by default (configurable via `.env`)
- **Testing:** PHPUnit 11, Pest-compatible structure

## Development Commands
```bash
# Full project setup (install deps, generate key, migrate, build assets)
composer setup

# Start development (runs server, queue worker, and Vite concurrently)
composer dev

# Run tests with fresh config
composer test
```

## Project Structure Patterns

### Controllers
Place in `app/Http/Controllers/`. Extend the base `Controller` class:
```php
namespace App\Http\Controllers;
class ExampleController extends Controller { }
```

### Models
Place in `app/Models/`. Use `HasFactory` and `Notifiable` traits for user-related models. Define `$fillable`, `$hidden`, and `casts()` method for attribute handling.

### Migrations
Naming: `YYYY_MM_DD_HHMMSS_description.php` in `database/migrations/`. Use anonymous class syntax:
```php
return new class extends Migration { }
```

### Views
Blade templates in `resources/views/`. Use `@vite(['resources/css/app.css', 'resources/js/app.js'])` for asset loading.

## Tailwind CSS 4 Configuration
- Uses new CSS-based config in `resources/css/app.css` with `@theme` directive
- Custom font: 'Instrument Sans' defined in `--font-sans`
- Source paths configured via `@source` directives

## Key Files
- [routes/web.php](routes/web.php) - Web routes
- [app/Providers/AppServiceProvider.php](app/Providers/AppServiceProvider.php) - Service bindings and bootstrapping
- [vite.config.js](vite.config.js) - Vite + Tailwind plugin configuration
- [resources/css/app.css](resources/css/app.css) - Tailwind CSS entry point with theme config

## Testing Conventions
- Feature tests: `tests/Feature/` - for HTTP endpoint testing
- Unit tests: `tests/Unit/` - for isolated logic testing
- Extend `Tests\TestCase` for all tests
- Use `RefreshDatabase` trait when testing database interactions

## Environment
- Local development uses Laragon on Windows
- Default database is SQLite at `database/database.sqlite`
- Queue worker runs with `--tries=1` in dev mode
