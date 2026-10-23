<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Ruang BK - Pemantauan Sekolah')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=1">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
    @yield('styles')
</head>
<body>
    <div class="app-layout">
        <!-- Sidebar Navigation Pemantauan Sekolah -->
        <aside class="sidebar" id="appSidebar" aria-label="Navigasi Menu Pemantauan">
            <div class="sidebar-header">
                <div class="sidebar-brand-wrapper">
                    @php
                        $roleRoute = auth()->user()->role;
                    @endphp
                    <a href="{{ route($roleRoute . '.dashboard') }}" style="text-decoration: none;">
                        <div class="brand-title">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                            </svg>
                            <span>RUANG BK</span>
                        </div>
                        <span class="brand-badge">{{ strtoupper(auth()->user()->role_label) }}</span>
                    </a>
                    <button type="button" class="sidebar-close-btn" id="sidebarCloseBtn" aria-label="Tutup Menu Navigasi" onclick="closeMobileSidebar()">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-group-label">
                    <span class="nav-group-label-icon">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    </span>
                    <span>Pemantauan</span>
                </div>
                <a href="{{ route($roleRoute . '.dashboard') }}" class="nav-item {{ request()->routeIs($roleRoute . '.dashboard') ? 'active' : '' }}" title="Dashboard Sekolah">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                    <span>Dashboard Sekolah</span>
                </a>
                <a href="{{ route($roleRoute . '.siswa.index') }}" class="nav-item {{ request()->routeIs($roleRoute . '.siswa.*') ? 'active' : '' }}" title="Data Siswa">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <span>Data Siswa</span>
                </a>
                <a href="{{ route($roleRoute . '.ekspor.index') }}" class="nav-item {{ request()->routeIs($roleRoute . '.ekspor.*') ? 'active' : '' }}" title="Ekspor Data Siswa">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    <span>Ekspor Data</span>
                </a>

                <div class="nav-group-label">
                    <span class="nav-group-label-icon">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                    </span>
                    <span>Akun & Aktivitas</span>
                </div>
                <a href="{{ route($roleRoute . '.log.index') }}" class="nav-item {{ request()->routeIs($roleRoute . '.log.*') ? 'active' : '' }}" title="Log Aktivitas Saya">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    <span>Log Aktivitas</span>
                </a>
                <a href="{{ route($roleRoute . '.profil.show') }}" class="nav-item {{ request()->routeIs($roleRoute . '.profil.*') ? 'active' : '' }}" title="Profil Akun">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                    <span>Profil Akun</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <a href="{{ route($roleRoute . '.profil.show') }}" style="text-decoration: none; color: inherit; display: block;" title="Lihat Profil Akun">
                    <div class="user-snippet">
                        <div class="user-avatar-initial">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="user-meta">
                            <div class="user-name">{{ auth()->user()->name }}</div>
                            <div class="user-role-text">{{ auth()->user()->role_label }}</div>
                        </div>
                    </div>
                </a>
            </div>
        </aside>

        <!-- Main Wrapper -->
        <div class="main-wrapper">
            <header class="topbar">
                <div class="topbar-left">
                    <button type="button" class="sidebar-toggle-btn" id="sidebarToggleBtn" aria-label="Buka Menu Navigasi" onclick="toggleMobileSidebar()">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </button>
                    <div class="topbar-search">
                        <span class="topbar-search-icon">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </span>
                        <input type="text" class="topbar-search-input" id="globalSearchInput" placeholder="Cari data siswa, NISN, rombel..." aria-label="Pencarian data siswa" onkeydown="handleGlobalSearch(event)" />
                    </div>
                </div>
                <div class="topbar-actions">
                    <a href="{{ route($roleRoute . '.profil.show') }}" class="topbar-user-pill" title="Profil Akun">
                        <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="topbar-user-avatar" />
                        <div class="topbar-user-details">
                            <span class="topbar-user-name">{{ auth()->user()->name }}</span>
                            <span class="topbar-user-role">{{ auth()->user()->role_label }}</span>
                        </div>
                    </a>

                    <form id="logoutFormPemantau" action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="button" class="btn btn-secondary btn-sm" onclick="openLogoutModal()" title="Keluar dari Akun">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                            <span>Keluar</span>
                        </button>
                    </form>
                </div>
            </header>

            <main class="content-body">
                <div class="container">
                    @if(session('success'))
                        <div class="alert alert-success" role="alert">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0; margin-top:1px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                            <div>{{ session('success') }}</div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger" role="alert">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0; margin-top:1px;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                            <div>{{ session('error') }}</div>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="alert alert-warning" role="alert">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0; margin-top:1px;"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                            <div>{{ session('warning') }}</div>
                        </div>
                    @endif

                    @if(isset($errors) && $errors->any())
                        <div class="alert alert-danger" role="alert">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0; margin-top:1px;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                            <div>
                                <strong>Perhatian:</strong>
                                <ul style="margin-left: 20px; margin-top: 4px;">
                                    @foreach($errors->all() as $err)
                                        <li>{{ $err }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>

            <footer class="app-footer">
                <div class="container footer-content">
                    <p class="copyright-text">
                        &copy; {{ date('Y') }} Ruang BK Digital. Hak Cipta Dilindungi. Sistem Informasi Pemantauan Sekolah.
                    </p>
                </div>
            </footer>
        </div>
    </div>

    <!-- Mobile Sidebar Backdrop -->
    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="closeMobileSidebar()"></div>

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
                    Apakah Anda yakin ingin keluar dari sistem? Seluruh sesi pemantauan, pengaturan, dan berkas yang telah disimpan tetap aman di server.
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
        function toggleMobileSidebar() {
            var sidebar = document.getElementById('appSidebar');
            var backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar.classList.contains('open')) {
                closeMobileSidebar();
            } else {
                sidebar.classList.add('open');
                backdrop.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeMobileSidebar() {
            var sidebar = document.getElementById('appSidebar');
            var backdrop = document.getElementById('sidebarBackdrop');
            sidebar.classList.remove('open');
            backdrop.classList.remove('active');
            document.body.style.overflow = '';
        }

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
            var form = document.getElementById('logoutFormPemantau');
            if (form) {
                form.submit();
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLogoutModal();
                closeMobileSidebar();
            }
        });

        function handleGlobalSearch(e) {
            if (e.key === 'Enter') {
                var query = encodeURIComponent(e.target.value.trim());
                window.location.href = "{{ route($roleRoute . '.siswa.index') }}?q=" + query;
            }
        }
    </script>
    @include('components.avatar-crop-modal')
    @yield('scripts')
</body>
</html>
