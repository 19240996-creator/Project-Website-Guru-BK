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
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
    @yield('styles')
</head>
<body>
    <div class="app-layout">
        <!-- Sidebar Navigation Siswa (Executive Midnight Navy) -->
        <aside class="sidebar" id="appSidebar" aria-label="Navigasi Menu Siswa">
            <div class="sidebar-header">
                <div class="sidebar-brand-wrapper">
                    <a href="{{ route('siswa.dashboard') }}" style="text-decoration: none;">
                        <div class="brand-title">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                            </svg>
                            <span>RUANG BK</span>
                        </div>
                        <span class="brand-badge">PORTAL SISWA</span>
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
                    <span>Menu Utama</span>
                </div>
                <a href="{{ route('siswa.dashboard') }}" class="nav-item {{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}" title="Beranda">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                    <span>Beranda</span>
                </a>

                <div class="nav-group-label">
                    <span class="nav-group-label-icon">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                    </span>
                    <span>Layanan Konseling & Asesmen</span>
                </div>
                <a href="{{ route('siswa.konseling.index') }}" class="nav-item {{ request()->routeIs('siswa.konseling.*') ? 'active' : '' }}" title="Konseling">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                    <span>Konseling</span>
                </a>
                <a href="{{ route('siswa.asesmen.index') }}" class="nav-item {{ request()->routeIs('siswa.asesmen.*') ? 'active' : '' }}" title="Asesmen Diri">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    <span>Asesmen Diri</span>
                </a>

                <div class="nav-group-label">
                    <span class="nav-group-label-icon">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                    </span>
                    <span>Eksplorasi Masa Depan</span>
                </div>
                <a href="{{ route('siswa.rencana.show') }}" class="nav-item {{ request()->routeIs('siswa.rencana.*') ? 'active' : '' }}" title="Rute Masa Depan">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"></polygon></svg>
                    <span>Rute Masa Depan</span>
                </a>
                <a href="{{ route('siswa.peluang.index') }}" class="nav-item {{ request()->routeIs('siswa.peluang.*') ? 'active' : '' }}" title="Peluang & Beasiswa">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg>
                    <span>Peluang & Beasiswa</span>
                </a>
                <a href="{{ route('siswa.alumni.index') }}" class="nav-item {{ request()->routeIs('siswa.alumni.*') ? 'active' : '' }}" title="Jejak Alumni">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>
                    <span>Jejak Alumni</span>
                </a>

                <div class="nav-group-label">
                    <span class="nav-group-label-icon">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    </span>
                    <span>Akun Siswa</span>
                </div>
                <a href="{{ route('siswa.profil.show') }}" class="nav-item {{ request()->routeIs('siswa.profil.*') ? 'active' : '' }}" title="Profil Saya">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    <span>Profil Saya</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <a href="{{ route('siswa.profil.show') }}" style="text-decoration: none; color: inherit; display: block;" title="Lihat Profil Siswa">
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
                </a>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
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
                    <!-- Topbar Search matching student reference UI -->
                    <div class="topbar-search">
                        <span class="topbar-search-icon">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </span>
                        <input type="text" class="topbar-search-input" placeholder="Cari informasi, kegiatan, atau artikel..." aria-label="Pencarian portal siswa" />
                    </div>
                </div>
                <div class="topbar-actions">
                    @php
                        $unreadCount = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count();
                    @endphp
                    <a href="{{ route('notifikasi.index') }}" class="topbar-icon-btn" title="Pusat Pemberitahuan" aria-label="Notifikasi">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                        @if($unreadCount > 0)
                            <span class="topbar-badge">{{ $unreadCount }}</span>
                        @endif
                    </a>

                    <a href="{{ route('siswa.profil.show') }}" class="topbar-user-pill" title="Profil Siswa">
                        <img src="{{ asset('images/student-hero.jpg') }}" alt="{{ auth()->user()->name }}" class="topbar-user-avatar" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" />
                        <div class="topbar-user-avatar-initial" style="display: none;">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="topbar-user-details">
                            <span class="topbar-user-name">{{ auth()->user()->name }}</span>
                            <span class="topbar-user-role">
                                {{ auth()->user()->student && auth()->user()->student->studentClass ? auth()->user()->student->studentClass->name : 'Siswa Aktif' }}
                            </span>
                        </div>
                    </a>

                    <form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display: inline;">
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
                    Apakah Anda yakin ingin keluar dari sistem? Seluruh pembaruan dan rute masa depan yang telah Anda simpan tetap tersimpan dengan aman di server.
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

    <!-- Overlay Backdrop Sidebar Mobile -->
    <div id="sidebarBackdrop" class="sidebar-backdrop" onclick="closeMobileSidebar()"></div>

    <script>
        function toggleMobileSidebar() {
            var sidebar = document.getElementById('appSidebar');
            var backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar) {
                var isOpen = sidebar.classList.toggle('mobile-open');
                if (backdrop) backdrop.classList.toggle('active', isOpen);
                document.body.classList.toggle('sidebar-locked', isOpen);
            }
        }

        function closeMobileSidebar() {
            var sidebar = document.getElementById('appSidebar');
            var backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar) {
                sidebar.classList.remove('mobile-open');
                if (backdrop) backdrop.classList.remove('active');
                document.body.classList.remove('sidebar-locked');
            }
        }

        window.addEventListener('resize', function() {
            if (window.innerWidth > 992) {
                closeMobileSidebar();
            }
        });

        document.querySelectorAll('.sidebar-nav a').forEach(function(link) {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 992) {
                    closeMobileSidebar();
                }
            });
        });

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
                closeMobileSidebar();
            }
        });
    </script>
    @yield('scripts')
</body>
</html>
