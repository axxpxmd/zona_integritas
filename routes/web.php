<?php

use App\Http\Controllers\Cms\DashboardController;
use App\Http\Controllers\Cms\OpdController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('cms.dashboard');

// OPD Management
Route::resource('opd', OpdController::class)->names('cms.opd')->except(['show']);
