<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Koordinator;
use App\Models\Mahasiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AuthenticationAndRbacIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private Koordinator $koordinator;
    private Dosen $dosen;

    protected function setUp(): void
    {
        parent::setUp();

        $this->koordinator = Koordinator::create([
            'nama' => 'Admin Koordinator',
            'email' => 'koor@test.com',
            'nidn' => '0099000001',
            'password' => Hash::make('password123'),
        ]);

        $this->dosen = Dosen::create([
            'nama' => 'Dosen Pengampu',
            'email' => 'dosen@test.com',
            'nidn' => '0099000002',
            'password' => Hash::make('password123'),
            'is_aktif' => true,
        ]);
    }

    /**
     * Uji login Koordinator berhasil dengan NIDN dan password.
     */
    public function test_koordinator_berhasil_login_dan_diarahkan_ke_dashboard(): void
    {
        $response = $this->post(route('login'), [
            'identifier' => '0099000001',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('koordinator.dashboard'));
        $this->assertTrue(Auth::guard('koordinator')->check());
        $this->assertSame($this->koordinator->id, Auth::guard('koordinator')->id());
    }

    /**
     * Uji login Dosen berhasil dengan NIDN dan password.
     */
    public function test_dosen_berhasil_login_dan_diarahkan_ke_pilih_semester(): void
    {
        $response = $this->post(route('login'), [
            'identifier' => '0099000002',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('dosen.pilih-semester'));
        $this->assertTrue(Auth::guard('dosen')->check());
        $this->assertSame($this->dosen->id, Auth::guard('dosen')->id());
    }

    /**
     * Uji login Mahasiswa melalui API SIAKAD dengan Http::fake.
     */
    public function test_mahasiswa_berhasil_login_via_siakad_dan_record_terbuat(): void
    {
        Http::fake([
            'https://siakad.polinema.ac.id/login' => Http::sequence()
                ->push('<html><input name="csrf_token" value="dummy-csrf-token"></html>', 200, ['Set-Cookie' => 'ci_session=fake_cookie'])
                ->push(['output' => 'ok'], 200),
        ]);

        $nim = '244107060099';

        $response = $this->post(route('login'), [
            'identifier' => $nim,
            'password' => 'rahasia123',
        ]);

        $response->assertRedirect(route('mahasiswa.pilih-semester'));
        $this->assertTrue(Auth::guard('mahasiswa')->check());

        $this->assertDatabaseHas('mahasiswa', [
            'nim' => $nim,
            'email' => $nim . '@student.polinema.ac.id',
        ]);
    }

    /**
     * Uji login gagal jika password salah untuk NIDN lokal.
     */
    public function test_login_gagal_jika_password_salah(): void
    {
        $response = $this->from(route('login'))->post(route('login'), [
            'identifier' => '0099000002',
            'password' => 'password_salah',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('identifier');
        $this->assertFalse(Auth::guard('dosen')->check());
        $this->assertFalse(Auth::guard('koordinator')->check());
        $this->assertFalse(Auth::guard('mahasiswa')->check());
    }

    /**
     * Uji validasi form login: identifier dan password wajib diisi.
     */
    public function test_form_login_memvalidasi_kolom_wajib(): void
    {
        $response = $this->from(route('login'))->post(route('login'), [
            'identifier' => '',
            'password' => '',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['identifier', 'password']);
    }

    /**
     * Uji logout mengeluarkan seluruh guard dan mengarahkan ke halaman login.
     */
    public function test_logout_mengakhiri_sesi_dan_mengarahkan_ke_login(): void
    {
        $this->actingAs($this->koordinator, 'koordinator');
        $this->assertTrue(Auth::guard('koordinator')->check());

        $response = $this->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertFalse(Auth::guard('koordinator')->check());
    }

    /**
     * Uji proteksi middleware: pengguna yang belum login diarahkan ke login saat mengakses dashboard.
     */
    public function test_unauthenticated_user_diarahkan_ke_login(): void
    {
        $this->get(route('mahasiswa.dashboard'))->assertRedirect(route('login'));
        $this->get(route('dosen.dashboard'))->assertRedirect(route('login'));
        $this->get(route('koordinator.dashboard'))->assertRedirect(route('login'));
    }

    /**
     * Uji pembatasan hak akses: Mahasiswa tidak dapat mengakses area Koordinator.
     */
    public function test_mahasiswa_tidak_dapat_mengakses_area_koordinator(): void
    {
        $mahasiswa = Mahasiswa::create([
            'nama' => 'Mahasiswa Test',
            'email' => 'mhs@test.com',
            'password' => 'password123',
            'nim' => '244107060001',
            'is_aktif' => true,
        ]);

        // Guard koordinator tidak aktif untuk mahasiswa, route middleware auth:koordinator akan melempar unauthenticated -> redirect to login
        $this->actingAs($mahasiswa, 'mahasiswa');

        $response = $this->get(route('koordinator.dashboard'));
        $response->assertRedirect(route('login'));
    }
}
