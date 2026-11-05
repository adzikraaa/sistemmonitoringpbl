<?php

namespace Database\Seeders;

use App\Models\Dosen;
use App\Models\DosenPengampu;
use App\Models\Kelas;
use App\Models\Koordinator;
use App\Models\MataKuliah;
use App\Models\Periode;
use Illuminate\Database\Seeder;

class PenugasanDosenSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Periode Aktif
        $periode = Periode::where('is_aktif', true)->first();
        if (!$periode) {
            $periode = Periode::create([
                'nama_periode'    => 'Ganjil 2026/2027',
                'tahun_akademik'  => '2026/2027',
                'tanggal_mulai'   => '2026-08-31',
                'tanggal_selesai' => '2027-01-15',
                'is_aktif'        => true,
            ]);
        }

        // 2. Akun Koordinator (Nadia Kusuma)
        $koordinator = Koordinator::firstOrCreate(
            ['nidn' => '0099000001'],
            [
                'nama'     => 'Nadia Kusuma',
                'email'    => 'koordinator@test.com',
                'password' => 'password',
            ]
        );
        if ($koordinator->nama !== 'Nadia Kusuma') {
            $koordinator->update(['nama' => 'Nadia Kusuma']);
        }

        // 3. 3 Mata Kuliah Penilai Wajib
        $mkPwl = MataKuliah::firstOrCreate(
            ['nama_matkul' => 'Pemrograman Web Lanjut'],
            ['prodi' => 'D4 Sistem Informasi Bisnis', 'sks' => 3, 'is_aktif' => true]
        );
        $mkMpsi = MataKuliah::firstOrCreate(
            ['nama_matkul' => 'Manajemen Proyek Sistem Informasi'],
            ['prodi' => 'D4 Sistem Informasi Bisnis', 'sks' => 3, 'is_aktif' => true]
        );
        $mkPmpl = MataKuliah::firstOrCreate(
            ['nama_matkul' => 'Penjaminan Mutu Perangkat Lunak'],
            ['prodi' => 'D4 Sistem Informasi Bisnis', 'sks' => 3, 'is_aktif' => true]
        );

        // 4. 3 Kelas Aktif
        $kelasA = Kelas::firstOrCreate(
            ['nama_kelas' => 'SIB 3A'],
            ['prodi' => 'D4 Sistem Informasi Bisnis', 'periode_id' => $periode->id, 'is_aktif' => true]
        );
        $kelasB = Kelas::firstOrCreate(
            ['nama_kelas' => 'SIB 3B'],
            ['prodi' => 'D4 Sistem Informasi Bisnis', 'periode_id' => $periode->id, 'is_aktif' => true]
        );
        $kelasC = Kelas::firstOrCreate(
            ['nama_kelas' => 'SIB 3C'],
            ['prodi' => 'D4 Sistem Informasi Bisnis', 'periode_id' => $periode->id, 'is_aktif' => true]
        );

        // 5. 8 Dosen Aktif sesuai referensi SIM-PBL
        $dosenList = [
            'DSN-001' => Dosen::firstOrCreate(['nidn' => 'DSN-001'], ['nama' => 'Dr. Rian Hidayat',         'email' => 'rian@test.com',   'password' => 'password', 'is_aktif' => true]),
            'DSN-002' => Dosen::firstOrCreate(['nidn' => 'DSN-002'], ['nama' => 'Siti Farida, M.T.',        'email' => 'siti@test.com',   'password' => 'password', 'is_aktif' => true]),
            'DSN-003' => Dosen::firstOrCreate(['nidn' => 'DSN-003'], ['nama' => 'Bambang Pratama, M.Kom.', 'email' => 'bambang@test.com','password' => 'password', 'is_aktif' => true]),
            'DSN-004' => Dosen::firstOrCreate(['nidn' => 'DSN-004'], ['nama' => 'Dr. Ahmad Fikri',         'email' => 'ahmad@test.com',  'password' => 'password', 'is_aktif' => true]),
            'DSN-005' => Dosen::firstOrCreate(['nidn' => 'DSN-005'], ['nama' => 'Dian Hanifudin, M.Kom.',  'email' => 'dian@test.com',   'password' => 'password', 'is_aktif' => true]),
            'DSN-006' => Dosen::firstOrCreate(['nidn' => 'DSN-006'], ['nama' => 'Ir. Maya Safitri, M.Sc.', 'email' => 'maya@test.com',   'password' => 'password', 'is_aktif' => true]),
            'DSN-007' => Dosen::firstOrCreate(['nidn' => 'DSN-007'], ['nama' => 'Fitri Rahmawati, M.Kom.', 'email' => 'fitri@test.com',  'password' => 'password', 'is_aktif' => true]),
            'DSN-008' => Dosen::firstOrCreate(['nidn' => 'DSN-008'], ['nama' => 'Indra Hermawan, M.T.',    'email' => 'indra@test.com',  'password' => 'password', 'is_aktif' => true]),
        ];

        // 6. Penugasan Dosen Penilai PBL (DosenPengampu)
        // Kelas SIB 3A (Lengkap 3/3)
        DosenPengampu::updateOrCreate(
            ['kelas_id' => $kelasA->id, 'mata_kuliah_id' => $mkPwl->id],
            ['dosen_id' => $dosenList['DSN-001']->id]
        );
        DosenPengampu::updateOrCreate(
            ['kelas_id' => $kelasA->id, 'mata_kuliah_id' => $mkMpsi->id],
            ['dosen_id' => $dosenList['DSN-002']->id]
        );
        DosenPengampu::updateOrCreate(
            ['kelas_id' => $kelasA->id, 'mata_kuliah_id' => $mkPmpl->id],
            ['dosen_id' => $dosenList['DSN-003']->id]
        );

        // Kelas SIB 3B (Lengkap 3/3)
        DosenPengampu::updateOrCreate(
            ['kelas_id' => $kelasB->id, 'mata_kuliah_id' => $mkPwl->id],
            ['dosen_id' => $dosenList['DSN-004']->id]
        );
        DosenPengampu::updateOrCreate(
            ['kelas_id' => $kelasB->id, 'mata_kuliah_id' => $mkMpsi->id],
            ['dosen_id' => $dosenList['DSN-005']->id]
        );
        DosenPengampu::updateOrCreate(
            ['kelas_id' => $kelasB->id, 'mata_kuliah_id' => $mkPmpl->id],
            ['dosen_id' => $dosenList['DSN-006']->id]
        );

        // Kelas SIB 3C (Fitri dan Indra sudah mengampu SIB 3C sesuai direktori screenshot)
        DosenPengampu::updateOrCreate(
            ['kelas_id' => $kelasC->id, 'mata_kuliah_id' => $mkPwl->id],
            ['dosen_id' => $dosenList['DSN-007']->id]
        );
        DosenPengampu::updateOrCreate(
            ['kelas_id' => $kelasC->id, 'mata_kuliah_id' => $mkMpsi->id],
            ['dosen_id' => $dosenList['DSN-008']->id]
        );
    }
}
