<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class LoginController extends Controller
{
    public function show()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'role' => 'required|in:mahasiswa,dosen,koordinator',
            'identifier' => 'required|string',
            'password' => 'required',
        ]);

        $role = $request->role;
        $remember = $request->boolean('remember');

        // ─── Login Mahasiswa via SIAKAD ────────────────────────────────────────
        if ($role === 'mahasiswa') {
            return $this->loginViaSiakad($request, $remember);
        }

        // ─── Login Dosen / Koordinator (lokal) ────────────────────────────────
        $field = 'nidn';

        if (Auth::guard($role)->attempt([
            $field => $request->identifier,
            'password' => $request->password,
        ], $remember)) {
            $request->session()->regenerate();

            return redirect()->route("{$role}.dashboard");
        }

        return back()
            ->withInput($request->only('identifier', 'role'))
            ->withErrors(['identifier' => 'NIDN atau password salah.']);
    }

    /**
     * Autentikasi mahasiswa melalui API SIAKAD Polinema.
     * Step 1: GET halaman login untuk ambil cookie & CSRF token.
     * Step 2: POST login dengan cookie & token tersebut.
     */
    private function loginViaSiakad(Request $request, bool $remember = false)
    {
        $nim = $request->identifier;
        $password = $request->password;
        $baseUrl = 'https://siakad.polinema.ac.id';

        try {
            // Step 1: GET halaman login untuk mendapatkan session cookie & CSRF token
            $getResponse = Http::withoutVerifying()
                ->timeout(10)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                ])
                ->get("{$baseUrl}/login");

            // Ambil cookie dari response GET (session cookie SIAKAD)
            $cookies = $getResponse->cookies()->toArray();
            $cookieStr = collect($cookies)->map(fn ($c) => "{$c['Name']}={$c['Value']}")->implode('; ');

            // Coba ekstrak CSRF token dari HTML (jika ada)
            $csrfToken = null;
            if (preg_match('/name=["\']csrf_token["\'][^>]*value=["\'](.*?)["\']/i', $getResponse->body(), $m)) {
                $csrfToken = $m[1];
            } elseif (preg_match('/value=["\'](.*?)["\']\s+name=["\']csrf_token["\']/i', $getResponse->body(), $m)) {
                $csrfToken = $m[1];
            }

            // Step 2: POST login
            $postData = [
                'username' => $nim,
                'password' => $password,
            ];
            if ($csrfToken) {
                $postData['csrf_token'] = $csrfToken;
            }

            $response = Http::withoutVerifying()
                ->asForm()
                ->timeout(10)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'Referer' => "{$baseUrl}/login",
                    'Cookie' => $cookieStr,
                    'Accept' => 'application/json, text/plain, */*',
                    'Origin' => $baseUrl,
                    'X-Requested-With' => 'XMLHttpRequest',
                ])
                ->post("{$baseUrl}/login", $postData);

            $data = $response->json();

            // Log untuk debug (hapus di production)
            \Log::info('SIAKAD Login Response', [
                'status' => $response->status(),
                'body' => $response->body(),
                'data' => $data,
            ]);

            // SIAKAD mengembalikan {"output":"ok"} jika login berhasil
            if (! $response->successful() || ($data['output'] ?? '') !== 'ok') {
                $siakadMsg = $data['output'] ?? 'Login gagal';

                return back()
                    ->withInput($request->only('identifier', 'role'))
                    ->withErrors(['identifier' => 'SIAKAD: '.$siakadMsg]);
            }
        } catch (\Exception $e) {
            \Log::error('SIAKAD Login Error', ['message' => $e->getMessage()]);

            return back()
                ->withInput($request->only('identifier', 'role'))
                ->withErrors(['identifier' => 'Tidak dapat terhubung ke SIAKAD: '.$e->getMessage()]);
        }

        // Cari mahasiswa di database lokal berdasarkan NIM
        // Jika belum ada, buat akun otomatis dari data SIAKAD
        $mahasiswa = Mahasiswa::firstOrCreate(
            ['nim' => $nim],
            [
                'nama' => 'Mahasiswa '.$nim, // sementara, bisa diupdate nanti
                'email' => $nim.'@student.polinema.ac.id',
                'password' => bcrypt(\Str::random(32)), // password random (login via SIAKAD)
                'kelas_id' => null,
            ]
        );

        // Login mahasiswa ke guard 'mahasiswa'
        Auth::guard('mahasiswa')->login($mahasiswa, $remember);
        $request->session()->regenerate();

        return redirect()->route('mahasiswa.pilih-semester');
    }

    public function logout(Request $request)
    {
        $guards = ['mahasiswa', 'dosen', 'koordinator'];

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                Auth::guard($guard)->logout();
            }
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
