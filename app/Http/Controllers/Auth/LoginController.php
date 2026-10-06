<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function show()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'role' => ['required', 'in:mahasiswa,dosen,koordinator'],
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $role = $request->input('role');
        $credentials = $request->only('email', 'password');

        if (Auth::guard($role)->attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended(route("{$role}.dashboard"));
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email', 'role');
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
