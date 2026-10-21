@extends('layouts.app')

@section('title', $isKoordinator ? 'Dashboard Koordinator BK - Ruang BK' : 'Beranda & Prioritas Kerja - Guru BK')
@section('header_title', $isKoordinator ? 'Pusat Koordinasi & Pembagian Tugas BK' : 'Pusat Kerja Guru Bimbingan Konseling')

@section('content')
<!-- Page Header Bar -->
<div class="page-header-bar">
    <div>
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
            <span class="badge {{ $isKoordinator ? 'badge-primary' : 'badge-info' }}" style="font-size: 11px; padding: 3px 8px; font-weight: 700;">
                {{ $isKoordinator ? 'GURU KOORDINATOR BK' : 'GURU BK' }}
            </span>
            <span style="font-size: 12px; color: var(--color-text-muted);">
                {{ $isKoordinator ? 'Hak Akses Penuh Sekolah & Manajemen Tugas' : 'Hak Akses Siswa Terpilih (Sesuai Penugasan)' }}
            </span>
        </div>
        <h1 class="page-header-title">
            {{ $isKoordinator ? 'Pusat Koordinasi & Pengawasan Layanan BK' : 'Pusat Kerja Guru Bimbingan Konseling' }}
        </h1>
    </div>
    <div class="page-header-date">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
        <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
    </div>
</div>

<!-- Hero Welcome Section with Role Context -->
<div class="hero-banner-executive" style="margin-bottom: 24px;">
    <div class="hero-banner-inner">
        <div class="hero-banner-text">
            <div class="hero-banner-greeting">
                {{ $isKoordinator ? 'Selamat Bertugas, Koordinator BK' : 'Selamat Bertugas, Guru BK' }}
            </div>
            <h2 class="hero-banner-heading">{{ auth()->user()->name }}</h2>
            <p class="hero-banner-desc">
                @if($isKoordinator)
                    Pantau beban kerja seluruh Guru BK, bagikan tugas pendampingan siswa berdasarkan kelas dan jurusan, serta awasi perkembangan bimbingan sekolah secara terpusat.
                @else
                    Fokus pada pendampingan <strong>{{ number_format($totalStudents) }} siswa binaan</strong> yang resmi ditugaskan kepada Anda oleh Koordinator BK.
                @endif
            </p>
            @if($isKoordinator)
                <div style="display: flex; gap: 10px; margin-top: 14px; flex-wrap: wrap;">
                    <a href="{{ route('guru.penugasan.index') }}" class="btn btn-primary btn-sm" style="background: #FFFFFF; color: var(--color-navy); border: none; font-weight: 700;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        <span>Menu Pembagian Tugas Guru BK</span>
                    </a>
                    <a href="{{ route('guru.koordinator.guru_bk.index') }}" class="btn btn-secondary btn-sm" style="background: rgba(255,255,255,0.15); color: #FFFFFF; border: 1px solid rgba(255,255,255,0.3); font-weight: 600;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        <span>Kelola Akun Guru BK</span>
                    </a>
                </div>
            @endif
        </div>
        <div class="hero-banner-visual">
            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="hero-banner-photo" />
            <div class="hero-banner-quote-badge">
                @if($isKoordinator)
                    &ldquo;Koordinasi terstruktur, pembagian tugas presisi, data siswa terlindungi.&rdquo;
                @else
                    &ldquo;Bersama, kita bantu siswa menemukan jalan terbaik untuk masa depan.&rdquo;
                @endif
            </div>
        </div>
    </div>
</div>

@if(!$isKoordinator)
    <!-- Info Banner Kelas Tanggung Jawab Guru BK -->
    <div class="card" style="margin-bottom: 24px; background: #FFFFFF; border-left: 4px solid var(--color-primary);">
        <div class="card-body" style="padding: 16px 20px;">
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
                <div>
                    <div style="font-size: 11.5px; font-weight: 700; color: var(--color-primary); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">
                        Wilayah Penugasan Resmi Anda
                    </div>
                    <div style="font-size: 14px; font-weight: 700; color: var(--color-navy);">
                        Anda menangani {{ count($assignedClasses) }} Rombongan Belajar (Total {{ number_format($totalStudents) }} Siswa Binaan)
                    </div>
                </div>
                <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
                    @forelse($assignedClasses as $cls)
                        <a href="{{ route('guru.siswa.index', ['class_id' => $cls->id]) }}" class="badge badge-info" style="font-size: 12px; padding: 6px 12px; text-decoration: none; font-weight: 600;" title="Buka siswa kelas {{ $cls->name }}">
                            {{ $cls->name }}
                        </a>
                    @empty
                        <span class="badge badge-warning" style="font-size: 12px; padding: 6px 12px;">
                            Belum Ada Kelas yang Ditugaskan
                        </span>
                        <span style="font-size: 12px; color: var(--color-text-muted);">
                            Hubungi Koordinator BK untuk pembagian kelas bimbingan Anda.
                        </span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Key Aggregate Metrics (4 Stat Cards Row) -->
<div class="grid-4" style="margin-bottom: 24px;">
    @if($isKoordinator)
        <!-- Stat 1 Koordinator: Total Siswa Sekolah -->
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
                        <span class="stat-card-number">{{ number_format($totalSchoolStudents) }}</span>
                        <span class="stat-trend-tag">Sekolah</span>
                    </div>
                </div>
                <p class="stat-card-desc">Seluruh siswa aktif sekolah di semua kelas & jurusan</p>
            </div>
        </a>

        <!-- Stat 2 Koordinator: Status Pembagian Tugas -->
        <a href="{{ route('guru.penugasan.index') }}" style="text-decoration: none; color: inherit;">
            <div class="stat-card-clean">
                <div>
                    <div class="stat-card-top">
                        <div class="stat-icon-pill green">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                        </div>
                    </div>
                    <div class="stat-card-label">STATUS PEMBAGIAN TUGAS</div>
                    <div class="stat-value-row">
                        <span class="stat-card-number">{{ $schoolAssignmentPercentage }}%</span>
                        <span class="stat-trend-tag">{{ $totalAssignedSchoolStudents }}/{{ $totalSchoolStudents }}</span>
                    </div>
                </div>
                <p class="stat-card-desc">
                    {{ $totalUnassignedSchoolStudents > 0 ? "{$totalUnassignedSchoolStudents} siswa belum memiliki Guru BK" : "Seluruh siswa telah memiliki penanggung jawab" }}
                </p>
            </div>
        </a>

        <!-- Stat 3 Koordinator: Tim Guru BK Terdaftar -->
        <a href="{{ route('guru.koordinator.guru_bk.index') }}" style="text-decoration: none; color: inherit;">
            <div class="stat-card-clean">
                <div>
                    <div class="stat-card-top">
                        <div class="stat-icon-pill amber">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        </div>
                    </div>
                    <div class="stat-card-label">TIM GURU BK</div>
                    <div class="stat-value-row">
                        <span class="stat-card-number">{{ $totalGuruBk }}</span>
                        <span class="stat-trend-tag">{{ $activeGuruBk }} Aktif</span>
                    </div>
                </div>
                <p class="stat-card-desc">Guru Bimbingan Konseling yang terdaftar</p>
            </div>
        </a>

        <!-- Stat 4 Koordinator: Pengajuan Konseling Sekolah -->
        <a href="{{ route('guru.konseling.index') }}" style="text-decoration: none; color: inherit;">
            <div class="stat-card-clean">
                <div>
                    <div class="stat-card-top">
                        <div class="stat-icon-pill red">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><polyline points="9 12 11 14 15 10"></polyline></svg>
                        </div>
                    </div>
                    <div class="stat-card-label">PERMOHONAN KONSELING</div>
                    <div class="stat-value-row">
                        <span class="stat-card-number">{{ number_format($newCounselingCount) }}</span>
                        <span class="stat-trend-tag">Sekolah</span>
                    </div>
                </div>
                <p class="stat-card-desc">Pengajuan konseling baru belum diproses</p>
            </div>
        </a>
    @else
        <!-- Stat 1 Guru BK: Siswa Binaan Saya -->
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
                        <span class="stat-trend-tag">{{ count($assignedClasses) }} Kelas</span>
                    </div>
                </div>
                <p class="stat-card-desc">Siswa binaan yang resmi ditugaskan kepada Anda</p>
            </div>
        </a>

        <!-- Stat 2 Guru BK: Perlu Perhatian Khusus -->
        <a href="{{ route('guru.siswa.index', ['attention_level' => 'perlu_perhatian']) }}" style="text-decoration: none; color: inherit;">
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
                        <span class="stat-trend-tag">Prioritas</span>
                    </div>
                </div>
                <p class="stat-card-desc">Di antara siswa binaan tanggung jawab Anda</p>
            </div>
        </a>

        <!-- Stat 3 Guru BK: Pengajuan Konseling Siswa Binaan -->
        <a href="{{ route('guru.konseling.index', ['status' => 'diajukan']) }}" style="text-decoration: none; color: inherit;">
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
                        <span class="stat-trend-tag">Menunggu</span>
                    </div>
                </div>
                <p class="stat-card-desc">Dari siswa yang Anda bimbing</p>
            </div>
        </a>

        <!-- Stat 4 Guru BK: Tindak Lanjut Jatuh Tempo -->
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
                        <span class="stat-trend-tag">Aksi</span>
                    </div>
                </div>
                <p class="stat-card-desc">Agenda tindak lanjut butuh penanganan</p>
            </div>
        </a>
    @endif
</div>

@if($isKoordinator)
    <!-- Section Khusus Koordinator BK: Ringkasan Pembagian Tugas Guru BK -->
    <div class="card" style="margin-bottom: 24px;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; padding: 18px 24px; border-bottom: 1px solid var(--color-border); background-color: #FFFFFF; flex-wrap: wrap; gap: 14px;">
            <div>
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                    <span class="badge badge-primary" style="font-size: 11px; padding: 3px 8px; font-weight: 700;">
                        DISTRIBUSI GURU BK
                    </span>
                    <span style="font-size: 12px; color: var(--color-text-muted);">
                        Sistem Penugasan & Otorisasi Data Siswa
                    </span>
                </div>
                <h2 class="card-title" style="margin: 0; font-size: 16px; font-weight: 700; color: var(--color-navy);">
                    Ringkasan Beban Kerja & Pembagian Tugas Guru BK
                </h2>
                <p style="margin: 4px 0 0; font-size: 12.5px; color: var(--color-text-muted);">
                    Pengawasan distribusi bimbingan siswa pada masing-masing Guru BK terdaftar
                </p>
            </div>
            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <a href="{{ route('guru.penugasan.index') }}" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 6px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <span>Atur Pembagian Tugas</span>
                </a>
                <a href="{{ route('guru.koordinator.guru_bk.create') }}" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 6px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    <span>Tambah Guru BK</span>
                </a>
            </div>
        </div>

        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="table" style="width: 100%; min-width: 920px; border-collapse: collapse;">
                    <thead class="table-thead-navy">
                        <tr>
                            <th style="width: 50px; text-align: center; padding: 14px 12px;">No</th>
                            <th style="width: 28%; text-align: left; padding: 14px 18px;">Guru Bimbingan Konseling</th>
                            <th style="width: 17%; text-align: left; padding: 14px 18px;">NIP / NUPTK</th>
                            <th style="width: 12%; text-align: center; padding: 14px 16px;">Status Akun</th>
                            <th style="width: 14%; text-align: center; padding: 14px 16px;">Siswa Binaan</th>
                            <th style="width: 21%; text-align: left; padding: 14px 18px;">Kelas & Jurusan Tanggung Jawab</th>
                            <th style="width: 8%; text-align: center; padding: 14px 16px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($counselorWorkloads as $item)
                            @php $guru = $item['guru']; @endphp
                            <tr>
                                <td style="text-align: center; color: var(--color-text-muted); font-weight: 700; font-size: 12.5px;">
                                    {{ $loop->iteration }}
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <img src="{{ $guru->avatar_url }}" alt="{{ $guru->name }}" style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover; flex-shrink: 0; border: 2px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.06);" />
                                        <div style="min-width: 0;">
                                            <div style="font-weight: 700; color: var(--color-navy); font-size: 13.5px; line-height: 1.35;">
                                                {{ $guru->name }}
                                            </div>
                                            <div style="font-size: 11.5px; color: var(--color-text-muted); margin-top: 2px;">
                                                {{ $guru->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if(!empty($guru->nip))
                                        <span style="font-size: 12.5px; font-weight: 600; color: #334155; background: #F8FAFC; border: 1px solid #E2E8F0; padding: 3px 8px; border-radius: 4px; display: inline-block;">
                                            {{ $guru->nip }}
                                        </span>
                                    @else
                                        <span style="font-size: 12px; color: var(--color-text-subtle);">-</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    @if($guru->is_active)
                                        <span class="badge badge-success" style="font-size: 11px; padding: 4px 10px; font-weight: 700; min-width: 68px;">
                                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #16866A; display: inline-block; margin-right: 5px;"></span>Aktif
                                        </span>
                                    @else
                                        <span class="badge badge-danger" style="font-size: 11px; padding: 4px 10px; font-weight: 700; min-width: 68px;">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <div style="display: inline-flex; align-items: baseline; gap: 4px;">
                                        <span style="font-size: 16px; font-weight: 800; color: var(--color-navy);">
                                            {{ number_format($item['student_count']) }}
                                        </span>
                                        <span style="font-size: 12px; font-weight: 600; color: var(--color-text-muted);">Siswa</span>
                                    </div>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 6px; flex-wrap: wrap; align-items: center;">
                                        @forelse($item['classes'] as $cls)
                                            <span class="badge badge-info" style="font-size: 11px; padding: 4px 9px; font-weight: 600; background: #EEF4FF; color: #1E40AF; border: 1px solid #BFDBFE;">
                                                {{ $cls->name }}
                                            </span>
                                        @empty
                                            <span style="font-size: 11.5px; color: #94A3B8; font-style: italic; background: #F8FAFC; padding: 3px 8px; border-radius: 4px; border: 1px dashed #CBD5E1;">
                                                Belum ada kelas ditugaskan
                                            </span>
                                        @endforelse
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <a href="{{ route('guru.koordinator.guru_bk.show', $guru->id) }}" class="btn btn-secondary btn-sm" style="font-size: 12px; padding: 5px 12px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px;" title="Lihat detail bimbingan & beban kerja">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                        <span>Detail</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 32px 20px; color: var(--color-text-muted);">
                                    <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color: #94A3B8;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><line x1="19" y1="8" x2="19" y2="14"></line><line x1="22" y1="11" x2="16" y2="11"></line></svg>
                                        <div style="font-weight: 600; color: var(--color-navy); font-size: 14px;">Belum Ada Guru BK Terdaftar</div>
                                        <div style="font-size: 12.5px; color: var(--color-text-muted);">Silakan tambahkan akun Guru BK untuk mulai membagikan tugas bimbingan siswa.</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer" style="display: flex; justify-content: space-between; align-items: center; padding: 12px 24px; font-size: 12.5px; background: #F8FAFC; border-top: 1px solid var(--color-border); flex-wrap: wrap; gap: 10px;">
            <div style="display: flex; align-items: center; gap: 8px; color: var(--color-text-muted);">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--color-primary); flex-shrink: 0;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                <span>
                    Menampilkan <strong>{{ count($counselorWorkloads) }} Guru BK</strong> terdaftar dalam pemantauan distribusi bimbingan
                </span>
            </div>
            <div>
                <a href="{{ route('guru.penugasan.index') }}" style="color: var(--color-primary); font-weight: 700; text-decoration: none; font-size: 12.5px; display: inline-flex; align-items: center; gap: 4px;">
                    <span>Buka Sistem Pembagian Tugas</span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </a>
            </div>
        </div>
    </div>
@endif

<!-- Visual Monitoring Analytics (2 Columns: Column Chart & Donut Chart) -->
<div class="grid-2" style="margin-bottom: 24px;">
    <!-- Column Chart: Distribusi Kategori Layanan Konseling -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header card-header-navy" style="display: flex; align-items: center; justify-content: space-between; padding: 16px 20px;">
            <div>
                <h2 class="card-title" style="margin: 0; font-size: 15px; color: #FFFFFF;">Distribusi Kategori Layanan Konseling</h2>
                <p style="margin: 3px 0 0; font-size: 12px; color: rgba(255, 255, 255, 0.75);">
                    Grafik kasus bimbingan {{ $isKoordinator ? 'seluruh sekolah' : 'siswa binaan Anda' }} (Total {{ array_sum($categoryChartData) }} Kasus)
                </p>
            </div>
            <a href="{{ route('guru.konseling.index') }}" class="btn btn-secondary btn-sm" style="font-size: 12px; padding: 4px 12px; border-radius: var(--radius-sm);" title="Lihat Detail Konseling">
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
        <div class="card-header card-header-navy" style="display: flex; align-items: center; justify-content: space-between; padding: 16px 20px;">
            <div>
                <h2 class="card-title" style="margin: 0; font-size: 15px; color: #FFFFFF;">Peta Rencana Masa Depan Siswa</h2>
                <p style="margin: 3px 0 0; font-size: 12px; color: rgba(255, 255, 255, 0.75);">
                    Grafik lingkaran proporsi karier {{ $isKoordinator ? 'seluruh siswa' : 'siswa binaan Anda' }} (Total {{ $totalPlanStudents }} Siswa)
                </p>
            </div>
            <a href="{{ route('guru.peminatan.index') }}" class="btn btn-secondary btn-sm" style="font-size: 12px; padding: 4px 12px; border-radius: var(--radius-sm);" title="Lihat Detail Peminatan">
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
                            {{ $isKoordinator ? 'Siswa Aktif' : 'Siswa Binaan' }}
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

<!-- 3 Priority Panels Row -->
<div class="priority-three-grid" style="margin-bottom: 24px;">
    <!-- Panel 1: Pengajuan Konseling Terbaru -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header card-header-navy" style="display: flex; align-items: center; justify-content: space-between; padding: 14px 20px;">
            <h2 class="card-title" style="margin: 0; font-size: 14.5px; color: #FFFFFF;">Pengajuan Konseling Terbaru</h2>
            <a href="{{ route('guru.konseling.index') }}" class="btn btn-secondary btn-sm" style="font-size: 11.5px; padding: 3px 10px; border-radius: var(--radius-sm);" title="Lihat Semua Pengajuan">
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
                    <p class="empty-state-desc" style="font-size: 12px;">Semua permohonan konseling telah diverifikasi.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Panel 2: Jadwal Konseling Mendatang -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header card-header-navy" style="display: flex; align-items: center; justify-content: space-between; padding: 14px 20px;">
            <h2 class="card-title" style="margin: 0; font-size: 14.5px; color: #FFFFFF;">Jadwal Konseling Mendatang</h2>
            <a href="{{ route('guru.konseling.index') }}" class="btn btn-secondary btn-sm" style="font-size: 11.5px; padding: 3px 10px; border-radius: var(--radius-sm);" title="Lihat Semua Jadwal">
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
                                    {{ substr($sched->scheduled_time ?? '09:00', 0, 5) }} WIB
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
        <div class="card-header card-header-navy" style="display: flex; align-items: center; justify-content: space-between; padding: 14px 20px;">
            <h2 class="card-title" style="margin: 0; font-size: 14.5px; color: #FFFFFF;">Aktivitas Terkini Sistem</h2>
            @if($isKoordinator)
            <a href="{{ route('guru.audit.index') }}" class="btn btn-secondary btn-sm" style="font-size: 11.5px; padding: 3px 10px; border-radius: var(--radius-sm);" title="Lihat Semua Aktivitas">
                Lihat Semua
            </a>
            @endif
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
