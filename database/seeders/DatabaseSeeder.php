<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command?->warn(
                'DatabaseSeeder dilewati di production. Jalankan seeder master secara eksplisit setelah persetujuan.'
            );

            return;
        }

        $this->call([
            MasterDataSeeder::class, // periode, mata_kuliah, kelas
            MilestoneSeeder::class,  // milestone & deliverable (bergantung pada periode aktif)
            MilestoneMataKuliahSeeder::class, // pivot milestone x mata kuliah aktif
            RoleSeeder::class,       // koordinator, dosen, mahasiswa
            KelompokSeeder::class,   // kelompok, anggota_kelompok, dosen_pengampu
        ]);
    }
}
