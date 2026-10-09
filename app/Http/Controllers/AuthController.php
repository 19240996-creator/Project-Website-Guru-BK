<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return Auth::user()->role === 'guru_bk'
                ? redirect()->route('guru.dashboard')
                : redirect()->route('siswa.dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required' => 'Nomor Identitas (NISN), Username, atau Email wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $loginInput = $credentials['login'];
        $password = $credentials['password'];

        // Find user by username, email, or student NISN
        $user = User::where('username', $loginInput)
            ->orWhere('email', $loginInput)
            ->orWhereHas('student', function ($q) use ($loginInput) {
                $q->where('nisn', $loginInput)->orWhere('nis', $loginInput);
            })
            ->first();

        if ($user && Auth::attempt(['email' => $user->email, 'password' => $password])) {
            $request->session()->regenerate();

            if ($user->role === 'guru_bk') {
                return redirect()->intended(route('guru.dashboard'))->with('success', 'Selamat datang kembali, ' . $user->name);
            } else {
                return redirect()->intended(route('siswa.dashboard'))->with('success', 'Selamat datang di Ruang Konseling & Karier, ' . $user->name);
            }
        }

        return back()->withErrors([
            'login' => 'Username atau password yang Anda masukkan salah.',
        ])->onlyInput('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari sistem.');
    }
}
