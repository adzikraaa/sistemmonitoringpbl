<?php

use App\Http\Controllers\Dosen\DashboardController;
use App\Http\Controllers\Dosen\DosenController;
use App\Http\Controllers\Dosen\KelompokController;
use App\Http\Controllers\Dosen\PenyelesaianMilestoneController;
use Illuminate\Support\Facades\Route;

Route::prefix('dosen')->name('dosen.')->group(function () {
    Route::middleware('auth:dosen')->group(function () {
        Route::get('/dashboard', [DosenController::class, 'dashboard'])->name('dashboard');
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
        Route::get('/penyelesaian-milestone/{kelompok}/{milestone}/{mataKuliah}/download', [PenyelesaianMilestoneController::class, 'download'])
            ->name('penyelesaian-milestone.download');
        Route::get('/penyelesaian-milestone/unduh/{pengumpulan}', [PenyelesaianMilestoneController::class, 'downloadPengumpulan'])
            ->name('penyelesaian-milestone.download-pengumpulan');

        Route::get('/laporan-aktivitas', [DashboardController::class, 'laporanAktivitas'])->name('laporan-aktivitas');
        Route::get('/laporan-aktivitas/logbook/{id}', [DashboardController::class, 'showLogbook'])->name('laporan-aktivitas.logbook');
        Route::post('/laporan-aktivitas/logbook/{id}/review', [DashboardController::class, 'reviewLogbook'])->name('laporan-aktivitas.logbook.review');
        Route::get('/laporan-aktivitas/logbook/{id}/download', [DashboardController::class, 'downloadLogbook'])->name('laporan-aktivitas.logbook.download');
        Route::get('/laporan-aktivitas/proposal/{id}', [DashboardController::class, 'showProposal'])->name('laporan-aktivitas.proposal');
        Route::post('/laporan-aktivitas/proposal/{id}/review', [DashboardController::class, 'reviewProposal'])->name('laporan-aktivitas.proposal.review');
        Route::get('/laporan-aktivitas/proposal/{id}/download', [DashboardController::class, 'downloadProposal'])->name('laporan-aktivitas.proposal.download');
        Route::get('/laporan-aktivitas/milestone/{kelompok}/{milestone}', [DashboardController::class, 'showMilestone'])->name('laporan-aktivitas.milestone');
        Route::post('/laporan-aktivitas/milestone/{kelompok}/{milestone}/review', [DashboardController::class, 'reviewMilestone'])->name('laporan-aktivitas.milestone.review');
        Route::get('/notifikasi', [DosenController::class, 'notifikasi'])->name('notifikasi');
        Route::post('/notifikasi/{notifikasi}/baca', [DosenController::class, 'tandaiNotifikasiDibaca'])->name('notifikasi.baca');
        Route::post('/notifikasi/baca-semua', [DosenController::class, 'tandaiSemuaNotifikasiDibaca'])->name('notifikasi.baca-semua');
    });
});
