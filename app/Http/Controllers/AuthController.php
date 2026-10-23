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
            $user = Auth::user();
            if ($user->isGuruOrKoordinator()) {
                return redirect()->route('guru.dashboard');
            }
            if ($user->role === 'kepala_kampus') {
                return redirect()->route('kepala_kampus.dashboard');
            }
            if ($user->role === 'admin_dico') {
                return redirect()->route('admin_dico.dashboard');
            }
            return redirect()->route('siswa.dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required' => 'NISN (untuk Siswa) atau Username / Email (untuk Pendidik / Pemantau) wajib diisi.',
            'password.required' => 'Kata sandi / Nomor HP wajib diisi.',
        ]);

        $loginInput = trim($credentials['login']);
        $password = trim($credentials['password']);
        $cleanPassword = preg_replace('/[^0-9]/', '', $password);

        // 1. Cek login Staf / Guru BK / Koordinator BK / Kepala Kampus / Admin DICO (username atau email)
        $staffUser = User::whereIn('role', ['guru_bk', 'koordinator_bk', 'kepala_kampus', 'admin_dico'])
            ->where(function ($q) use ($loginInput) {
                $q->where('username', $loginInput)
                  ->orWhere('email', $loginInput);
            })->first();

        if ($staffUser) {
            if (\Illuminate\Support\Facades\Hash::check($password, $staffUser->password)) {
                Auth::login($staffUser);
                $request->session()->regenerate();

                \App\Models\AuditLog::log('LOGIN', 'User', $staffUser->id, "Pengguna {$staffUser->role_label} ({$staffUser->name}) berhasil masuk ke sistem.");

                if ($staffUser->role === 'kepala_kampus') {
                    return redirect()->intended(route('kepala_kampus.dashboard'))->with('success', 'Selamat datang kembali di Dashboard Pemantauan, ' . $staffUser->name);
                }
                if ($staffUser->role === 'admin_dico') {
                    return redirect()->intended(route('admin_dico.dashboard'))->with('success', 'Selamat datang kembali di Dashboard Pemantauan, ' . $staffUser->name);
                }

                return redirect()->intended(route('guru.dashboard'))->with('success', 'Selamat datang kembali, ' . $staffUser->name);
            }

            return back()->withErrors([
                'login' => 'Username atau kata sandi yang Anda masukkan salah.',
            ])->onlyInput('login');
        }

        // 2. Cek login Siswa: Username adalah NISN yang sudah terdaftar, Password adalah Nomor HP yang sudah terdaftar
        $cleanNisn = preg_replace('/[^0-9]/', '', $loginInput);

        $student = \App\Models\Student::where('nisn', $loginInput)
            ->orWhere(function ($q) use ($cleanNisn) {
                if (!empty($cleanNisn) && strlen($cleanNisn) >= 8) {
                    $q->where('nisn', $cleanNisn);
                }
            })->first();

        if ($student) {
            // Siswa ditemukan di database! Sekarang verifikasi Nomor HP (password)
            $user = $student->user;
            if (!$user) {
                // Buat akun user siswa otomatis jika belum tersedia
                $initialPassword = !empty($student->phone) ? $student->phone : $student->nisn;
                $user = User::create([
                    'name' => $student->name,
                    'username' => $student->nisn,
                    'email' => $student->nisn . '@siswa.sch.id',
                    'password' => \Illuminate\Support\Facades\Hash::make($initialPassword),
                    'role' => 'siswa',
                    'phone' => $student->phone,
                    'avatar' => $student->avatar,
                    'is_active' => true,
                ]);
                $student->update(['user_id' => $user->id]);
            }

            // Periksa apakah password cocok dengan Nomor HP siswa yang terdaftar
            $studentPhoneClean = preg_replace('/[^0-9]/', '', (string) $student->phone);
            $matchesPhone = (!empty($student->phone) && $password === $student->phone)
                || (!empty($studentPhoneClean) && !empty($cleanPassword) && $cleanPassword === $studentPhoneClean);

            $matchesHash = \Illuminate\Support\Facades\Hash::check($password, $user->password)
                || (!empty($cleanPassword) && \Illuminate\Support\Facades\Hash::check($cleanPassword, $user->password));

            if ($matchesPhone || $matchesHash) {
                // Sinkronisasi password user jika cocok dengan nomor HP
                if ($matchesPhone && !\Illuminate\Support\Facades\Hash::check($password, $user->password)) {
                    $user->update(['password' => \Illuminate\Support\Facades\Hash::make($password)]);
                }

                Auth::login($user);
                $request->session()->regenerate();
                \App\Models\AuditLog::log('LOGIN', 'User', $user->id, "Siswa {$user->name} berhasil masuk ke sistem.");
                return redirect()->intended(route('siswa.dashboard'))->with('success', 'Selamat datang di Ruang Konseling & Karier, ' . $user->name);
            }

            return back()->withErrors([
                'login' => 'Kata sandi (Nomor HP) yang Anda masukkan salah atau tidak sesuai dengan NISN terdaftar.',
            ])->onlyInput('login');
        }

        // 3. Fallback jika ada akun user non-siswa lainnya
        $generalUser = User::where('username', $loginInput)
            ->orWhere('email', $loginInput)
            ->first();

        if ($generalUser && \Illuminate\Support\Facades\Hash::check($password, $generalUser->password)) {
            Auth::login($generalUser);
            $request->session()->regenerate();
            \App\Models\AuditLog::log('LOGIN', 'User', $generalUser->id, "Pengguna {$generalUser->role_label} ({$generalUser->name}) berhasil masuk ke sistem.");

            if ($generalUser->role === 'kepala_kampus') {
                return redirect()->intended(route('kepala_kampus.dashboard'))->with('success', 'Selamat datang, ' . $generalUser->name);
            }
            if ($generalUser->role === 'admin_dico') {
                return redirect()->intended(route('admin_dico.dashboard'))->with('success', 'Selamat datang, ' . $generalUser->name);
            }
            return in_array($generalUser->role, ['guru_bk', 'koordinator_bk'])
                ? redirect()->intended(route('guru.dashboard'))->with('success', 'Selamat datang kembali, ' . $generalUser->name)
                : redirect()->intended(route('siswa.dashboard'))->with('success', 'Selamat datang, ' . $generalUser->name);
        }

        // 4. Jika NISN atau Username belum terdaftar sama sekali
        return back()->withErrors([
            'login' => 'NISN atau Username belum terdaftar dalam sistem Ruang BK.',
        ])->onlyInput('login');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            \App\Models\AuditLog::log('LOGOUT', 'User', $user->id, "Pengguna {$user->role_label} ({$user->name}) keluar dari sistem.");
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari sistem.');
    }
}
