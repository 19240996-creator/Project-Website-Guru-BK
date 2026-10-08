<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ruang BK - Platform Digital Bimbingan Konseling - SIM BK</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=1">
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: #c8ebfe;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .login-card {
            width: 100%;
            max-width: 870px;
            background: #ffffff;
            border-radius: 36px;
            display: flex;
            overflow: hidden;
            box-shadow: 0 20px 40px -15px rgba(9, 44, 112, 0.15);
        }

        /* Sisi Kiri: Ilustrasi Ruang BK */
        .login-illustration-pane {
            flex: 0 0 504px;
            width: 504px;
            line-height: 0;
            background: #ffffff;
        }

        .login-illustration-pane img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        /* Sisi Kanan: Form Login */
        .login-form-pane {
            flex: 1;
            padding: 48px 42px 48px 36px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #ffffff;
        }

        .form-group {
            margin-bottom: 22px;
        }

        .form-label {
            display: block;
            font-size: 15px;
            font-weight: 800;
            color: #092c70;
            margin-bottom: 8px;
            letter-spacing: -0.01em;
        }

        .input-wrapper {
            display: flex;
            align-items: center;
            background-color: #e7efff;
            border-radius: 14px;
            padding: 4px 14px;
            height: 52px;
            transition: box-shadow 0.2s ease, background-color 0.2s ease;
        }

        .input-wrapper:focus-within {
            background-color: #e2ecff;
            box-shadow: 0 0 0 2px #092c70;
        }

        .input-icon-left {
            display: flex;
            align-items: center;
            justify-content: center;
            color: #092c70;
            margin-right: 12px;
            flex-shrink: 0;
        }

        .input-control {
            flex: 1;
            border: none;
            background: transparent;
            font-size: 14px;
            font-family: inherit;
            color: #092c70;
            font-weight: 500;
            outline: none;
            height: 100%;
        }

        .input-control::placeholder {
            color: #7890b8;
            font-weight: 400;
        }

        .password-toggle-btn {
            background: none;
            border: none;
            color: #092c70;
            cursor: pointer;
            padding: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.15s ease;
        }

        .password-toggle-btn:hover {
            opacity: 0.7;
        }

        .btn-submit-login {
            width: 100%;
            height: 52px;
            background-color: #082d61;
            color: #ffffff;
            font-size: 15px;
            font-weight: 700;
            border: none;
            border-radius: 14px;
            cursor: pointer;
            transition: background-color 0.2s ease, transform 0.1s ease;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            letter-spacing: -0.01em;
        }

        .btn-submit-login:hover {
            background-color: #051d3f;
        }

        .btn-submit-login:active {
            transform: scale(0.99);
        }

        .error-message {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 12px;
            margin-bottom: 16px;
            line-height: 1.4;
        }

        @media (max-width: 890px) {
            .login-card {
                flex-direction: column;
                max-width: 480px;
                border-radius: 28px;
            }

            .login-illustration-pane {
                flex: none;
                width: 100%;
                height: 280px;
            }

            .login-illustration-pane img {
                height: 100%;
                object-position: top center;
            }

            .login-form-pane {
                padding: 32px 28px;
            }
        }
    </style>
</head>
<body>
    <div class="login-card">
        <!-- Sisi Kiri: Visual Banner Ruang BK -->
        <div class="login-illustration-pane">
            <img src="{{ asset('images/login-illustration.png') }}" alt="RUANG BK - Platform Digital Bimbingan, Konseling dan Pengembangan Siswa">
        </div>

        <!-- Sisi Kanan: Form Login -->
        <div class="login-form-pane">
            @if(session('error'))
                <div class="error-message">{{ session('error') }}</div>
            @endif

            @if($errors->has('login'))
                <div class="error-message">{{ $errors->first('login') }}</div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="loginInput" class="form-label">Username</label>
                    <div class="input-wrapper">
                        <span class="input-icon-left">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </span>
                        <input type="text" id="loginInput" name="login" class="input-control" value="{{ old('login') }}" placeholder="Masukkan username" required autofocus autocomplete="username">
                    </div>
                </div>

                <div class="form-group">
                    <label for="passwordInput" class="form-label">Password</label>
                    <div class="input-wrapper">
                        <span class="input-icon-left">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                        </span>
                        <input type="password" id="passwordInput" name="password" class="input-control" placeholder="Masukkan password" required autocomplete="current-password">
                        <button type="button" class="password-toggle-btn" id="togglePasswordBtn" onclick="togglePasswordVisibility()" aria-label="Buka tutup password" title="Buka/Tutup Password">
                            <svg id="eyeOpenIcon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <svg id="eyeClosedIcon" style="display: none;" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-submit-login">
                    Login
                </button>
            </form>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('passwordInput');
            const eyeOpen = document.getElementById('eyeOpenIcon');
            const eyeClosed = document.getElementById('eyeClosedIcon');

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
    </script>
</body>
</html>
