<?php

namespace Database\Seeders;

use App\Models\Dosen;
use App\Models\Kelas;
use App\Models\Koordinator;
use App\Models\Mahasiswa;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $koordinator = Koordinator::updateOrCreate(
            ['nidn' => '0099000001'],
            [
                'nama'     => 'Nadia Kusuma, S.Kom., M.T.',
                'email'    => 'nadia.kusuma@polinema.ac.id',
                'password' => 'password',
                'nidn'     => '0099000001',
            ]
        );

        $dosenList = [
            ['nidn' => '0099000002', 'nama' => 'Dosen Test', 'email' => 'dosen@test.com'],
            ['nidn' => '0012058401', 'nama' => 'Dr. Bambang Pratama, M.Kom.', 'email' => 'bambang.pratama@polinema.ac.id'],
            ['nidn' => '0024088902', 'nama' => 'Dr. Rian Hidayat, M.T.', 'email' => 'rian.hidayat@polinema.ac.id'],
            ['nidn' => '0015039203', 'nama' => 'Siti Aminah, S.Kom., M.Kom.', 'email' => 'siti.aminah@polinema.ac.id'],
        ];

        foreach ($dosenList as $d) {
            Dosen::updateOrCreate(
                ['nidn' => $d['nidn']],
                [
                    'nama'           => $d['nama'],
                    'email'          => $d['email'],
                    'password'       => 'password',
                    'nidn'           => $d['nidn'],
                    'koordinator_id' => $koordinator->id,
                    'is_aktif'       => true,
                ]
            );
        }

        $kelas3A = Kelas::where('nama_kelas', 'SIB 3A')->first();
        $kelas3B = Kelas::where('nama_kelas', 'SIB 3B')->first();
        $kelas3C = Kelas::where('nama_kelas', 'SIB 3C')->first();

        $mahasiswaList = [
            ['nim' => '2024001', 'nama' => 'Mahasiswa Test', 'email' => 'mahasiswa@test.com', 'kelas_id' => $kelas3B?->id],
            ['nim' => '2241720012', 'nama' => 'Ahmad Fadhil Pratama', 'email' => '2241720012@student.polinema.ac.id', 'kelas_id' => $kelas3A?->id],
            ['nim' => '2241720045', 'nama' => 'Bella Safitri', 'email' => '2241720045@student.polinema.ac.id', 'kelas_id' => $kelas3B?->id],
            ['nim' => '2241720089', 'nama' => 'Dimas Aditya Nugroho', 'email' => '2241720089@student.polinema.ac.id', 'kelas_id' => $kelas3C?->id],
            ['nim' => '2241720104', 'nama' => 'Fitri Rahmadani', 'email' => '2241720104@student.polinema.ac.id', 'kelas_id' => $kelas3A?->id],
            ['nim' => '2241720138', 'nama' => 'Gilang Ramadhan', 'email' => '2241720138@student.polinema.ac.id', 'kelas_id' => $kelas3B?->id],
        ];

        foreach ($mahasiswaList as $m) {
            Mahasiswa::updateOrCreate(
                ['nim' => $m['nim']],
                [
                    'nama'     => $m['nama'],
                    'email'    => $m['email'],
                    'password' => 'password',
                    'nim'      => $m['nim'],
                    'kelas_id' => $m['kelas_id'],
                    'is_aktif' => true,
                ]
            );
        }
    }
}
