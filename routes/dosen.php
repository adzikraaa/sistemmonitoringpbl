<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dosen\DashboardController;
use App\Http\Controllers\Dosen\KelompokController;

Route::prefix('dosen')->name('dosen.')->group(function () {
    Route::middleware('auth:dosen')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

                // 1. Halaman Daftar Kelompok Bimbingan Dosen
        Route::get('/kelompok', [KelompokController::class, 'index'])->name('kelompok.index');

        // 2. Halaman Detail Kelompok Bimbingan
        Route::get('/kelompok/{id}', [KelompokController::class, 'show'])->name('kelompok.show');
    });
});
