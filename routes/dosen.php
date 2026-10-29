<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dosen\DosenController;
use App\Http\Controllers\Dosen\DashboardController;
use App\Http\Controllers\Dosen\KelompokController;

Route::prefix('dosen')->name('dosen.')->group(function () {
    Route::middleware('auth:dosen')->group(function () {

        // Pilih Semester — entry point setelah login (dari branch pilih-semester)
        Route::get('/pilih-semester', [DosenController::class, 'pilihSemester'])->name('pilih-semester');
        Route::post('/set-semester',  [DosenController::class, 'setSemester'])->name('set-semester');

        // Dashboard Dosen Pengampu (tujuan setelah memilih semester)
        Route::get('/dashboard', [DosenController::class, 'dashboard'])->name('dashboard');

        // Halaman Daftar Kelompok Bimbingan Dosen (dari main)
        Route::get('/kelompok',       [KelompokController::class, 'index'])->name('kelompok.index');
        Route::get('/kelompok/{id}',  [KelompokController::class, 'show'])->name('kelompok.show');

        // Laporan Aktivitas (dari main)
        Route::get('/laporan-aktivitas', [DashboardController::class, 'laporanAktivitas'])->name('laporan-aktivitas');
    });
});
