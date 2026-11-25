<?php

namespace Database\Seeders;

use App\Models\Dosen;
use Illuminate\Database\Seeder;

class DosenUatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dosenList = [
            ['nidn' => 'UAT-DOSEN-001', 'nama' => 'Rian Hidayat, S.Kom., M.Kom.', 'email' => 'rian.hidayat.uat@sim-pbl.test'],
            ['nidn' => 'UAT-DOSEN-002', 'nama' => 'Siti Rahmawati, S.Kom., M.T.', 'email' => 'siti.rahmawati.uat@sim-pbl.test'],
            ['nidn' => 'UAT-DOSEN-003', 'nama' => 'Budi Santoso, S.Kom., M.Kom.', 'email' => 'budi.santoso.uat@sim-pbl.test'],
            ['nidn' => 'UAT-DOSEN-004', 'nama' => 'Dewi Lestari, S.Kom., M.Sc.', 'email' => 'dewi.lestari.uat@sim-pbl.test'],
            ['nidn' => 'UAT-DOSEN-005', 'nama' => 'Ahmad Fauzan, S.Kom., M.T.', 'email' => 'ahmad.fauzan.uat@sim-pbl.test'],
        ];

        foreach ($dosenList as $dosen) {
            Dosen::query()->updateOrCreate(
                ['nidn' => $dosen['nidn']],
                [
                    'nama' => $dosen['nama'],
                    'email' => $dosen['email'],
                    'password' => 'DosenUAT2026!',
                    'is_aktif' => true,
                ]
            );
        }
    }
}
