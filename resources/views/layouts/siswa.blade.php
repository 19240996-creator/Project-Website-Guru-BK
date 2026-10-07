<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Ruang Siswa - Bimbingan & Karier')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=1">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @yield('styles')
</head>
<body style="background-color: #f8fafc;">
    <!-- Student Header Navbar -->
    <header class="siswa-navbar">
        <div style="display: flex; align-items: center; gap: 20px;">
            <a href="{{ route('siswa.dashboard') }}" style="display: flex; align-items: center; gap: 10px; font-weight: 800; font-size: 16px; color: var(--color-primary);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
                <span>Ruang BK & Masa Depan</span>
            </a>

            <nav class="siswa-nav-links">
                <a href="{{ route('siswa.dashboard') }}" class="siswa-nav-link {{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}">Beranda</a>
                <a href="{{ route('siswa.konseling.index') }}" class="siswa-nav-link {{ request()->routeIs('siswa.konseling.*') ? 'active' : '' }}">Konseling</a>
                <a href="{{ route('siswa.asesmen.index') }}" class="siswa-nav-link {{ request()->routeIs('siswa.asesmen.*') ? 'active' : '' }}">Asesmen Diri</a>
                <a href="{{ route('siswa.rencana.show') }}" class="siswa-nav-link {{ request()->routeIs('siswa.rencana.*') ? 'active' : '' }}">Rute Masa Depan</a>
                <a href="{{ route('siswa.peluang.index') }}" class="siswa-nav-link {{ request()->routeIs('siswa.peluang.*') ? 'active' : '' }}">Peluang & Beasiswa</a>
                <a href="{{ route('siswa.alumni.index') }}" class="siswa-nav-link {{ request()->routeIs('siswa.alumni.*') ? 'active' : '' }}">Jejak Alumni</a>
                <a href="{{ route('siswa.profil.show') }}" class="siswa-nav-link {{ request()->routeIs('siswa.profil.*') ? 'active' : '' }}">Profil Saya</a>
            </nav>
        </div>

        <div style="display: flex; align-items: center; gap: 12px;">
            @php
                $unreadCount = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count();
            @endphp
            <a href="{{ route('notifikasi.index') }}" class="btn btn-secondary btn-sm" title="Pemberitahuan">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                @if($unreadCount > 0)
                    <span class="badge badge-danger" style="margin-left: 4px;">{{ $unreadCount }}</span>
                @endif
            </a>

            <div style="display: flex; align-items: center; gap: 8px;">
                <div class="user-avatar-initial" style="width: 32px; height: 32px; font-size: 12px;">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <span style="font-weight: 600; font-size: 13px; color: var(--color-text-main);">{{ auth()->user()->name }}</span>
            </div>

            <form action="{{ route('logout') }}" method="POST" style="margin-left: 8px;">
                @csrf
                <button type="submit" class="btn btn-secondary btn-sm" onclick="return confirm('Keluar dari sistem?')">
                    Keluar
                </button>
            </form>
        </div>
    </header>

    <main class="content-body" style="padding: 28px 20px;">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <strong>Perhatian:</strong>
                    <ul style="margin-left: 20px; margin-top: 4px;">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>
    @yield('scripts')
</body>
</html>
