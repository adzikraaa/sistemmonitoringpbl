<?php

namespace Tests\Feature;

use App\Models\Dosen;
use Database\Seeders\DosenUatSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DosenUatSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_membuat_akun_dosen_aktif_yang_dapat_digunakan(): void
    {
        $this->seed(DosenUatSeeder::class);

        $dosenList = Dosen::query()->where('nidn', 'like', 'UAT-DOSEN-%')->get();

        $this->assertCount(5, $dosenList);
        $this->assertTrue($dosenList->every(fn (Dosen $dosen): bool => $dosen->is_aktif));
        $this->assertTrue($dosenList->every(fn (Dosen $dosen): bool => Hash::check('DosenUAT2026!', $dosen->password)));
        $this->assertDatabaseCount('dosen_pengampu', 0);
    }

    public function test_seeder_dapat_dijalankan_ulang_tanpa_menggandakan_dosen(): void
    {
        $this->seed(DosenUatSeeder::class);
        $this->seed(DosenUatSeeder::class);

        $this->assertSame(5, Dosen::query()->where('nidn', 'like', 'UAT-DOSEN-%')->count());
    }
}
