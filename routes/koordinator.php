
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Koordinator\DashboardController;
use App\Http\Controllers\Koordinator\KelasController;
use App\Http\Controllers\Koordinator\PenggunaController;
use App\Http\Controllers\Koordinator\MataKuliahController;
use App\Http\Controllers\Koordinator\PenugasanDosenController;
use App\Http\Controllers\Koordinator\PeriodeController;
use App\Http\Controllers\Koordinator\MilestoneController;
use App\Http\Controllers\Koordinator\KelompokController;

Route::prefix('koordinator')->name('koordinator.')->group(function () {
    Route::middleware('auth:koordinator')->group(function () {

        // Dashboard Koordinator
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // Master Data Kelas
        Route::get('/kelas', [KelasController::class, 'index'])->name('kelas');
        Route::get('/master-data/kelas', [KelasController::class, 'index'])->name('master-data.kelas');
        Route::post('/master-data/kelas', [KelasController::class, 'store'])->name('master-data.kelas.store');
        Route::put('/master-data/kelas/{kelas}', [KelasController::class, 'update'])->name('master-data.kelas.update');
        Route::patch('/master-data/kelas/{kelas}/toggle', [KelasController::class, 'toggleStatus'])->name('master-data.kelas.toggle');
        Route::delete('/master-data/kelas/{kelas}', [KelasController::class, 'destroy'])->name('master-data.kelas.destroy');

        // Master Data Pengguna
        Route::get('/pengguna', [PenggunaController::class, 'index'])->name('pengguna');
        Route::get('/master-data/pengguna', [PenggunaController::class, 'index'])->name('master-data.pengguna');
        Route::post('/master-data/pengguna', [PenggunaController::class, 'store'])->name('master-data.pengguna.store');
        Route::put('/master-data/pengguna/{role}/{id}', [PenggunaController::class, 'update'])->name('master-data.pengguna.update');
        Route::patch('/master-data/pengguna/{role}/{id}/toggle', [PenggunaController::class, 'toggleStatus'])->name('master-data.pengguna.toggle');
        Route::post('/master-data/pengguna/reset-password', [PenggunaController::class, 'resetPasswordGeneral'])->name('master-data.pengguna.reset-password-general');
        Route::post('/master-data/pengguna/{role}/{id}/reset-password', [PenggunaController::class, 'resetPassword'])->name('master-data.pengguna.reset-password');
        Route::post('/master-data/pengguna/invite', [PenggunaController::class, 'invite'])->name('master-data.pengguna.invite');
        Route::delete('/master-data/pengguna/{role}/{id}', [PenggunaController::class, 'destroy'])->name('master-data.pengguna.destroy');

        // Master Data Mata Kuliah
        Route::get('/master-data/mata-kuliah', [MataKuliahController::class, 'index'])->name('master-data.mata-kuliah');
        Route::post('/master-data/mata-kuliah', [MataKuliahController::class, 'store'])->name('master-data.mata-kuliah.store');
        Route::put('/master-data/mata-kuliah/{mataKuliah}', [MataKuliahController::class, 'update'])->name('master-data.mata-kuliah.update');
        Route::patch('/master-data/mata-kuliah/{mataKuliah}/toggle', [MataKuliahController::class, 'toggleStatus'])->name('master-data.mata-kuliah.toggle');
        Route::delete('/master-data/mata-kuliah/{mataKuliah}', [MataKuliahController::class, 'destroy'])->name('master-data.mata-kuliah.destroy');


        // Kelompok PBL
        Route::get('/kelompok-pbl', [KelompokController::class, 'index'])->name('kelompok.index');
        Route::get('/kelompok', [KelompokController::class, 'index'])->name('kelompok');
        Route::post('/kelompok', [KelompokController::class, 'store'])->name('kelompok.store');
        Route::get('/kelompok/{id}', [KelompokController::class, 'show'])->name('kelompok.show');
        Route::post('/kelompok/{id}/anggota', [KelompokController::class, 'storeAnggota'])->name('kelompok.anggota.store');

        // Penugasan Dosen
        Route::get('/penugasan-dosen', [PenugasanDosenController::class, 'index'])->name('penugasan-dosen.index');
        Route::post('/penugasan-dosen', [PenugasanDosenController::class, 'store'])->name('penugasan-dosen.store');
        Route::put('/penugasan-dosen/{dosenPengampu}', [PenugasanDosenController::class, 'update'])->name('penugasan-dosen.update');
        Route::delete('/penugasan-dosen/{dosenPengampu}', [PenugasanDosenController::class, 'destroy'])->name('penugasan-dosen.destroy');
        Route::get('/penugasan-dosen/api/data', [PenugasanDosenController::class, 'apiData'])->name('penugasan-dosen.api-data');


        // Route Fitur Semester & Periode
        Route::get('/periode', [PeriodeController::class, 'index'])->name('periode.index');
        Route::post('/periode', [PeriodeController::class, 'store'])->name('periode.store');
        Route::put('/periode/{id}', [PeriodeController::class, 'update'])->name('periode.update');
        Route::patch('/periode/{id}/toggle-aktif', [PeriodeController::class, 'toggleAktif'])->name('periode.toggle-aktif');

        // Milestone Global
        Route::get('/milestone-global', [MilestoneController::class, 'index'])->name('milestone-global.index');
        Route::post('/milestone-global', [MilestoneController::class, 'store'])->name('milestone-global.store');
        Route::put('/milestone-global/{milestone}', [MilestoneController::class, 'update'])->name('milestone-global.update');
    });
});