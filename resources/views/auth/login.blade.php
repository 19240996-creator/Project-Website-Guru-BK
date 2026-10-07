<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Sistem Informasi Bimbingan Konseling & Karier</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=1">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        .login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(180deg, #f1f5f9 0%, #e2e8f0 100%);
            padding: 24px;
        }
        .login-card {
            width: 100%;
            max-width: 440px;
            background: #ffffff;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            padding: 36px 32px;
        }
        .demo-box {
            margin-top: 24px;
            padding: 16px;
            background-color: #f8fafc;
            border: 1px dashed var(--color-border-strong);
            border-radius: var(--radius-md);
        }
        .demo-btn {
            display: block;
            width: 100%;
            text-align: left;
            padding: 8px 12px;
            margin-top: 6px;
            background: #ffffff;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-sm);
            font-size: 12px;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .demo-btn:hover {
            border-color: var(--color-primary);
            background-color: var(--color-primary-light);
        }
    </style>
</head>
<body class="login-page">
    <div class="login-card">
        <div style="text-align: center; margin-bottom: 28px;">
            <div style="display: inline-flex; align-items: center; justify-content: center; width: 48px; height: 48px; border-radius: 12px; background: var(--color-primary-light); color: var(--color-primary); margin-bottom: 12px;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
            </div>
            <h1 style="font-size: 20px; font-weight: 800; color: var(--color-text-main);">SIM BK & Karier</h1>
            <p style="font-size: 13px; color: var(--color-text-muted); margin-top: 4px;">
                Sistem Informasi Bimbingan, Pengembangan Siswa & Perencanaan Karier
            </p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if($errors->has('login'))
            <div class="alert alert-danger">{{ $errors->first('login') }}</div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="loginInput" class="form-label">Username, NISN, atau Email</label>
                <input type="text" id="loginInput" name="login" class="form-control" value="{{ old('login') }}" placeholder="Contoh: gurubk atau 0071234561" required autofocus>
                <div class="form-hint">Siswa dapat masuk menggunakan NISN masing-masing.</div>
            </div>

            <div class="form-group">
                <label for="passwordInput" class="form-label">Kata Sandi</label>
                <input type="password" id="passwordInput" name="password" class="form-control" placeholder="Masukkan kata sandi akun" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 8px;">
                Masuk ke Aplikasi
            </button>
        </form>

        <!-- Akun Demo Cepat -->
        <div class="demo-box">
            <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--color-text-subtle);">
                Akses Uji Coba Cepat (Klik untuk mengisi):
            </div>
            <button type="button" class="demo-btn" onclick="fillLogin('gurubk', 'password123')">
                <strong>Guru BK:</strong> Dra. Endang Sri Rahayu (gurubk)
            </button>
            <button type="button" class="demo-btn" onclick="fillLogin('0071234561', 'password123')">
                <strong>Siswa 1 (Target Kuliah ITB):</strong> Ahmad Rizky (0071234561)
            </button>
            <button type="button" class="demo-btn" onclick="fillLogin('0071234564', 'password123')">
                <strong>Siswa 2 (Perlu Bimbingan):</strong> Dewi Lestari (0071234564)
            </button>
        </div>
    </div>

    <script>
        function fillLogin(username, password) {
            document.getElementById('loginInput').value = username;
            document.getElementById('passwordInput').value = password;
        }
    </script>
</body>
</html>
