<?php

namespace Tests\Feature;

use App\Models\AnggotaKelompok;
use App\Models\Kelas;
use App\Models\Kelompok;
use App\Models\Mahasiswa;
use App\Models\Periode;
use App\Models\Proposal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DummyKelompokTestingScenarioTest extends TestCase
{
    use RefreshDatabase;

    private Periode $periode;

    private Kelas $kelas;

    protected function setUp(): void
    {
        parent::setUp();

        $this->periode = Periode::create([
            'nama_periode' => 'Ganjil 2026/2027',
            'tahun_akademik' => '2026/2027',
            'tanggal_mulai' => '2026-09-01',
            'tanggal_selesai' => '2027-01-31',
            'is_aktif' => true,
        ]);

        $this->kelas = Kelas::create([
            'nama_kelas' => 'TI 3A',
            'periode_id' => $this->periode->id,
            'is_aktif' => true,
        ]);
    }

    private function mockSiakadLoginSuccess(): void
    {
        Http::fake([
            'https://siakad.polinema.ac.id/login' => function ($request) {
                if ($request->method() === 'GET') {
                    return Http::response('<html><input type="hidden" name="csrf_token" value="test-csrf-token"></html>', 200);
                }

                return Http::response(['output' => 'ok'], 200);
            },
        ]);
    }

    /**
     * Skenario A: Login Pertama
     * - Mahasiswa login via SIAKAD.
     * - Data mahasiswa tersinkronisasi.
     * - Kelompok dummy otomatis terbentuk.
     * - Mahasiswa login menjadi Anggota 1 (Ketua).
     * - Tiga anggota dummy (Anggota 2, 3, 4) ditambahkan.
     * - Total anggota tepat empat orang.
     */
    public function test_skenario_a_login_pertama_otomatis_membentuk_kelompok_dummy_tepat_empat_anggota(): void
    {
        $this->mockSiakadLoginSuccess();

        $nim = '2241720001';
        $response = $this->post(route('login'), [
            'identifier' => $nim,
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('mahasiswa.pilih-semester'));

        // Pastikan mahasiswa tersimpan
        $mahasiswa = Mahasiswa::where('nim', $nim)->first();
        $this->assertNotNull($mahasiswa);
        $this->assertSame('Mahasiswa '.$nim, $mahasiswa->nama);

        // Pastikan kelompok terbentuk pada periode aktif
        $kelompok = Kelompok::where('periode_id', $this->periode->id)
            ->whereHas('mahasiswa', fn ($q) => $q->where('mahasiswa.id', $mahasiswa->id))
            ->first();

        $this->assertNotNull($kelompok);
        $this->assertSame('Kelompok Uji '.$nim, $kelompok->nama_kelompok);

        // Pastikan total anggota tepat 4 orang
        $anggotaList = $kelompok->anggotaKelompok()->with('mahasiswa')->get();
        $this->assertCount(4, $anggotaList);

        // Anggota 1 adalah mahasiswa asli (Ketua)
        $ketua = $kelompok->ketua();
        $this->assertNotNull($ketua);
        $this->assertSame($mahasiswa->id, $ketua->mahasiswa_id);
        $this->assertTrue($ketua->is_ketua);
        $this->assertSame('Ketua', $ketua->role_label);

        // Anggota 2, 3, 4 adalah anggota dummy
        $dummyAnggota = $anggotaList->where('mahasiswa_id', '!=', $mahasiswa->id)->values();
        $this->assertCount(3, $dummyAnggota);

        for ($i = 0; $i < 3; $i++) {
            $expectedIndex = $i + 2;
            $item = $dummyAnggota[$i];
            $this->assertFalse($item->is_ketua);
            $this->assertSame('Anggota', $item->role_label);
            $this->assertSame("Anggota {$expectedIndex}", $item->mahasiswa->nama);
            $this->assertStringStartsWith('DMY', $item->mahasiswa->nim);
            $this->assertStringContainsString('@student.dummy.local', $item->mahasiswa->email);
        }
    }

    /**
     * Skenario B: Login Ulang (Idempotensi)
     * - Mahasiswa yang sudah punya kelompok dummy login kembali.
     * - Kelompok yang sama tetap digunakan.
     * - Tidak ada kelompok duplikat atau anggota duplikat.
     */
    public function test_skenario_b_login_ulang_tidak_menduplikasi_kelompok_atau_anggota(): void
    {
        $this->mockSiakadLoginSuccess();

        $nim = '2241720002';

        // Login pertama
        $this->post(route('login'), [
            'identifier' => $nim,
            'password' => 'secret123',
        ]);

        $initialKelompokCount = Kelompok::count();
        $initialMahasiswaCount = Mahasiswa::count();
        $initialAnggotaCount = AnggotaKelompok::count();

        $this->assertSame(1, $initialKelompokCount);
        $this->assertSame(4, $initialMahasiswaCount);
        $this->assertSame(4, $initialAnggotaCount);

        // Logout
        $this->post(route('logout'));
        $this->assertNull(Auth::guard('mahasiswa')->user());

        // Login kedua (ulang)
        $this->mockSiakadLoginSuccess();
        $response = $this->post(route('login'), [
            'identifier' => $nim,
            'password' => 'secret123',
        ]);
        $response->assertRedirect(route('mahasiswa.pilih-semester'));

        // Jumlah kelompok, mahasiswa, dan anggota tetap sama (tidak terduplikasi)
        $this->assertSame($initialKelompokCount, Kelompok::count());
        $this->assertSame($initialMahasiswaCount, Mahasiswa::count());
        $this->assertSame($initialAnggotaCount, AnggotaKelompok::count());

        $mahasiswa = Mahasiswa::where('nim', $nim)->first();
        $kelompok = Kelompok::where('periode_id', $this->periode->id)
            ->whereHas('mahasiswa', fn ($q) => $q->where('mahasiswa.id', $mahasiswa->id))
            ->get();

        $this->assertCount(1, $kelompok);
        $this->assertSame(4, $kelompok->first()->anggotaKelompok()->count());
    }

    /**
     * Skenario C: Mahasiswa Lain
     * - Mahasiswa kedua login dan mendapatkan kelompok tersendiri.
     * - Anggota kedua kelompok tidak saling tercampur.
     */
    public function test_skenario_c_mahasiswa_lain_mendapatkan_kelompok_terpisah_dan_tidak_tercampur(): void
    {
        // Mahasiswa 1 login
        $this->mockSiakadLoginSuccess();
        $this->post(route('login'), [
            'identifier' => '2241720010',
            'password' => 'secret123',
        ]);
        $this->post(route('logout'));

        // Mahasiswa 2 login
        $this->mockSiakadLoginSuccess();
        $this->post(route('login'), [
            'identifier' => '2241720020',
            'password' => 'secret123',
        ]);

        // Dua kelompok terbentuk
        $this->assertSame(2, Kelompok::count());

        $mhs1 = Mahasiswa::where('nim', '2241720010')->first();
        $mhs2 = Mahasiswa::where('nim', '2241720020')->first();

        $kelompok1 = Kelompok::whereHas('mahasiswa', fn ($q) => $q->where('mahasiswa.id', $mhs1->id))->first();
        $kelompok2 = Kelompok::whereHas('mahasiswa', fn ($q) => $q->where('mahasiswa.id', $mhs2->id))->first();

        $this->assertNotSame($kelompok1->id, $kelompok2->id);

        $anggotaIds1 = $kelompok1->mahasiswa()->pluck('mahasiswa.id')->all();
        $anggotaIds2 = $kelompok2->mahasiswa()->pluck('mahasiswa.id')->all();

        // Tidak ada id anggota yang beririsan
        $intersection = array_intersect($anggotaIds1, $anggotaIds2);
        $this->assertEmpty($intersection);
        $this->assertCount(4, $anggotaIds1);
        $this->assertCount(4, $anggotaIds2);
    }

    /**
     * Skenario D: Mahasiswa dengan Kelompok Asli
     * - Mahasiswa sudah memiliki kelompok riil sebelum login.
     * - Saat login, sistem tidak membuat kelompok dummy dan tidak mengubah keanggotaan asli.
     */
    public function test_skenario_d_mahasiswa_dengan_kelompok_asli_tidak_diubah(): void
    {
        // Buat mahasiswa dan kelompok riil terlebih dahulu
        $mhsAsli = Mahasiswa::create([
            'nama' => 'Budi Santoso Asli',
            'email' => 'budi@polinema.ac.id',
            'password' => bcrypt('password123'),
            'nim' => '2241720099',
            'kelas_id' => $this->kelas->id,
            'is_aktif' => true,
        ]);

        $kelompokAsli = Kelompok::create([
            'nama_kelompok' => 'Kelompok 1 Proyek Asli',
            'judul_proyek' => 'Sistem Pemantauan IoT Pabrik',
            'kelas_id' => $this->kelas->id,
            'periode_id' => $this->periode->id,
            'status' => 'Aktif',
            'tanggal_dibuat' => '2026-09-02',
        ]);

        AnggotaKelompok::create([
            'kelompok_id' => $kelompokAsli->id,
            'mahasiswa_id' => $mhsAsli->id,
            'role_label' => 'Anggota',
            'is_ketua' => false,
        ]);

        $this->assertSame(1, Kelompok::count());
        $this->assertSame(1, AnggotaKelompok::count());

        // Login dengan akun mahasiswa asli ini
        $this->mockSiakadLoginSuccess();
        $this->post(route('login'), [
            'identifier' => '2241720099',
            'password' => 'secret123',
        ]);

        // Tidak ada kelompok baru yang dibuat
        $this->assertSame(1, Kelompok::count());
        $this->assertSame(1, AnggotaKelompok::count());

        // Data keanggotaan tetap sama persis
        $anggota = AnggotaKelompok::first();
        $this->assertSame($kelompokAsli->id, $anggota->kelompok_id);
        $this->assertSame($mhsAsli->id, $anggota->mahasiswa_id);
        $this->assertFalse($anggota->is_ketua);
        $this->assertSame('Anggota', $anggota->role_label);
    }

    /**
     * Skenario E: Pengajuan Proposal
     * - Mahasiswa yang memiliki kelompok dummy dapat membuka halaman proposal.
     * - Mengunggah berkas proposal PDF dan mengisi formulir.
     * - Berkas tersimpan di disk local, data tersimpan di DB, dan halaman menampilkan data proposal baru.
     */
    public function test_skenario_e_pengajuan_proposal_berhasil_tersimpan_di_database_dan_storage(): void
    {
        Storage::fake('local');
        $this->mockSiakadLoginSuccess();

        $nim = '2241720055';
        $this->post(route('login'), [
            'identifier' => $nim,
            'password' => 'secret123',
        ]);

        $mahasiswa = Mahasiswa::where('nim', $nim)->firstOrFail();

        // 1. Akses halaman proposal
        $responsePage = $this->actingAs($mahasiswa, 'mahasiswa')
            ->get(route('mahasiswa.proposal'));

        $responsePage->assertOk();
        $responsePage->assertSee('Ajukan proposal');

        // 2. Submit form pengajuan proposal beserta file PDF
        $file = UploadedFile::fake()->create('dokumen-proposal-pbl.pdf', 1024, 'application/pdf');

        $responseSubmit = $this->actingAs($mahasiswa, 'mahasiswa')
            ->post(route('mahasiswa.proposal.store'), [
                'judul' => 'Sistem Otomasi Monitoring PBL Polinema',
                'deskripsi' => 'Platform web monitoring logbook, milestone, dan proposal.',
                'tujuan' => 'Meningkatkan efisiensi evaluasi proyek PBL berbasis luaran.',
                'latar_belakang' => 'Sebelumnya monitoring dilakukan parsial dan manual.',
                'metode' => 'Metode Scrum dengan sprint review mingguan.',
                'file_proposal' => $file,
            ]);

        $responseSubmit->assertRedirect(route('mahasiswa.proposal'));
        $responseSubmit->assertSessionHas('success');

        // 3. Verifikasi di database
        $this->assertDatabaseHas('proposal', [
            'judul' => 'Sistem Otomasi Monitoring PBL Polinema',
            'status' => 'diajukan',
        ]);

        $proposal = Proposal::where('judul', 'Sistem Otomasi Monitoring PBL Polinema')->firstOrFail();
        $this->assertNotNull($proposal->file_proposal);

        // 4. Verifikasi file tersimpan di disk storage 'local'
        Storage::disk('local')->assertExists($proposal->file_proposal);

        // 5. Verifikasi halaman proposal sekarang menampilkan proposal yang baru diajukan
        $responseAfterSubmit = $this->actingAs($mahasiswa, 'mahasiswa')
            ->get(route('mahasiswa.proposal'));

        $responseAfterSubmit->assertOk();
        $responseAfterSubmit->assertSee('Sistem Otomasi Monitoring PBL Polinema');
        $responseAfterSubmit->assertSee('Diajukan');
    }
}
