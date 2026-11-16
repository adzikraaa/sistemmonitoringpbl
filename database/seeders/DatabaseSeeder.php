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
        $this->call([
            MasterDataSeeder::class, // periode, mata_kuliah, kelas
            MilestoneSeeder::class,  // milestone & deliverable (bergantung pada periode aktif)
            RoleSeeder::class,       // koordinator, dosen, mahasiswa
            KelompokSeeder::class,   // kelompok, anggota_kelompok, dosen_pengampu
        ]);
    }
}
