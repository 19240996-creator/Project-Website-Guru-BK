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
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Silakan masuk terlebih dahulu untuk mengakses halaman ini.');
        }

        $allowedRoles = [];
        foreach ($roles as $r) {
            foreach (explode(',', $r) as $sub) {
                $sub = trim($sub);
                if (!empty($sub)) {
                    $allowedRoles[] = $sub;
                }
            }
        }

        $userRole = auth()->user()->role;

        if (!in_array($userRole, $allowedRoles)) {
            if ($userRole === 'siswa') {
                return redirect()->route('siswa.dashboard')->with('error', 'Akses ditolak: Akun Anda memiliki hak akses Siswa.');
            }

            abort(403, 'Akses ditolak: Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}
