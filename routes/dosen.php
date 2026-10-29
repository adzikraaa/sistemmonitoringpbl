<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dosen\DashboardController;

Route::prefix('dosen')->name('dosen.')->group(function () {
    Route::middleware('auth:dosen')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/pilih-kelas', [DashboardController::class, 'pilihKelas'])->name('pilih-kelas');
        Route::post('/pilih-kelas/select', [DashboardController::class, 'selectKelas'])->name('pilih-kelas.select');
        Route::get('/kelompok', [DashboardController::class, 'kelompok'])->name('kelompok');
    });
});
