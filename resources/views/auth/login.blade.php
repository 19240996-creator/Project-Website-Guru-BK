<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ruang BK - Platform Digital Bimbingan, Konseling dan Pengembangan Siswa</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=1">
    <style>
        :root {
            --color-navy: #0D1527;
            --color-navy-dark: #070B16;
            --color-primary: #2447A8;
            --color-primary-hover: #1C3886;
            --color-electric: #4778F5;
            --color-gold: #D5B779;
            --color-gold-hover: #C5A462;
            --color-gold-light: #FAF5EA;
            --color-bg: #F0F4FA;
            --color-surface: #FFFFFF;
            --color-border: #E2E8F0;
            --color-border-strong: #CBD5E1;
            --color-text-main: #101828;
            --color-text-navy: #0D1527;
            --color-text-muted: #64748B;
            --color-text-subtle: #94A3B8;
            --color-danger: #D94A57;
            --color-danger-bg: #FDF2F3;
            --color-danger-border: #F8C1C6;
            --radius-sm: 10px;
            --radius-md: 14px;
            --radius-lg: 20px;
            --radius-xl: 32px;
            --shadow-card: 0 25px 50px -12px rgba(13, 21, 39, 0.22);
        }

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #EBF1FA 0%, #F5F8FD 100%);
            color: var(--color-text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 28px 20px;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .login-card {
            width: 100%;
            max-width: 1080px;
            background: #FFFFFF;
            border-radius: var(--radius-xl);
            display: flex;
            overflow: hidden;
            box-shadow: var(--shadow-card);
            border: 1px solid rgba(255, 255, 255, 0.8);
            position: relative;
        }

        /* Sisi Kiri: Visual Midnight Navy Identity */
        .login-visual-pane {
            flex: 1.15;
            background: linear-gradient(155deg, #0A1122 0%, #0F1E3D 55%, #19356E 100%);
            color: #FFFFFF;
            padding: 48px 44px 36px 44px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        /* Gold Ambient Ribbon Waves */
        .login-visual-pane::before {
            content: '';
            position: absolute;
            top: -100px;
            right: -100px;
            width: 380px;
            height: 380px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(213, 183, 121, 0.18) 0%, transparent 68%);
            pointer-events: none;
        }

        .login-visual-pane::after {
            content: '';
            position: absolute;
            bottom: 60px;
            left: -120px;
            width: 420px;
            height: 180px;
            background: radial-gradient(ellipse, rgba(71, 120, 245, 0.22) 0%, transparent 70%);
            transform: rotate(-15deg);
            pointer-events: none;
        }

        .visual-top-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
            z-index: 2;
        }

        .visual-brand-icon {
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--color-gold);
            flex-shrink: 0;
            filter: drop-shadow(0 2px 6px rgba(213, 183, 121, 0.35));
        }

        .visual-brand-title {
            font-size: 22px;
            font-weight: 900;
            letter-spacing: -0.02em;
            color: #FFFFFF;
        }

        .visual-brand-tagline {
            font-size: 11.5px;
            color: rgba(255, 255, 255, 0.68);
            letter-spacing: 0.01em;
        }

        .visual-body-content {
            margin: 28px 0 20px 0;
            position: relative;
            z-index: 2;
        }

        .visual-headline {
            font-size: 32px;
            font-weight: 800;
            line-height: 1.25;
            letter-spacing: -0.02em;
            color: #FFFFFF;
            margin-bottom: 12px;
        }

        .visual-headline-gold {
            color: var(--color-gold);
            display: block;
        }

        .visual-subtext {
            font-size: 13.5px;
            color: rgba(255, 255, 255, 0.82);
            line-height: 1.55;
            max-width: 440px;
            margin-bottom: 22px;
        }

        /* Foto Siswa & Konselor */
        .visual-photo-wrapper {
            width: 100%;
            height: 220px;
            border-radius: var(--radius-lg);
            overflow: hidden;
            position: relative;
            box-shadow: 0 16px 32px rgba(0, 0, 0, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .visual-photo-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center 25%;
            display: block;
            transition: transform 0.4s ease;
        }

        .visual-photo-wrapper:hover img {
            transform: scale(1.03);
        }

        .visual-photo-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, transparent 40%, rgba(10, 17, 34, 0.75) 100%);
            pointer-events: none;
        }

        /* 4 Fitur Icon Pills di Bawah */
        .visual-bottom-pills {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-top: 24px;
            position: relative;
            z-index: 2;
        }

        .visual-pill-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 6px;
        }

        .visual-pill-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(213, 183, 121, 0.3);
            color: var(--color-gold);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s ease, transform 0.2s ease;
        }

        .visual-pill-item:hover .visual-pill-icon {
            background: rgba(213, 183, 121, 0.25);
            transform: translateY(-2px);
        }

        .visual-pill-label {
            font-size: 11px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.3;
        }

        /* Sisi Kanan: Formulir Login Card */
        .login-form-pane {
            flex: 0.95;
            padding: 52px 46px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #FFFFFF;
        }

        .form-brand-badge {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 22px;
        }

        .form-brand-icon {
            width: 36px;
            height: 36px;
            color: var(--color-primary);
        }

        .form-brand-name {
            font-size: 18px;
            font-weight: 800;
            color: var(--color-navy);
            letter-spacing: -0.01em;
        }

        .form-title {
            font-size: 24px;
            font-weight: 800;
            color: var(--color-navy);
            letter-spacing: -0.02em;
            margin-bottom: 6px;
        }

        .form-subtitle {
            font-size: 13.5px;
            color: var(--color-text-muted);
            line-height: 1.5;
            margin-bottom: 28px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: var(--color-navy);
            margin-bottom: 7px;
            letter-spacing: -0.01em;
        }

        .input-wrapper {
            display: flex;
            align-items: center;
            background-color: #FFFFFF;
            border: 1px solid var(--color-border-strong);
            border-radius: var(--radius-sm);
            padding: 0 14px;
            height: 48px;
            transition: all 0.15s ease;
        }

        .input-wrapper:focus-within {
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(36, 71, 168, 0.15);
        }

        .input-icon-left {
            color: var(--color-text-muted);
            margin-right: 12px;
            display: flex;
            align-items: center;
            flex-shrink: 0;
        }

        .input-wrapper:focus-within .input-icon-left {
            color: var(--color-primary);
        }

        .input-control {
            flex: 1;
            border: none;
            background: transparent;
            font-size: 13.5px;
            font-family: inherit;
            color: var(--color-text-main);
            font-weight: 500;
            outline: none;
            height: 100%;
        }

        .input-control::placeholder {
            color: var(--color-text-subtle);
            font-weight: 400;
        }

        .password-toggle-btn {
            background: none;
            border: none;
            color: var(--color-text-muted);
            cursor: pointer;
            padding: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.15s ease;
        }

        .password-toggle-btn:hover {
            color: var(--color-navy);
        }

        .form-meta-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
            font-size: 12.5px;
        }

        .remember-checkbox-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--color-text-muted);
            cursor: pointer;
            font-weight: 500;
            user-select: none;
        }

        .remember-checkbox-label input {
            accent-color: var(--color-primary);
            width: 15px;
            height: 15px;
            cursor: pointer;
        }

        .forgot-link {
            color: var(--color-primary);
            font-weight: 600;
            text-decoration: none;
            transition: color 0.15s ease;
        }

        .forgot-link:hover {
            color: var(--color-primary-hover);
            text-decoration: underline;
        }

        .btn-submit-login {
            width: 100%;
            height: 48px;
            background: linear-gradient(180deg, var(--color-primary) 0%, #1A3788 100%);
            color: #FFFFFF;
            font-size: 14.5px;
            font-weight: 700;
            border: none;
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: all 0.15s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            letter-spacing: -0.01em;
            box-shadow: 0 4px 14px rgba(36, 71, 168, 0.3);
        }

        .btn-submit-login:hover {
            background: linear-gradient(180deg, #2A51BC 0%, var(--color-primary) 100%);
            box-shadow: 0 6px 18px rgba(36, 71, 168, 0.4);
            transform: translateY(-1px);
        }

        .btn-submit-login:active {
            transform: scale(0.99);
        }

        .divider-row {
            display: flex;
            align-items: center;
            gap: 14px;
            margin: 22px 0;
            color: var(--color-text-subtle);
            font-size: 12px;
        }

        .divider-row::before,
        .divider-row::after {
            content: '';
            flex: 1;
            height: 1px;
            background-color: var(--color-border);
        }

        .btn-student-quick {
            width: 100%;
            height: 44px;
            background-color: #F8FAFC;
            border: 1px solid var(--color-border-strong);
            border-radius: var(--radius-sm);
            color: var(--color-navy);
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.15s ease;
        }

        .btn-student-quick:hover {
            background-color: #EDF2FE;
            border-color: var(--color-primary);
            color: var(--color-primary);
        }

        .error-message {
            background-color: var(--color-danger-bg);
            border: 1px solid var(--color-danger-border);
            color: var(--color-danger);
            padding: 12px 14px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 20px;
            line-height: 1.45;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .error-message svg {
            flex-shrink: 0;
            margin-top: 1px;
        }

        .form-footer-copyright {
            margin-top: 28px;
            font-size: 12px;
            color: var(--color-text-subtle);
            text-align: center;
        }

        /* Responsive Breakpoint */
        @media (max-width: 920px) {
            body {
                padding: 16px 12px;
            }

            .login-card {
                flex-direction: column;
                max-width: 500px;
                border-radius: var(--radius-lg);
            }

            .login-visual-pane {
                padding: 36px 28px;
            }

            .visual-headline {
                font-size: 24px;
            }

            .visual-photo-wrapper {
                height: 190px;
            }

            .visual-bottom-pills {
                grid-template-columns: repeat(2, 1fr);
                gap: 14px;
            }

            .login-form-pane {
                padding: 36px 28px 40px 28px;
            }
        }
    </style>
</head>
<body>
    <div class="login-card">
        <!-- Sisi Kiri: Visual Midnight Navy Identity -->
        <div class="login-visual-pane">
            <div class="visual-top-brand">
                <div class="visual-brand-icon">
                    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </svg>
                </div>
                <div>
                    <div class="visual-brand-title">RUANG BK</div>
                    <div class="visual-brand-tagline">Platform Digital Bimbingan, Konseling dan Pengembangan Siswa</div>
                </div>
            </div>

            <div class="visual-body-content">
                <h1 class="visual-headline">
                    Setiap Siswa Punya Potensi.
                    <span class="visual-headline-gold">Bantu Mereka Menemukannya.</span>
                </h1>
                <p class="visual-subtext">
                    Kolaborasi guru, siswa, sekolah, dan dunia nyata untuk masa depan yang lebih baik.
                </p>

                <!-- Foto Siswa & Konselor Riil -->
                <div class="visual-photo-wrapper">
                    <img src="{{ asset('images/login-hero.jpg') }}" alt="Siswa dan Guru Bimbingan Konseling">
                    <div class="visual-photo-overlay"></div>
                </div>
            </div>

            <!-- 4 Fitur Layanan di Bagian Bawah -->
            <div class="visual-bottom-pills">
                <div class="visual-pill-item">
                    <div class="visual-pill-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                    </div>
                    <span class="visual-pill-label">Konseling</span>
                </div>
                <div class="visual-pill-item">
                    <div class="visual-pill-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    </div>
                    <span class="visual-pill-label">Pengembangan Diri</span>
                </div>
                <div class="visual-pill-item">
                    <div class="visual-pill-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>
                    </div>
                    <span class="visual-pill-label">Peminatan Masa Depan</span>
                </div>
                <div class="visual-pill-item">
                    <div class="visual-pill-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                    </div>
                    <span class="visual-pill-label">Kolaborasi Industri</span>
                </div>
            </div>
        </div>

        <!-- Sisi Kanan: Formulir Login Card -->
        <div class="login-form-pane">
            <div class="form-brand-badge">
                <div class="form-brand-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </svg>
                </div>
                <div class="form-brand-name">RUANG BK</div>
            </div>

            <h2 class="form-title">Selamat Datang Kembali</h2>
            <p class="form-subtitle">
                Masuk untuk melanjutkan aktivitas Anda di RUANG BK
            </p>

            @if(session('error'))
                <div class="error-message" role="alert">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            @if(isset($errors) && $errors->has('login'))
                <div class="error-message" role="alert">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <div>{{ $errors->first('login') }}</div>
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" id="loginForm">
                @csrf
                <div class="form-group">
                    <label for="loginInput" class="form-label">Username atau Email</label>
                    <div class="input-wrapper">
                        <span class="input-icon-left">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </span>
                        <input type="text" id="loginInput" name="login" class="input-control" value="{{ old('login') }}" placeholder="Masukkan NISN, No. HP, atau username" required autofocus autocomplete="username">
                    </div>
                </div>

                <div class="form-group">
                    <label for="passwordInput" class="form-label">Password</label>
                    <div class="input-wrapper">
                        <span class="input-icon-left">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                        </span>
                        <input type="password" id="passwordInput" name="password" class="input-control" placeholder="Masukkan password" required autocomplete="current-password">
                        <button type="button" class="password-toggle-btn" id="togglePasswordBtn" onclick="togglePasswordVisibility()" aria-label="Buka tutup password" title="Buka/Tutup Password">
                            <svg id="eyeOpenIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <svg id="eyeClosedIcon" style="display: none;" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="form-meta-row">
                    <label class="remember-checkbox-label">
                        <input type="checkbox" name="remember" id="rememberMe">
                        <span>Ingat saya</span>
                    </label>
                    <a href="javascript:void(0)" onclick="alert('Silakan hubungi koordinator Guru BK sekolah untuk pemulihan kredensial akun Anda.')" class="forgot-link">
                        Lupa password?
                    </a>
                </div>

                <button type="submit" class="btn-submit-login" id="submitBtn">
                    <span>Masuk</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </button>
            </form>

            <div class="divider-row">
                <span>atau masuk dengan</span>
            </div>

            <button type="button" class="btn-student-quick" onclick="focusStudentLogin()">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>
                <span>Masuk sebagai Siswa</span>
            </button>

            <div class="form-footer-copyright">
                &copy; 2026 RUANG BK. Semua hak dilindungi.
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            var passwordInput = document.getElementById('passwordInput');
            var eyeOpen = document.getElementById('eyeOpenIcon');
            var eyeClosed = document.getElementById('eyeClosedIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeOpen.style.display = 'none';
                eyeClosed.style.display = 'block';
            } else {
                passwordInput.type = 'password';
                eyeOpen.style.display = 'block';
                eyeClosed.style.display = 'none';
            }
        }

        function focusStudentLogin() {
            var input = document.getElementById('loginInput');
            input.focus();
            input.placeholder = "Ketik NISN atau No. HP siswa...";
        }

        document.getElementById('loginForm').addEventListener('submit', function() {
            var btn = document.getElementById('submitBtn');
            btn.style.opacity = '0.85';
            btn.innerHTML = '<span>Memverifikasi...</span>';
        });
    </script>
</body>
</html>
