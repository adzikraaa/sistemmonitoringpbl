<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dosen\DosenController;

Route::prefix('dosen')->name('dosen.')->group(function () {
    Route::middleware('auth:dosen')->group(function () {

        // Pilih Semester — entry point setelah login
        Route::get('/pilih-semester', [DosenController::class, 'pilihSemester'])->name('pilih-semester');
        Route::post('/set-semester',  [DosenController::class, 'setSemester'])->name('set-semester');

        // Dashboard Dosen Pengampu (tujuan setelah memilih semester)
        Route::get('/dashboard', [DosenController::class, 'dashboard'])->name('dashboard');
    });
});
