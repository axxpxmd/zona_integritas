<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IndikatorController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\KomponenController;
use App\Http\Controllers\KuesionerController;
use App\Http\Controllers\LogViewerController;
use App\Http\Controllers\OpdController;
use App\Http\Controllers\PeriodeController;
use App\Http\Controllers\PertanyaanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubKategoriController;
use App\Http\Controllers\SubPertanyaanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerifikasiController;
use App\Http\Controllers\VerifikasiMenpanController;
use Illuminate\Support\Facades\Route;

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
});

Route::post('logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Public file routes
Route::get('kuesioner/file/{id}', [KuesionerController::class, 'viewFile'])->name('kuesioner.file.view');
Route::get('kuesioner/file-item/{id}', [KuesionerController::class, 'viewFileItem'])->name('kuesioner.file.item.view');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Admin Only Routes
    Route::middleware('admin')->group(function () {
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

        // Sub Kategori Management
        Route::resource('sub-kategori', SubKategoriController::class)->names('sub-kategori');

        // Indikator Management
        Route::resource('indikator', IndikatorController::class)->names('indikator');

        // Pertanyaan Management
        Route::resource('pertanyaan', PertanyaanController::class)->names('pertanyaan');

        // Sub Pertanyaan Management
        Route::resource('sub-pertanyaan', SubPertanyaanController::class)->names('sub-pertanyaan');

        // Log Viewer
        Route::get('logs', [LogViewerController::class, 'index'])->name('logs.index');
        Route::get('logs/{file}/download', [LogViewerController::class, 'download'])->name('logs.download');
        Route::delete('logs/{file}', [LogViewerController::class, 'destroy'])->name('logs.destroy');
    });

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Kuesioner
    Route::prefix('kuesioner')->name('kuesioner.')->group(function () {
        Route::get('/', [KuesionerController::class, 'index'])->name('index');
        Route::post('/file-item/delete/{id}', [KuesionerController::class, 'deleteFile'])->name('file.item.delete');
        Route::post('/file/delete-legacy/{id}', [KuesionerController::class, 'deleteLegacyFile'])->name('file.delete.legacy');
        Route::get('/{periode}/rekap', [KuesionerController::class, 'rekap'])->name('rekap');
        Route::get('/{periode}/rekap/pdf', [KuesionerController::class, 'exportPdf'])->name('rekap.pdf');
        Route::get('/{periode}', [KuesionerController::class, 'show'])->name('show');
        Route::post('/kirim', [KuesionerController::class, 'kirimVerifikator'])->name('kirim.verifikator');
        Route::get('/{periode}/{subKategori}', [KuesionerController::class, 'fill'])->name('fill');
        Route::post('/submit', [KuesionerController::class, 'submit'])->name('submit');
        Route::post('/hitung-nilai', [KuesionerController::class, 'hitungNilaiPreview'])->name('hitung-nilai');
        // Revisi
        Route::get('/{periode}/revisi/daftar', [KuesionerController::class, 'revisiIndex'])->name('revisi.index');
        Route::post('/revisi/submit', [KuesionerController::class, 'revisiSubmit'])->name('revisi.submit');
    });

    // Verifikator
    Route::prefix('verifikasi')->name('verifikasi.')->group(function () {
        Route::get('/', [VerifikasiController::class, 'index'])->name('index');
        Route::get('/rekap', [VerifikasiController::class, 'rekapDashboard'])->name('rekap');
        Route::get('/rekap/pdf', [VerifikasiController::class, 'exportPdf'])->name('rekap.pdf');
        Route::get('/{periode}/{opd}', [VerifikasiController::class, 'show'])->name('show');
        Route::get('/{periode}/{opd}/export-pdf', [VerifikasiController::class, 'exportLkePdf'])->name('export-pdf');
        Route::post('/{periode}/{opd}/kirim-menpan', [VerifikasiController::class, 'kirimMenpan'])->name('kirim-menpan');
        Route::post('/{periode}/{opd}/verify-all-dev', [VerifikasiController::class, 'verifyAllDev'])->name('verify-all-dev');
        Route::get('/{periode}/{opd}/kategori/{subKategori}', [VerifikasiController::class, 'detail'])->name('detail');
        Route::post('/{periode}/{opd}/kategori/{subKategori}', [VerifikasiController::class, 'store'])->name('store');
        Route::post('/{periode}/{opd}/kategori/{subKategori}/cancel-pertanyaan/{pertanyaan}', [VerifikasiController::class, 'cancelPertanyaan'])->name('cancel-pertanyaan');
        Route::post('/{periode}/{opd}/kategori/{subKategori}/kirim-revisi/{pertanyaan}', [VerifikasiController::class, 'kirimRevisi'])->name('kirim-revisi');
        Route::post('/{periode}/{opd}/kategori/{subKategori}/cancel-revisi/{pertanyaan}', [VerifikasiController::class, 'cancelRevisi'])->name('cancel-revisi');
    });

    // Verifikator Menpan
    Route::prefix('verifikasi-menpan')->name('verifikasi-menpan.')->group(function () {
        Route::get('/', [VerifikasiMenpanController::class, 'index'])->name('index');
        Route::get('/{periode}/{opd}', [VerifikasiMenpanController::class, 'show'])->name('show');
        Route::post('/{periode}/{opd}/verify-all-dev', [VerifikasiMenpanController::class, 'verifyAllDev'])->name('verify-all-dev');
        Route::get('/{periode}/{opd}/kategori/{subKategori}', [VerifikasiMenpanController::class, 'detail'])->name('detail');
        Route::post('/{periode}/{opd}/kategori/{subKategori}', [VerifikasiMenpanController::class, 'store'])->name('store');
    });
});
