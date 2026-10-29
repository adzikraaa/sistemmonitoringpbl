<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ─── Auth routes (hanya untuk yang belum login) ───────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
});

Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ─── Ruang Mahasiswa (wajib login sebagai mahasiswa) ─────────────────────────
// Rute mahasiswa didefinisikan secara terpusat di routes/mahasiswa.php

// ─── Role-specific routes ─────────────────────────────────────────────────────
require __DIR__.'/mahasiswa.php';
require __DIR__.'/dosen.php';
require __DIR__.'/koordinator.php';
