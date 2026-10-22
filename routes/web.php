<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordRecoveryController;

Route::get('/', function () {
    return view('welcome');
});

// ─── Auth routes (hanya untuk yang belum login) ───────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::get('/lupa-password', [PasswordRecoveryController::class, 'show'])->name('password.request');
    Route::post('/lupa-password', [PasswordRecoveryController::class, 'submit'])->name('password.request.submit');
});

Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ─── Ruang Mahasiswa (wajib login sebagai mahasiswa) ─────────────────────────
Route::prefix('mahasiswa')->name('mahasiswa.')->middleware('auth:mahasiswa')->group(function () {
    Route::get('/pilih-semester', [MahasiswaController::class, 'pilihSemester'])->name('pilih-semester');
    Route::get('/dashboard', [MahasiswaController::class, 'dashboard'])->name('dashboard');
    Route::get('/kelompok', [MahasiswaController::class, 'kelompok'])->name('kelompok');
    Route::get('/proposal', [MahasiswaController::class, 'proposal'])->name('proposal');
    Route::get('/logbook', [MahasiswaController::class, 'logbook'])->name('logbook');
    Route::post('/logbook', [MahasiswaController::class, 'storeLogbook'])->name('logbook.store');


    Route::get('/milestone', [MahasiswaController::class, 'milestone'])->name('milestone');
    Route::post('/milestone/{id}/upload', [MahasiswaController::class, 'storeMilestoneUpload'])->name('milestone.upload');
    Route::get('/notifikasi', [MahasiswaController::class, 'notifikasi'])->name('notifikasi');

    Route::post('/proposal', [MahasiswaController::class, 'storeProposal'])->name('proposal.store');
});

// ─── Role-specific routes ─────────────────────────────────────────────────────
require __DIR__.'/mahasiswa.php';
require __DIR__.'/dosen.php';
require __DIR__.'/koordinator.php';
