@extends('layouts.app')

@section('title', 'Beranda & Prioritas Kerja - Guru BK')
@section('header_title', 'Pusat Kerja Guru Bimbingan Konseling')

@section('content')
<div style="margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
    <div>
        <h2 style="font-size: 20px; font-weight: 800; color: var(--color-text-main);">Selamat Bertugas, {{ auth()->user()->name }}</h2>
        <p style="font-size: 13px; color: var(--color-text-muted);">
            Berikut adalah ringkasan perkembangan siswa dan daftar prioritas pendampingan yang perlu Anda tindak lanjuti hari ini.
        </p>
    </div>
    <div>
        <a href="{{ route('guru.profil.show') }}" class="btn btn-secondary btn-sm" title="Lihat Profil Guru BK">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            <span>Profil Guru BK</span>
        </a>
    </div>
</div>

<!-- Aggregated Key Metrics (Section 4.1) -->
<div class="grid-4" style="margin-bottom: 24px;">
    <div class="stat-card">
        <span class="stat-label">Total Siswa Aktif</span>
        <span class="stat-value">{{ $totalStudents }}</span>
        <span class="stat-desc">Terdaftar dalam tahun ajaran berjalan</span>
    </div>

    <div class="stat-card">
        <span class="stat-label">Perlu Perhatian Khusus</span>
        <span class="stat-value">{{ $attentionStudentsCount }}</span>
        <span class="stat-desc">Status perhatian, prioritas, atau tindak lanjut</span>
    </div>

    <div class="stat-card">
        <span class="stat-label">Pengajuan Konseling Baru</span>
        <span class="stat-value">{{ $newCounselingCount }}</span>
        <span class="stat-desc">Menunggu tinjauan & penjadwalan</span>
    </div>

    <div class="stat-card">
        <span class="stat-label">Tindak Lanjut Jatuh Tempo</span>
        <span class="stat-value">{{ $overdueFollowUpsCount }}</span>
        <span class="stat-desc">Butuh verifikasi dan aksi lanjutan</span>
    </div>
</div>

<!-- Visual Monitoring Analytics (Column Chart & Pie Chart - Antislop Compliant) -->
<!-- Visual Monitoring Analytics (Column Chart & Pie Chart - Zero-Dependency & Always Visible) -->
<div class="grid-2" style="margin-bottom: 24px;">
    <!-- Column Chart: Distribusi Kategori Layanan Konseling -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header card-header-navy">
            <div>
                <h3 class="card-title">Distribusi Kategori Layanan Konseling</h3>
                <p style="font-size: 12px; margin-top: 2px;">
                    Grafik beban masalah bimbingan siswa berdasarkan kategori (Total {{ array_sum($categoryChartData) }} kasus)
                </p>
            </div>
            <a href="{{ route('guru.konseling.index') }}" class="btn btn-secondary btn-sm" title="Buka Data Konseling">
                Data Konseling
            </a>
        </div>
        <div class="card-body">
            @php
                $maxVal = max(array_merge([1], $categoryChartData));
                $categoryShortNames = [
                    'Masalah Belajar' => 'Belajar',
                    'Pengembangan Pribadi' => 'Pribadi',
                    'Hubungan Sosial' => 'Sosial',
                    'Masalah Keluarga & Lingkungan' => 'Keluarga',
                    'Perencanaan Karier' => 'Karier',
                ];
                $colColors = ['#1e3a8a', '#2563eb', '#0d9488', '#d97706', '#059669'];
            @endphp
            <div id="counselingCategoryColumnChart" style="display: flex; flex-direction: column; height: 230px; justify-content: flex-end; padding-top: 10px;">
                <!-- Column Bars Area -->
                <div style="display: flex; align-items: flex-end; justify-content: space-around; height: 170px; gap: 12px; border-bottom: 2px solid #cbd5e1; padding-bottom: 4px;">
                    @foreach($categoryChartLabels as $idx => $label)
                        @php
                            $val = $categoryChartData[$idx] ?? 0;
                            $heightPercent = $maxVal > 0 ? ($val / $maxVal) : 0;
                            $barHeight = $val > 0 ? max(18, round($heightPercent * 140)) : 6;
                            $color = $colColors[$idx % count($colColors)];
                            $shortName = $categoryShortNames[$label] ?? \Illuminate\Support\Str::limit($label, 10);
                        @endphp
                        <div style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: flex-end; height: 100%;" title="{{ $label }}: {{ $val }} Kasus">
                            <span style="font-size: 12px; font-weight: 700; color: {{ $val > 0 ? $color : 'var(--color-text-subtle)' }}; margin-bottom: 4px;">
                                {{ $val }}
                            </span>
                            <div style="width: 100%; max-width: 44px; height: {{ $barHeight }}px; background: {{ $val > 0 ? $color : '#e2e8f0' }}; border-radius: 6px 6px 0 0; transition: height 0.3s ease;"></div>
                        </div>
                    @endforeach
                </div>
                <!-- Labels Area -->
                <div style="display: flex; justify-content: space-around; gap: 12px; margin-top: 8px;">
                    @foreach($categoryChartLabels as $idx => $label)
                        @php
                            $shortName = $categoryShortNames[$label] ?? \Illuminate\Support\Str::limit($label, 10);
                        @endphp
                        <div style="flex: 1; text-align: center;" title="{{ $label }}">
                            <div style="font-size: 11px; font-weight: 600; color: var(--color-text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $shortName }}
                            </div>
                            <div style="font-size: 10px; color: var(--color-text-muted);">
                                {{ $categoryChartData[$idx] ?? 0 }} sesi
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Pie / Donut Chart: Proporsi Rencana Masa Depan Kelas XII -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header card-header-navy">
            <div>
                <h3 class="card-title">Peta Rencana Masa Depan Kelas XII</h3>
                <p style="font-size: 12px; margin-top: 2px;">
                    Grafik lingkaran proporsi karier siswa tingkat akhir ({{ $totalGradeXII }} siswa)
                </p>
            </div>
            <a href="{{ route('guru.peminatan.index') }}" class="btn btn-secondary btn-sm" title="Buka Detail Peminatan">
                Detail Peminatan
            </a>
        </div>
        <div class="card-body">
            @php
                $planColors = [
                    'Target Kuliah' => '#1e3a8a',
                    'Target Bekerja' => '#059669',
                    'Target Wirausaha' => '#d97706',
                    'Kuliah & Kerja' => '#0d9488',
                    'Belum Menentukan' => '#dc2626',
                ];
                $circumference = 339.292; // 2 * pi * 54
                $cumulativeOffset = 0;
            @endphp
            <div id="futurePlanPieChart" style="display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap; min-height: 230px;">
                <!-- SVG Donut Graphic -->
                <div style="position: relative; width: 140px; height: 140px; margin: 0 auto; flex-shrink: 0;">
                    <svg viewBox="0 0 140 140" width="140" height="140" style="transform: rotate(-90deg);">
                        <!-- Background Circle Ring -->
                        <circle cx="70" cy="70" r="54" fill="transparent" stroke="#f1f5f9" stroke-width="20" />
                        @if($totalGradeXII > 0)
                            @foreach($futurePlanChartLabels as $idx => $label)
                                @php
                                    $val = $futurePlanChartData[$idx] ?? 0;
                                    $fraction = $totalGradeXII > 0 ? ($val / $totalGradeXII) : 0;
                                    $dashArray = ($fraction * $circumference) . ' ' . $circumference;
                                    $dashOffset = -$cumulativeOffset;
                                    $color = $planColors[$label] ?? '#64748b';
                                    if ($val > 0) {
                                        $cumulativeOffset += ($fraction * $circumference);
                                    }
                                @endphp
                                @if($val > 0)
                                    <circle cx="70" cy="70" r="54" fill="transparent"
                                            stroke="{{ $color }}"
                                            stroke-width="20"
                                            stroke-dasharray="{{ $dashArray }}"
                                            stroke-dashoffset="{{ $dashOffset }}" />
                                @endif
                            @endforeach
                        @else
                            <circle cx="70" cy="70" r="54" fill="transparent" stroke="#e2e8f0" stroke-width="20" />
                        @endif
                    </svg>
                    <!-- Center Metric Text -->
                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; pointer-events: none;">
                        <span style="font-size: 22px; font-weight: 800; color: var(--color-text-main); line-height: 1;">
                            {{ $totalGradeXII }}
                        </span>
                        <span style="font-size: 10px; font-weight: 600; color: var(--color-text-muted); margin-top: 2px;">
                            Siswa XII
                        </span>
                    </div>
                </div>

                <!-- Structured Legend with Metrics -->
                <div style="flex: 1; min-width: 170px; display: flex; flex-direction: column; gap: 8px;">
                    @foreach($futurePlanChartLabels as $idx => $label)
                        @php
                            $val = $futurePlanChartData[$idx] ?? 0;
                            $percent = $totalGradeXII > 0 ? round(($val / $totalGradeXII) * 100) : 0;
                            $color = $planColors[$label] ?? '#64748b';
                        @endphp
                        <div style="display: flex; align-items: center; justify-content: space-between; font-size: 12px;">
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <span style="display: inline-block; width: 10px; height: 10px; border-radius: 2px; background: {{ $color }}; flex-shrink: 0;"></span>
                                <span style="color: var(--color-text-main); font-weight: 500;">{{ $label }}</span>
                            </div>
                            <div style="font-weight: 700; color: var(--color-text-main);">
                                {{ $val }} <span style="font-weight: 400; color: var(--color-text-muted); font-size: 11px;">({{ $percent }}%)</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<div class="grid-2" style="margin-bottom: 24px;">
    <!-- SECTION 36: MESIN PRIORITAS PEKERJAAN GURU BK ("YANG PERLU SAYA KERJAKAN") -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header card-header-navy">
            <div>
                <h3 class="card-title">YANG PERLU SAYA KERJAKAN</h3>
                <p style="font-size: 11px; margin-top: 1px;">Daftar tugas utama & tindak lanjut mendesak</p>
            </div>
            <a href="{{ route('guru.konseling.index') }}" class="btn btn-secondary btn-sm" title="Lihat Tugas Konseling">
                {{ count($priorityTasks) }} Prioritas
            </a>
        </div>
        <div class="card-body" style="padding: 0;">
            @if(count($priorityTasks) > 0)
                <div>
                    @foreach($priorityTasks as $task)
                        <div class="priority-task-item">
                            <div style="flex: 1;">
                                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                    <span class="badge badge-{{ $task['badge_color'] }}">{{ $task['badge'] }}</span>
                                    <span style="font-size: 11px; color: var(--color-text-subtle);">{{ $task['date'] }}</span>
                                </div>
                                <div style="font-weight: 700; font-size: 13px; color: var(--color-text-main);">
                                    {{ $task['title'] }}
                                </div>
                                <div style="font-size: 12px; color: var(--color-text-muted); margin-top: 2px;">
                                    {{ $task['desc'] }}
                                </div>
                            </div>
                            <div>
                                <a href="{{ $task['action_url'] }}" class="btn btn-secondary btn-sm">
                                    {{ $task['action_text'] }}
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state" style="padding: 32px 16px;">
                    <p class="empty-state-title">Semua Tugas Utama Telah Dituntaskan</p>
                    <p class="empty-state-desc">Tidak ada permohonan konseling baru atau agenda tindak lanjut yang tertunda saat ini.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- AGENDA KEGIATAN MITRA TERDEKAT (Section 18 & Laporan Kurikulum) -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header card-header-navy">
            <div>
                <h3 class="card-title">Agenda Mitra Terdekat</h3>
                <p style="font-size: 11px; margin-top: 1px;">Sosialisasi & kunjungan 7 hari ke depan</p>
            </div>
            <a href="{{ route('guru.mitra.activities') }}" class="btn btn-secondary btn-sm">Kelola Jadwal</a>
        </div>
        <div class="card-body" style="padding: 0;">
            @if(count($upcomingActivities) > 0)
                <div>
                    @foreach($upcomingActivities as $act)
                        <div class="priority-task-item">
                            <div style="flex: 1;">
                                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                    <span class="badge badge-success">{{ ucfirst($act->status) }}</span>
                                    <span style="font-size: 11px; color: var(--color-text-subtle);">
                                        {{ \Carbon\Carbon::parse($act->date)->translatedFormat('d M Y') }} &bull; {{ substr($act->start_time, 0, 5) }} WIB
                                    </span>
                                </div>
                                <div style="font-weight: 700; font-size: 13px; color: var(--color-text-main);">
                                    {{ $act->title }}
                                </div>
                                <div style="font-size: 12px; color: var(--color-text-muted); margin-top: 2px;">
                                    {{ $act->partner->name }} &bull; {{ $act->room_location }} &bull; {{ $act->targetClass ? $act->targetClass->name : 'Seluruh Siswa' }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state" style="padding: 32px 16px;">
                    <p class="empty-state-title">Tidak Ada Agenda Kegiatan Dalam Waktu Dekat</p>
                    <p class="empty-state-desc">Belum ada sosialisasi kampus atau kunjungan industri yang dijadwalkan pada pekan ini.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
