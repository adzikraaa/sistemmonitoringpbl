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
        $mk1 = MataKuliah::updateOrCreate(
            ['nama_matkul' => 'Pemrograman Web Lanjut'],
            ['kode' => 'SIB301', 'prodi' => 'D4 Sistem Informasi Bisnis', 'sks' => 3, 'semester' => 5, 'is_aktif' => true]
        );

        $mk2 = MataKuliah::updateOrCreate(
            ['nama_matkul' => 'Manajemen Proyek Sistem Informasi'],
            ['kode' => 'SIB302', 'prodi' => 'D4 Sistem Informasi Bisnis', 'sks' => 3, 'semester' => 5, 'is_aktif' => true]
        );

        $mk3 = MataKuliah::updateOrCreate(
            ['nama_matkul' => 'Penjaminan Mutu Perangkat Lunak'],
            ['kode' => 'SIB303', 'prodi' => 'D4 Sistem Informasi Bisnis', 'sks' => 3, 'semester' => 5, 'is_aktif' => true]
        );

        // Kelas terhubung ke periode
        foreach (['SIB 3A', 'SIB 3B', 'SIB 3C'] as $namaKelas) {
            Kelas::updateOrCreate(
                ['nama_kelas' => $namaKelas],
                [
                    'prodi'      => 'D4 Sistem Informasi Bisnis',
                    'periode_id' => $periode->id,
                    'is_aktif'   => true,
                ]
            );
        }
    }
}
