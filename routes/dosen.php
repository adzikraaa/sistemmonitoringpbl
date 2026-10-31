<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dosen\DosenController;
use App\Http\Controllers\Dosen\DashboardController;
use App\Http\Controllers\Dosen\KelompokController;

Route::prefix('dosen')->name('dosen.')->group(function () {
    Route::middleware('auth:dosen')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/pilih-semester', [DosenController::class, 'pilihSemester'])->name('pilih-semester');
        Route::post('/set-semester', [DosenController::class, 'setSemester'])->name('set-semester');
        Route::get('/pilih-kelas', [DashboardController::class, 'pilihKelas'])->name('pilih-kelas');
        Route::post('/pilih-kelas/select', [DashboardController::class, 'selectKelas'])->name('pilih-kelas.select');

        Route::get('/kelompok', [KelompokController::class, 'index'])->name('kelompok');
        Route::get('/kelompok-index', [KelompokController::class, 'index'])->name('kelompok.index');
        Route::get('/kelompok/{id}', [KelompokController::class, 'show'])->name('kelompok.show');

        Route::get('/laporan-aktivitas', [DashboardController::class, 'laporanAktivitas'])->name('laporan-aktivitas');
        Route::get('/notifikasi', [DosenController::class, 'notifikasi'])->name('notifikasi');
    });
});
