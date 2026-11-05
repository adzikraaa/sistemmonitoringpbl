<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Koordinator\DashboardController;
use App\Http\Controllers\Koordinator\PenugasanDosenController;

Route::prefix('koordinator')->name('koordinator.')->group(function () {
    Route::middleware('auth:koordinator')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Fitur Penugasan Dosen (Aktor: Koordinator)
        Route::get('/penugasan-dosen', [PenugasanDosenController::class, 'index'])->name('penugasan-dosen.index');
        Route::post('/penugasan-dosen', [PenugasanDosenController::class, 'store'])->name('penugasan-dosen.store');
        Route::put('/penugasan-dosen/{dosenPengampu}', [PenugasanDosenController::class, 'update'])->name('penugasan-dosen.update');
        Route::delete('/penugasan-dosen/{dosenPengampu}', [PenugasanDosenController::class, 'destroy'])->name('penugasan-dosen.destroy');
        Route::get('/penugasan-dosen/api/data', [PenugasanDosenController::class, 'apiData'])->name('penugasan-dosen.api-data');
    });
});
