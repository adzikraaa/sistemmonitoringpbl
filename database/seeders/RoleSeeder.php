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
                'nama'     => 'Koordinator Test',
                'email'    => 'koordinator@test.com',
                'password' => 'password',
                'nidn'     => '0099000001',
            ]
        );

        Dosen::updateOrCreate(
            ['nidn' => '0099000002'],
            [
                'nama'           => 'Dosen Test',
                'email'          => 'dosen@test.com',
                'password'       => 'password',
                'nidn'           => '0099000002',
                'koordinator_id' => $koordinator->id,
            ]
        );

        $kelas = Kelas::where('nama_kelas', 'SIB 3B')->first();

        Mahasiswa::updateOrCreate(
            ['nim' => '2024001'],
            [
                'nama'     => 'Mahasiswa Test',
                'email'    => 'mahasiswa@test.com',
                'password' => 'password',
                'nim'      => '2024001',
                'kelas_id' => $kelas?->id,
            ]
        );
    }
}
