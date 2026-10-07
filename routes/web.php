<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\Auth\LoginController;

Route::get('/', function () {
    return view('welcome');
});

// Authentication routes
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Ruang Mahasiswa routes
Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('/dashboard', [MahasiswaController::class, 'dashboard'])->name('dashboard');
    Route::get('/kelompok', [MahasiswaController::class, 'kelompok'])->name('kelompok');
    Route::get('/proposal', [MahasiswaController::class, 'proposal'])->name('proposal');
    Route::get('/logbook', [MahasiswaController::class, 'logbook'])->name('logbook');
    Route::get('/milestone', [MahasiswaController::class, 'milestone'])->name('milestone');

    // Proposal submission
    Route::post('/proposal', [MahasiswaController::class, 'storeProposal'])->name('proposal.store');
});

// Role-specific routes
require __DIR__.'/mahasiswa.php';
require __DIR__.'/dosen.php';
require __DIR__.'/koordinator.php';