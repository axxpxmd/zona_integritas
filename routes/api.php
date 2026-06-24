<?php

use App\Http\Controllers\PengusulanController;
use Illuminate\Support\Facades\Route;

Route::get('/pengusulan/unit-wbk', [PengusulanController::class, 'getUnitWbk']);
