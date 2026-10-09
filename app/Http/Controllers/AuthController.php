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
            'login.required' => 'Nomor Identitas (NISN), No. HP, Username, atau Email wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $loginInput = trim($credentials['login']);
        $password = trim($credentials['password']);

        // Cari user berdasarkan username, email, no_hp akun, atau NISN / NIS / No. HP siswa aktif
        $digitsOnly = preg_replace('/[^0-9]/', '', $loginInput);

        $user = User::where(function ($query) use ($loginInput, $digitsOnly) {
            $query->where('username', $loginInput)
                ->orWhere('email', $loginInput)
                ->orWhere('phone', $loginInput)
                ->orWhereHas('student', function ($q) use ($loginInput, $digitsOnly) {
                    $q->where('nisn', $loginInput)
                        ->orWhere('nis', $loginInput)
                        ->orWhere('phone', $loginInput);
                    if (!empty($digitsOnly) && strlen($digitsOnly) >= 8) {
                        $q->orWhere('phone', $digitsOnly);
                    }
                });

            if (!empty($digitsOnly) && strlen($digitsOnly) >= 8) {
                $query->orWhere('phone', $digitsOnly);
            }
        })->first();

        if ($user) {
            $authenticated = Auth::attempt(['email' => $user->email, 'password' => $password]);

            // Jika gagal dan password berisi format nomor telepon (misal ada strip/spasi), coba juga dengan angka murni
            $digitsOnlyPassword = preg_replace('/[^0-9]/', '', $password);
            if (!$authenticated && !empty($digitsOnlyPassword) && $digitsOnlyPassword !== $password) {
                $authenticated = Auth::attempt(['email' => $user->email, 'password' => $digitsOnlyPassword]);
            }

            if ($authenticated) {
                $request->session()->regenerate();

                if ($user->role === 'guru_bk') {
                    return redirect()->intended(route('guru.dashboard'))->with('success', 'Selamat datang kembali, ' . $user->name);
                } else {
                    return redirect()->intended(route('siswa.dashboard'))->with('success', 'Selamat datang di Ruang Konseling & Karier, ' . $user->name);
                }
            }
        }

        return back()->withErrors([
            'login' => 'Username, No. HP, NISN, atau kata sandi yang Anda masukkan salah.',
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
