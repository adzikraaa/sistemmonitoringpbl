<?php

namespace Database\Seeders;

use App\Models\Dosen;
use App\Models\Koordinator;
use App\Models\Mahasiswa;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Koordinator::create([
            'nama' => 'Koordinator Test',
            'email' => 'koordinator@test.com',
            'password' => 'password',
            'nip' => '199000001',
            'nidn' => '0099000001',
        ]);

        Dosen::create([
            'nama' => 'Dosen Test',
            'email' => 'dosen@test.com',
            'password' => 'password',
            'nip' => '199000002',
            'nidn' => '0099000002',
        ]);

        Mahasiswa::create([
            'nama' => 'Mahasiswa Test',
            'email' => 'mahasiswa@test.com',
            'password' => 'password',
            'nim' => '2024001',
        ]);
    }
}
