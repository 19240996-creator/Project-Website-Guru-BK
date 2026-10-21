@extends('layouts.app')

@section('title', 'Laporan & Grafik Analitik - ' . (auth()->user()->isKoordinatorBk() ? 'Koordinator BK' : 'Guru BK'))
@section('header_title', 'Pusat Analisis Data & Laporan Pimpinan')

@section('content')
<!-- Page Header Bar -->
<div class="page-header-bar" style="margin-bottom: 20px;">
    <div>
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
            @if(auth()->user()->isKoordinatorBk())
                <span class="badge badge-primary" style="font-size: 11px; padding: 3px 8px; font-weight: 700;">
                    KOORDINATOR BK
                </span>
                <span style="font-size: 12px; color: var(--color-text-muted);">
                    Pengawasan Komprehensif Seluruh Sekolah
                </span>
            @else
                <span class="badge badge-primary" style="font-size: 11px; padding: 3px 8px; font-weight: 700;">
                    GURU BK
                </span>
                <span style="font-size: 12px; color: var(--color-text-muted);">
                    Layanan & Pendampingan Siswa Binaan
                </span>
            @endif
        </div>
        <h1 class="page-header-title">Laporan & Grafik Analitik Bimbingan Konseling</h1>
        <p style="font-size: 13px; color: var(--color-text-muted); margin: 4px 0 0 0;">
            Visualisasi data komprehensif, pemantauan tren bimbingan siswa, serta generator dokumen laporan berkala resmi pimpinan sekolah.
        </p>
    </div>
    <div class="page-header-date">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
        <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
    </div>
</div>

<!-- Navigasi Tab Utama Hub Laporan -->
<div style="display: flex; gap: 8px; margin-bottom: 24px; border-bottom: 2px solid var(--color-border); overflow-x: auto;">
    <a href="{{ route('guru.laporan.index', ['tab' => 'grafik']) }}" 
       style="display: inline-flex; align-items: center; gap: 8px; padding: 11px 20px; font-size: 13.5px; font-weight: 700; text-decoration: none; border-bottom: 3px solid {{ $activeTab === 'grafik' ? 'var(--color-primary)' : 'transparent' }}; color: {{ $activeTab === 'grafik' ? 'var(--color-primary)' : 'var(--color-text-muted)' }}; transition: all 0.2s ease; white-space: nowrap;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 20V10"></path><path d="M12 20V4"></path><path d="M6 20v-6"></path></svg>
        <span>Grafik & Analisis Visual</span>
    </a>
    <a href="{{ route('guru.laporan.index', ['tab' => 'berkala']) }}" 
       style="display: inline-flex; align-items: center; gap: 8px; padding: 11px 20px; font-size: 13.5px; font-weight: 700; text-decoration: none; border-bottom: 3px solid {{ $activeTab === 'berkala' ? 'var(--color-primary)' : 'transparent' }}; color: {{ $activeTab === 'berkala' ? 'var(--color-primary)' : 'var(--color-text-muted)' }}; transition: all 0.2s ease; white-space: nowrap;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
        <span>Laporan Berkala (Mingguan, Bulanan, Tahunan)</span>
        <span class="badge badge-primary" style="font-size: 10px; padding: 2px 7px;">Terintegrasi</span>
    </a>
    <a href="{{ route('guru.laporan.index', ['tab' => 'paket']) }}" 
       style="display: inline-flex; align-items: center; gap: 8px; padding: 11px 20px; font-size: 13.5px; font-weight: 700; text-decoration: none; border-bottom: 3px solid {{ $activeTab === 'paket' ? 'var(--color-primary)' : 'transparent' }}; color: {{ $activeTab === 'paket' ? 'var(--color-primary)' : 'var(--color-text-muted)' }}; transition: all 0.2s ease; white-space: nowrap;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
        <span>Paket Laporan Pimpinan</span>
    </a>
</div>

{{-- ========================================================================= --}}
{{-- TAB 1: GRAFIK & ANALISIS VISUAL                                           --}}
{{-- ========================================================================= --}}
@if($activeTab === 'grafik')
    <!-- Filter Bar Card -->
    <div class="card" style="margin-bottom: 22px; border-left: 4px solid var(--color-primary);">
        <div class="card-body" style="padding: 18px 20px;">
            <form method="GET" action="{{ route('guru.laporan.index') }}" id="formGrafikFilter" style="margin: 0;">
                <input type="hidden" name="tab" value="grafik">
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; margin-bottom: 12px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 6px; background: rgba(37, 99, 235, 0.12); color: var(--color-primary);">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                        </span>
                        <strong style="font-size: 14px; color: var(--color-text-main);">Filter Parameter Grafik & Analisis</strong>
                    </div>

                    @if($selectedGrade || $selectedMajor || $selectedPeriod)
                        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                            <span class="badge badge-primary" style="font-size: 11px;">
                                Filter Aktif: {{ ($selectedGrade ? 'Kelas ' . $selectedGrade : '') . ($selectedGrade && $selectedMajor ? ' • ' : '') . ($selectedMajor ?: '') . (($selectedGrade || $selectedMajor) && $selectedPeriod ? ' • ' : '') . ($selectedPeriod === 'custom' ? ('Kustom: ' . ($selectedStartDate ? \Carbon\Carbon::parse($selectedStartDate)->format('d/m/Y') : '') . ' s.d. ' . ($selectedEndDate ? \Carbon\Carbon::parse($selectedEndDate)->format('d/m/Y') : '')) : ($selectedPeriod ? ucfirst(str_replace('_', ' ', $selectedPeriod)) : '')) }}
                            </span>
                            <a href="{{ route('guru.laporan.index', ['tab' => 'grafik']) }}" class="btn btn-secondary btn-sm" style="font-size: 11px; padding: 3px 8px;">
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
                        <select name="period" id="grafikPeriodSelect" class="form-select" style="min-height: 38px; font-size: 13px;" onchange="toggleGrafikCustomDate(this.value)">
                            <option value="">Semua Periode Waktu</option>
                            <option value="mingguan" {{ $selectedPeriod === 'mingguan' ? 'selected' : '' }}>Mingguan (7 Hari Terakhir)</option>
                            <option value="bulanan" {{ in_array($selectedPeriod, ['bulanan', '1_bulan']) ? 'selected' : '' }}>Bulanan (30 Hari Terakhir)</option>
                            <option value="tahunan" {{ in_array($selectedPeriod, ['tahunan', '1_tahun']) ? 'selected' : '' }}>Tahunan (1 Tahun Terakhir)</option>
                            <option value="custom" {{ $selectedPeriod === 'custom' ? 'selected' : '' }}>Kustom Rentang Tanggal</option>
                            @if(in_array($selectedPeriod, ['3_bulan', '6_bulan']))
                                <option value="{{ $selectedPeriod }}" selected>{{ str_replace('_', ' ', $selectedPeriod) }} Terakhir</option>
                            @endif
                        </select>
                    </div>

                    <div>
                        <button type="submit" class="btn btn-primary btn-sm" style="width: 100%; min-height: 38px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                            <span>Filter Data</span>
                        </button>
                    </div>
                </div>

                <!-- Input Tanggal Kustom Dinamis dengan Pilihan Cepat -->
                <div id="grafikCustomDateContainer" style="{{ $selectedPeriod === 'custom' ? 'display: block;' : 'display: none;' }} margin-top: 14px; padding: 12px 14px; background: var(--color-surface-subtle, #F8FAFD); border: 1px solid var(--color-border); border-radius: 8px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; margin-bottom: 10px;">
                        <span style="font-size: 12px; font-weight: 700; color: var(--color-navy);">
                            Pilih Rentang Tanggal Kustom:
                        </span>
                        <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                            <span style="font-size: 11px; color: var(--color-text-muted);">Pilihan Cepat:</span>
                            <button type="button" class="btn btn-secondary btn-sm" style="font-size: 11px; padding: 2px 8px;" onclick="setGrafikDatePreset(0)">Hari Ini</button>
                            <button type="button" class="btn btn-secondary btn-sm" style="font-size: 11px; padding: 2px 8px;" onclick="setGrafikDatePreset(2)">3 Hari Terakhir (Hari Ini & 2 Hari Lalu)</button>
                            <button type="button" class="btn btn-secondary btn-sm" style="font-size: 11px; padding: 2px 8px;" onclick="setGrafikDatePreset(6)">7 Hari Terakhir</button>
                            <button type="button" class="btn btn-secondary btn-sm" style="font-size: 11px; padding: 2px 8px;" onclick="setGrafikDatePreset(29)">30 Hari Terakhir</button>
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div>
                            <label class="form-label" style="font-size: 11.5px; font-weight: 600; margin-bottom: 4px;">Tanggal Mulai</label>
                            <input type="date" name="start_date" id="grafikStartDate" value="{{ $selectedStartDate }}" class="form-control" style="min-height: 36px; font-size: 12.5px;">
                        </div>
                        <div>
                            <label class="form-label" style="font-size: 11.5px; font-weight: 600; margin-bottom: 4px;">Tanggal Selesai</label>
                            <input type="date" name="end_date" id="grafikEndDate" value="{{ $selectedEndDate }}" class="form-control" style="min-height: 36px; font-size: 12.5px;">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Key Aggregate Metrics (4 Stat Cards Row) -->
    <div class="grid-4" style="margin-bottom: 24px;">
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

    <!-- Section 1: Diagram Kasus & Rencana Masa Depan -->
    <div style="margin-bottom: 12px;">
        <h3 style="font-size: 16px; font-weight: 700; color: var(--color-navy); margin: 0 0 4px 0;">
            1. Analisis Kategori Konseling & Peta Rencana Masa Depan
        </h3>
        <small style="color: var(--color-text-subtle);">Diagram analitik terharmonisasi dari Pusat Kerja Beranda dengan filter aktif.</small>
    </div>

    <div class="grid-2" style="margin-bottom: 24px;">
        <!-- Diagram 1: Kategori Kasus Bimbingan (Column Chart) -->
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

        <!-- Diagram 2: Peta Rencana Masa Depan Siswa (SVG Donut Chart) -->
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
                    $circumference = 339.292;
                    $cumulativeOffset = 0;
                    $totalPlanSum = max(1, array_sum($futurePlanChartData));
                @endphp
                <div id="futurePlanPieChart" style="display: flex; align-items: center; justify-content: space-between; gap: 24px; flex-wrap: wrap; min-height: 235px;">
                    <div style="position: relative; width: 146px; height: 146px; margin: 0 auto; flex-shrink: 0;">
                        <svg viewBox="0 0 140 140" width="146" height="146" style="transform: rotate(-90deg);">
                            <circle cx="70" cy="70" r="54" fill="transparent" stroke="#F1F4FA" stroke-width="16" />
                            @if(array_sum($futurePlanChartData) > 0)
                                @foreach($futurePlanChartLabels as $idx => $label)
                                    @php
                                        $val = $futurePlanChartData[$idx] ?? 0;
                                        $fraction = $val / $totalPlanSum;
                                        $dashArray = ($fraction * $circumference) . ' ' . $circumference;
                                        $dashOffset = -$cumulativeOffset;
                                        $hexColor = $planColors[$label] ?? '#64748B';
                                        if ($val > 0) {
                                            $cumulativeOffset += ($fraction * $circumference);
                                        }
                                    @endphp
                                    @if($val > 0)
                                        <circle cx="70" cy="70" r="54" fill="transparent"
                                                stroke="{{ $hexColor }}"
                                                stroke-width="16"
                                                stroke-dasharray="{{ $dashArray }}"
                                                stroke-dashoffset="{{ $dashOffset }}" />
                                    @endif
                                @endforeach
                            @else
                                <circle cx="70" cy="70" r="54" fill="transparent" stroke="#E5E9F2" stroke-width="16" />
                            @endif
                        </svg>
                        <div style="position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; pointer-events: none; text-align: center; padding: 0 10px;">
                            <span style="font-size: 25px; font-weight: 800; color: var(--color-navy); line-height: 1; letter-spacing: -0.02em;">
                                {{ $totalPlanStudents }}
                            </span>
                            <span style="font-size: 9px; font-weight: 700; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.02em; margin-top: 4px; white-space: nowrap;">
                                Terdata
                            </span>
                        </div>
                    </div>

                    <div style="flex: 1; min-width: 190px; display: flex; flex-direction: column; gap: 8px;">
                        @foreach($futurePlanChartLabels as $idx => $label)
                            @php
                                $val = $futurePlanChartData[$idx] ?? 0;
                                $percent = $totalPlanSum > 0 ? round(($val / $totalPlanSum) * 100) : 0;
                                $color = $planColors[$label] ?? '#64748B';
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

    <!-- Section 2: Diagram Status Layanan & Tingkat Perhatian Siswa -->
    <div style="margin-bottom: 12px;">
        <h3 style="font-size: 16px; font-weight: 700; color: var(--color-navy); margin: 0 0 4px 0;">
            2. Efektivitas Penanganan Konseling & Tingkat Atensi Siswa
        </h3>
        <small style="color: var(--color-text-subtle);">Evaluasi penyelesaian kasus dan pemantauan siswa yang memerlukan intervensi pembinaan.</small>
    </div>

    <div class="grid-2" style="margin-bottom: 24px;">
        <!-- Diagram 3: Status Penyelesaian Konseling (Donut Chart) -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header-clean">
                <div>
                    <h2 class="card-title-clean">Status Penyelesaian Layanan Konseling</h2>
                    <p class="card-subtitle-clean">
                        Persentase kasus selesai vs dalam proses (Total {{ $totalFilteredCounselings }} Kasus)
                    </p>
                </div>
                <a href="{{ route('guru.konseling.index') }}" class="btn-detail-pill" title="Buka Data Konseling">
                    Konseling
                </a>
            </div>
            <div class="card-body">
                @php
                    $statusColors = [
                        'Selesai' => '#10B981',
                        'Dilaksanakan' => '#3B82F6',
                        'Tindak Lanjut' => '#F59E0B',
                        'Menunggu / Baru' => '#8B5CF6',
                        'Dibatalkan' => '#EF4444',
                    ];
                    $counselingTotal = max(1, $totalFilteredCounselings);
                    $counselingOffset = 0;
                @endphp
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 24px; flex-wrap: wrap; min-height: 235px;">
                    <div style="position: relative; width: 146px; height: 146px; margin: 0 auto; flex-shrink: 0;">
                        <svg viewBox="0 0 140 140" width="146" height="146" style="transform: rotate(-90deg);">
                            <circle cx="70" cy="70" r="54" fill="transparent" stroke="#F1F4FA" stroke-width="16" />
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
                                                stroke-width="16"
                                                stroke-dasharray="{{ $dashArray }}"
                                                stroke-dashoffset="{{ $dashOffset }}" />
                                    @endif
                                @endforeach
                            @else
                                <circle cx="70" cy="70" r="54" fill="transparent" stroke="#E5E9F2" stroke-width="16" />
                            @endif
                        </svg>
                        <div style="position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; pointer-events: none; text-align: center; padding: 0 12px;">
                            <span style="font-size: 25px; font-weight: 800; color: #10B981; line-height: 1; letter-spacing: -0.02em;">
                                {{ $totalFilteredCounselings > 0 ? round(($counselingStatusCounts['Selesai'] / $totalFilteredCounselings) * 100) : 0 }}%
                            </span>
                            <span style="font-size: 10px; font-weight: 700; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.03em; margin-top: 4px; white-space: nowrap;">
                                Kasus Tuntas
                            </span>
                        </div>
                    </div>

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
                                <div style="width: {{ $barWidth }}%; height: 100%; background: {{ $color }}; border-radius: 999px; transition: width 0.3s ease;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Section 3: Diagram Siswa per Jenjang & Peta Alumni -->
    <div style="margin-bottom: 12px;">
        <h3 style="font-size: 16px; font-weight: 700; color: var(--color-navy); margin: 0 0 4px 0;">
            3. Demografi Siswa per Jenjang & Penyerapan Alumni
        </h3>
        <small style="color: var(--color-text-subtle);">Distribusi jenjang kelas aktif dan data hasil pelacakan lulusan sekolah.</small>
    </div>

    <div class="grid-2" style="margin-bottom: 24px;">
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header-clean">
                <div>
                    <h2 class="card-title-clean">Distribusi Siswa per Jenjang Kelas</h2>
                    <p class="card-subtitle-clean">
                        Total {{ $totalFilteredStudents }} siswa terdata aktif
                    </p>
                </div>
            </div>
            <div class="card-body">
                @php
                    $maxGrade = max(array_merge([1], array_values($gradeCounts)));
                    $gradeColors = ['Kelas X' => '#3B82F6', 'Kelas XI' => '#0D9488', 'Kelas XII' => '#F59E0B'];
                @endphp
                <div style="display: flex; flex-direction: column; gap: 16px; min-height: 235px; justify-content: center;">
                    @foreach($gradeCounts as $gName => $gVal)
                        @php
                            $gPercent = $totalFilteredStudents > 0 ? round(($gVal / $totalFilteredStudents) * 100) : 0;
                            $barWidth = $maxGrade > 0 ? max(4, round(($gVal / $maxGrade) * 100)) : 4;
                            $color = $gradeColors[$gName] ?? '#2447A8';
                        @endphp
                        <div>
                            <div style="display: flex; justify-content: space-between; font-size: 13px; font-weight: 600; margin-bottom: 6px;">
                                <span style="color: var(--color-navy); font-weight: 700;">{{ $gName }}</span>
                                <span style="color: var(--color-text-main); font-weight: 700;">
                                    {{ $gVal }} Siswa <span style="font-weight: 500; color: #64748B; font-size: 11.5px;">({{ $gPercent }}%)</span>
                                </span>
                            </div>
                            <div style="width: 100%; height: 10px; background: #F1F5F9; border-radius: 999px; overflow: hidden;">
                                <div style="width: {{ $barWidth }}%; height: 100%; background: {{ $color }}; border-radius: 999px; transition: width 0.3s ease;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

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
                    <div style="position: relative; width: 146px; height: 146px; margin: 0 auto; flex-shrink: 0;">
                        <svg viewBox="0 0 140 140" width="146" height="146" style="transform: rotate(-90deg);">
                            <circle cx="70" cy="70" r="54" fill="transparent" stroke="#F1F4FA" stroke-width="16" />
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
                                                stroke-width="16"
                                                stroke-dasharray="{{ $dashArray }}"
                                                stroke-dashoffset="{{ $dashOffset }}" />
                                    @endif
                                @endforeach
                            @else
                                <circle cx="70" cy="70" r="54" fill="transparent" stroke="#E5E9F2" stroke-width="16" />
                            @endif
                        </svg>
                        <div style="position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; pointer-events: none; text-align: center; padding: 0 10px;">
                            <span style="font-size: 25px; font-weight: 800; color: var(--color-navy); line-height: 1; letter-spacing: -0.02em;">
                                {{ $totalAlumni }}
                            </span>
                            <span style="font-size: 9px; font-weight: 700; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.02em; margin-top: 4px; white-space: nowrap;">
                                Alumni Terlacak
                            </span>
                        </div>
                    </div>

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


{{-- ========================================================================= --}}
{{-- TAB 2: LAPORAN BERKALA (MINGGUAN, BULANAN, TAHUNAN)                       --}}
{{-- ========================================================================= --}}
@elseif($activeTab === 'berkala')
    <!-- Generator Form Card -->
    <div class="card" style="margin-bottom: 24px; border-top: 4px solid var(--color-primary);">
        <div class="card-header card-header-navy" style="display: flex; align-items: center; justify-content: space-between; padding: 14px 20px;">
            <div>
                <h2 class="card-title" style="margin: 0; font-size: 15px; color: #FFFFFF;">
                    Parameter Generator Laporan Berkala Terintegrasi
                </h2>
                <p style="font-size: 12px; color: #CCD8FD; margin: 2px 0 0 0;">
                    Laporan dibuat otomatis dari aktivitas, penanganan konseling, dan profil siswa di database.
                </p>
            </div>
            <div>
                @if($isKoordinator)
                    <span class="badge" style="background: rgba(255,255,255,0.18); color: #FFFFFF; font-size: 11px;">
                        Mode Koordinator BK
                    </span>
                @else
                    <span class="badge" style="background: rgba(255,255,255,0.18); color: #FFFFFF; font-size: 11px;">
                        Mode Guru BK
                    </span>
                @endif
            </div>
        </div>
        <div class="card-body" style="padding: 20px;">
            <form method="GET" action="{{ route('guru.laporan.index') }}" id="formLaporanBerkala">
                <input type="hidden" name="tab" value="berkala">
                <input type="hidden" name="period_type" id="periodTypeInput" value="{{ $periodType }}">

                <!-- Selector Jenis Periode (Segmented Pill Switcher) -->
                <div style="margin-bottom: 18px;">
                    <label class="form-label" style="font-size: 12.5px; font-weight: 700; color: var(--color-navy); margin-bottom: 8px; display: block;">
                        1. Pilih Jenis Periode Laporan:
                    </label>
                    <div id="periodTypeBtnGroup" style="display: inline-flex; background: var(--color-surface-hover, #F1F4FA); padding: 4px; border-radius: var(--radius-sm); gap: 6px; border: 1px solid var(--color-border); flex-wrap: wrap;">
                        <button type="button" 
                                onclick="setPeriodType('mingguan')" 
                                class="btn btn-sm {{ $periodType === 'mingguan' ? 'btn-primary' : 'btn-secondary' }}" 
                                style="font-weight: 700; padding: 6px 16px; border-radius: 6px;">
                            Laporan Mingguan
                        </button>
                        <button type="button" 
                                onclick="setPeriodType('bulanan')" 
                                class="btn btn-sm {{ $periodType === 'bulanan' ? 'btn-primary' : 'btn-secondary' }}" 
                                style="font-weight: 700; padding: 6px 16px; border-radius: 6px;">
                            Laporan Bulanan
                        </button>
                        <button type="button" 
                                onclick="setPeriodType('tahunan')" 
                                class="btn btn-sm {{ $periodType === 'tahunan' ? 'btn-primary' : 'btn-secondary' }}" 
                                style="font-weight: 700; padding: 6px 16px; border-radius: 6px;">
                            Laporan Tahunan
                        </button>
                        <button type="button" 
                                id="btnTypeCustom"
                                onclick="setPeriodType('custom')" 
                                class="btn btn-sm {{ $periodType === 'custom' ? 'btn-primary' : 'btn-secondary' }}" 
                                style="font-weight: 700; padding: 6px 16px; border-radius: 6px;">
                            Kustom Rentang Tanggal
                        </button>
                    </div>
                </div>

                <!-- Input Kontrol Waktu Dinamis -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 14px; margin-bottom: 16px; background: var(--color-surface-subtle, #F8FAFD); padding: 14px 16px; border-radius: 8px; border: 1px solid var(--color-border);">
                    <!-- Kustom: Rentang Bebas Tanggal Mulai & Tanggal Selesai -->
                    <div id="fieldCustom" style="{{ $periodType === 'custom' ? 'grid-column: 1 / -1;' : 'display: none;' }}">
                        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; margin-bottom: 8px;">
                            <label class="form-label" style="font-size: 12px; font-weight: 700; color: var(--color-navy); margin: 0;">
                                Rentang Bebas Tanggal:
                            </label>
                            <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                <span style="font-size: 11px; color: var(--color-text-muted);">Pilihan Cepat:</span>
                                <button type="button" class="btn btn-secondary btn-sm" style="font-size: 11px; padding: 2px 8px;" onclick="setBerkalaCustomPreset(0)">Hari Ini</button>
                                <button type="button" class="btn btn-secondary btn-sm" style="font-size: 11px; padding: 2px 8px;" onclick="setBerkalaCustomPreset(2)">3 Hari Terakhir (Hari Ini & 2 Hari Lalu)</button>
                                <button type="button" class="btn btn-secondary btn-sm" style="font-size: 11px; padding: 2px 8px;" onclick="setBerkalaCustomPreset(6)">7 Hari Terakhir</button>
                                <button type="button" class="btn btn-secondary btn-sm" style="font-size: 11px; padding: 2px 8px;" onclick="setBerkalaCustomPreset(29)">30 Hari Terakhir</button>
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px;">
                            <div>
                                <label class="form-label" style="font-size: 11.5px; font-weight: 600; margin-bottom: 4px;">Tanggal Mulai</label>
                                <input type="date" name="custom_start_date" id="berkalaStartDate" value="{{ $customStartDate }}" class="form-control" style="min-height: 38px; font-size: 13px;">
                            </div>
                            <div>
                                <label class="form-label" style="font-size: 11.5px; font-weight: 600; margin-bottom: 4px;">Tanggal Selesai</label>
                                <input type="date" name="custom_end_date" id="berkalaEndDate" value="{{ $customEndDate }}" class="form-control" style="min-height: 38px; font-size: 13px;">
                            </div>
                        </div>
                    </div>

                    <!-- Mingguan: Pilih Tanggal dalam Minggu -->
                    <div id="fieldMingguan" style="{{ $periodType === 'mingguan' ? '' : 'display: none;' }}">
                        <label class="form-label" style="font-size: 12px; font-weight: 600; margin-bottom: 5px;">Pilih Tanggal dalam Minggu</label>
                        <input type="date" name="week_date" value="{{ $weekDate }}" class="form-control" style="min-height: 38px; font-size: 13px;">
                        <small style="color: var(--color-text-muted); font-size: 11px; display: block; margin-top: 4px;">
                            Sistem menghitung rentang Senin s.d. Minggu secara otomatis.
                        </small>
                    </div>

                    <!-- Bulanan: Pilih Bulan -->
                    <div id="fieldBulan" style="{{ $periodType === 'bulanan' ? '' : 'display: none;' }}">
                        <label class="form-label" style="font-size: 12px; font-weight: 600; margin-bottom: 5px;">Bulan Laporan</label>
                        <select name="month" class="form-select" style="min-height: 38px; font-size: 13px;">
                            @php
                                $indonesianMonths = [
                                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                ];
                            @endphp
                            @foreach($indonesianMonths as $num => $mName)
                                <option value="{{ $num }}" {{ $selectedMonth === $num ? 'selected' : '' }}>
                                    {{ $mName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Bulanan & Tahunan: Pilih Tahun -->
                    <div id="fieldTahun" style="{{ in_array($periodType, ['mingguan', 'custom']) ? 'display: none;' : '' }}">
                        <label class="form-label" style="font-size: 12px; font-weight: 600; margin-bottom: 5px;">Tahun</label>
                        <select name="year" class="form-select" style="min-height: 38px; font-size: 13px;">
                            @for($y = 2024; $y <= 2027; $y++)
                                <option value="{{ $y }}" {{ $selectedYear === $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <!-- Khusus Koordinator: Filter Guru BK -->
                    @if($isKoordinator)
                        <div>
                            <label class="form-label" style="font-size: 12px; font-weight: 600; margin-bottom: 5px;">
                                Sasaran Konselor / Guru BK
                            </label>
                            <select name="counselor_id" class="form-select" style="min-height: 38px; font-size: 13px;">
                                <option value="semua" {{ $selectedCounselorId === 'semua' ? 'selected' : '' }}>
                                    Semua Guru BK (Laporan Komprehensif Sekolah)
                                </option>
                                @foreach($allCounselors as $csl)
                                    <option value="{{ $csl->id }}" {{ (string)$selectedCounselorId === (string)$csl->id ? 'selected' : '' }}>
                                        {{ $csl->name }} ({{ $csl->role_label }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <!-- Filter Tingkat -->
                    <div>
                        <label class="form-label" style="font-size: 12px; font-weight: 600; margin-bottom: 5px;">Tingkat Kelas</label>
                        <select name="berkala_grade" class="form-select" style="min-height: 38px; font-size: 13px;">
                            <option value="">Semua Tingkat</option>
                            @foreach($availableGrades as $g)
                                <option value="{{ $g }}" {{ $selectedGrade === $g ? 'selected' : '' }}>Kelas {{ $g }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Jurusan -->
                    <div>
                        <label class="form-label" style="font-size: 12px; font-weight: 600; margin-bottom: 5px;">Jurusan</label>
                        <select name="berkala_major" class="form-select" style="min-height: 38px; font-size: 13px;">
                            <option value="">Semua Jurusan</option>
                            @foreach($availableMajors as $m)
                                <option value="{{ $m }}" {{ $selectedMajor === $m ? 'selected' : '' }}>{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Tombol Aksi Form -->
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                    <div style="font-size: 12px; color: var(--color-text-muted);">
                        <span style="font-weight: 700; color: var(--color-navy);">Rentang Aktif:</span>
                        {{ $periodLabel }}
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn btn-primary" style="font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                            <span>Tampilkan Rangkuman Laporan</span>
                        </button>
                        <a href="{{ route('guru.laporan.generate_periodic', request()->all()) }}" target="_blank" class="btn btn-secondary" style="font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                            <span>Buka Dokumen Cetak / PDF</span>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Live Report Presentation Banner -->
    <div style="background: linear-gradient(135deg, #101A35 0%, #1E3A8A 100%); border-radius: var(--radius-md); padding: 22px 26px; color: #FFFFFF; margin-bottom: 22px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; box-shadow: var(--shadow-md);">
        <div>
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                <span class="badge" style="background: rgba(255,255,255,0.2); color: #FFFFFF; font-size: 11px; padding: 3px 8px; font-weight: 700;">
                    {{ strtoupper($periodType) }}
                </span>
                <span style="font-size: 12px; color: #E2E8F0;">
                    {{ $periodLabel }}
                </span>
            </div>
            <h2 style="font-size: 19px; font-weight: 800; color: #FFFFFF; margin: 0 0 4px 0;">
                {{ $reportTitle }}
            </h2>
            <p style="font-size: 12.5px; color: #CBD5E1; margin: 0;">
                Penyusun: <strong>{{ $targetCounselor ? $targetCounselor->name : ($isKoordinator ? $user->name . ' (Koordinator BK)' : $user->name) }}</strong> 
                • Cakupan: <strong>{{ $targetCounselor ? 'Siswa Binaan ' . $targetCounselor->name : 'Seluruh Siswa Bimbingan Sekolah' }}</strong>
            </p>
        </div>
        <div>
            <a href="{{ route('guru.laporan.generate_periodic', request()->all()) }}" target="_blank" class="btn" style="background: #FFFFFF; color: var(--color-navy); font-weight: 700; display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: var(--radius-sm); box-shadow: var(--shadow-sm);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                <span>Cetak / Ekspor PDF Resmi</span>
            </a>
        </div>
    </div>

    <!-- 4 KPI Stat Cards Row -->
    <div class="grid-4" style="margin-bottom: 24px;">
        <div class="stat-card-clean">
            <div>
                <div class="stat-card-top">
                    <div class="stat-icon-pill blue">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                    </div>
                </div>
                <div class="stat-card-label">TOTAL KASUS PERIODE INI</div>
                <div class="stat-value-row">
                    <span class="stat-card-number">{{ number_format($totalCases) }}</span>
                    <span class="stat-trend-tag">Kasus</span>
                </div>
            </div>
            <p class="stat-card-desc">Tercatat dalam rentang waktu</p>
        </div>

        <div class="stat-card-clean">
            <div>
                <div class="stat-card-top">
                    <div class="stat-icon-pill teal">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                </div>
                <div class="stat-card-label">KASUS SELESAI (TUNTAS)</div>
                <div class="stat-value-row">
                    <span class="stat-card-number" style="color: #10B981;">{{ number_format($completedCases) }}</span>
                    <span class="stat-trend-tag" style="background: rgba(16, 185, 129, 0.12); color: #10B981;">{{ $completionRate }}% Tuntas</span>
                </div>
            </div>
            <p class="stat-card-desc">Layanan terselesaikan tuntas</p>
        </div>

        <div class="stat-card-clean">
            <div>
                <div class="stat-card-top">
                    <div class="stat-icon-pill amber">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    </div>
                </div>
                <div class="stat-card-label">DALAM PROSES / TINDAK LANJUT</div>
                <div class="stat-value-row">
                    <span class="stat-card-number" style="color: #D97706;">{{ number_format($inProgressCases + $followUpCases + $newCases) }}</span>
                    <span class="stat-trend-tag" style="background: rgba(217, 119, 6, 0.12); color: #D97706;">Berjalan</span>
                </div>
            </div>
            <p class="stat-card-desc">Sesi aktif dan pendampingan</p>
        </div>

        <div class="stat-card-clean">
            <div>
                <div class="stat-card-top">
                    <div class="stat-icon-pill purple">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    </div>
                </div>
                <div class="stat-card-label">SISWA ATENSI KHUSUS</div>
                <div class="stat-value-row">
                    <span class="stat-card-number" style="color: #6366F1;">{{ number_format($attentionStudents->count()) }}</span>
                    <span class="stat-trend-tag" style="background: rgba(99, 102, 241, 0.12); color: #6366F1;">Prioritas</span>
                </div>
            </div>
            <p class="stat-card-desc">Siswa perlu perhatian intensif</p>
        </div>
    </div>

    <!-- Breakdown Kategori & Status (Grid 2) -->
    <div class="grid-2" style="margin-bottom: 24px;">
        <!-- Kategori Masalah Bimbingan -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header-clean">
                <div>
                    <h2 class="card-title-clean">Distribusi Kategori Masalah Periode Ini</h2>
                    <p class="card-subtitle-clean">Rekapitulasi bidang bimbingan siswa yang ditangani</p>
                </div>
            </div>
            <div class="card-body">
                <div style="display: flex; flex-direction: column; gap: 14px;">
                    @php
                        $catBadgeColors = ['#1E3A8A', '#3B82F6', '#0D9488', '#F59E0B', '#10B981'];
                    @endphp
                    @foreach($categoryBreakdown as $cIdx => $cat)
                        @php
                            $cColor = $catBadgeColors[$cIdx % count($catBadgeColors)];
                        @endphp
                        <div>
                            <div style="display: flex; justify-content: space-between; font-size: 13px; font-weight: 600; margin-bottom: 5px;">
                                <span style="color: var(--color-navy); display: flex; align-items: center; gap: 8px;">
                                    <span style="display: inline-block; width: 9px; height: 9px; border-radius: 50%; background: {{ $cColor }};"></span>
                                    {{ $cat['name'] }}
                                </span>
                                <span style="font-weight: 700; color: var(--color-navy);">
                                    {{ $cat['count'] }} Kasus <span style="font-weight: 500; color: #64748B; font-size: 11px;">({{ $cat['percentage'] }}%)</span>
                                </span>
                            </div>
                            <div style="width: 100%; height: 8px; background: #F1F5F9; border-radius: 999px; overflow: hidden;">
                                <div style="width: {{ $cat['percentage'] }}%; height: 100%; background: {{ $cColor }}; border-radius: 999px; transition: width 0.3s ease;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Status Penyelesaian & Urgensi -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header-clean">
                <div>
                    <h2 class="card-title-clean">Status Penanganan & Tingkat Urgensi</h2>
                    <p class="card-subtitle-clean">Status operasional tindak lanjut dan klasifikasi urgensi</p>
                </div>
            </div>
            <div class="card-body">
                <div style="margin-bottom: 16px;">
                    <div style="font-size: 12px; font-weight: 700; color: var(--color-navy); text-transform: uppercase; margin-bottom: 8px;">
                        Tingkat Urgensi Kasus
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; text-align: center;">
                        <div style="background: #F8FAFC; border: 1px solid var(--color-border); padding: 8px; border-radius: 6px;">
                            <div style="font-size: 15px; font-weight: 800; color: #166534;">{{ $urgencyBreakdown['rendah'] }}</div>
                            <div style="font-size: 10px; font-weight: 700; color: #64748B; text-transform: uppercase;">Rendah</div>
                        </div>
                        <div style="background: #F8FAFC; border: 1px solid var(--color-border); padding: 8px; border-radius: 6px;">
                            <div style="font-size: 15px; font-weight: 800; color: #1E40AF;">{{ $urgencyBreakdown['sedang'] }}</div>
                            <div style="font-size: 10px; font-weight: 700; color: #64748B; text-transform: uppercase;">Sedang</div>
                        </div>
                        <div style="background: #F8FAFC; border: 1px solid var(--color-border); padding: 8px; border-radius: 6px;">
                            <div style="font-size: 15px; font-weight: 800; color: #D97706;">{{ $urgencyBreakdown['tinggi'] }}</div>
                            <div style="font-size: 10px; font-weight: 700; color: #64748B; text-transform: uppercase;">Tinggi</div>
                        </div>
                        <div style="background: #F8FAFC; border: 1px solid var(--color-border); padding: 8px; border-radius: 6px;">
                            <div style="font-size: 15px; font-weight: 800; color: #DC2626;">{{ $urgencyBreakdown['darurat'] }}</div>
                            <div style="font-size: 10px; font-weight: 700; color: #64748B; text-transform: uppercase;">Darurat</div>
                        </div>
                    </div>
                </div>

                <div>
                    <div style="font-size: 12px; font-weight: 700; color: var(--color-navy); text-transform: uppercase; margin-bottom: 8px;">
                        Status Penanganan Kasus
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 8px; font-size: 12.5px;">
                        <div style="display: flex; justify-content: space-between; padding: 4px 0; border-bottom: 1px solid #F1F5F9;">
                            <span style="color: #166534; font-weight: 600;">✓ Selesai (Tuntas)</span>
                            <strong style="color: #101A35;">{{ $completedCases }} Kasus</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 4px 0; border-bottom: 1px solid #F1F5F9;">
                            <span style="color: #1E40AF; font-weight: 600;">⚡ Dilaksanakan / Dijadwalkan</span>
                            <strong style="color: #101A35;">{{ $inProgressCases }} Kasus</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 4px 0; border-bottom: 1px solid #F1F5F9;">
                            <span style="color: #D97706; font-weight: 600;">↻ Perlu Tindak Lanjut</span>
                            <strong style="color: #101A35;">{{ $followUpCases }} Kasus</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 4px 0;">
                            <span style="color: #64748B; font-weight: 600;">⏳ Menunggu / Baru Diajukan</span>
                            <strong style="color: #101A35;">{{ $newCases }} Kasus</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Khusus Koordinator: Tabel Rekapitulasi Beban Penanganan Guru BK -->
    @if($isKoordinator && count($counselorWorkload) > 0)
        <div class="card" style="margin-bottom: 24px;">
            <div class="card-header card-header-navy" style="display: flex; align-items: center; justify-content: space-between; padding: 14px 20px;">
                <h3 class="card-title" style="margin: 0; font-size: 15px; color: #FFFFFF;">
                    Rekapitulasi Kinerja &amp; Beban Kasus Seluruh Guru BK
                </h3>
                <span class="badge" style="background: rgba(255,255,255,0.2); color: #FFFFFF; font-size: 11px;">
                    Lintas Konselor Sekolah
                </span>
            </div>
            <div class="card-body" style="padding: 0;">
                <div style="overflow-x: auto;">
                    <table class="table" style="margin-bottom: 0; width: 100%;">
                        <thead>
                            <tr style="background: var(--color-surface-subtle, #F8FAFD);">
                                <th style="width: 45px; text-align: center;">No</th>
                                <th>Nama Guru BK</th>
                                <th style="text-align: center;">NIP / Identitas</th>
                                <th style="text-align: center;">Siswa Binaan</th>
                                <th style="text-align: center;">Kasus Periode Ini</th>
                                <th style="text-align: center;">Kasus Selesai</th>
                                <th style="text-align: center;">Ketuntasan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($counselorWorkload as $cwIdx => $cw)
                                <tr>
                                    <td style="text-align: center; font-weight: 600;">{{ $cwIdx + 1 }}</td>
                                    <td>
                                        <strong style="color: var(--color-navy); font-size: 13.5px;">{{ $cw['counselor']->name }}</strong>
                                        <div style="font-size: 11px; color: var(--color-text-muted);">
                                            {{ $cw['counselor']->role_label }}
                                        </div>
                                    </td>
                                    <td style="text-align: center; font-family: monospace; font-size: 12px;">
                                        {{ $cw['counselor']->nip ?: '-' }}
                                    </td>
                                    <td style="text-align: center; font-weight: 700; color: var(--color-navy);">
                                        {{ $cw['assigned_students'] }} Siswa
                                    </td>
                                    <td style="text-align: center; font-weight: 700; color: var(--color-primary);">
                                        {{ $cw['total_cases'] }} Kasus
                                    </td>
                                    <td style="text-align: center; font-weight: 700; color: #10B981;">
                                        {{ $cw['completed_cases'] }} Kasus
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="badge {{ $cw['completion_rate'] >= 70 ? 'badge-success' : 'badge-warning' }}" style="font-size: 11.5px; font-weight: 700;">
                                            {{ $cw['completion_rate'] }}%
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Tabel Rinci Aktivitas & Penanganan Siswa Terintegrasi Database -->
    <div class="card" style="margin-bottom: 24px;">
        <div class="card-header card-header-navy" style="display: flex; align-items: center; justify-content: space-between; padding: 14px 20px;">
            <div>
                <h3 class="card-title" style="margin: 0; font-size: 15px; color: #FFFFFF;">
                    Daftar Rinci Penanganan Konseling Siswa (Data Riil Database)
                </h3>
                <p style="font-size: 12px; color: #CCD8FD; margin: 2px 0 0 0;">
                    Total {{ $totalCases }} sesi bimbingan konseling tercatat pada rentang waktu {{ $periodLabel }}.
                </p>
            </div>
            <div>
                <a href="{{ route('guru.laporan.generate_periodic', request()->all()) }}" target="_blank" class="btn btn-secondary btn-sm" style="font-size: 11.5px; padding: 4px 10px;">
                    Cetak Dokumen
                </a>
            </div>
        </div>
        <div class="card-body" style="padding: 0;">
            <div style="overflow-x: auto;">
                <table class="table" style="margin-bottom: 0; width: 100%;">
                    <thead>
                        <tr style="background: var(--color-surface-subtle, #F8FAFD);">
                            <th style="width: 40px; text-align: center;">No</th>
                            <th style="width: 95px; text-align: center;">Tanggal</th>
                            <th style="width: 105px; text-align: center;">Kode</th>
                            <th>Nama Siswa & Rombel</th>
                            <th>Topik Masalah</th>
                            <th>Kategori</th>
                            <th style="width: 85px; text-align: center;">Urgensi</th>
                            <th style="width: 100px; text-align: center;">Status</th>
                            <th>Rencana Tindak Lanjut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($counselings as $idx => $csl)
                            <tr>
                                <td style="text-align: center; font-weight: 600;">{{ $idx + 1 }}</td>
                                <td style="text-align: center; font-size: 12px; white-space: nowrap;">
                                    {{ $csl->created_at->format('d/m/Y') }}
                                </td>
                                <td style="text-align: center; font-family: monospace; font-size: 11.5px; font-weight: 600; color: var(--color-primary);">
                                    {{ $csl->code ?: 'KSL-' . $csl->id }}
                                </td>
                                <td>
                                    <strong style="color: var(--color-navy); font-size: 13px;">{{ $csl->student->name ?? 'Siswa' }}</strong>
                                    <div style="font-size: 11px; color: var(--color-text-muted);">
                                        NISN: {{ $csl->student->nisn ?? '-' }} • {{ $csl->student->studentClass->name ?? '-' }}
                                    </div>
                                </td>
                                <td style="font-size: 12.5px; color: var(--color-text-main);">
                                    {{ $csl->topic ?: 'Konsultasi Layanan Bimbingan' }}
                                </td>
                                <td>
                                    <span class="badge badge-info" style="font-size: 11px;">
                                        {{ $csl->category->name ?? '-' }}
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    @if($csl->urgency === 'darurat')
                                        <span class="badge badge-danger" style="font-size: 10px;">Darurat</span>
                                    @elseif($csl->urgency === 'tinggi')
                                        <span class="badge badge-warning" style="font-size: 10px;">Tinggi</span>
                                    @elseif($csl->urgency === 'sedang')
                                        <span class="badge badge-primary" style="font-size: 10px;">Sedang</span>
                                    @else
                                        <span class="badge badge-secondary" style="font-size: 10px;">Rendah</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    @if($csl->status === 'selesai')
                                        <span class="badge badge-success" style="font-size: 10.5px;">Selesai</span>
                                    @elseif(in_array($csl->status, ['dilaksanakan', 'dijadwalkan']))
                                        <span class="badge badge-primary" style="font-size: 10.5px;">Proses</span>
                                    @elseif($csl->status === 'tindak_lanjut')
                                        <span class="badge badge-warning" style="font-size: 10.5px;">Tindak Lanjut</span>
                                    @else
                                        <span class="badge badge-secondary" style="font-size: 10.5px;">{{ ucfirst($csl->status) }}</span>
                                    @endif
                                </td>
                                <td style="font-size: 11.5px; color: var(--color-text-muted); max-width: 200px;">
                                    {{ \Illuminate\Support\Str::limit($csl->student_action_plan ?: ($csl->counselor_notes ?: 'Penanganan berlangsung sesuai alur konseling.'), 75) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" style="text-align: center; color: var(--color-text-muted); padding: 24px;">
                                    Tidak ada data aktivitas konseling yang tercatat pada rentang waktu {{ $periodLabel }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pemantauan Siswa Perlu Perhatian Khusus -->
    @if($attentionStudents->count() > 0)
        <div class="card" style="margin-bottom: 24px; border-left: 4px solid #D97706;">
            <div class="card-header" style="display: flex; align-items: center; justify-content: space-between; padding: 14px 20px;">
                <div>
                    <h3 class="card-title" style="margin: 0; font-size: 15px; color: var(--color-navy);">
                        Siswa Memerlukan Perhatian Khusus & Tindak Lanjut Cepat
                    </h3>
                    <p style="font-size: 12px; color: var(--color-text-muted); margin: 2px 0 0 0;">
                        Deteksi siswa binaan dengan tingkat atensi prioritas dalam lingkup pelaporan.
                    </p>
                </div>
                <span class="badge badge-warning" style="font-size: 11px;">
                    {{ $attentionStudents->count() }} Siswa Prioritas
                </span>
            </div>
            <div class="card-body" style="padding: 0;">
                <div style="overflow-x: auto;">
                    <table class="table" style="margin-bottom: 0; width: 100%;">
                        <thead>
                            <tr style="background: var(--color-surface-subtle, #F8FAFD);">
                                <th style="width: 40px; text-align: center;">No</th>
                                <th>Nama Siswa</th>
                                <th style="text-align: center;">NISN</th>
                                <th style="text-align: center;">Kelas</th>
                                <th style="text-align: center;">Tingkat Perhatian</th>
                                <th>Fokus Pembinaan & Catatan Khusus</th>
                                <th style="width: 110px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attentionStudents as $aIdx => $att)
                                <tr>
                                    <td style="text-align: center; font-weight: 600;">{{ $aIdx + 1 }}</td>
                                    <td>
                                        <strong style="color: var(--color-navy); font-size: 13px;">{{ $att->name }}</strong>
                                    </td>
                                    <td style="text-align: center; font-family: monospace; font-size: 12px;">{{ $att->nisn }}</td>
                                    <td style="text-align: center; font-weight: 600;">{{ $att->studentClass->name ?? '-' }}</td>
                                    <td style="text-align: center;">
                                        @if($att->attention_level === 'segera_ditindaklanjuti')
                                            <span class="badge badge-danger" style="font-size: 10.5px;">Segera Ditindaklanjuti</span>
                                        @elseif($att->attention_level === 'prioritas')
                                            <span class="badge badge-warning" style="font-size: 10.5px;">Prioritas</span>
                                        @else
                                            <span class="badge badge-secondary" style="font-size: 10.5px;">Perlu Perhatian</span>
                                        @endif
                                    </td>
                                    <td style="font-size: 12px; color: var(--color-text-muted);">
                                        {{ $att->special_notes ?: 'Memerlukan koordinasi berkala dengan wali kelas dan orang tua.' }}
                                    </td>
                                    <td style="text-align: center;">
                                        <a href="{{ route('guru.siswa.show', $att->id) }}" class="btn btn-secondary btn-sm" style="font-size: 11px; padding: 3px 8px;">
                                            Profil 360°
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

{{-- ========================================================================= --}}
{{-- TAB 3: PAKET LAPORAN PIMPINAN (A, B, C)                                  --}}
{{-- ========================================================================= --}}
@elseif($activeTab === 'paket')
    <div style="margin-bottom: 14px;">
        <h3 style="font-size: 18px; font-weight: 800; color: var(--color-navy); margin: 0 0 4px 0;">
            Paket Template Laporan Otomatis Pimpinan
        </h3>
        <p style="font-size: 13px; color: var(--color-text-muted); margin: 0;">
            Format dokumen siap cetak / PDF terstandarisasi untuk didistribusikan kepada jajaran manajemen sekolah.
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
                        Fokus pada sinkronisasi jadwal pembelajaran dengan agenda mitra: sosialisasi kampus, kunjungan industri, seminar vokasi, dan status verifikasi ruang/kelas.
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
@endif

<script>
    // Tab 1: Grafik & Analisis Visual Custom Date Controls
    function toggleGrafikCustomDate(val) {
        var box = document.getElementById('grafikCustomDateContainer');
        if (!box) return;
        if (val === 'custom') {
            box.style.display = 'block';
        } else {
            box.style.display = 'none';
        }
    }

    function setGrafikDatePreset(daysAgo) {
        var today = new Date();
        var past = new Date();
        past.setDate(today.getDate() - daysAgo);

        var formatYMD = function(d) {
            var month = '' + (d.getMonth() + 1);
            var day = '' + d.getDate();
            var year = d.getFullYear();
            if (month.length < 2) month = '0' + month;
            if (day.length < 2) day = '0' + day;
            return [year, month, day].join('-');
        };

        var startInput = document.getElementById('grafikStartDate');
        var endInput = document.getElementById('grafikEndDate');
        var periodSelect = document.getElementById('grafikPeriodSelect');
        var box = document.getElementById('grafikCustomDateContainer');

        if (periodSelect) periodSelect.value = 'custom';
        if (box) box.style.display = 'block';
        if (startInput && endInput) {
            startInput.value = formatYMD(past);
            endInput.value = formatYMD(today);
        }

        var form = document.getElementById('formGrafikFilter');
        if (form) {
            form.submit();
        }
    }

    // Tab 2: Laporan Berkala Controls
    function setPeriodType(type) {
        var input = document.getElementById('periodTypeInput');
        if (input) {
            input.value = type;
        }

        var fieldMingguan = document.getElementById('fieldMingguan');
        var fieldBulan = document.getElementById('fieldBulan');
        var fieldTahun = document.getElementById('fieldTahun');
        var fieldCustom = document.getElementById('fieldCustom');

        if (type === 'mingguan') {
            if (fieldMingguan) fieldMingguan.style.display = 'block';
            if (fieldBulan) fieldBulan.style.display = 'none';
            if (fieldTahun) fieldTahun.style.display = 'none';
            if (fieldCustom) fieldCustom.style.display = 'none';
            document.getElementById('formLaporanBerkala')?.submit();
        } else if (type === 'bulanan') {
            if (fieldMingguan) fieldMingguan.style.display = 'none';
            if (fieldBulan) fieldBulan.style.display = 'block';
            if (fieldTahun) fieldTahun.style.display = 'block';
            if (fieldCustom) fieldCustom.style.display = 'none';
            document.getElementById('formLaporanBerkala')?.submit();
        } else if (type === 'tahunan') {
            if (fieldMingguan) fieldMingguan.style.display = 'none';
            if (fieldBulan) fieldBulan.style.display = 'none';
            if (fieldTahun) fieldTahun.style.display = 'block';
            if (fieldCustom) fieldCustom.style.display = 'none';
            document.getElementById('formLaporanBerkala')?.submit();
        } else if (type === 'custom') {
            if (fieldMingguan) fieldMingguan.style.display = 'none';
            if (fieldBulan) fieldBulan.style.display = 'none';
            if (fieldTahun) fieldTahun.style.display = 'none';
            if (fieldCustom) fieldCustom.style.display = 'block';

            var group = document.getElementById('periodTypeBtnGroup');
            if (group) {
                var buttons = group.querySelectorAll('button');
                buttons.forEach(function(b) {
                    b.className = 'btn btn-sm btn-secondary';
                });
            }
            var btnCustom = document.getElementById('btnTypeCustom');
            if (btnCustom) {
                btnCustom.className = 'btn btn-sm btn-primary';
            }
        }
    }

    function setBerkalaCustomPreset(daysAgo) {
        var today = new Date();
        var past = new Date();
        past.setDate(today.getDate() - daysAgo);

        var formatYMD = function(d) {
            var month = '' + (d.getMonth() + 1);
            var day = '' + d.getDate();
            var year = d.getFullYear();
            if (month.length < 2) month = '0' + month;
            if (day.length < 2) day = '0' + day;
            return [year, month, day].join('-');
        };

        var startInput = document.getElementById('berkalaStartDate');
        var endInput = document.getElementById('berkalaEndDate');
        var input = document.getElementById('periodTypeInput');

        if (input) input.value = 'custom';
        if (startInput && endInput) {
            startInput.value = formatYMD(past);
            endInput.value = formatYMD(today);
        }

        var form = document.getElementById('formLaporanBerkala');
        if (form) {
            form.submit();
        }
    }
</script>
@endsection
