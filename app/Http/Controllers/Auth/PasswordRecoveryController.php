<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;

class PasswordRecoveryController extends Controller
{
    /**
     * Tampilkan halaman pemulihan password.
     */
    public function show()
    {
        return view('auth.recovery-password');
    }

    /**
     * Proses permintaan pemulihan password.
     *
     * Validasi NIM, tanggal lahir, dan email mahasiswa.
     * Jika cocok, arahkan ke login dengan pesan instruksi.
     */
    public function submit(Request $request)
    {
        $request->validate([
            'nim'           => 'required|string',
            'tanggal_lahir' => 'required|date',
            'email'         => 'required|email',
        ], [
            'nim.required'           => 'NIM wajib diisi.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tanggal_lahir.date'     => 'Format tanggal lahir tidak valid.',
            'email.required'         => 'Email pemulihan wajib diisi.',
            'email.email'            => 'Format email tidak valid.',
        ]);

        // Cek apakah mahasiswa dengan NIM tersebut ada di database
        $mahasiswa = Mahasiswa::where('nim', $request->nim)->first();

        if (! $mahasiswa) {
            return back()
                ->withInput()
                ->withErrors(['nim' => 'NIM tidak ditemukan dalam sistem.']);
        }

        // Verifikasi email (email pada database lokal)
        if ($mahasiswa->email && strtolower($mahasiswa->email) !== strtolower($request->email)) {
            return back()
                ->withInput($request->only('nim', 'tanggal_lahir'))
                ->withErrors(['email' => 'Email pemulihan tidak sesuai dengan data kami.']);
        }

        // Redirect ke login dengan pesan sukses
        return redirect()->route('login')->with(
            'status',
            'Permintaan pemulihan password berhasil dikirim. '
            . 'Silakan ubah password Anda melalui SIAKAD Polinema di '
            . 'https://siakad.polinema.ac.id/recovery_password, '
            . 'kemudian gunakan password baru tersebut untuk masuk.'
        );
    }
}

