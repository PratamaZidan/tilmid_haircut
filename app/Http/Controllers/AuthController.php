<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function show()
    {
        return view('auth.login');
    }

    public function login(Request $r)
    {
        $data = $r->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $remember = $r->boolean('remember');

        if (Auth::attempt($data, $remember)) {
            $user = Auth::user();

            if ($user->status !== 'aktif') {
                Auth::logout();
                $r->session()->invalidate();
                $r->session()->regenerateToken();

                return back()->withErrors(['username' => 'Akun nonaktif'])->onlyInput('username');
            }

            $r->session()->regenerate();

            return $user->role === 'admin'
                ? redirect('/admin')
                : redirect('/capster');
        }

        return back()->withErrors(['username' => 'Username / password salah'])->onlyInput('username');
    }

    public function logout(Request $r)
    {
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();
        return redirect('/');
    }
}
