<?php

namespace Database\Seeders;

use App\Models\AnggotaKelompok;
use App\Models\Dosen;
use App\Models\DosenPengampu;
use App\Models\Kelas;
use App\Models\Kelompok;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Periode;
use Illuminate\Database\Seeder;

class KelompokSeeder extends Seeder
{
    public function run(): void
    {
        $periode  = Periode::where('is_aktif', true)->firstOrFail();
        $kelas    = Kelas::where('nama_kelas', 'SIB 3B')->firstOrFail();
        $dosen    = Dosen::where('nidn', '0099000002')->firstOrFail();
        $mahasiswa = Mahasiswa::where('nim', '2024001')->firstOrFail();
        $matkul   = MataKuliah::where('nama_matkul', 'Pemrograman Web Lanjut')->firstOrFail();

        // 1 kelompok contoh
        $kelompok = Kelompok::updateOrCreate(
            [
                'nama_kelompok' => 'Kelompok Contoh',
                'kelas_id'      => $kelas->id,
                'periode_id'    => $periode->id,
            ],
            [
                'dosen_id'      => $dosen->id,
                'jadwal_bimbingan' => 'Jumat, 13.00–15.00',
                'tanggal_dibuat' => now()->toDateString(),
            ]
        );

        // Anggota kelompok
        AnggotaKelompok::updateOrCreate(
            ['kelompok_id' => $kelompok->id, 'mahasiswa_id' => $mahasiswa->id],
            ['role_label' => 'PM', 'is_ketua' => true]
        );

        // Dosen pengampu
        DosenPengampu::updateOrCreate(
            [
                'dosen_id'      => $dosen->id,
                'mata_kuliah_id' => $matkul->id,
                'kelas_id'      => $kelas->id,
            ]
        );
    }
}
