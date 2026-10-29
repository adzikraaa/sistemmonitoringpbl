<?php

use App\Http\Controllers\Dosen\DashboardController;
use App\Http\Controllers\Dosen\KelompokController;
use Illuminate\Support\Facades\Route;

Route::prefix('dosen')->name('dosen.')->group(function () {
    Route::middleware('auth:dosen')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Halaman Daftar Kelompok Bimbingan Dosen
        Route::get('/kelompok', [KelompokController::class, 'index'])->name('kelompok.index');
        // Halaman Detail Kelompok Bimbingan
        Route::get('/kelompok/{id}', [KelompokController::class, 'show'])->name('kelompok.show');
        Route::get('/laporan-aktivitas', [DashboardController::class, 'laporanAktivitas'])->name('laporan-aktivitas');
    });
});
