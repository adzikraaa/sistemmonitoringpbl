<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Kelas;
use App\Models\Kelompok;
use App\Models\Periode;
use App\Models\Proposal;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class DosenDashboardReminderTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_dashboard_reminder_uses_the_latest_real_group_update(): void
    {
        $this->travelTo('2026-11-24 12:00:00');
        [$dosen, $periode, $kelompok] = $this->createDashboardContext(now()->subDays(12));

        $proposal = Proposal::create([
            'kelompok_id' => $kelompok->id,
            'judul' => 'Proposal terbaru',
            'deskripsi' => 'Deskripsi proposal.',
            'latar_belakang' => 'Latar belakang proposal.',
            'tujuan' => 'Tujuan proposal.',
            'metode' => 'Metode proposal.',
            'status' => 'diajukan',
            'tanggal_pengajuan' => now()->subDays(4)->toDateString(),
        ]);
        $proposal->forceFill([
            'created_at' => now()->subDays(4),
            'updated_at' => now()->subDays(4),
        ])->saveQuietly();

        $this->actingAs($dosen, 'dosen')
            ->withSession(['dosen.semester_id' => (string) $periode->id])
            ->get(route('dosen.dashboard'))
            ->assertOk()
            ->assertViewHas('sinyalKelompok', function (array $sinyal) use ($kelompok): bool {
                return $sinyal['id'] === $kelompok->id
                    && $sinyal['nama'] === 'Kelompok Data Nyata'
                    && $sinyal['hari_lalu'] === 4
                    && $sinyal['has_update'] === true;
            })
            ->assertSee('Pembaruan terakhir Kelompok Data Nyata dikirim')
            ->assertSee('4 hari lalu');
    }

    public function test_dashboard_reminder_uses_registration_date_when_group_has_no_updates(): void
    {
        $this->travelTo('2026-11-24 12:00:00');
        [$dosen, $periode, $kelompok] = $this->createDashboardContext(now()->subDays(6));

        $this->actingAs($dosen, 'dosen')
            ->withSession(['dosen.semester_id' => (string) $periode->id])
            ->get(route('dosen.dashboard'))
            ->assertOk()
            ->assertViewHas('sinyalKelompok', function (array $sinyal) use ($kelompok): bool {
                return $sinyal['id'] === $kelompok->id
                    && $sinyal['hari_lalu'] === 6
                    && $sinyal['has_update'] === false;
            })
            ->assertSee('Kelompok Data Nyata belum pernah mengirim pembaruan sejak terdaftar')
            ->assertSee('6 hari lalu');
    }

    /**
     * @return array{Dosen, Periode, Kelompok}
     */
    private function createDashboardContext(\DateTimeInterface $registeredAt): array
    {
        $periode = Periode::create([
            'nama_periode' => 'Semester 5',
            'tahun_akademik' => '2026/2027',
            'tanggal_mulai' => '2026-08-01',
            'tanggal_selesai' => '2026-12-31',
            'is_aktif' => true,
        ]);
        $dosen = Dosen::create([
            'nama' => 'Dosen Penguji',
            'email' => 'dosen-reminder@example.com',
            'password' => 'secret123',
            'nidn' => '123456789',
            'is_aktif' => true,
        ]);
        $kelas = Kelas::create([
            'nama_kelas' => 'SIB 3A',
            'prodi' => 'Sistem Informasi Bisnis',
            'periode_id' => $periode->id,
            'is_aktif' => true,
        ]);
        $kelompok = Kelompok::create([
            'nama_kelompok' => 'Kelompok Data Nyata',
            'dosen_id' => $dosen->id,
            'kelas_id' => $kelas->id,
            'periode_id' => $periode->id,
            'status' => 'aktif',
            'tanggal_dibuat' => $registeredAt->format('Y-m-d'),
        ]);
        $kelompok->forceFill([
            'created_at' => $registeredAt,
            'updated_at' => $registeredAt,
        ])->saveQuietly();

        return [$dosen, $periode, $kelompok];
    }
}
