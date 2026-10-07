<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Silakan masuk terlebih dahulu untuk mengakses halaman ini.');
        }

        if (auth()->user()->role !== $role) {
            if (auth()->user()->role === 'guru_bk') {
                return redirect()->route('guru.dashboard')->with('error', 'Akses ditolak: Akun Anda memiliki hak akses Guru BK.');
            } else {
                return redirect()->route('siswa.dashboard')->with('error', 'Akses ditolak: Akun Anda memiliki hak akses Siswa.');
            }
        }

        return $next($request);
    }
}
