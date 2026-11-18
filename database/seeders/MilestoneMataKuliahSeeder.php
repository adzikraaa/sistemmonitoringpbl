<?php

namespace Database\Seeders;

use App\Services\MilestoneMataKuliahSync;
use Illuminate\Database\Seeder;

class MilestoneMataKuliahSeeder extends Seeder
{
    public function run(): void
    {
        app(MilestoneMataKuliahSync::class)->forSemua();
    }
}
