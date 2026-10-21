@extends('layouts.app')

@section('title', 'Pembagian Tugas Guru BK - Ruang BK')
@section('header_title', 'Sistem Pembagian Tugas & Penugasan Guru BK')

@section('content')
<!-- Page Header -->
<div class="page-header-bar">
    <div>
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
            <span class="badge badge-primary" style="font-size: 11px; padding: 3px 8px; font-weight: 700;">
                KOORDINATOR BK
            </span>
            <span style="font-size: 12px; color: var(--color-text-muted);">
                Manajemen Distribusi & Hak Akses Data Siswa
            </span>
        </div>
        <h1 class="page-header-title">Pembagian Tugas & Penugasan Siswa Guru BK</h1>
    </div>
    <div style="display: flex; gap: 8px; align-items: center;">
        <button type="button" class="btn btn-secondary btn-sm" onclick="openReassignModal()">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m16 3 4 4-4 4"></path><path d="M20 7H4"></path><path d="m8 21-4-4 4-4"></path><path d="M4 17h16"></path></svg>
            <span>Pindah Tugas Massal</span>
        </button>
        <a href="{{ route('guru.koordinator.guru_bk.index') }}" class="btn btn-primary btn-sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            <span>Daftar Guru BK</span>
        </a>
    </div>
</div>

<!-- Key Stat Cards for Assignment Status -->
<div class="grid-4" style="margin-bottom: 24px;">
    <!-- Stat 1: Total Siswa Aktif -->
    <div class="stat-card-clean">
        <div>
            <div class="stat-card-top">
                <div class="stat-icon-pill blue">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </div>
            </div>
            <div class="stat-card-label">TOTAL SISWA SEKOLAH</div>
            <div class="stat-value-row">
                <span class="stat-card-number">{{ number_format($totalActiveStudents) }}</span>
                <span class="stat-trend-tag">Aktif</span>
            </div>
        </div>
        <p class="stat-card-desc">Terdaftar dalam tahun ajaran berjalan</p>
    </div>

    <!-- Stat 2: Sudah Ditugaskan -->
    <div class="stat-card-clean">
        <div>
            <div class="stat-card-top">
                <div class="stat-icon-pill green">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                </div>
            </div>
            <div class="stat-card-label">SUDAH DITUGASKAN</div>
            <div class="stat-value-row">
                <span class="stat-card-number">{{ number_format($totalAssignedStudents) }}</span>
                <span class="stat-trend-tag">{{ $assignmentPercentage }}%</span>
            </div>
        </div>
        <p class="stat-card-desc">Memiliki Guru BK penanggung jawab</p>
    </div>

    <!-- Stat 3: Belum Ditugaskan -->
    <div class="stat-card-clean">
        <div>
            <div class="stat-card-top">
                <div class="stat-icon-pill {{ $totalUnassignedStudents > 0 ? 'amber' : 'green' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                </div>
            </div>
            <div class="stat-card-label">BELUM DITUGASKAN</div>
            <div class="stat-value-row">
                <span class="stat-card-number">{{ number_format($totalUnassignedStudents) }}</span>
                <span class="stat-trend-tag">{{ $totalUnassignedStudents > 0 ? 'Perlu Dibagi' : 'Tuntas' }}</span>
            </div>
        </div>
        <p class="stat-card-desc">Belum memiliki Guru BK pembimbing</p>
    </div>

    <!-- Stat 4: Guru BK Aktif -->
    <div class="stat-card-clean">
        <div>
            <div class="stat-card-top">
                <div class="stat-icon-pill blue">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                </div>
            </div>
            <div class="stat-card-label">GURU BK PENERIMA TUGAS</div>
            <div class="stat-value-row">
                <span class="stat-card-number">{{ count($counselors) }}</span>
                <span class="stat-trend-tag">Tersedia</span>
            </div>
        </div>
        <p class="stat-card-desc">Siap menerima delegasi bimbingan</p>
    </div>
</div>

<!-- Tabs Navigation -->
<div style="display: flex; gap: 8px; border-bottom: 2px solid var(--color-border); margin-bottom: 20px;">
    <button type="button" id="tabBtnForm" class="btn btn-sm" onclick="switchPenugasanTab('form')" style="border-radius: 8px 8px 0 0; font-weight: 700; border: none; padding: 10px 18px; background: var(--color-primary); color: #FFFFFF;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 4px;"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
        1. Form Pembagian Tugas Siswa
    </button>
    <button type="button" id="tabBtnKelas" class="btn btn-sm" onclick="switchPenugasanTab('kelas')" style="border-radius: 8px 8px 0 0; font-weight: 600; border: none; padding: 10px 18px; background: transparent; color: var(--color-text-muted);">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 4px;"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
        2. Rekap Penugasan Seluruh Kelas
    </button>
    <button type="button" id="tabBtnGuru" class="btn btn-sm" onclick="switchPenugasanTab('guru')" style="border-radius: 8px 8px 0 0; font-weight: 600; border: none; padding: 10px 18px; background: transparent; color: var(--color-text-muted);">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 4px;"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg>
        3. Beban Kerja Tiap Guru BK
    </button>
</div>

<!-- ========================================== -->
<!-- TAB 1: FORM FILTER & PEMBAGIAN TUGAS SISWA -->
<!-- ========================================== -->
<div id="tabContentForm">
    <!-- Filter Card Berdasarkan Kelas & Jurusan -->
    <div class="card" style="margin-bottom: 24px;">
        <div class="card-header-clean">
            <div>
                <h2 class="card-title-clean">Filter Siswa Berdasarkan Kelas & Jurusan</h2>
                <p class="card-subtitle-clean">
                    Pilih tingkat kelas dan jurusan untuk menampilkan siswa yang akan dibagikan tugasnya kepada Guru BK
                </p>
            </div>
            @if($selectedGrade || $selectedMajor || $selectedClassId || $assignmentStatus || $search)
                <a href="{{ route('guru.penugasan.index') }}" class="btn btn-secondary btn-sm" title="Reset semua filter">
                    Reset Filter
                </a>
            @endif
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('guru.penugasan.index') }}" id="filterForm">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px;">
                    <!-- Filter 1: Tingkat (Grade) -->
                    <div>
                        <label for="filterGrade" class="form-label" style="font-weight: 700; font-size: 12px; color: var(--color-navy);">
                            1. Tingkat Kelas
                        </label>
                        <select name="grade" id="filterGrade" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Semua Tingkat (X, XI, XII) --</option>
                            @foreach($grades as $g)
                                <option value="{{ $g }}" {{ $selectedGrade === $g ? 'selected' : '' }}>
                                    Kelas {{ $g }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter 2: Jurusan (Major) -->
                    <div>
                        <label for="filterMajor" class="form-label" style="font-weight: 700; font-size: 12px; color: var(--color-navy);">
                            2. Jurusan
                        </label>
                        <select name="major" id="filterMajor" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Semua Jurusan --</option>
                            @foreach($majors as $m)
                                <option value="{{ $m }}" {{ $selectedMajor === $m ? 'selected' : '' }}>
                                    {{ $m }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter 3: Kelas Spesifik -->
                    <div>
                        <label for="filterClass" class="form-label" style="font-weight: 700; font-size: 12px; color: var(--color-navy);">
                            3. Rombel / Kelas Spesifik
                        </label>
                        <select name="class_id" id="filterClass" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Semua Rombel --</option>
                            @foreach($availableClasses as $c)
                                <option value="{{ $c->id }}" {{ (string)$selectedClassId === (string)$c->id ? 'selected' : '' }}>
                                    {{ $c->name }} ({{ $c->students_count }} Siswa)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter 4: Status Penugasan -->
                    <div>
                        <label for="filterStatus" class="form-label" style="font-weight: 700; font-size: 12px; color: var(--color-navy);">
                            4. Status Penugasan
                        </label>
                        <select name="assignment_status" id="filterStatus" class="form-select" onchange="this.form.submit()">
                            <option value="all" {{ $assignmentStatus === 'all' || empty($assignmentStatus) ? 'selected' : '' }}>Semua Status</option>
                            <option value="unassigned" {{ $assignmentStatus === 'unassigned' ? 'selected' : '' }}>Belum Ditugaskan</option>
                            <option value="assigned" {{ $assignmentStatus === 'assigned' ? 'selected' : '' }}>Sudah Ditugaskan</option>
                        </select>
                    </div>

                    <!-- Filter 5: Cari Nama / NISN -->
                    <div>
                        <label for="filterSearch" class="form-label" style="font-weight: 700; font-size: 12px; color: var(--color-navy);">
                            5. Cari Nama / NISN
                        </label>
                        <div class="form-search-box">
                            <span class="form-search-icon">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                            </span>
                            <input type="text" name="q" id="filterSearch" class="form-search-input" placeholder="Cari nama atau NISN..." value="{{ $search }}" onkeydown="if(event.key==='Enter'){this.form.submit();}" />
                            @if($search)
                                <a href="{{ route('guru.penugasan.index', array_filter(['grade' => $selectedGrade, 'major' => $selectedMajor, 'class_id' => $selectedClassId, 'assignment_status' => $assignmentStatus])) }}" class="form-search-clear" title="Hapus kata kunci pencarian">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Form Aksi Penugasan Siswa -->
    <form method="POST" action="{{ route('guru.penugasan.assign') }}" id="assignSubmitForm">
        @csrf
        <div class="card" style="margin-bottom: 24px;">
            <!-- Header Aksi & Pemilihan Guru BK -->
            <div class="card-header-clean" style="background: #F8FAFD; border-bottom: 1px solid var(--color-border); padding: 16px 20px;">
                <div style="display: flex; align-items: center; gap: 14px; flex-wrap: wrap; width: 100%; justify-content: space-between;">
                    <div>
                        <h2 class="card-title-clean">Daftar Siswa Hasil Filter ({{ $students->total() }} Siswa)</h2>
                        <p class="card-subtitle-clean">
                            Tandai siswa di bawah ini, lalu pilih Guru BK untuk menetapkan atau mengubah penugasan
                        </p>
                    </div>

                    <!-- Action Panel Penugasan -->
                    <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                        <div style="min-width: 240px;">
                            <select name="counselor_id" id="targetCounselorSelect" class="form-select" required style="font-weight: 700; border-color: var(--color-primary);">
                                <option value="">-- Pilih Guru BK Penanggung Jawab --</option>
                                @foreach($counselors as $guru)
                                    <option value="{{ $guru->id }}">
                                        {{ $guru->name }} (Saat ini: {{ $guru->student_assignments_count }} Siswa)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="button" class="btn btn-primary btn-sm" id="btnSubmitAssign" onclick="handleBatchAssignClick()" style="font-weight: 700;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                            <span>Simpan Penugasan Siswa Terpilih</span>
                        </button>

                        <button type="button" class="btn btn-danger btn-sm" onclick="handleBatchUnassignClick()" title="Lepas penugasan dari siswa yang dipilih">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            <span>Lepas Tugas Terpilih</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Toolbar Select All, Per-Page & Status -->
            <div style="padding: 10px 20px; background: #FFFFFF; border-bottom: 1px solid var(--color-border); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <label style="display: flex; align-items: center; gap: 6px; font-weight: 700; font-size: 13px; color: var(--color-navy); cursor: pointer; margin: 0;">
                        <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)" style="width: 17px; height: 17px; cursor: pointer;" />
                        <span>Pilih Semua Siswa di Halaman Ini</span>
                    </label>
                    <span id="selectedCountBadge" class="badge badge-info" style="font-size: 11px; display: none;">
                        0 siswa terpilih
                    </span>
                </div>

                <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                    <div style="display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--color-navy); font-weight: 600;">
                        <span>Tampilkan:</span>
                        <select name="per_page" form="filterForm" class="form-select" style="min-height: 30px; height: 30px; padding: 2px 26px 2px 8px; font-size: 12px; border-radius: var(--radius-sm); width: auto;" onchange="document.getElementById('filterForm').submit()">
                            <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>10 Siswa</option>
                            <option value="25" {{ ($perPage ?? 10) == 25 ? 'selected' : '' }}>25 Siswa</option>
                            <option value="50" {{ ($perPage ?? 10) == 50 ? 'selected' : '' }}>50 Siswa</option>
                        </select>
                    </div>
                    <div style="font-size: 12px; color: var(--color-text-muted);">
                        Sistem mencegah penugasan ganda: siswa yang dipindahkan akan otomatis dicabut hak akses guru sebelumnya.
                    </div>
                </div>
            </div>

            <!-- Tabel Siswa -->
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table" style="width: 100%; border-collapse: collapse;">
                        <thead class="table-thead-navy">
                            <tr>
                                <th style="width: 44px; text-align: center; padding: 13px 12px;">Pilih</th>
                                <th style="padding: 13px 18px;">Nama Lengkap Siswa</th>
                                <th style="padding: 13px 18px;">NISN / NIS</th>
                                <th style="padding: 13px 18px;">Kelas & Jurusan</th>
                                <th style="padding: 13px 18px;">Status Guru BK Penanggung Jawab</th>
                                <th style="padding: 13px 18px; text-align: right;">Aksi Cepat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $st)
                                @php
                                    $assignment = $st->counselorAssignment;
                                    $currentCounselor = $assignment ? $assignment->counselor : null;
                                @endphp
                                <tr style="{{ $assignment ? '' : 'background: #FFFDF7;' }}">
                                    <td style="text-align: center;">
                                        <input type="checkbox" name="student_ids[]" value="{{ $st->id }}" class="student-checkbox" onchange="updateSelectedCount()" style="width: 16px; height: 16px; cursor: pointer;" />
                                    </td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <img src="{{ $st->avatar_url }}" alt="{{ $st->name }}" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; flex-shrink: 0;" />
                                            <div>
                                                <a href="{{ route('guru.siswa.show', $st->id) }}" style="font-weight: 700; color: var(--color-navy); text-decoration: none; font-size: 13.5px;" title="Lihat Profil 360 Siswa">
                                                    {{ $st->name }}
                                                </a>
                                                <div style="font-size: 11.5px; color: var(--color-text-muted);">
                                                    Jenis Kelamin: {{ $st->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span style="font-weight: 700; font-size: 12.5px; color: #1E293B;">{{ $st->nisn }}</span>
                                        <div style="font-size: 11px; color: #64748B;">NIS: {{ $st->nis }}</div>
                                    </td>
                                    <td>
                                        <span class="badge badge-info" style="font-size: 11.5px; font-weight: 700;">
                                            {{ $st->studentClass ? $st->studentClass->name : '-' }}
                                        </span>
                                        <div style="font-size: 11px; color: #64748B; margin-top: 2px;">
                                            {{ $st->studentClass ? $st->studentClass->major : '-' }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($currentCounselor)
                                            <div style="display: flex; align-items: center; gap: 8px;">
                                                <img src="{{ $currentCounselor->avatar_url }}" alt="{{ $currentCounselor->name }}" style="width: 24px; height: 24px; border-radius: 50%; object-fit: cover;" />
                                                <div>
                                                    <span class="badge badge-success" style="font-size: 11.5px; font-weight: 700; padding: 4px 8px;">
                                                        {{ $currentCounselor->name }}
                                                    </span>
                                                    <div style="font-size: 10.5px; color: #64748B; margin-top: 1px;">
                                                        Ditugaskan {{ $assignment->created_at ? $assignment->created_at->translatedFormat('d M Y') : '' }}
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="badge badge-warning" style="font-size: 11.5px; font-weight: 700; padding: 4px 8px;">
                                                Belum Ada Guru BK
                                            </span>
                                            <div style="font-size: 10.5px; color: #D97706; margin-top: 1px;">
                                                Perlu Ditugaskan
                                            </div>
                                        @endif
                                    </td>
                                    <td style="text-align: right;">
                                        <div style="display: flex; gap: 6px; justify-content: flex-end;">
                                            <button type="button" class="btn btn-secondary btn-sm" onclick="openSingleAssignModal('{{ $st->id }}', '{{ addslashes($st->name) }}', '{{ $currentCounselor ? $currentCounselor->id : '' }}')" title="Tugaskan atau pindahkan siswa ini">
                                                {{ $currentCounselor ? 'Ganti Guru' : 'Tugaskan' }}
                                            </button>
                                            @if($currentCounselor)
                                                <button type="button" class="btn btn-danger btn-sm" onclick="handleSingleUnassignClick('{{ $st->id }}', '{{ addslashes($st->name) }}')" title="Lepas penugasan siswa ini">
                                                    Lepas
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 36px 16px;">
                                        <div style="max-width: 420px; margin: 0 auto;">
                                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color: #94A3B8; margin-bottom: 10px;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                            <p style="font-weight: 700; font-size: 14px; color: var(--color-navy); margin-bottom: 4px;">Tidak Ada Data Siswa Ditemukan</p>
                                            <p style="font-size: 12.5px; color: var(--color-text-muted);">Silakan sesuaikan filter tingkat kelas, jurusan, atau kata kunci pencarian di atas.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $students->links() }}
            </div>
        </div>
    </form>
</div>

<!-- ========================================== -->
<!-- TAB 2: REKAP PENUGASAN SELURUH KELAS -->
<!-- ========================================== -->
<div id="tabContentKelas" style="display: none;">
    <div class="card" style="margin-bottom: 24px;">
        <div class="card-header-clean">
            <div>
                <h2 class="card-title-clean">Status Keterisian Penugasan Tiap Kelas</h2>
                <p class="card-subtitle-clean">
                    Pantau apakah semua rombongan belajar telah memiliki Guru BK pembimbing yang bertanggung jawab
                </p>
            </div>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="table" style="width: 100%; border-collapse: collapse;">
                    <thead class="table-thead-navy">
                        <tr>
                            <th style="padding: 13px 18px;">Kelas / Rombel</th>
                            <th style="padding: 13px 18px; text-align: center;">Tingkat</th>
                            <th style="padding: 13px 18px;">Jurusan</th>
                            <th style="padding: 13px 18px; text-align: center;">Total Siswa</th>
                            <th style="padding: 13px 18px; text-align: center;">Siswa Ditugaskan</th>
                            <th style="padding: 13px 18px;">Guru BK Penanggung Jawab</th>
                            <th style="padding: 13px 18px; text-align: right;">Aksi Cepat Kelas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allClassesOverview as $ov)
                            @php
                                $cls = $ov['class'];
                                $counselor = $ov['counselor'];
                                $isFull = $ov['is_fully_assigned'];
                            @endphp
                            <tr>
                                <td>
                                    <span style="font-weight: 700; font-size: 14px; color: var(--color-navy);">
                                        {{ $cls->name }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-info" style="font-size: 11px;">
                                        Kelas {{ $cls->grade }}
                                    </span>
                                </td>
                                <td>
                                    <span style="font-size: 12.5px; color: #334155; font-weight: 600;">
                                        {{ $cls->major }}
                                    </span>
                                </td>
                                <td>
                                    <span style="font-weight: 700; font-size: 13.5px;">{{ $ov['total_students'] }}</span> Siswa
                                </td>
                                <td>
                                    @if($isFull)
                                        <span class="badge badge-success" style="font-size: 11.5px; font-weight: 700;">
                                            100% ({{ $ov['assigned_count'] }}/{{ $ov['total_students'] }})
                                        </span>
                                    @elseif($ov['assigned_count'] > 0)
                                        <span class="badge badge-warning" style="font-size: 11.5px; font-weight: 700;">
                                            Sebagian ({{ $ov['assigned_count'] }}/{{ $ov['total_students'] }})
                                        </span>
                                    @else
                                        <span class="badge badge-danger" style="font-size: 11.5px; font-weight: 700;">
                                            0% Belum Ditugaskan
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($counselor)
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <img src="{{ $counselor->avatar_url }}" alt="{{ $counselor->name }}" style="width: 24px; height: 24px; border-radius: 50%; object-fit: cover;" />
                                            <span style="font-weight: 700; font-size: 12.5px; color: var(--color-navy);">
                                                {{ $counselor->name }}
                                            </span>
                                        </div>
                                    @else
                                        <span style="font-size: 12px; color: #94A3B8; font-style: italic;">
                                            Belum ditentukan
                                        </span>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="assignWholeClassPrompt('{{ $cls->id }}', '{{ addslashes($cls->name) }}')">
                                        Tugaskan Seluruh Kelas
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 24px;">Belum ada data kelas yang terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- TAB 3: BEBAN KERJA TIAP GURU BK -->
<!-- ========================================== -->
<div id="tabContentGuru" style="display: none;">
    <div class="card" style="margin-bottom: 24px;">
        <div class="card-header-clean">
            <div>
                <h2 class="card-title-clean">Distribusi Beban Bimbingan Siswa per Guru BK</h2>
                <p class="card-subtitle-clean">
                    Pastikan beban siswa terbagi secara proporsional dan merata di antara seluruh Guru BK
                </p>
            </div>
            <a href="{{ route('guru.koordinator.guru_bk.create') }}" class="btn btn-primary btn-sm">
                Tambah Akun Guru BK
            </a>
        </div>
        <div class="card-body">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 16px;">
                @forelse($counselorWorkloads as $cw)
                    @php $guru = $cw['guru']; @endphp
                    <div style="border: 1px solid var(--color-border); border-radius: 12px; padding: 18px; background: #FFFFFF; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <img src="{{ $guru->avatar_url }}" alt="{{ $guru->name }}" style="width: 44px; height: 44px; border-radius: 50%; object-fit: cover;" />
                                    <div>
                                        <div style="font-weight: 700; color: var(--color-navy); font-size: 14px;">{{ $guru->name }}</div>
                                        <div style="font-size: 11.5px; color: var(--color-text-muted);">{{ $guru->nip ?: 'NIP belum diisi' }}</div>
                                    </div>
                                </div>
                                <span class="badge {{ $guru->is_active ? 'badge-success' : 'badge-danger' }}" style="font-size: 11px;">
                                    {{ $guru->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>

                            <!-- Beban Siswa -->
                            <div style="background: #F8FAFD; border-radius: 8px; padding: 10px 14px; margin-bottom: 12px; display: flex; align-items: center; justify-content: space-between;">
                                <span style="font-size: 12.5px; color: var(--color-text-muted); font-weight: 600;">Siswa yang Ditangani:</span>
                                <span style="font-size: 16px; font-weight: 800; color: var(--color-primary);">{{ $cw['student_count'] }} Siswa</span>
                            </div>

                            <!-- Kelas yang Ditangani -->
                            <div style="margin-bottom: 12px;">
                                <div style="font-size: 11.5px; font-weight: 700; color: var(--color-navy); margin-bottom: 6px;">Rombel Binaan:</div>
                                <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                    @forelse($cw['classes'] as $cls)
                                        <span class="badge badge-info" style="font-size: 11px;">{{ $cls->name }}</span>
                                    @empty
                                        <span style="font-size: 11.5px; color: #94A3B8; font-style: italic;">Belum ada kelas</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <div style="display: flex; gap: 8px; margin-top: 10px; border-top: 1px solid var(--color-border); padding-top: 12px;">
                            <a href="{{ route('guru.koordinator.guru_bk.show', $guru->id) }}" class="btn btn-secondary btn-sm" style="flex: 1; text-align: center;">
                                Detail Guru
                            </a>
                            <button type="button" class="btn btn-primary btn-sm" onclick="openReassignModal('{{ $guru->id }}')" style="flex: 1; text-align: center;">
                                Alihkan Tugas
                            </button>
                        </div>
                    </div>
                @empty
                    <p style="color: var(--color-text-muted);">Belum ada Guru BK terdaftar.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL 1: TUGASKAN TUNGGAL SISWA -->
<!-- ========================================== -->
<div id="singleAssignModal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="singleAssignModalTitle" onclick="handleSingleAssignBackdropClick(event)">
    <div class="modal-dialog" style="max-width: 480px;">
        <div class="modal-body">
            <div class="modal-icon-badge primary">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <polyline points="16 11 18 13 22 9"></polyline>
                </svg>
            </div>
            <h3 id="singleAssignModalTitle" class="modal-title">Penugasan Guru BK</h3>
            <p class="modal-desc">
                Tetapkan Guru BK yang bertanggung jawab menangani siswa: <strong id="modalSingleStudentName" style="color: var(--color-navy);">-</strong>
            </p>
            <form id="singleAssignForm" method="POST" action="{{ route('guru.penugasan.assign') }}" style="margin-top: 14px; text-align: left;">
                @csrf
                <input type="hidden" name="student_ids[]" id="modalSingleStudentId" value="" />
                <div class="form-group" style="margin-bottom: 12px;">
                    <label for="modalSingleCounselorSelect" class="form-label">Pilih Guru BK Penanggung Jawab:</label>
                    <select name="counselor_id" id="modalSingleCounselorSelect" class="form-select" required>
                        <option value="">-- Pilih Guru BK --</option>
                        @foreach($counselors as $guru)
                            <option value="{{ $guru->id }}">{{ $guru->name }} ({{ $guru->student_assignments_count }} Siswa)</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="modalSingleNotes" class="form-label">Catatan Penugasan (Opsional):</label>
                    <textarea name="notes" id="modalSingleNotes" class="form-control" rows="2" placeholder="Contoh: Fokus pendampingan akademik semester genap..."></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeSingleAssignModal()">Batal</button>
            <button type="button" class="btn btn-primary" onclick="submitSingleAssignForm()">Simpan Penugasan</button>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL 2: PEMINDAHAN TUGAS MASSAL ANTAR GURU BK -->
<!-- ========================================== -->
<div id="reassignModal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="reassignModalTitle" onclick="handleReassignBackdropClick(event)">
    <div class="modal-dialog" style="max-width: 520px;">
        <div class="modal-body">
            <div class="modal-icon-badge warning">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="17 1 21 5 17 9"></polyline>
                    <path d="M3 11V9a4 4 0 0 1 4-4h14"></path>
                    <polyline points="7 23 3 19 7 15"></polyline>
                    <path d="M21 13v2a4 4 0 0 1-4 4H3"></path>
                </svg>
            </div>
            <h3 id="reassignModalTitle" class="modal-title">Pengalihan Tugas Massal</h3>
            <p class="modal-desc">
                Fitur ini akan memindahkan <strong>seluruh siswa binaan</strong> dari Guru BK asal ke Guru BK penerima baru secara sekaligus. Hak akses Guru BK lama otomatis dicabut.
            </p>
            <form id="reassignForm" method="POST" action="{{ route('guru.penugasan.reassign_massal') }}" style="margin-top: 14px; text-align: left;">
                @csrf
                <div class="form-group" style="margin-bottom: 12px;">
                    <label for="reassignFromSelect" class="form-label">Dari Guru BK Asal:</label>
                    <select name="from_counselor_id" id="reassignFromSelect" class="form-select" required>
                        <option value="">-- Pilih Guru BK Asal --</option>
                        @foreach($counselors as $guru)
                            <option value="{{ $guru->id }}">{{ $guru->name }} ({{ $guru->student_assignments_count }} Siswa)</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 12px;">
                    <label for="reassignToSelect" class="form-label">Ke Guru BK Penerima Tugas Baru:</label>
                    <select name="to_counselor_id" id="reassignToSelect" class="form-select" required>
                        <option value="">-- Pilih Guru BK Penerima --</option>
                        @foreach($counselors as $guru)
                            <option value="{{ $guru->id }}">{{ $guru->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="reassignNotes" class="form-label">Alasan / Catatan Pemindahan:</label>
                    <textarea name="notes" id="reassignNotes" class="form-control" rows="2" placeholder="Contoh: Guru asal sedang cuti belajar / rotasi semester..."></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeReassignModal()">Batal</button>
            <button type="button" class="btn btn-primary" onclick="submitReassignForm()">Alihkan Seluruh Tugas Sekarang</button>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL 3: KONFIRMASI LEPAS PENUGASAN (PENGGANTI CONFIRM NATIVE) -->
<!-- ========================================== -->
<div id="unassignModal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="unassignModalTitle" onclick="handleUnassignBackdropClick(event)">
    <div class="modal-dialog" style="max-width: 480px;">
        <div class="modal-body">
            <div class="modal-icon-badge danger">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="15" y1="9" x2="9" y2="15"></line>
                    <line x1="9" y1="9" x2="15" y2="15"></line>
                </svg>
            </div>
            <h3 id="unassignModalTitle" class="modal-title">Konfirmasi Lepas Penugasan</h3>
            <p id="unassignModalDesc" class="modal-desc">
                Apakah Anda yakin ingin melepas penugasan Guru BK untuk siswa ini?
            </p>
            <div style="background-color: var(--color-surface-hover); border: 1px solid var(--color-border); border-radius: var(--radius-sm); padding: 12px 14px; margin-top: 14px; font-size: 12px; color: var(--color-text-muted); display: flex; align-items: flex-start; gap: 8px; line-height: 1.45; text-align: left;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0; margin-top: 2px; color: var(--color-warning);">
                    <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
                <span>Hak akses Guru BK penanggung jawab saat ini akan langsung dicabut. Siswa akan kembali berstatus <strong>Belum Ditugaskan</strong>.</span>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeUnassignModal()">Batal</button>
            <button type="button" id="btnConfirmExecuteUnassign" class="btn btn-danger" onclick="executeConfirmedUnassign()">Ya, Lepas Penugasan</button>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL 4: KONFIRMASI SIMPAN PENUGASAN MASSAL -->
<!-- ========================================== -->
<div id="batchAssignModal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="batchAssignModalTitle" onclick="handleBatchAssignBackdropClick(event)">
    <div class="modal-dialog" style="max-width: 490px;">
        <div class="modal-body">
            <div class="modal-icon-badge primary">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <polyline points="16 11 18 13 22 9"></polyline>
                </svg>
            </div>
            <h3 id="batchAssignModalTitle" class="modal-title">Konfirmasi Penetapan Tugas</h3>
            <p id="batchAssignModalDesc" class="modal-desc">
                Anda akan menetapkan penugasan bimbingan untuk siswa terpilih.
            </p>
            <div style="background-color: var(--color-surface-hover); border: 1px solid var(--color-border); border-radius: var(--radius-sm); padding: 12px 14px; margin-top: 14px; font-size: 12px; color: var(--color-text-muted); display: flex; align-items: flex-start; gap: 8px; line-height: 1.45; text-align: left;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0; margin-top: 2px; color: var(--color-primary);">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
                <span>Sistem secara otomatis membuka hak akses siswa kepada Guru BK terpilih. Siswa yang sebelumnya ditangani oleh Guru BK lain akan otomatis diperbarui penanggung jawabnya.</span>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeBatchAssignModal()">Batal</button>
            <button type="button" class="btn btn-primary" onclick="executeConfirmedBatchAssign()">Ya, Tetapkan Penugasan</button>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL 5: PERINGATAN / VALIDASI (PENGGANTI ALERT NATIVE) -->
<!-- ========================================== -->
<div id="validationAlertModal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="valAlertModalTitle" onclick="handleValAlertBackdropClick(event)">
    <div class="modal-dialog" style="max-width: 440px;">
        <div class="modal-body">
            <div class="modal-icon-badge warning">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
            </div>
            <h3 id="valAlertModalTitle" class="modal-title">Perhatian</h3>
            <p id="valAlertModalDesc" class="modal-desc">
                Pemberitahuan sistem...
            </p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="closeValidationAlertModal()">Mengerti</button>
        </div>
    </div>
</div>

<!-- Form tersembunyi untuk Unassign -->
<form id="hiddenUnassignForm" method="POST" action="{{ route('guru.penugasan.unassign') }}" style="display:none;">
    @csrf
    <div id="hiddenUnassignInputs"></div>
</form>

<script>
// State penampung unassign
let unassignPendingIds = [];

function switchPenugasanTab(tab) {
    const tabForm = document.getElementById('tabContentForm');
    const tabKelas = document.getElementById('tabContentKelas');
    const tabGuru = document.getElementById('tabContentGuru');
    const btnForm = document.getElementById('tabBtnForm');
    const btnKelas = document.getElementById('tabBtnKelas');
    const btnGuru = document.getElementById('tabBtnGuru');

    tabForm.style.display = tab === 'form' ? 'block' : 'none';
    tabKelas.style.display = tab === 'kelas' ? 'block' : 'none';
    tabGuru.style.display = tab === 'guru' ? 'block' : 'none';

    btnForm.style.background = tab === 'form' ? 'var(--color-primary)' : 'transparent';
    btnForm.style.color = tab === 'form' ? '#FFFFFF' : 'var(--color-text-muted)';
    btnKelas.style.background = tab === 'kelas' ? 'var(--color-primary)' : 'transparent';
    btnKelas.style.color = tab === 'kelas' ? '#FFFFFF' : 'var(--color-text-muted)';
    btnGuru.style.background = tab === 'guru' ? 'var(--color-primary)' : 'transparent';
    btnGuru.style.color = tab === 'guru' ? '#FFFFFF' : 'var(--color-text-muted)';
}

function toggleSelectAll(masterCheckbox) {
    const checkboxes = document.querySelectorAll('.student-checkbox');
    checkboxes.forEach(cb => cb.checked = masterCheckbox.checked);
    updateSelectedCount();
}

function updateSelectedCount() {
    const checked = document.querySelectorAll('.student-checkbox:checked');
    const badge = document.getElementById('selectedCountBadge');
    if (checked.length > 0) {
        badge.style.display = 'inline-block';
        badge.textContent = checked.length + ' siswa terpilih';
    } else {
        badge.style.display = 'none';
    }
}

// --------------------------------------------------------------------------
// MODAL VALIDATION ALERT (PENGGANTI ALERT NATIVE)
// --------------------------------------------------------------------------
function showValidationAlert(message, title = 'Perhatian Validasi Penugasan') {
    document.getElementById('valAlertModalTitle').textContent = title;
    document.getElementById('valAlertModalDesc').textContent = message;
    const modal = document.getElementById('validationAlertModal');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeValidationAlertModal() {
    const modal = document.getElementById('validationAlertModal');
    modal.classList.remove('active');
    document.body.style.overflow = '';
}

function handleValAlertBackdropClick(e) {
    if (e.target.id === 'validationAlertModal') {
        closeValidationAlertModal();
    }
}

// --------------------------------------------------------------------------
// MODAL SINGLE ASSIGN
// --------------------------------------------------------------------------
function openSingleAssignModal(studentId, studentName, counselorId) {
    document.getElementById('modalSingleStudentId').value = studentId;
    document.getElementById('modalSingleStudentName').textContent = studentName;
    if (counselorId) {
        document.getElementById('modalSingleCounselorSelect').value = counselorId;
    } else {
        document.getElementById('modalSingleCounselorSelect').value = '';
    }
    const modal = document.getElementById('singleAssignModal');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeSingleAssignModal() {
    const modal = document.getElementById('singleAssignModal');
    modal.classList.remove('active');
    document.body.style.overflow = '';
}

function handleSingleAssignBackdropClick(e) {
    if (e.target.id === 'singleAssignModal') {
        closeSingleAssignModal();
    }
}

function submitSingleAssignForm() {
    const select = document.getElementById('modalSingleCounselorSelect');
    if (!select.value) {
        showValidationAlert('Silakan pilih Guru BK penanggung jawab terlebih dahulu sebelum menyimpan.', 'Pilihan Guru BK Belum Dipilih');
        return;
    }
    document.getElementById('singleAssignForm').submit();
}

// --------------------------------------------------------------------------
// MODAL REASSIGN MASSAL
// --------------------------------------------------------------------------
function openReassignModal(fromCounselorId = null) {
    if (fromCounselorId) {
        document.getElementById('reassignFromSelect').value = fromCounselorId;
    }
    const modal = document.getElementById('reassignModal');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeReassignModal() {
    const modal = document.getElementById('reassignModal');
    modal.classList.remove('active');
    document.body.style.overflow = '';
}

function handleReassignBackdropClick(e) {
    if (e.target.id === 'reassignModal') {
        closeReassignModal();
    }
}

function submitReassignForm() {
    const fromSelect = document.getElementById('reassignFromSelect');
    const toSelect = document.getElementById('reassignToSelect');

    if (!fromSelect.value) {
        showValidationAlert('Silakan pilih Guru BK asal terlebih dahulu.', 'Guru BK Asal Belum Dipilih');
        return;
    }
    if (!toSelect.value) {
        showValidationAlert('Silakan pilih Guru BK penerima tugas baru.', 'Guru BK Penerima Belum Dipilih');
        return;
    }
    if (fromSelect.value === toSelect.value) {
        showValidationAlert('Guru BK asal dan Guru BK penerima tidak boleh sama. Silakan pilih Guru BK yang berbeda.', 'Pemilihan Guru Tidak Valid');
        return;
    }
    document.getElementById('reassignForm').submit();
}

// --------------------------------------------------------------------------
// MODAL UNASSIGN (LEPAS TUGAS)
// --------------------------------------------------------------------------
function handleSingleUnassignClick(studentId, studentName) {
    unassignPendingIds = [studentId];
    document.getElementById('unassignModalTitle').textContent = 'Lepas Penugasan Siswa';
    document.getElementById('unassignModalDesc').innerHTML = 'Apakah Anda yakin ingin melepas penugasan Guru BK untuk siswa <strong>' + studentName + '</strong>?';
    const modal = document.getElementById('unassignModal');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function handleBatchUnassignClick() {
    const checked = document.querySelectorAll('.student-checkbox:checked');
    if (checked.length === 0) {
        showValidationAlert('Silakan centang minimal satu siswa pada tabel terlebih dahulu untuk melepas penugasan.', 'Belum Ada Siswa Dipilih');
        return;
    }

    unassignPendingIds = Array.from(checked).map(cb => cb.value);
    document.getElementById('unassignModalTitle').textContent = 'Lepas Penugasan ' + checked.length + ' Siswa';
    document.getElementById('unassignModalDesc').innerHTML = 'Apakah Anda yakin ingin melepas penugasan Guru BK untuk <strong>' + checked.length + ' siswa terpilih</strong>?';
    const modal = document.getElementById('unassignModal');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeUnassignModal() {
    const modal = document.getElementById('unassignModal');
    modal.classList.remove('active');
    document.body.style.overflow = '';
    unassignPendingIds = [];
}

function handleUnassignBackdropClick(e) {
    if (e.target.id === 'unassignModal') {
        closeUnassignModal();
    }
}

function executeConfirmedUnassign() {
    if (unassignPendingIds.length === 0) return;

    const container = document.getElementById('hiddenUnassignInputs');
    container.innerHTML = '';
    unassignPendingIds.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'student_ids[]';
        input.value = id;
        container.appendChild(input);
    });
    document.getElementById('hiddenUnassignForm').submit();
}

// --------------------------------------------------------------------------
// MODAL BATCH ASSIGN (SIMPAN PENUGASAN MASSAL)
// --------------------------------------------------------------------------
function handleBatchAssignClick() {
    const select = document.getElementById('targetCounselorSelect');
    if (!select.value) {
        showValidationAlert('Silakan pilih Guru BK Penanggung Jawab terlebih dahulu dari menu pilihan di samping tombol simpan.', 'Guru BK Belum Dipilih');
        return;
    }

    const checked = document.querySelectorAll('.student-checkbox:checked');
    if (checked.length === 0) {
        showValidationAlert('Silakan centang minimal satu siswa pada tabel untuk menetapkan penugasan ke Guru BK terpilih.', 'Belum Ada Siswa Dipilih');
        return;
    }

    const counselorName = select.options[select.selectedIndex].text;
    document.getElementById('batchAssignModalDesc').innerHTML = 'Anda akan menetapkan penugasan bimbingan untuk <strong>' + checked.length + ' siswa terpilih</strong> kepada <strong>' + counselorName + '</strong>.';

    const modal = document.getElementById('batchAssignModal');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeBatchAssignModal() {
    const modal = document.getElementById('batchAssignModal');
    modal.classList.remove('active');
    document.body.style.overflow = '';
}

function handleBatchAssignBackdropClick(e) {
    if (e.target.id === 'batchAssignModal') {
        closeBatchAssignModal();
    }
}

function executeConfirmedBatchAssign() {
    document.getElementById('assignSubmitForm').submit();
}

function assignWholeClassPrompt(classId, className) {
    const url = new URL(window.location.href);
    url.searchParams.set('class_id', classId);
    url.searchParams.delete('grade');
    url.searchParams.delete('major');
    window.location.href = url.toString();
}

// Close modals on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeSingleAssignModal();
        closeReassignModal();
        closeUnassignModal();
        closeBatchAssignModal();
        closeValidationAlertModal();
    }
});
</script>
@endsection
