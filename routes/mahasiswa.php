<?php

use App\Http\Controllers\MahasiswaController;
use Illuminate\Support\Facades\Route;

Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::middleware('auth:mahasiswa')->group(function () {
        Route::get('/dashboard', [MahasiswaController::class, 'dashboard'])->name('dashboard');
        Route::get('/pilih-semester', [MahasiswaController::class, 'pilihSemester'])->name('pilih-semester');
        Route::get('/kelompok', [MahasiswaController::class, 'kelompok'])->name('kelompok');
        Route::get('/proposal', [MahasiswaController::class, 'proposal'])->name('proposal');
        Route::post('/proposal', [MahasiswaController::class, 'storeProposal'])->name('proposal.store');
        Route::get('/logbook', [MahasiswaController::class, 'logbook'])->name('logbook');
        Route::post('/logbook', [MahasiswaController::class, 'storeLogbook'])->name('logbook.store');
        Route::get('/milestone', [MahasiswaController::class, 'milestone'])->name('milestone');
        Route::get('/milestone/{id}', [MahasiswaController::class, 'milestoneDetail'])->name('milestone.detail');
        Route::post('/milestone/{id}/upload', [MahasiswaController::class, 'storeMilestoneUpload'])->name('milestone.upload');
        Route::get('/notifikasi', [MahasiswaController::class, 'notifikasi'])->name('notifikasi');
    });
});
