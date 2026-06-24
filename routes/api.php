<?php

use App\Http\Controllers\PengusulanController;
use Illuminate\Support\Facades\Route;

Route::get('/pengusulan/unit-wbk', [PengusulanController::class, 'getUnitWbk']);
Route::get('/pengusulan/unit-wbbm', [PengusulanController::class, 'getUnitWbbm']);
Route::get('/lke/jawaban-unit/{unit_id}', [PengusulanController::class, 'getJawabanUnit']);
