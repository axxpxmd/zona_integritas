<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\KomponenController;
use App\Http\Controllers\KuesionerController;
use App\Http\Controllers\OpdController;
use App\Http\Controllers\PeriodeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
});

Route::post('logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // OPD Management
    Route::resource('opd', OpdController::class)->names('opd');

    // User Management
    Route::resource('user', UserController::class)->names('user');

    // Periode Management
    Route::resource('periode', PeriodeController::class)->names('periode');

    // Komponen Management
    Route::resource('komponen', KomponenController::class)->names('komponen');

    // Kategori Management
    Route::resource('kategori', KategoriController::class)->names('kategori');

    // Kuesioner
    Route::prefix('kuesioner')->name('kuesioner.')->group(function () {
        Route::get('/', [KuesionerController::class, 'index'])->name('index');
        Route::get('/{periode}', [KuesionerController::class, 'show'])->name('show');
        Route::post('/auto-save', [KuesionerController::class, 'autoSave'])->name('auto-save');
    });
});
