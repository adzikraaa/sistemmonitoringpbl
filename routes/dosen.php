<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dosen\DashboardController;

Route::prefix('dosen')->name('dosen.')->group(function () {
    Route::middleware('auth:dosen')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });
});
