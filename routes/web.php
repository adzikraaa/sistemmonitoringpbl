<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MahasiswaController;

Route::get('/', function () {
    return view('welcome');
});

// Ruang Mahasiswa routes
Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('/dashboard', [MahasiswaController::class, 'dashboard'])->name('dashboard');
    Route::get('/kelompok', [MahasiswaController::class, 'kelompok'])->name('kelompok');
    Route::get('/proposal', [MahasiswaController::class, 'proposal'])->name('proposal');
    Route::get('/logbook', [MahasiswaController::class, 'logbook'])->name('logbook');
    Route::get('/milestone', [MahasiswaController::class, 'milestone'])->name('milestone');

    // Proposal submission (POST — backend stub)
    Route::post('/proposal', [MahasiswaController::class, 'storeProposal'])->name('proposal.store');
});
