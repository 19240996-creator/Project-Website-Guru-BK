@extends('layouts.app')

@section('title', 'Beranda & Prioritas Kerja - Guru BK')
@section('header_title', 'Pusat Kerja Guru Bimbingan Konseling')

@section('content')
<div style="margin-bottom: 24px;">
    <h2 style="font-size: 20px; font-weight: 800; color: var(--color-text-main);">Selamat Bertugas, {{ auth()->user()->name }}</h2>
    <p style="font-size: 13px; color: var(--color-text-muted);">
        Berikut adalah ringkasan perkembangan siswa dan daftar prioritas pendampingan yang perlu Anda tindak lanjuti hari ini.
    </p>
</div>

<!-- Aggregated Key Metrics (Section 4.1) -->
<div class="grid-4" style="margin-bottom: 24px;">
    <div class="stat-card">
        <span class="stat-label">Total Siswa Aktif</span>
        <span class="stat-value">{{ $totalStudents }}</span>
        <span class="stat-desc">Terdaftar dalam tahun ajaran berjalan</span>
    </div>

    <div class="stat-card" style="border-left: 4px solid var(--color-warning);">
        <span class="stat-label">Perlu Perhatian Khusus</span>
        <span class="stat-value" style="color: var(--color-warning);">{{ $attentionStudentsCount }}</span>
        <span class="stat-desc">Status perhatian, prioritas, atau tindak lanjut</span>
    </div>

    <div class="stat-card" style="border-left: 4px solid var(--color-info);">
        <span class="stat-label">Pengajuan Konseling Baru</span>
        <span class="stat-value" style="color: var(--color-info);">{{ $newCounselingCount }}</span>
        <span class="stat-desc">Menunggu tinjauan & penjadwalan</span>
    </div>

    <div class="stat-card" style="border-left: 4px solid var(--color-danger);">
        <span class="stat-label">Tindak Lanjut Jatuh Tempo</span>
        <span class="stat-value" style="color: var(--color-danger);">{{ $overdueFollowUpsCount }}</span>
        <span class="stat-desc">Butuh verifikasi dan aksi lanjutan</span>
    </div>
</div>

<div class="grid-2" style="margin-bottom: 24px;">
    <!-- SECTION 36: MESIN PRIORITAS PEKERJAAN GURU BK ("YANG PERLU SAYA KERJAKAN") -->
    <div class="card" style="border-top: 3px solid var(--color-primary);">
        <div class="card-header">
            <div style="display: flex; align-items: center; gap: 8px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--color-primary);">
                    <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"></path>
                </svg>
                <h3 class="card-title">YANG PERLU SAYA KERJAKAN</h3>
            </div>
            <span class="badge badge-primary">{{ count($priorityTasks) }} Agenda Prioritas</span>
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
                <div class="empty-state">
                    <p class="empty-state-title">Semua Tugas Utama Telah Dituntaskan</p>
                    <p class="empty-state-desc">Tidak ada permohonan konseling baru atau agenda tindak lanjut yang tertunda saat ini.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- PETA RENCANA MASA DEPAN KELAS XII (Section 23) -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Peta Rencana Masa Depan Kelas Akhir (Kelas XII)</h3>
            <a href="{{ route('guru.peminatan.index') }}" class="btn btn-secondary btn-sm">Buka Detail Peminatan</a>
        </div>
        <div class="card-body">
            <p style="font-size: 13px; color: var(--color-text-muted); margin-bottom: 16px;">
                Total siswa kelas XII: <strong>{{ $totalGradeXII }} siswa</strong>. Data ini menjadi rujukan laporan pimpinan dan perencanaan karier sekolah.
            </p>

            <div style="display: flex; flex-direction: column; gap: 12px;">
                <div>
                    <div style="display: flex; justify-content: space-between; font-size: 13px; font-weight: 600; margin-bottom: 4px;">
                        <span>Target Kuliah (PTN / PTS)</span>
                        <span>{{ $collegeCount }} Siswa ({{ $totalGradeXII > 0 ? round(($collegeCount/$totalGradeXII)*100) : 0 }}%)</span>
                    </div>
                    <div style="height: 8px; background: #e2e8f0; border-radius: 4px; overflow: hidden;">
                        <div style="height: 100%; width: {{ $totalGradeXII > 0 ? ($collegeCount/$totalGradeXII)*100 : 0 }}%; background: #2563eb;"></div>
                    </div>
                </div>

                <div>
                    <div style="display: flex; justify-content: space-between; font-size: 13px; font-weight: 600; margin-bottom: 4px;">
                        <span>Target Bekerja (Industri / Perusahaan)</span>
                        <span>{{ $workCount }} Siswa ({{ $totalGradeXII > 0 ? round(($workCount/$totalGradeXII)*100) : 0 }}%)</span>
                    </div>
                    <div style="height: 8px; background: #e2e8f0; border-radius: 4px; overflow: hidden;">
                        <div style="height: 100%; width: {{ $totalGradeXII > 0 ? ($workCount/$totalGradeXII)*100 : 0 }}%; background: #059669;"></div>
                    </div>
                </div>

                <div>
                    <div style="display: flex; justify-content: space-between; font-size: 13px; font-weight: 600; margin-bottom: 4px;">
                        <span>Target Berwirausaha</span>
                        <span>{{ $businessCount }} Siswa ({{ $totalGradeXII > 0 ? round(($businessCount/$totalGradeXII)*100) : 0 }}%)</span>
                    </div>
                    <div style="height: 8px; background: #e2e8f0; border-radius: 4px; overflow: hidden;">
                        <div style="height: 100%; width: {{ $totalGradeXII > 0 ? ($businessCount/$totalGradeXII)*100 : 0 }}%; background: #d97706;"></div>
                    </div>
                </div>

                <div style="padding-top: 8px; border-top: 1px dashed var(--color-border);">
                    <div style="display: flex; justify-content: space-between; font-size: 13px; font-weight: 700; color: var(--color-danger); margin-bottom: 4px;">
                        <span>Belum Menentukan Pilihan</span>
                        <span>{{ $undecidedGradeXIICount }} Siswa (Prioritas BK)</span>
                    </div>
                    <div style="height: 8px; background: #e2e8f0; border-radius: 4px; overflow: hidden;">
                        <div style="height: 100%; width: {{ $totalGradeXII > 0 ? ($undecidedGradeXIICount/$totalGradeXII)*100 : 0 }}%; background: #dc2626;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JADWAL KEGIATAN MITRA MENDATANG (Section 18 & Laporan Kurikulum) -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Agenda Kegiatan Mitra Mendatang (7 Hari ke Depan)</h3>
        <a href="{{ route('guru.mitra.activities') }}" class="btn btn-secondary btn-sm">Kelola Jadwal Mitra</a>
    </div>
    <div class="card-body" style="padding: 0;">
        @if(count($upcomingActivities) > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tanggal & Waktu</th>
                            <th>Mitra Penyelenggara</th>
                            <th>Kegiatan</th>
                            <th>Sasaran Kelas</th>
                            <th>Tempat / Ruangan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($upcomingActivities as $act)
                            <tr>
                                <td>
                                    <strong>{{ \Carbon\Carbon::parse($act->date)->translatedFormat('d M Y') }}</strong><br>
                                    <small class="text-muted">{{ substr($act->start_time, 0, 5) }} - {{ substr($act->end_time, 0, 5) }} WIB</small>
                                </td>
                                <td>
                                    <strong>{{ $act->partner->name }}</strong><br>
                                    <small style="color: var(--color-text-subtle);">{{ ucfirst(str_replace('_', ' ', $act->partner->type)) }}</small>
                                </td>
                                <td>{{ $act->title }}</td>
                                <td>{{ $act->targetClass ? $act->targetClass->name : 'Seluruh Siswa' }}</td>
                                <td>{{ $act->room_location }}</td>
                                <td>
                                    <span class="badge badge-success">{{ ucfirst($act->status) }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <p class="empty-state-title">Tidak Ada Agenda Kegiatan Dalam Waktu Dekat</p>
                <p class="empty-state-desc">Belum ada sosialisasi kampus atau kunjungan industri yang dijadwalkan pada pekan ini.</p>
            </div>
        @endif
    </div>
</div>
@endsection
