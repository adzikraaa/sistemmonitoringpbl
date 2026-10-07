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
        'role' => 'required|in:mahasiswa,dosen,koordinator',
        'identifier' => 'required|string',
        'password' => 'required',
    ]);

    $role = $request->role;
    $field = $role === 'mahasiswa' ? 'nim' : 'nidn';

    if (Auth::guard($role)->attempt([
        $field => $request->identifier,
        'password' => $request->password,
    ])) {
        $request->session()->regenerate();
        return redirect()->route("{$role}.dashboard");
    }

    return back()
        ->withInput($request->only('identifier', 'role'))
        ->withErrors(['identifier' => ucfirst($field) . ' atau password salah.']);
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
