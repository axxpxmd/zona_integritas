<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OpdController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// OPD Management
Route::resource('opd', OpdController::class)->names('opd');

// User Management
Route::resource('user', UserController::class)->names('user');
