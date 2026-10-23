@extends('layouts.pemantau')

@section('title', 'Dashboard Pemantauan Sekolah - Ruang BK Digital')

@section('content')
<!-- Page Header Bar -->
<div class="page-header-bar">
    <div>
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
            <span class="badge badge-primary" style="font-size: 11px; padding: 3px 8px; font-weight: 700;">
                {{ strtoupper(auth()->user()->role_label) }}
            </span>
            <span style="font-size: 12px; color: var(--color-text-muted);">
                Hak Akses Pemantauan Menyeluruh & Ekspor Data Siswa
            </span>
        </div>
        <h1 class="page-header-title">
            Dashboard Pemantauan Ruang BK Digital
        </h1>
    </div>
    <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
        <a href="{{ route($rolePrefix . '.ekspor.index') }}" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            <span>Menu Ekspor Data Siswa</span>
        </a>
        <div class="page-header-date">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
        </div>
    </div>
</div>

<!-- Hero Welcome Section with Role Context -->
<div class="hero-banner-executive" style="margin-bottom: 24px;">
    <div class="hero-banner-inner">
        <div class="hero-banner-text">
            <div class="hero-banner-greeting">
                Selamat Datang di Portal Pemantauan
            </div>
            <h2 class="hero-banner-heading">{{ auth()->user()->name }}</h2>
            <p class="hero-banner-desc">
                Pantau sebaran seluruh <strong>{{ number_format($totalStudents) }} siswa sekolah</strong> lintas tingkat kelas X, XI, dan XII, periksa sebaran rombongan belajar, serta ekspor data aktual siswa ke format Excel atau CSV secara langsung.
            </p>
            <div style="display: flex; gap: 10px; margin-top: 14px; flex-wrap: wrap;">
                <a href="{{ route($rolePrefix . '.siswa.index') }}" class="btn btn-primary btn-sm" style="background: #FFFFFF; color: var(--color-navy); border: none; font-weight: 700;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <span>Jelajahi Data Siswa</span>
                </a>
                <a href="{{ route($rolePrefix . '.ekspor.index') }}" class="btn btn-secondary btn-sm" style="background: rgba(255,255,255,0.15); color: #FFFFFF; border: 1px solid rgba(255,255,255,0.3); font-weight: 600;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    <span>Unduh Rekapitulasi Siswa</span>
                </a>
            </div>
        </div>
        <div class="hero-banner-visual">
            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="hero-banner-photo" />
            <div class="hero-banner-quote-badge">
                &ldquo;Data akurat, pemantauan transparan, privasi terlindungi.&rdquo;
            </div>
        </div>
    </div>
</div>

<!-- 4 Key Stat Cards (Total, Kelas X, Kelas XI, Kelas XII) -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <!-- Total Siswa -->
    <div class="card" style="background: #FFFFFF; border-top: 4px solid var(--color-primary);">
        <div class="card-body" style="padding: 18px 20px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-size: 12px; font-weight: 700; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.04em;">
                    Total Siswa Sekolah
                </span>
                <span style="display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 8px; background: var(--color-primary-light); color: var(--color-primary);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </span>
            </div>
            <div style="font-size: 28px; font-weight: 800; color: var(--color-navy); line-height: 1.2;">
                {{ number_format($totalStudents) }}
            </div>
            <div style="font-size: 12px; color: var(--color-text-muted); margin-top: 4px;">
                Terdaftar di sistem sekolah
            </div>
        </div>
    </div>

    <!-- Total Kelas X -->
    <div class="card" style="background: #FFFFFF; border-top: 4px solid #16866A;">
        <div class="card-body" style="padding: 18px 20px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-size: 12px; font-weight: 700; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.04em;">
                    Siswa Kelas X
                </span>
                <span style="display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 8px; background: var(--color-success-bg); color: var(--color-success);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                </span>
            </div>
            <div style="font-size: 28px; font-weight: 800; color: var(--color-navy); line-height: 1.2;">
                {{ number_format($totalGradeX) }}
            </div>
            <div style="font-size: 12px; color: var(--color-text-muted); margin-top: 4px;">
                {{ $totalStudents > 0 ? round(($totalGradeX / $totalStudents) * 100, 1) : 0 }}% dari total siswa
            </div>
        </div>
    </div>

    <!-- Total Kelas XI -->
    <div class="card" style="background: #FFFFFF; border-top: 4px solid #D99A32;">
        <div class="card-body" style="padding: 18px 20px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-size: 12px; font-weight: 700; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.04em;">
                    Siswa Kelas XI
                </span>
                <span style="display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 8px; background: var(--color-warning-bg); color: var(--color-warning);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                </span>
            </div>
            <div style="font-size: 28px; font-weight: 800; color: var(--color-navy); line-height: 1.2;">
                {{ number_format($totalGradeXI) }}
            </div>
            <div style="font-size: 12px; color: var(--color-text-muted); margin-top: 4px;">
                {{ $totalStudents > 0 ? round(($totalGradeXI / $totalStudents) * 100, 1) : 0 }}% dari total siswa
            </div>
        </div>
    </div>

    <!-- Total Kelas XII -->
    <div class="card" style="background: #FFFFFF; border-top: 4px solid #8C6D2B;">
        <div class="card-body" style="padding: 18px 20px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-size: 12px; font-weight: 700; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.04em;">
                    Siswa Kelas XII
                </span>
                <span style="display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 8px; background: var(--color-gold-light); color: var(--color-gold-text);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>
                </span>
            </div>
            <div style="font-size: 28px; font-weight: 800; color: var(--color-navy); line-height: 1.2;">
                {{ number_format($totalGradeXII) }}
            </div>
            <div style="font-size: 12px; color: var(--color-text-muted); margin-top: 4px;">
                {{ $totalStudents > 0 ? round(($totalGradeXII / $totalStudents) * 100, 1) : 0 }}% dari total siswa
            </div>
        </div>
    </div>
</div>

<!-- Section 2 Columns: Distribusi Jurusan & Rombel -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 20px; margin-bottom: 28px;">
    <!-- Sebaran Siswa Berdasarkan Jurusan -->
    <div class="card">
        <div class="card-header card-header-navy" style="display: flex; justify-content: space-between; align-items: center; padding: 16px 20px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: #60A5FA;"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                <h3 class="card-title" style="font-size: 15px; font-weight: 700; color: #FFFFFF; margin: 0;">Distribusi Siswa per Jurusan</h3>
            </div>
            <span class="badge" style="background: rgba(255, 255, 255, 0.15); color: #FFFFFF; border: 1px solid rgba(255, 255, 255, 0.25); font-size: 11px; padding: 3px 8px;">{{ count($majorStats) }} Jurusan</span>
        </div>
        <div class="card-body" style="padding: 16px 20px;">
            @if(count($majorStats) > 0)
                <div style="display: flex; flex-direction: column; gap: 14px;">
                    @foreach($majorStats as $stat)
                        <div>
                            <div style="display: flex; justify-content: space-between; font-size: 13px; font-weight: 600; margin-bottom: 4px;">
                                <span style="color: var(--color-navy);">{{ $stat['major'] }}</span>
                                <span style="color: var(--color-text-muted);">{{ number_format($stat['count']) }} Siswa ({{ $stat['percentage'] }}%)</span>
                            </div>
                            <div style="width: 100%; height: 8px; background: var(--color-border-subtle); border-radius: 4px; overflow: hidden;">
                                <div style="width: {{ $stat['percentage'] }}%; height: 100%; background: var(--color-primary); border-radius: 4px;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p style="color: var(--color-text-muted); font-size: 13px; text-align: center; margin: 10px 0;">Belum ada data jurusan yang tercatat.</p>
            @endif
        </div>
    </div>

    <!-- Sebaran Siswa Berdasarkan Rombongan Belajar -->
    <div class="card">
        <div class="card-header card-header-navy" style="display: flex; justify-content: space-between; align-items: center; padding: 16px 20px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: #34D399;"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                <h3 class="card-title" style="font-size: 15px; font-weight: 700; color: #FFFFFF; margin: 0;">Distribusi Rombongan Belajar</h3>
            </div>
            <span class="badge" style="background: rgba(255, 255, 255, 0.15); color: #FFFFFF; border: 1px solid rgba(255, 255, 255, 0.25); font-size: 11px; padding: 3px 8px;">{{ $rombelList->count() }} Rombel</span>
        </div>
        <div class="card-body" style="padding: 16px 20px;">
            @if($rombelList->count() > 0)
                <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                    @foreach($rombelList as $rombel)
                        <div style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 12px; background: var(--color-surface-subtle); border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-size: 12.5px;">
                            <span style="font-weight: 700; color: var(--color-navy);">{{ $rombel->name }}</span>
                            <span class="badge {{ $rombel->grade === 'X' ? 'badge-success' : ($rombel->grade === 'XI' ? 'badge-warning' : 'badge-primary') }}" style="font-size: 10.5px; padding: 2px 6px;">
                                {{ $rombel->students_count }} Siswa
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p style="color: var(--color-text-muted); font-size: 13px; text-align: center; margin: 10px 0;">Belum ada rombel terdaftar.</p>
            @endif
        </div>
    </div>
</div>

<!-- Filter Bar & Tabel Data Siswa -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header card-header-navy" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; padding: 16px 20px;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(255, 255, 255, 0.12); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#60A5FA" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </div>
            <div>
                <h3 class="card-title" style="font-size: 16px; font-weight: 700; color: #FFFFFF; margin: 0 0 2px 0;">
                    Tabel Pemantauan Data Siswa
                </h3>
                <span style="font-size: 12.5px; color: rgba(255, 255, 255, 0.75);">
                    Menampilkan data siswa aktif dan pembina BK yang terdaftar di sekolah
                </span>
            </div>
        </div>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route($rolePrefix . '.ekspor.index', request()->query()) }}" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 6px;" title="Ekspor sesuai filter aktif">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                <span>Ekspor Data</span>
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div style="padding: 16px 20px; background: var(--color-surface-subtle); border-bottom: 1px solid var(--color-border);">
        <form method="GET" action="{{ route($rolePrefix . '.dashboard') }}" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 1; min-width: 200px;">
                <label style="display: block; font-size: 12px; font-weight: 600; color: var(--color-text-muted); margin-bottom: 4px;">Pencarian Siswa</label>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Nama, NISN, atau NIS..." style="height: 38px;" />
            </div>

            <div style="width: 140px;">
                <label style="display: block; font-size: 12px; font-weight: 600; color: var(--color-text-muted); margin-bottom: 4px;">Tingkat Kelas</label>
                <select name="grade" class="form-control" style="height: 38px;">
                    <option value="semua">Semua Tingkat</option>
                    <option value="X" {{ request('grade') === 'X' ? 'selected' : '' }}>Kelas X</option>
                    <option value="XI" {{ request('grade') === 'XI' ? 'selected' : '' }}>Kelas XI</option>
                    <option value="XII" {{ request('grade') === 'XII' ? 'selected' : '' }}>Kelas XII</option>
                </select>
            </div>

            <div style="min-width: 180px;">
                <label style="display: block; font-size: 12px; font-weight: 600; color: var(--color-text-muted); margin-bottom: 4px;">Jurusan</label>
                <select name="major" class="form-control" style="height: 38px;">
                    <option value="semua">Semua Jurusan</option>
                    @foreach($majors as $m)
                        <option value="{{ $m }}" {{ request('major') === $m ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>

            <div style="min-width: 160px;">
                <label style="display: block; font-size: 12px; font-weight: 600; color: var(--color-text-muted); margin-bottom: 4px;">Rombel</label>
                <select name="student_class_id" class="form-control" style="height: 38px;">
                    <option value="semua">Semua Rombel</option>
                    @foreach($rombelList as $r)
                        <option value="{{ $r->id }}" {{ request('student_class_id') == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-primary" style="height: 38px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <span>Filter</span>
                </button>
                <a href="{{ route($rolePrefix . '.dashboard') }}" class="btn btn-secondary" style="height: 38px;" title="Reset Filter">
                    <span>Reset</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Table Content -->
    <div class="table-responsive" style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead class="table-thead-navy">
                <tr>
                    <th style="width: 40px; text-align: center;">No</th>
                    <th>Identitas Siswa</th>
                    <th>NISN / NIS</th>
                    <th>Tingkat & Rombel</th>
                    <th>Jurusan</th>
                    <th>Guru Pembina BK</th>
                    <th style="text-align: center;">Status</th>
                    <th style="text-align: center; width: 90px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $index => $st)
                    <tr>
                        <td style="text-align: center; color: var(--color-text-muted); font-size: 12px;">
                            {{ $students->firstItem() + $index }}
                        </td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <img src="{{ $st->avatar_url }}" alt="{{ $st->name }}" style="width: 34px; height: 34px; border-radius: 50%; object-fit: cover; border: 1px solid var(--color-border);" />
                                <div>
                                    <div style="font-weight: 700; color: var(--color-navy); font-size: 13.5px;">{{ $st->name }}</div>
                                    <div style="font-size: 11.5px; color: var(--color-text-muted);">{{ $st->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="font-family: monospace; font-size: 12.5px; font-weight: 600; color: var(--color-navy);">{{ $st->nisn ?? '-' }}</div>
                            <div style="font-size: 11.5px; color: var(--color-text-muted);">NIS: {{ $st->nis ?? '-' }}</div>
                        </td>
                        <td>
                            <span class="badge {{ $st->studentClass->grade === 'X' ? 'badge-success' : ($st->studentClass->grade === 'XI' ? 'badge-warning' : 'badge-primary') }}" style="font-size: 11px;">
                                {{ $st->studentClass->name ?? '-' }}
                            </span>
                        </td>
                        <td style="font-size: 12.5px; color: var(--color-text-navy);">
                            {{ $st->studentClass->major ?? '-' }}
                        </td>
                        <td>
                            @if($st->counselor)
                                <div style="font-weight: 600; font-size: 12.5px; color: var(--color-navy);">{{ $st->counselor->name }}</div>
                                <div style="font-size: 11px; color: var(--color-text-muted);">Guru BK Pembimbing</div>
                            @else
                                <span style="font-size: 12px; color: var(--color-text-muted); font-style: italic;">Belum ditugaskan</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <span class="badge {{ $st->status === 'aktif' ? 'badge-success' : 'badge-secondary' }}" style="font-size: 11px;">
                                {{ ucfirst((string) $st->status) }}
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <a href="{{ route($rolePrefix . '.siswa.show', $st->encrypted_id) }}" class="btn btn-secondary btn-sm" style="padding: 4px 8px; font-size: 12px;" title="Lihat Profil Siswa">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                <span>Detail</span>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 36px 20px;">
                            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color: var(--color-text-subtle);"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                <span style="font-size: 14px; font-weight: 600; color: var(--color-navy);">Tidak Ada Data Siswa Ditemukan</span>
                                <span style="font-size: 12.5px; color: var(--color-text-muted);">Coba ubah kata kunci pencarian atau bersihkan filter yang aktif.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($students->total() > 0)
        {{ $students->links('components.pagination') }}
    @endif
</div>
@endsection
