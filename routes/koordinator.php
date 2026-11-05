<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Koordinator\DashboardController;
use App\Http\Controllers\Koordinator\KelasController;
use App\Http\Controllers\Koordinator\PenggunaController;

Route::prefix('koordinator')->name('koordinator.')->group(function () {

    Route::middleware('auth:koordinator')->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // ─── Master Data Kelas (Akses via /koordinator/kelas atau /koordinator/master-data/kelas) ───
        Route::get('/kelas',                          [KelasController::class, 'index'])->name('kelas');
        Route::get('/master-data/kelas',              [KelasController::class, 'index'])->name('master-data.kelas');
        Route::post('/master-data/kelas',             [KelasController::class, 'store'])->name('master-data.kelas.store');
        Route::put('/master-data/kelas/{kelas}',      [KelasController::class, 'update'])->name('master-data.kelas.update');
        Route::patch('/master-data/kelas/{kelas}/toggle', [KelasController::class, 'toggleStatus'])->name('master-data.kelas.toggle');
        Route::delete('/master-data/kelas/{kelas}',   [KelasController::class, 'destroy'])->name('master-data.kelas.destroy');

        // ─── Master Data Pengguna (Akses via /koordinator/pengguna atau /koordinator/master-data/pengguna) ───
        Route::get('/pengguna',                          [PenggunaController::class, 'index'])->name('pengguna');
        Route::get('/master-data/pengguna',              [PenggunaController::class, 'index'])->name('master-data.pengguna');
        Route::post('/master-data/pengguna',             [PenggunaController::class, 'store'])->name('master-data.pengguna.store');
        Route::put('/master-data/pengguna/{role}/{id}',  [PenggunaController::class, 'update'])->name('master-data.pengguna.update');
        Route::patch('/master-data/pengguna/{role}/{id}/toggle', [PenggunaController::class, 'toggleStatus'])->name('master-data.pengguna.toggle');
        Route::post('/master-data/pengguna/reset-password', [PenggunaController::class, 'resetPasswordGeneral'])->name('master-data.pengguna.reset-password-general');
        Route::post('/master-data/pengguna/{role}/{id}/reset-password', [PenggunaController::class, 'resetPassword'])->name('master-data.pengguna.reset-password');
        Route::post('/master-data/pengguna/invite',      [PenggunaController::class, 'invite'])->name('master-data.pengguna.invite');
        Route::delete('/master-data/pengguna/{role}/{id}', [PenggunaController::class, 'destroy'])->name('master-data.pengguna.destroy');

    });

});