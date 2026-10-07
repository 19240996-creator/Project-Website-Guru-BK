@extends('layouts.siswa')

@section('title', 'Profil Belum Terhubung')

@section('content')
<div class="card" style="max-width: 600px; margin: 40px auto; text-align: center; padding: 40px 24px;">
    <h3 style="font-size: 18px; font-weight: 800; color: var(--color-text-main); margin-bottom: 8px;">Akun Belum Terhubung ke Data Siswa</h3>
    <p style="font-size: 13px; color: var(--color-text-muted); margin-bottom: 20px;">
        Akun login Anda belum dikaitkan dengan profil siswa sekolah. Silakan hubungi Guru Bimbingan Konseling atau operator sekolah untuk mengaktifkan data Anda.
    </p>
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-secondary">Keluar dari Akun</button>
    </form>
</div>
@endsection
