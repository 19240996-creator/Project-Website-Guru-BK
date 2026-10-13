@extends('layouts.app')

@section('title', 'Laporan & Grafik Analitik - Guru BK')
@section('header_title', 'Pusat Analisis Data & Laporan Pimpinan')

@section('content')
<!-- Page Header Bar -->
<div class="page-header-bar" style="margin-bottom: 20px;">
    <div>
        <h1 class="page-header-title">Laporan & Grafik Analitik Bimbingan Konseling</h1>
        <p style="font-size: 13px; color: var(--color-text-muted); margin: 4px 0 0 0;">
            Visualisasi data komprehensif, pemantauan tren bimbingan siswa, serta ekspor dokumen laporan resmi pimpinan sekolah.
        </p>
    </div>
    <div class="page-header-date">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
        <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
    </div>
</div>

<!-- Filter Bar Card -->
<div class="card" style="margin-bottom: 22px; border-left: 4px solid var(--color-primary);">
    <div class="card-body" style="padding: 18px 20px;">
        <form method="GET" action="{{ route('guru.laporan.index') }}" style="margin: 0;">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; margin-bottom: 12px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 6px; background: rgba(37, 99, 235, 0.12); color: var(--color-primary);">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                    </span>
                    <strong style="font-size: 14px; color: var(--color-text-main);">Filter Parameter Grafik & Analisis</strong>
                </div>

                @if($selectedGrade || $selectedMajor || $selectedPeriod)
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span class="badge badge-primary" style="font-size: 11px;">
                            Filter Aktif: {{ ($selectedGrade ? 'Kelas ' . $selectedGrade : '') . ($selectedGrade && $selectedMajor ? ' • ' : '') . ($selectedMajor ?: '') . (($selectedGrade || $selectedMajor) && $selectedPeriod ? ' • ' : '') . ($selectedPeriod ? str_replace('_', ' ', $selectedPeriod) : '') }}
                        </span>
                        <a href="{{ route('guru.laporan.index') }}" class="btn btn-secondary btn-sm" style="font-size: 11px; padding: 3px 8px;">
                            ✕ Reset Filter
                        </a>
                    </div>
                @endif
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) 130px; gap: 12px; align-items: flex-end;">
                <div>
                    <label class="form-label" style="font-size: 12px; font-weight: 600; margin-bottom: 5px;">Tingkat Kelas</label>
                    <select name="grade" class="form-select" style="min-height: 38px; font-size: 13px;">
                        <option value="">Semua Tingkat</option>
                        @foreach($availableGrades as $g)
                            <option value="{{ $g }}" {{ $selectedGrade === $g ? 'selected' : '' }}>Kelas {{ $g }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label" style="font-size: 12px; font-weight: 600; margin-bottom: 5px;">Jurusan / Program Keahlian</label>
                    <select name="major" class="form-select" style="min-height: 38px; font-size: 13px;">
                        <option value="">Semua Jurusan</option>
                        @foreach($availableMajors as $m)
                            <option value="{{ $m }}" {{ $selectedMajor === $m ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label" style="font-size: 12px; font-weight: 600; margin-bottom: 5px;">Rentang Periode Konseling</label>
                    <select name="period" class="form-select" style="min-height: 38px; font-size: 13px;">
                        <option value="">Semua Periode Waktu</option>
                        <option value="1_bulan" {{ $selectedPeriod === '1_bulan' ? 'selected' : '' }}>1 Bulan Terakhir</option>
                        <option value="3_bulan" {{ $selectedPeriod === '3_bulan' ? 'selected' : '' }}>3 Bulan Terakhir</option>
                        <option value="6_bulan" {{ $selectedPeriod === '6_bulan' ? 'selected' : '' }}>6 Bulan Terakhir</option>
                        <option value="1_tahun" {{ $selectedPeriod === '1_tahun' ? 'selected' : '' }}>1 Tahun Terakhir</option>
                    </select>
                </div>

                <div>
                    <button type="submit" class="btn btn-primary btn-sm" style="width: 100%; min-height: 38px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                        <span>Filter Data</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Key Aggregate Metrics (4 Stat Cards Row) -->
<div class="grid-4" style="margin-bottom: 24px;">
    <!-- Stat 1: Total Siswa Terfilter -->
    <div class="stat-card-clean">
        <div>
            <div class="stat-card-top">
                <div class="stat-icon-pill blue">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </div>
            </div>
            <div class="stat-card-label">TOTAL SISWA AKTIF</div>
            <div class="stat-value-row">
                <span class="stat-card-number">{{ number_format($totalFilteredStudents) }}</span>
                <span class="stat-trend-tag">Siswa</span>
            </div>
        </div>
        <p class="stat-card-desc">Cakupan data siswa terfilter</p>
    </div>

    <!-- Stat 2: Total Kasus Konseling Terfilter -->
    <div class="stat-card-clean">
        <div>
            <div class="stat-card-top">
                <div class="stat-icon-pill teal">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                </div>
            </div>
            <div class="stat-card-label">LAYANAN KONSELING</div>
            <div class="stat-value-row">
                <span class="stat-card-number">{{ number_format($totalFilteredCounselings) }}</span>
                <span class="stat-trend-tag" style="background: rgba(13, 148, 136, 0.12); color: #0D9488;">Kasus</span>
            </div>
        </div>
        <p class="stat-card-desc">Beban bimbingan tersinkronisasi</p>
    </div>

    <!-- Stat 3: Siswa Perlu Perhatian -->
    <div class="stat-card-clean">
        <div>
            <div class="stat-card-top">
                <div class="stat-icon-pill amber">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                </div>
            </div>
            <div class="stat-card-label">PERLU PERHATIAN KHUSUS</div>
            <div class="stat-value-row">
                <span class="stat-card-number" style="color: #D97706;">{{ number_format($attentionStudentsCount) }}</span>
                <span class="stat-trend-tag" style="background: rgba(217, 119, 6, 0.12); color: #D97706;">Prioritas</span>
            </div>
        </div>
        <p class="stat-card-desc">Memerlukan atensi & pendampingan</p>
    </div>

    <!-- Stat 4: Mitra & Agenda Sekolah -->
    <div class="stat-card-clean">
        <div>
            <div class="stat-card-top">
                <div class="stat-icon-pill purple">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                </div>
            </div>
            <div class="stat-card-label">MITRA & AGENDA AKTIF</div>
            <div class="stat-value-row">
                <span class="stat-card-number">{{ number_format($activePartnersCount) }}</span>
                <span class="stat-trend-tag" style="background: rgba(99, 102, 241, 0.12); color: #6366F1;">{{ $totalActivitiesCount }} Kegiatan</span>
            </div>
        </div>
        <p class="stat-card-desc">Jaringan perguruan tinggi & industri</p>
    </div>
</div>

<!-- ========================================================================= -->
<!-- SECTION 1: DIAGRAM UTAMA DARI BERANDA (Harmonisasi 2 Kolom)               -->
<!-- ========================================================================= -->
<div style="margin-bottom: 12px;">
    <h3 style="font-size: 16px; font-weight: 700; color: var(--color-navy); margin: 0 0 4px 0;">
        1. Analisis Kategori Konseling & Peta Rencana Masa Depan
    </h3>
    <small style="color: var(--color-text-subtle);">Diagram analitik terharmonisasi dari Pusat Kerja Beranda dengan filter aktif.</small>
</div>

<div class="grid-2" style="margin-bottom: 24px;">
    <!-- Diagram 1: Kategori Kasus Bimbingan (Column Chart dari Beranda) -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header-clean">
            <div>
                <h2 class="card-title-clean">Distribusi Kategori Layanan Konseling</h2>
                <p class="card-subtitle-clean">
                    Grafik beban masalah bimbingan siswa berdasarkan kategori (Total {{ array_sum($categoryChartData) }} Kasus)
                </p>
            </div>
            <a href="{{ route('guru.konseling.index') }}" class="btn-detail-pill" title="Lihat Detail Konseling">
                Buka Konseling
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

    <!-- Diagram 2: Peta Rencana Masa Depan Siswa (SVG Donut Chart dari Beranda) -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header-clean">
            <div>
                <h2 class="card-title-clean">Peta Rencana Masa Depan Siswa</h2>
                <p class="card-subtitle-clean">
                    Grafik lingkaran proporsi karier siswa binaan terfilter (Total {{ $totalPlanStudents }} Siswa)
                </p>
            </div>
            <a href="{{ route('guru.peminatan.index') }}" class="btn-detail-pill" title="Lihat Detail Peminatan">
                Buka Peminatan
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

<!-- ========================================================================= -->
<!-- SECTION 2: DIAGRAM ANALITIK TAMBAHAN (Status Konseling & Attention Level)  -->
<!-- ========================================================================= -->
<div style="margin-bottom: 12px;">
    <h3 style="font-size: 16px; font-weight: 700; color: var(--color-navy); margin: 0 0 4px 0;">
        2. Efektivitas Penanganan Konseling & Pemetaan Tingkat Perhatian Siswa
    </h3>
    <small style="color: var(--color-text-subtle);">Memantau rasio keberhasilan layanan bimbingan serta deteksi dini siswa rentan pembinaan.</small>
</div>

<div class="grid-2" style="margin-bottom: 24px;">
    <!-- Diagram 3: Status Penyelesaian Layanan Konseling (SVG Donut Chart) -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header-clean">
            <div>
                <h2 class="card-title-clean">Status Penyelesaian Layanan Konseling</h2>
                <p class="card-subtitle-clean">
                    Persentase efektivitas penyelesaian kasus konseling sekolah
                </p>
            </div>
            <span class="badge badge-primary" style="font-size: 11px;">
                {{ $totalFilteredCounselings }} Sesi Terdata
            </span>
        </div>
        <div class="card-body">
            @php
                $statusColors = [
                    'Selesai' => '#10B981',
                    'Dilaksanakan' => '#3B82F6',
                    'Tindak Lanjut' => '#F59E0B',
                    'Menunggu / Baru' => '#6366F1',
                    'Dibatalkan' => '#EF4444',
                ];
                $counselingTotal = max(1, $totalFilteredCounselings);
                $counselingOffset = 0;
            @endphp
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 24px; flex-wrap: wrap; min-height: 235px;">
                <!-- SVG Donut -->
                <div style="position: relative; width: 146px; height: 146px; margin: 0 auto; flex-shrink: 0;">
                    <svg viewBox="0 0 140 140" width="146" height="146" style="transform: rotate(-90deg);">
                        <circle cx="70" cy="70" r="54" fill="transparent" stroke="#F1F4FA" stroke-width="18" />
                        @if($totalFilteredCounselings > 0)
                            @foreach($counselingStatusCounts as $stName => $stVal)
                                @php
                                    $fraction = $stVal / $counselingTotal;
                                    $dashArray = ($fraction * $circumference) . ' ' . $circumference;
                                    $dashOffset = -$counselingOffset;
                                    $hexColor = $statusColors[$stName] ?? '#64748B';
                                    if ($stVal > 0) {
                                        $counselingOffset += ($fraction * $circumference);
                                    }
                                @endphp
                                @if($stVal > 0)
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
                        <span style="font-size: 26px; font-weight: 800; color: #10B981; line-height: 1; letter-spacing: -0.02em;">
                            {{ $totalFilteredCounselings > 0 ? round(($counselingStatusCounts['Selesai'] / $totalFilteredCounselings) * 100) : 0 }}%
                        </span>
                        <span style="font-size: 10px; font-weight: 700; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.04em; margin-top: 3px;">
                            Kasus Tuntas
                        </span>
                    </div>
                </div>

                <!-- Structured Legend with Metrics -->
                <div style="flex: 1; min-width: 190px; display: flex; flex-direction: column; gap: 8px;">
                    @foreach($counselingStatusCounts as $stName => $stVal)
                        @php
                            $stPercent = $totalFilteredCounselings > 0 ? round(($stVal / $totalFilteredCounselings) * 100) : 0;
                            $stColor = $statusColors[$stName] ?? '#64748B';
                        @endphp
                        <div style="display: flex; align-items: center; justify-content: space-between; font-size: 12.5px; padding: 4px 6px; border-radius: 6px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="display: inline-block; width: 9px; height: 9px; border-radius: 50%; background: {{ $stColor }}; flex-shrink: 0;"></span>
                                <span style="color: var(--color-navy); font-weight: 600;">{{ $stName }}</span>
                            </div>
                            <div style="font-weight: 700; color: var(--color-navy); font-size: 12.5px;">
                                {{ $stVal }} <span style="font-weight: 500; color: #64748B; font-size: 11px;">({{ $stPercent }}%)</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Diagram 4: Tingkat Perhatian Siswa (Attention Level Bar Chart) -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header-clean">
            <div>
                <h2 class="card-title-clean">Distribusi Tingkat Perhatian & Pembinaan Siswa</h2>
                <p class="card-subtitle-clean">
                    Deteksi status pembinaan siswa binaan sekolah terfilter
                </p>
            </div>
            <a href="{{ route('guru.siswa.index') }}" class="btn-detail-pill" title="Buka Data Siswa">
                Profil Siswa 360°
            </a>
        </div>
        <div class="card-body">
            @php
                $attColors = [
                    'Aman / Normal' => '#10B981',
                    'Perlu Perhatian' => '#F59E0B',
                    'Prioritas' => '#F97316',
                    'Tindak Lanjut Cepat' => '#EF4444',
                ];
                $maxAtt = max(array_merge([1], array_values($attentionLevelCounts)));
            @endphp
            <div style="display: flex; flex-direction: column; gap: 14px; min-height: 235px; justify-content: center;">
                @foreach($attentionLevelCounts as $attName => $attVal)
                    @php
                        $attPercent = $totalFilteredStudents > 0 ? round(($attVal / $totalFilteredStudents) * 100) : 0;
                        $barWidth = $maxAtt > 0 ? max(4, round(($attVal / $maxAtt) * 100)) : 4;
                        $color = $attColors[$attName] ?? '#64748B';
                    @endphp
                    <div>
                        <div style="display: flex; justify-content: space-between; font-size: 12.5px; font-weight: 600; margin-bottom: 5px;">
                            <span style="color: var(--color-navy); display: flex; align-items: center; gap: 6px;">
                                <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: {{ $color }};"></span>
                                {{ $attName }}
                            </span>
                            <span style="color: var(--color-text-main); font-weight: 700;">
                                {{ $attVal }} Siswa <span style="font-weight: 500; color: var(--color-text-subtle); font-size: 11px;">({{ $attPercent }}%)</span>
                            </span>
                        </div>
                        <div style="width: 100%; height: 9px; background: var(--color-surface-hover, #F1F5F9); border-radius: 999px; overflow: hidden;">
                            <div style="width: {{ $barWidth }}%; height: 100%; background: {{ $color }}; border-radius: 999px; transition: width 0.4s ease;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- SECTION 3: DIAGRAM DEMOGRAFI & PENYERAPAN LULUSAN                         -->
<!-- ========================================================================= -->
<div style="margin-bottom: 12px;">
    <h3 style="font-size: 16px; font-weight: 700; color: var(--color-navy); margin: 0 0 4px 0;">
        3. Demografi Siswa & Peta Penyerapan Alumni (Tracer Study)
    </h3>
    <small style="color: var(--color-text-subtle);">Distribusi jenjang rombel kelas terfilter serta pemantauan keterserapan lulusan sekolah.</small>
</div>

<div class="grid-2" style="margin-bottom: 26px;">
    <!-- Diagram 5: Distribusi Siswa per Jenjang Kelas (Bar Chart) -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header-clean">
            <div>
                <h2 class="card-title-clean">Distribusi Siswa per Jenjang Kelas</h2>
                <p class="card-subtitle-clean">
                    Proporsi populasi siswa terfilter per tingkat rombel (Total {{ $totalFilteredStudents }} Siswa)
                </p>
            </div>
            <span class="badge badge-secondary" style="font-size: 11px;">Jenjang Rombel</span>
        </div>
        <div class="card-body">
            @php
                $gradeColors = [
                    'Kelas X' => '#2563EB',
                    'Kelas XI' => '#0D9488',
                    'Kelas XII' => '#D97706',
                ];
                $maxGrade = max(array_merge([1], array_values($gradeCounts)));
            @endphp
            <div style="display: flex; flex-direction: column; gap: 16px; min-height: 235px; justify-content: center;">
                @foreach($gradeCounts as $gName => $gVal)
                    @php
                        $gPercent = $totalFilteredStudents > 0 ? round(($gVal / $totalFilteredStudents) * 100) : 0;
                        $gBarWidth = $maxGrade > 0 ? max(4, round(($gVal / $maxGrade) * 100)) : 4;
                        $gColor = $gradeColors[$gName] ?? '#2563EB';
                    @endphp
                    <div>
                        <div style="display: flex; justify-content: space-between; font-size: 13px; font-weight: 600; margin-bottom: 6px;">
                            <span style="color: var(--color-navy); display: flex; align-items: center; gap: 6px;">
                                <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: {{ $gColor }};"></span>
                                {{ $gName }}
                            </span>
                            <span style="color: var(--color-text-main); font-weight: 700;">
                                {{ $gVal }} Siswa <span style="font-weight: 500; color: var(--color-text-subtle); font-size: 11px;">({{ $gPercent }}%)</span>
                            </span>
                        </div>
                        <div style="width: 100%; height: 11px; background: var(--color-surface-hover, #F1F5F9); border-radius: 999px; overflow: hidden;">
                            <div style="width: {{ $gBarWidth }}%; height: 100%; background: {{ $gColor }}; border-radius: 999px; transition: width 0.4s ease;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Diagram 6: Peta Penyerapan Alumni (Tracer Study SVG Donut Chart) -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header-clean">
            <div>
                <h2 class="card-title-clean">Peta Penyerapan Alumni (Tracer Study)</h2>
                <p class="card-subtitle-clean">
                    Keterserapan lulusan di industri, perguruan tinggi, dan wirausaha (Total {{ $totalAlumni }} Alumni)
                </p>
            </div>
            <a href="{{ route('guru.alumni.index') }}" class="btn-detail-pill" title="Lihat Menu Kelulusan & Alumni">
                Kelulusan & Alumni
            </a>
        </div>
        <div class="card-body">
            @php
                $alumniColors = [
                    'Bekerja' => '#0D9488',
                    'Kuliah' => '#1E3A8A',
                    'Wirausaha' => '#F59E0B',
                    'Mencari Kerja' => '#8B5CF6',
                    'Belum Terlacak' => '#94A3B8',
                ];
                $alumniTotal = max(1, $totalAlumni);
                $alumniOffset = 0;
            @endphp
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 24px; flex-wrap: wrap; min-height: 235px;">
                <!-- SVG Donut -->
                <div style="position: relative; width: 146px; height: 146px; margin: 0 auto; flex-shrink: 0;">
                    <svg viewBox="0 0 140 140" width="146" height="146" style="transform: rotate(-90deg);">
                        <circle cx="70" cy="70" r="54" fill="transparent" stroke="#F1F4FA" stroke-width="18" />
                        @if($totalAlumni > 0)
                            @foreach($alumniStatusCounts as $alName => $alVal)
                                @php
                                    $fraction = $alVal / $alumniTotal;
                                    $dashArray = ($fraction * $circumference) . ' ' . $circumference;
                                    $dashOffset = -$alumniOffset;
                                    $hexColor = $alumniColors[$alName] ?? '#94A3B8';
                                    if ($alVal > 0) {
                                        $alumniOffset += ($fraction * $circumference);
                                    }
                                @endphp
                                @if($alVal > 0)
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
                            {{ $totalAlumni }}
                        </span>
                        <span style="font-size: 10px; font-weight: 700; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.04em; margin-top: 3px;">
                            Alumni Terlacak
                        </span>
                    </div>
                </div>

                <!-- Structured Legend with Metrics -->
                <div style="flex: 1; min-width: 190px; display: flex; flex-direction: column; gap: 8px;">
                    @foreach($alumniStatusCounts as $alName => $alVal)
                        @php
                            $alPercent = $totalAlumni > 0 ? round(($alVal / $totalAlumni) * 100) : 0;
                            $alColor = $alumniColors[$alName] ?? '#94A3B8';
                        @endphp
                        <div style="display: flex; align-items: center; justify-content: space-between; font-size: 12.5px; padding: 4px 6px; border-radius: 6px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="display: inline-block; width: 9px; height: 9px; border-radius: 50%; background: {{ $alColor }}; flex-shrink: 0;"></span>
                                <span style="color: var(--color-navy); font-weight: 600;">{{ $alName }}</span>
                            </div>
                            <div style="font-weight: 700; color: var(--color-navy); font-size: 12.5px;">
                                {{ $alVal }} <span style="font-weight: 500; color: #64748B; font-size: 11px;">({{ $alPercent }}%)</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- SECTION 4: PAKET DOKUMEN LAPORAN PIMPINAN (3 KARTU SIAP CETAK)             -->
<!-- ========================================================================= -->
<div style="margin-bottom: 14px;">
    <h3 style="font-size: 18px; font-weight: 800; color: var(--color-navy); margin: 0 0 4px 0;">
        4. Paket Template Laporan Otomatis Pimpinan
    </h3>
    <p style="font-size: 13px; color: var(--color-text-muted); margin: 0;">
        Guru BK membuat dan mendistribusikan berkas laporan resmi siap cetak / PDF sesuai format standar pimpinan sekolah.
    </p>
</div>

<div class="grid-3" style="margin-bottom: 24px;">
    <!-- Laporan Kesiswaan -->
    <div class="card" style="border-top: 4px solid #2563eb; display: flex; flex-direction: column; justify-content: space-between;">
        <div>
            <div class="card-header" style="display: flex; align-items: center; justify-content: space-between;">
                <h3 class="card-title">Wakasek Kesiswaan</h3>
                <span class="badge badge-primary">Kesiswaan</span>
            </div>
            <div class="card-body">
                <div style="font-weight: 700; font-size: 14px; margin-bottom: 8px; color: var(--color-text-main);">
                    Laporan Perkembangan & Kondisi Siswa
                </div>
                <p style="font-size: 13px; color: var(--color-text-muted); line-height: 1.6; margin-bottom: 12px;">
                    Memuat data agregat kondisi siswa, distribusi kelas, daftar siswa yang memerlukan pembinaan/perhatian, serta rekapitulasi layanan konseling sekolah.
                </p>
                <ul style="font-size: 12px; color: var(--color-text-subtle); padding-left: 18px; margin-bottom: 16px; line-height: 1.6;">
                    <li>Data agregat privasi terlindungi</li>
                    <li>Daftar prioritas penanganan siswa</li>
                    <li>Statistik penyelesaian layanan BK</li>
                </ul>
            </div>
        </div>
        <div class="card-footer">
            <a href="{{ route('guru.laporan.generate', ['package' => 'paket_a']) }}" target="_blank" class="btn btn-primary" style="width: 100%; font-weight: 600;">
                Buka & Cetak Laporan
            </a>
        </div>
    </div>

    <!-- Laporan Kurikulum -->
    <div class="card" style="border-top: 4px solid #059669; display: flex; flex-direction: column; justify-content: space-between;">
        <div>
            <div class="card-header" style="display: flex; align-items: center; justify-content: space-between;">
                <h3 class="card-title">Wakasek Kurikulum</h3>
                <span class="badge badge-success">Kurikulum</span>
            </div>
            <div class="card-body">
                <div style="font-weight: 700; font-size: 14px; margin-bottom: 8px; color: var(--color-text-main);">
                    Laporan Jadwal & Kegiatan Kampus/Industri
                </div>
                <p style="font-size: 13px; color: var(--color-text-muted); line-height: 1.6; margin-bottom: 12px;">
                    Fokus pada sinkronisasi jadwal pembelajaran dengan agenda mitra: sosialisasi kampus, kunjungan industri, seminar vokasi, dan status benturan ruang/kelas.
                </p>
                <ul style="font-size: 12px; color: var(--color-text-subtle); padding-left: 18px; margin-bottom: 16px; line-height: 1.6;">
                    <li>Rincian tanggal, jam, dan ruang</li>
                    <li>Kelas rombel yang terdampak kegiatan</li>
                    <li>Status verifikasi benturan jadwal</li>
                </ul>
            </div>
        </div>
        <div class="card-footer">
            <a href="{{ route('guru.laporan.generate', ['package' => 'paket_b']) }}" target="_blank" class="btn btn-accent" style="width: 100%; font-weight: 600;">
                Buka & Cetak Laporan
            </a>
        </div>
    </div>

    <!-- Laporan Kepala Sekolah -->
    <div class="card" style="border-top: 4px solid #d97706; display: flex; flex-direction: column; justify-content: space-between;">
        <div>
            <div class="card-header" style="display: flex; align-items: center; justify-content: space-between;">
                <h3 class="card-title">Kepala Sekolah</h3>
                <span class="badge badge-warning">Eksekutif</span>
            </div>
            <div class="card-body">
                <div style="font-weight: 700; font-size: 14px; margin-bottom: 8px; color: var(--color-text-main);">
                    Laporan Eksekutif Bimbingan, Karier & Lulusan
                </div>
                <p style="font-size: 13px; color: var(--color-text-muted); line-height: 1.6; margin-bottom: 12px;">
                    Ringkasan tingkat pimpinan: efektivitas bimbingan, peta persentase rencana masa depan lulusan kelas XII, capaian kemitraan, dan hasil penyerapan alumni.
                </p>
                <ul style="font-size: 12px; color: var(--color-text-subtle); padding-left: 18px; margin-bottom: 16px; line-height: 1.6;">
                    <li>Peta persentase kuliah vs kerja vs usaha</li>
                    <li>Rekap kemitraan PT & Industri</li>
                    <li>Hasil tracer study alumni</li>
                </ul>
            </div>
        </div>
        <div class="card-footer">
            <a href="{{ route('guru.laporan.generate', ['package' => 'paket_c']) }}" target="_blank" class="btn btn-warning" style="width: 100%; font-weight: 600;">
                Buka & Cetak Laporan
            </a>
        </div>
    </div>
</div>
@endsection
