<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mahasiswa\DashboardController;

Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::middleware('auth:mahasiswa')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });
});
