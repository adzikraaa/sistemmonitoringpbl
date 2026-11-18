<?php

use App\Http\Controllers\Dosen\DashboardController;
use App\Http\Controllers\Dosen\DosenController;
use App\Http\Controllers\Dosen\KelompokController;
use App\Http\Controllers\Dosen\PenyelesaianMilestoneController;
use Illuminate\Support\Facades\Route;

Route::prefix('dosen')->name('dosen.')->group(function () {
    Route::middleware('auth:dosen')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/pilih-semester', [DosenController::class, 'pilihSemester'])->name('pilih-semester');
        Route::post('/set-semester', [DosenController::class, 'setSemester'])->name('set-semester');
        Route::get('/pilih-kelas', [DashboardController::class, 'pilihKelas'])->name('pilih-kelas');
        Route::post('/pilih-kelas/select', [DashboardController::class, 'selectKelas'])->name('pilih-kelas.select');

        // Halaman Daftar Kelompok Bimbingan Dosen
        Route::get('/kelompok', [KelompokController::class, 'index'])->name('kelompok');
        Route::post('/kelompok/pengingat', [KelompokController::class, 'kirimPengingat'])->name('kelompok.pengingat');
        // Halaman Detail Kelompok Bimbingan
        Route::get('/kelompok/{id}', [KelompokController::class, 'show'])->name('kelompok.show');

        Route::get('/penyelesaian-milestone', [PenyelesaianMilestoneController::class, 'index'])
            ->name('penyelesaian-milestone.index');
        Route::patch('/penyelesaian-milestone/{kelompok}/{milestone}/{mataKuliah}', [PenyelesaianMilestoneController::class, 'update'])
            ->name('penyelesaian-milestone.update');

        Route::get('/laporan-aktivitas', [DashboardController::class, 'laporanAktivitas'])->name('laporan-aktivitas');
        Route::get('/notifikasi', [DosenController::class, 'notifikasi'])->name('notifikasi');
    });
});
