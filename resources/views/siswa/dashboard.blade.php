@extends('layouts.siswa')

@section('title', 'Ruang BK - ' . $student->name)
@section('header_title', 'Ruang Mandiri Siswa')

@section('content')
<!-- Hero Welcome Student Section with Student Photo Cutout & Quote -->
<div class="hero-banner-executive" style="background: linear-gradient(135deg, #0B1630 0%, #152E63 55%, #1F4594 100%);">
    <div class="hero-banner-inner">
        <div class="hero-banner-text">
            <h1 class="hero-banner-heading" style="font-size: 26px; margin-bottom: 6px;">
                Halo, {{ $student->name }}!
            </h1>
            <p class="hero-banner-desc">
                Kenali potensimu, rencanakan masa depanmu, dan terus berkembang bersama RUANG BK.
            </p>
        </div>
        <div class="hero-banner-visual">
            <img src="{{ asset('images/student-hero.jpg') }}" alt="{{ $student->name }}" class="hero-banner-photo" />
            <div class="hero-banner-quote-badge">
                &ldquo;Langkah kecil hari ini, bisa menjadi awal dari masa depan yang besar.&rdquo;
            </div>
        </div>
    </div>
</div>

<!-- 4 Quick Access Service Cards Row matching Reference UI -->
<div class="student-quick-grid">
    <!-- Card 1: Bimbingan & Konseling -->
    <a href="{{ route('siswa.konseling.index') }}" class="student-quick-card">
        <div class="stat-icon-pill blue">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
        </div>
        <div>
            <div class="student-quick-title">Bimbingan & Konseling</div>
            <p class="student-quick-desc">Ajukan konseling dan lihat jadwalmu</p>
        </div>
    </a>

    <!-- Card 2: Asesmen Diri -->
    <a href="{{ route('siswa.asesmen.index') }}" class="student-quick-card">
        <div class="stat-icon-pill green">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><polyline points="9 12 11 14 15 10"></polyline></svg>
        </div>
        <div>
            <div class="student-quick-title">Asesmen Diri</div>
            <p class="student-quick-desc">Kenali minat, bakat dan potensimu</p>
        </div>
    </a>

    <!-- Card 3: Peminatan & Masa Depan -->
    <a href="{{ route('siswa.rencana.show') }}" class="student-quick-card">
        <div class="stat-icon-pill amber">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
        </div>
        <div>
            <div class="student-quick-title">Peminatan & Masa Depan</div>
            <p class="student-quick-desc">Tentukan rencana setelah lulus</p>
        </div>
    </a>

    <!-- Card 4: Perguruan Tinggi & Industri -->
    <a href="{{ route('siswa.peluang.index') }}" class="student-quick-card">
        <div class="stat-icon-pill blue" style="background: #E0E7FF; color: #1E3A8A;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
        </div>
        <div>
            <div class="student-quick-title">Perguruan Tinggi & Industri</div>
            <p class="student-quick-desc">Informasi kampus dan dunia kerja</p>
        </div>
    </a>
</div>

<!-- Middle Section: 3 Status Cards Row matching Reference UI -->
<div class="priority-three-grid" style="margin-bottom: 24px;">
    <!-- Card 1: Status Pengajuan Konseling -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header-clean">
            <h2 class="card-title-clean">Status Pengajuan Konseling</h2>
            <a href="{{ route('siswa.konseling.index') }}" class="link-all-blue" title="Lihat Semua Status">
                Lihat Semua
            </a>
        </div>
        <div class="card-body">
            @if($upcomingCounseling)
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 36px; height: 36px; border-radius: 50%; background: #ECFDF5; color: #059669; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        </div>
                        <div>
                            <div style="font-weight: 700; font-size: 13.5px; color: var(--color-navy);">
                                Konseling - {{ $upcomingCounseling->category ? $upcomingCounseling->category->name : 'Karier' }}
                            </div>
                            <div style="margin-top: 3px; display: flex; align-items: center; gap: 8px;">
                                @if($upcomingCounseling->status === 'dijadwalkan')
                                    <span class="badge badge-success" style="font-size: 10.5px; padding: 2px 7px;">Disetujui</span>
                                @elseif($upcomingCounseling->status === 'diajukan')
                                    <span class="badge badge-warning" style="font-size: 10.5px; padding: 2px 7px;">Menunggu Tinjauan</span>
                                @else
                                    <span class="badge badge-primary" style="font-size: 10.5px; padding: 2px 7px;">{{ ucfirst($upcomingCounseling->status) }}</span>
                                @endif
                                <span style="font-size: 11px; color: #94A3B8;">
                                    Tanggal pengajuan: {{ $upcomingCounseling->created_at->translatedFormat('d M Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('siswa.konseling.show', $upcomingCounseling->id) }}" class="btn-detail-pill">
                        Detail
                    </a>
                </div>
            @else
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 36px; height: 36px; border-radius: 50%; background: #EEF4FF; color: #2447A8; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                        </div>
                        <div>
                            <div style="font-weight: 700; font-size: 13.5px; color: var(--color-navy);">Belum Ada Konseling Aktif</div>
                            <div style="font-size: 11.5px; color: #64748B;">Butuh arahan akademik atau karier?</div>
                        </div>
                    </div>
                    <a href="{{ route('siswa.konseling.create') }}" class="btn-detail-pill">
                        Ajukan
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Card 2: Jadwal Konseling -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header-clean">
            <h2 class="card-title-clean">Jadwal Konseling</h2>
            <a href="{{ route('siswa.konseling.index') }}" class="link-all-blue" title="Lihat Semua Jadwal">
                Lihat Semua
            </a>
        </div>
        <div class="card-body">
            @if($scheduledCounseling)
                @php
                    $schedDate = $scheduledCounseling->scheduled_date ? \Carbon\Carbon::parse($scheduledCounseling->scheduled_date) : \Carbon\Carbon::now();
                @endphp
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div class="date-badge-box">
                            <span class="date-badge-day">{{ $schedDate->format('d') }}</span>
                            <span class="date-badge-month">{{ $schedDate->translatedFormat('M') }}</span>
                        </div>
                        <div>
                            <div style="font-weight: 700; font-size: 13.5px; color: var(--color-navy);">
                                Konseling - {{ $scheduledCounseling->category ? $scheduledCounseling->category->name : 'Karier' }}
                            </div>
                            <div style="font-size: 11.5px; color: #64748B; margin-top: 3px; display: flex; align-items: center; gap: 6px;">
                                <span>{{ substr($scheduledCounseling->scheduled_time ?? '09:00', 0, 5) }} - 10:00</span>
                                <span>&bull;</span>
                                <span style="display: inline-flex; align-items: center; gap: 3px;">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg>
                                    {{ $scheduledCounseling->scheduled_location ?: 'Online' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('siswa.konseling.show', $scheduledCounseling->id) }}" class="btn-detail-pill">
                        Detail
                    </a>
                </div>
            @else
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div class="date-badge-box">
                            <span class="date-badge-day">{{ date('d') }}</span>
                            <span class="date-badge-month">{{ date('M') }}</span>
                        </div>
                        <div>
                            <div style="font-weight: 700; font-size: 13.5px; color: var(--color-navy);">Belum Ada Jadwal</div>
                            <div style="font-size: 11.5px; color: #64748B;">Sesi konseling terjadwal akan tampil di sini</div>
                        </div>
                    </div>
                    <a href="{{ route('siswa.konseling.create') }}" class="btn-detail-pill">
                        Jadwalkan
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Card 3: Rencana Masa Depan Saya (Progress Status) -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header-clean">
            <h2 class="card-title-clean">Rencana Masa Depan Saya</h2>
            <a href="{{ route('siswa.rencana.show') }}" class="link-all-blue" title="Lihat Rincian Rencana">
                Lihat Detail
            </a>
        </div>
        <div class="card-body">
            @php
                $planGoal = $student->futurePlan ? $student->futurePlan->primary_goal : 'belum_menentukan';
                $goalLabelMap = [
                    'kuliah' => 'Target Kuliah',
                    'bekerja' => 'Target Bekerja',
                    'kuliah_kerja' => 'Kuliah sambil Kerja',
                    'wirausaha' => 'Target Wirausaha',
                    'belum_menentukan' => 'Belum Menentukan',
                ];
                $goalLabel = $goalLabelMap[$planGoal] ?? 'Belum Menentukan';
                $isConfigured = $planGoal !== 'belum_menentukan';
            @endphp
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 12px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: {{ $isConfigured ? '#059669' : '#D97706' }};"></span>
                    <strong style="font-size: 13.5px; color: var(--color-navy);">{{ $goalLabel }}</strong>
                </div>
                <span class="badge {{ $isConfigured ? 'badge-success' : 'badge-warning' }}" style="font-size: 10.5px;">
                    {{ $isConfigured ? 'Terencana' : 'Belum Lengkap' }}
                </span>
            </div>
            <!-- Progress Bar -->
            <div style="width: 100%; height: 8px; background: #F1F5F9; border-radius: 9999px; overflow: hidden; margin-bottom: 8px;">
                <div style="height: 100%; width: {{ $isConfigured ? '100%' : '25%' }}; background: linear-gradient(90deg, #2447A8 0%, #4778F5 100%); border-radius: 9999px;"></div>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 11px; color: #94A3B8;">
                <span>Progres Rancangan Karier</span>
                <span>{{ $isConfigured ? '100%' : '25%' }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Section: 4 Pathway Cards + News Article Preview -->
<div class="grid-2" style="margin-bottom: 24px;">
    <!-- Left Column: Rencana Masa Depan Sayra (4 Pathway Cards) -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header-clean">
            <div>
                <h2 class="card-title-clean">Rencana Masa Depan Siswa</h2>
                <p class="card-subtitle-clean">Pilih dan mantapkan target kariermu setelah lulus sekolah</p>
            </div>
            <a href="{{ route('siswa.rencana.show') }}" class="link-all-blue" title="Kelola Rencana">
                Lihat Detail
            </a>
        </div>
        <div class="card-body">
            @php
                $activeGoal = $student->futurePlan ? $student->futurePlan->primary_goal : '';
            @endphp
            <div class="pathway-selector-grid">
                <!-- 1. Kuliah -->
                <a href="{{ route('siswa.rencana.show') }}" class="pathway-pill-card kuliah {{ $activeGoal === 'kuliah' ? 'active' : '' }}">
                    <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(36, 71, 168, 0.12); display: flex; align-items: center; justify-content: center;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>
                    </div>
                    <span class="pathway-pill-card-title">Kuliah</span>
                </a>

                <!-- 2. Bekerja -->
                <a href="{{ route('siswa.rencana.show') }}" class="pathway-pill-card bekerja {{ $activeGoal === 'bekerja' ? 'active' : '' }}">
                    <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(5, 150, 105, 0.12); display: flex; align-items: center; justify-content: center;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                    </div>
                    <span class="pathway-pill-card-title">Bekerja</span>
                </a>

                <!-- 3. Kuliah sambil Kerja -->
                <a href="{{ route('siswa.rencana.show') }}" class="pathway-pill-card kuliah-kerja {{ $activeGoal === 'kuliah_kerja' ? 'active' : '' }}">
                    <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(217, 119, 6, 0.12); display: flex; align-items: center; justify-content: center;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    </div>
                    <span class="pathway-pill-card-title">Kuliah & Kerja</span>
                </a>

                <!-- 4. Wirausaha -->
                <a href="{{ route('siswa.rencana.show') }}" class="pathway-pill-card wirausaha {{ $activeGoal === 'wirausaha' ? 'active' : '' }}">
                    <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(220, 38, 38, 0.12); display: flex; align-items: center; justify-content: center;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                    </div>
                    <span class="pathway-pill-card-title">Wirausaha</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Right Column: Artikel & Informasi Terbaru -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header-clean">
            <div>
                <h2 class="card-title-clean">Artikel & Informasi Terbaru</h2>
                <p class="card-subtitle-clean">Wawasan seputar perguruan tinggi, beasiswa, dan karier</p>
            </div>
            <a href="{{ route('siswa.peluang.index') }}" class="link-all-blue" title="Buka Semua Artikel">
                Lihat Semua
            </a>
        </div>
        <div class="card-body">
            <a href="{{ route('siswa.peluang.index') }}" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 16px; padding: 12px; border-radius: 12px; background: #F8FAFC; border: 1px solid #E2E8F0; transition: all var(--transition-fast);">
                <img src="{{ asset('images/login-hero.jpg') }}" alt="Tips Perkuliahan" style="width: 72px; height: 72px; border-radius: 10px; object-fit: cover; flex-shrink: 0;" />
                <div style="min-width: 0;">
                    <div style="font-weight: 700; font-size: 13.5px; color: var(--color-navy); line-height: 1.35; margin-bottom: 4px;">
                        Tips Menentukan Jurusan Kuliah Sesuai Minat dan Bakat
                    </div>
                    <div style="font-size: 11.5px; color: #64748B;">
                        oleh Guru BK &bull; 7 Okt 2026
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
