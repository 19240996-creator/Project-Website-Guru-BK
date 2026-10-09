@extends('layouts.app')

@section('title', 'Beranda & Prioritas Kerja - Guru BK')
@section('header_title', 'Pusat Kerja Guru Bimbingan Konseling')

@section('content')
<!-- Page Header Bar matching Reference UI -->
<div class="page-header-bar">
    <h1 class="page-header-title">Pusat Kerja Guru Bimbingan Konseling</h1>
    <div class="page-header-date">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
        <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
    </div>
</div>

<!-- Hero Welcome Executive Section with Counselor Photo Cutout & Quote -->
<div class="hero-banner-executive">
    <div class="hero-banner-inner">
        <div class="hero-banner-text">
            <div class="hero-banner-greeting">Selamat Bertugas,</div>
            <h2 class="hero-banner-heading">{{ auth()->user()->name }}</h2>
            <p class="hero-banner-desc">
                Pantau perkembangan siswa, kelola pendampingan, dan tindak lanjuti prioritas hari ini dalam satu ruang kerja.
            </p>
        </div>
        <div class="hero-banner-visual">
            <img src="{{ asset('images/counselor-hero.jpg') }}" alt="Konselor Guru BK" class="hero-banner-photo" />
            <div class="hero-banner-quote-badge">
                &ldquo;Bersama, kita bantu siswa menemukan jalan terbaik untuk masa depan.&rdquo;
            </div>
        </div>
    </div>
</div>

<!-- Key Aggregate Metrics (4 Stat Cards Row) -->
<div class="grid-4" style="margin-bottom: 24px;">
    <!-- Stat 1: Total Siswa Aktif -->
    <a href="{{ route('guru.siswa.index') }}" style="text-decoration: none; color: inherit;">
        <div class="stat-card-clean">
            <div>
                <div class="stat-card-top">
                    <div class="stat-icon-pill blue">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    </div>
                </div>
                <div class="stat-card-label">TOTAL SISWA AKTIF</div>
                <div class="stat-value-row">
                    <span class="stat-card-number">{{ number_format($totalStudents) }}</span>
                    <span class="stat-trend-tag">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"></polyline></svg>
                        +0%
                    </span>
                </div>
            </div>
            <p class="stat-card-desc">Terdaftar dalam tahun ajaran berjalan</p>
        </div>
    </a>

    <!-- Stat 2: Perlu Perhatian Khusus -->
    <a href="{{ route('guru.siswa.index') }}" style="text-decoration: none; color: inherit;">
        <div class="stat-card-clean">
            <div>
                <div class="stat-card-top">
                    <div class="stat-icon-pill amber">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle><line x1="12" y1="11" x2="12" y2="15"></line><line x1="12" y1="18" x2="12.01" y2="18"></line></svg>
                    </div>
                </div>
                <div class="stat-card-label">PERLU PERHATIAN KHUSUS</div>
                <div class="stat-value-row">
                    <span class="stat-card-number">{{ number_format($attentionStudentsCount) }}</span>
                    <span class="stat-trend-tag">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"></polyline></svg>
                        +0%
                    </span>
                </div>
            </div>
            <p class="stat-card-desc">Status perhatian, prioritas, atau tindak lanjut</p>
        </div>
    </a>

    <!-- Stat 3: Pengajuan Konseling Baru -->
    <a href="{{ route('guru.konseling.index') }}" style="text-decoration: none; color: inherit;">
        <div class="stat-card-clean">
            <div>
                <div class="stat-card-top">
                    <div class="stat-icon-pill green">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><polyline points="9 12 11 14 15 10"></polyline></svg>
                    </div>
                </div>
                <div class="stat-card-label">PENGAJUAN KONSELING BARU</div>
                <div class="stat-value-row">
                    <span class="stat-card-number">{{ number_format($newCounselingCount) }}</span>
                    <span class="stat-trend-tag">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"></polyline></svg>
                        +0%
                    </span>
                </div>
            </div>
            <p class="stat-card-desc">Menunggu tinjauan & penjadwalan</p>
        </div>
    </a>

    <!-- Stat 4: Tindak Lanjut Jatuh Tempo -->
    <a href="{{ route('guru.konseling.index') }}" style="text-decoration: none; color: inherit;">
        <div class="stat-card-clean">
            <div>
                <div class="stat-card-top">
                    <div class="stat-icon-pill red">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line><line x1="12" y1="14" x2="12" y2="18"></line></svg>
                    </div>
                </div>
                <div class="stat-card-label">TINDAK LANJUT JATUH TEMPO</div>
                <div class="stat-value-row">
                    <span class="stat-card-number">{{ number_format($overdueFollowUpsCount) }}</span>
                    <span class="stat-trend-tag">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"></polyline></svg>
                        +0%
                    </span>
                </div>
            </div>
            <p class="stat-card-desc">Butuh verifikasi dan aksi lanjutan</p>
        </div>
    </a>
</div>

<!-- Visual Monitoring Analytics (2 Columns: Column Chart & Donut Chart) -->
<div class="grid-2" style="margin-bottom: 24px;">
    <!-- Column Chart: Distribusi Kategori Layanan Konseling -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header-clean">
            <div>
                <h2 class="card-title-clean">Distribusi Kategori Layanan Konseling</h2>
                <p class="card-subtitle-clean">
                    Grafik beban masalah bimbingan siswa berdasarkan kategori (Total {{ array_sum($categoryChartData) }} Kasus)
                </p>
            </div>
            <a href="{{ route('guru.konseling.index') }}" class="btn-detail-pill" title="Lihat Detail Konseling">
                Lihat Detail
            </a>
        </div>
        <div class="card-body">
            @php
                $maxVal = max(array_merge([1], $categoryChartData));
                $colColors = ['#1E3A8A', '#3B82F6', '#0D9488', '#F59E0B', '#10B981'];
            @endphp
            <div id="counselingCategoryColumnChart" style="display: flex; flex-direction: column; height: 235px; justify-content: flex-end; padding-top: 10px;">
                <!-- Column Bars Area -->
                <div style="display: flex; align-items: flex-end; justify-content: space-around; height: 175px; gap: 14px; border-bottom: 1.5px solid var(--color-border); padding-bottom: 6px;">
                    @foreach($categoryChartLabels as $idx => $label)
                        @php
                            $val = $categoryChartData[$idx] ?? 0;
                            $fullName = $categoryChartFullNames[$idx] ?? $label;
                            $heightPercent = $maxVal > 0 ? ($val / $maxVal) : 0;
                            $barHeight = $val > 0 ? max(22, round($heightPercent * 135)) : 6;
                            $color = $colColors[$idx % count($colColors)];
                        @endphp
                        <div style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: flex-end; height: 100%; cursor: pointer;" title="{{ $fullName }}: {{ $val }} Kasus">
                            <span style="font-size: 13px; font-weight: 700; color: {{ $val > 0 ? $color : 'var(--color-text-subtle)' }}; margin-bottom: 6px;">
                                {{ $val }}
                            </span>
                            <div style="width: 100%; max-width: 44px; height: {{ $barHeight }}px; background: {{ $val > 0 ? $color : 'var(--color-border)' }}; border-radius: 6px 6px 0 0; transition: height 0.3s cubic-bezier(0.16, 1, 0.3, 1);"></div>
                        </div>
                    @endforeach
                </div>
                <!-- Labels Area -->
                <div style="display: flex; justify-content: space-around; gap: 14px; margin-top: 10px;">
                    @foreach($categoryChartLabels as $idx => $label)
                        @php
                            $fullName = $categoryChartFullNames[$idx] ?? $label;
                        @endphp
                        <div style="flex: 1; text-align: center;" title="{{ $fullName }}">
                            <div style="font-size: 12px; font-weight: 600; color: var(--color-navy); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $label }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Donut Chart: Peta Rencana Masa Depan Kelas XII -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header-clean">
            <div>
                <h2 class="card-title-clean">Peta Rencana Masa Depan Kelas XII</h2>
                <p class="card-subtitle-clean">
                    Grafik lingkaran proporsi karier siswa binaan (Total {{ $totalPlanStudents }} Siswa)
                </p>
            </div>
            <a href="{{ route('guru.peminatan.index') }}" class="btn-detail-pill" title="Lihat Detail Peminatan">
                Lihat Detail
            </a>
        </div>
        <div class="card-body">
            @php
                $planColors = [
                    'Target Kuliah' => '#1E3A8A',
                    'Target Bekerja' => '#0D9488',
                    'Target Wirausaha' => '#F59E0B',
                    'Kuliah & Kerja' => '#4338CA',
                    'Belum Menentukan' => '#DC2626',
                ];
                $circumference = 339.292; // 2 * pi * 54
                $cumulativeOffset = 0;
            @endphp
            <div id="futurePlanPieChart" style="display: flex; align-items: center; justify-content: space-between; gap: 24px; flex-wrap: wrap; min-height: 235px;">
                <!-- SVG Donut Graphic with Center Text -->
                <div style="position: relative; width: 146px; height: 146px; margin: 0 auto; flex-shrink: 0;">
                    <svg viewBox="0 0 140 140" width="146" height="146" style="transform: rotate(-90deg);">
                        <circle cx="70" cy="70" r="54" fill="transparent" stroke="#F1F4FA" stroke-width="18" />
                        @if($totalPlanStudents > 0)
                            @foreach($futurePlanChartLabels as $idx => $label)
                                @php
                                    $val = $futurePlanChartData[$idx] ?? 0;
                                    $fraction = $totalPlanStudents > 0 ? ($val / $totalPlanStudents) : 0;
                                    $dashArray = ($fraction * $circumference) . ' ' . $circumference;
                                    $dashOffset = -$cumulativeOffset;
                                    $hexColor = $planColors[$label] ?? '#DC2626';
                                    if ($val > 0) {
                                        $cumulativeOffset += ($fraction * $circumference);
                                    }
                                @endphp
                                @if($val > 0)
                                    <circle cx="70" cy="70" r="54" fill="transparent"
                                            stroke="{{ $hexColor }}"
                                            stroke-width="18"
                                            stroke-dasharray="{{ $dashArray }}"
                                            stroke-dashoffset="{{ $dashOffset }}" />
                                @endif
                            @endforeach
                        @else
                            <circle cx="70" cy="70" r="54" fill="transparent" stroke="#E5E9F2" stroke-width="18" />
                        @endif
                    </svg>
                    <!-- Center Metric Text -->
                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; pointer-events: none;">
                        <span style="font-size: 26px; font-weight: 800; color: var(--color-navy); line-height: 1; letter-spacing: -0.02em;">
                            {{ $totalPlanStudents }}
                        </span>
                        <span style="font-size: 10.5px; font-weight: 700; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.04em; margin-top: 3px;">
                            Siswa Binaan
                        </span>
                    </div>
                </div>

                <!-- Structured Legend with Metrics -->
                <div style="flex: 1; min-width: 190px; display: flex; flex-direction: column; gap: 8px;">
                    @foreach($futurePlanChartLabels as $idx => $label)
                        @php
                            $val = $futurePlanChartData[$idx] ?? 0;
                            $percent = $totalPlanStudents > 0 ? round(($val / $totalPlanStudents) * 100) : 0;
                            $color = $planColors[$label] ?? '#DC2626';
                        @endphp
                        <div style="display: flex; align-items: center; justify-content: space-between; font-size: 12.5px; padding: 4px 6px; border-radius: 6px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="display: inline-block; width: 9px; height: 9px; border-radius: 50%; background: {{ $color }}; flex-shrink: 0;"></span>
                                <span style="color: var(--color-navy); font-weight: 600;">{{ $label }}</span>
                            </div>
                            <div style="font-weight: 700; color: var(--color-navy); font-size: 12.5px;">
                                {{ $val }} <span style="font-weight: 500; color: #64748B; font-size: 11px;">({{ $percent }}%)</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 3 Priority Panels Row matching Reference UI -->
<div class="priority-three-grid" style="margin-bottom: 24px;">
    <!-- Panel 1: Pengajuan Konseling Terbaru -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header-clean">
            <h2 class="card-title-clean">Pengajuan Konseling Terbaru</h2>
            <a href="{{ route('guru.konseling.index') }}" class="link-all-blue" title="Lihat Semua Pengajuan">
                Lihat Semua
            </a>
        </div>
        <div class="card-body" style="padding: 0;">
            @if(count($recentCounselings) > 0)
                <div>
                    @foreach($recentCounselings as $c)
                        <div class="priority-list-item">
                            <div style="display: flex; align-items: center; gap: 12px; min-width: 0;">
                                <div style="width: 34px; height: 34px; border-radius: 8px; background: #EEF4FF; color: #2447A8; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 13px; font-weight: 700;">
                                    {{ strtoupper(substr($c->student ? $c->student->name : 'S', 0, 1)) }}
                                </div>
                                <div style="min-width: 0;">
                                    <div style="font-weight: 700; font-size: 13px; color: var(--color-navy); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ $c->student ? $c->student->name : 'Siswa' }}
                                    </div>
                                    <div style="font-size: 11.5px; color: #64748B; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        Konseling - {{ $c->category ? $c->category->name : 'Umum' }}
                                    </div>
                                </div>
                            </div>
                            <div style="text-align: right; flex-shrink: 0;">
                                <div>
                                    @if($c->status === 'diajukan')
                                        <span class="badge badge-warning" style="font-size: 10.5px; padding: 2px 7px;">Menunggu Tinjauan</span>
                                    @elseif($c->status === 'dijadwalkan')
                                        <span class="badge badge-primary" style="font-size: 10.5px; padding: 2px 7px;">Dijadwalkan</span>
                                    @else
                                        <span class="badge badge-success" style="font-size: 10.5px; padding: 2px 7px;">Selesai</span>
                                    @endif
                                </div>
                                <div style="font-size: 11px; color: #94A3B8; margin-top: 3px;">
                                    {{ $c->created_at->translatedFormat('d M Y') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state" style="padding: 28px 16px;">
                    <p class="empty-state-title" style="font-size: 13px;">Belum Ada Pengajuan Baru</p>
                    <p class="empty-state-desc" style="font-size: 12px;">Semua bimbingan siswa telah diverifikasi.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Panel 2: Jadwal Konseling Mendatang -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header-clean">
            <h2 class="card-title-clean">Jadwal Konseling Mendatang</h2>
            <a href="{{ route('guru.konseling.index') }}" class="link-all-blue" title="Lihat Semua Jadwal">
                Lihat Semua
            </a>
        </div>
        <div class="card-body" style="padding: 0;">
            @if(count($upcomingCounselings) > 0)
                <div>
                    @foreach($upcomingCounselings as $sched)
                        @php
                            $schedDate = $sched->scheduled_date ? \Carbon\Carbon::parse($sched->scheduled_date) : $sched->created_at;
                        @endphp
                        <div class="priority-list-item">
                            <div style="display: flex; align-items: center; gap: 12px; min-width: 0;">
                                <div class="date-badge-box">
                                    <span class="date-badge-day">{{ $schedDate->format('d') }}</span>
                                    <span class="date-badge-month">{{ $schedDate->translatedFormat('M') }}</span>
                                </div>
                                <div style="min-width: 0;">
                                    <div style="font-weight: 700; font-size: 13px; color: var(--color-navy); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ $sched->student ? $sched->student->name : 'Siswa' }}
                                    </div>
                                    <div style="font-size: 11.5px; color: #64748B; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        Konseling - {{ $sched->category ? $sched->category->name : 'Umum' }}
                                    </div>
                                </div>
                            </div>
                            <div style="text-align: right; flex-shrink: 0;">
                                <div style="font-size: 11.5px; font-weight: 600; color: #334155;">
                                    {{ substr($sched->scheduled_time ?? '09:00', 0, 5) }} - 10:00
                                </div>
                                <div style="font-size: 11px; color: #64748B; margin-top: 3px; display: flex; align-items: center; justify-content: flex-end; gap: 4px;">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                    <span>{{ $sched->scheduled_location ?: 'Ruang BK' }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state" style="padding: 28px 16px;">
                    <p class="empty-state-title" style="font-size: 13px;">Belum Ada Jadwal Pertemuan</p>
                    <p class="empty-state-desc" style="font-size: 12px;">Tidak ada sesi konseling terdekat yang dijadwalkan.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Panel 3: Aktivitas Terbaru -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header-clean">
            <h2 class="card-title-clean">Aktivitas Terbaru</h2>
            <a href="{{ route('guru.audit.index') }}" class="link-all-blue" title="Lihat Semua Aktivitas">
                Lihat Semua
            </a>
        </div>
        <div class="card-body" style="padding: 0;">
            @if(count($recentActivities) > 0)
                <div>
                    @foreach($recentActivities as $act)
                        <div class="priority-list-item">
                            <div style="display: flex; align-items: center; gap: 12px; min-width: 0;">
                                <div style="width: 32px; height: 32px; border-radius: 50%; background: #FFF8E6; color: #D97706; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                </div>
                                <div style="min-width: 0;">
                                    <div style="font-weight: 700; font-size: 13px; color: var(--color-navy); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ \Illuminate\Support\Str::limit($act->description, 26) }}
                                    </div>
                                    <div style="font-size: 11.5px; color: #64748B;">
                                        oleh {{ $act->user ? \Illuminate\Support\Str::limit($act->user->name, 18) : 'Sistem' }}
                                    </div>
                                </div>
                            </div>
                            <div style="font-size: 11px; color: #94A3B8; text-align: right; flex-shrink: 0;">
                                {{ $act->created_at->diffForHumans() }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state" style="padding: 28px 16px;">
                    <p class="empty-state-title" style="font-size: 13px;">Belum Ada Catatan Aktivitas</p>
                    <p class="empty-state-desc" style="font-size: 12px;">Aktivitas terkini siswa dan guru akan tercatat di sini.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
