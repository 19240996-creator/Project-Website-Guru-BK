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
    <div class="app-layout">
        <!-- Sidebar Navigation Siswa (Vertikal Sebelah Kiri) -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="{{ route('siswa.dashboard') }}" style="text-decoration: none;">
                    <div class="brand-title">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                        <span>RUANG BK</span>
                    </div>
                </a>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-group-label">Menu Utama</div>
                <a href="{{ route('siswa.dashboard') }}" class="nav-item {{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                    <span>Beranda</span>
                </a>

                <div class="nav-group-label">Layanan Konseling & Asesmen</div>
                <a href="{{ route('siswa.konseling.index') }}" class="nav-item {{ request()->routeIs('siswa.konseling.*') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                    <span>Konseling</span>
                </a>
                <a href="{{ route('siswa.asesmen.index') }}" class="nav-item {{ request()->routeIs('siswa.asesmen.*') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    <span>Asesmen Diri</span>
                </a>

                <div class="nav-group-label">Eksplorasi Masa Depan</div>
                <a href="{{ route('siswa.rencana.show') }}" class="nav-item {{ request()->routeIs('siswa.rencana.*') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"></polygon></svg>
                    <span>Rute Masa Depan</span>
                </a>
                <a href="{{ route('siswa.peluang.index') }}" class="nav-item {{ request()->routeIs('siswa.peluang.*') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg>
                    <span>Peluang & Beasiswa</span>
                </a>
                <a href="{{ route('siswa.alumni.index') }}" class="nav-item {{ request()->routeIs('siswa.alumni.*') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>
                    <span>Jejak Alumni</span>
                </a>

                <div class="nav-group-label">Akun Siswa</div>
                <a href="{{ route('siswa.profil.show') }}" class="nav-item {{ request()->routeIs('siswa.profil.*') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    <span>Profil Saya</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <div class="user-snippet">
                    <div class="user-avatar-initial">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="user-meta">
                        <div class="user-name">{{ auth()->user()->name }}</div>
                        <div class="user-role-text">
                            {{ auth()->user()->student && auth()->user()->student->studentClass ? auth()->user()->student->studentClass->name : 'Siswa Aktif' }}
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="main-wrapper">
            <header class="topbar">
                <div class="topbar-title">
                    @yield('header_title', 'Ruang Mandiri Siswa')
                </div>
                <div class="topbar-actions">
                    @php
                        $unreadCount = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count();
                    @endphp
                    <a href="{{ route('notifikasi.index') }}" class="btn btn-secondary btn-sm" title="Pemberitahuan">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                        <span>Notifikasi</span>
                        @if($unreadCount > 0)
                            <span class="badge badge-danger" style="margin-left: 4px;">{{ $unreadCount }}</span>
                        @endif
                    </a>

                    <form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="button" class="btn btn-secondary btn-sm" onclick="openLogoutModal()">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                            <span>Keluar</span>
                        </button>
                    </form>
                </div>
            </header>

            <main class="content-body">
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
        </div>
    </div>

    <!-- Modal Konfirmasi Keluar Akun -->
    <div id="logoutModal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="logoutModalTitle" onclick="handleBackdropClick(event)">
        <div class="modal-dialog">
            <div class="modal-body">
                <div class="modal-icon-badge danger">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                </div>
                <h3 id="logoutModalTitle" class="modal-title">Konfirmasi Keluar Akun</h3>
                <p class="modal-desc">
                    Apakah Anda yakin ingin keluar dari sistem? Seluruh pembaruan dan rute masa depan yang telah Anda simpan tetap tersimpan dengan aman.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeLogoutModal()">
                    Batal
                </button>
                <button type="button" class="btn btn-danger" onclick="confirmLogout()">
                    Ya, Keluar
                </button>
            </div>
        </div>
    </div>

    <script>
        function openLogoutModal() {
            var modal = document.getElementById('logoutModal');
            if (modal) {
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeLogoutModal() {
            var modal = document.getElementById('logoutModal');
            if (modal) {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            }
        }

        function handleBackdropClick(e) {
            if (e.target.id === 'logoutModal') {
                closeLogoutModal();
            }
        }

        function confirmLogout() {
            var form = document.getElementById('logoutForm');
            if (form) {
                form.submit();
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLogoutModal();
            }
        });
    </script>
    @yield('scripts')
</body>
</html>
