<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Koordinator\DashboardController;

Route::prefix('koordinator')->name('koordinator.')->group(function () {
    Route::middleware('auth:koordinator')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });
});
