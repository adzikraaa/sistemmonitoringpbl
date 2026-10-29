<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\MataKuliah;
use App\Models\Periode;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1 periode aktif
        $periode = Periode::updateOrCreate(
            ['nama_periode' => 'Ganjil 2026/2027'],
            [
                'tahun_akademik'  => '2026/2027',
                'tanggal_mulai'   => '2026-09-01',
                'tanggal_selesai' => '2027-01-31',
                'is_aktif'        => true,
            ]
        );

        // 3 mata kuliah
        MataKuliah::updateOrCreate(
            ['nama_matkul' => 'Pemrograman Web Lanjut'],
            ['prodi' => 'Teknologi Informasi', 'sks' => 3, 'is_aktif' => true]
        );

        MataKuliah::updateOrCreate(
            ['nama_matkul' => 'Manajemen Proyek Sistem Informasi'],
            ['prodi' => 'Teknologi Informasi', 'sks' => 3, 'is_aktif' => true]
        );

        MataKuliah::updateOrCreate(
            ['nama_matkul' => 'Penjaminan Mutu Perangkat Lunak'],
            ['prodi' => 'Teknologi Informasi', 'sks' => 3, 'is_aktif' => true]
        );

        // 1 kelas terhubung ke periode
        Kelas::updateOrCreate(
            ['nama_kelas' => 'SIB 3B'],
            [
                'prodi'     => 'Teknologi Informasi',
                'periode_id' => $periode->id,
                'is_aktif'  => true,
            ]
        );
    }
}
