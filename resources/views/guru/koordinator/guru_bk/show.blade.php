@extends('layouts.app')

@section('title', 'Detail Guru BK: ' . $counselor->name . ' - Ruang BK')
@section('header_title', 'Detail Profil & Penugasan Guru BK')

@section('content')
<!-- Page Header -->
<div class="page-header-bar">
    <div>
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
            <a href="{{ route('guru.koordinator.guru_bk.index') }}" style="color: var(--color-primary); text-decoration: none; font-size: 12px; font-weight: 700;">
                &larr; Kembali ke Daftar Guru BK
            </a>
        </div>
        <h1 class="page-header-title">Profil & Daftar Penugasan: {{ $counselor->name }}</h1>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('guru.penugasan.index') }}" class="btn btn-primary btn-sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            <span>Atur Penugasan Siswa</span>
        </a>
        <a href="{{ route('guru.koordinator.guru_bk.edit', $counselor->id) }}" class="btn btn-secondary btn-sm">
            Ubah Data Akun
        </a>
    </div>
</div>

<!-- Guru BK Profile Banner -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card-body" style="padding: 24px;">
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 24px; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 18px;">
                <img src="{{ $counselor->avatar_url }}" alt="{{ $counselor->name }}" style="width: 72px; height: 72px; border-radius: 50%; object-fit: cover; border: 3px solid #EDF2FE;" />
                <div>
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                        <h2 style="font-size: 18px; font-weight: 800; color: var(--color-navy); margin: 0;">{{ $counselor->name }}</h2>
                        @if($counselor->is_active)
                            <span class="badge badge-success" style="font-size: 11px;">Aktif</span>
                        @else
                            <span class="badge badge-danger" style="font-size: 11px;">Nonaktif</span>
                        @endif
                    </div>
                    <div style="font-size: 13px; color: var(--color-text-muted); display: flex; gap: 16px; flex-wrap: wrap;">
                        <span>NIP: <strong>{{ $counselor->nip ?: '-' }}</strong></span>
                        <span>Username: <strong>{{ $counselor->username }}</strong></span>
                        <span>Email: <strong>{{ $counselor->email }}</strong></span>
                        <span>No. HP: <strong>{{ $counselor->phone ?: '-' }}</strong></span>
                    </div>
                </div>
            </div>

            <!-- Mini Metrics -->
            <div style="display: flex; gap: 16px;">
                <div style="background: #F8FAFD; border: 1px solid var(--color-border); border-radius: 10px; padding: 12px 18px; text-align: center;">
                    <div style="font-size: 20px; font-weight: 800; color: var(--color-primary);">{{ $assignedStudents->total() }}</div>
                    <div style="font-size: 11px; font-weight: 700; color: var(--color-text-muted); text-transform: uppercase;">Siswa Binaan</div>
                </div>
                <div style="background: #F8FAFD; border: 1px solid var(--color-border); border-radius: 10px; padding: 12px 18px; text-align: center;">
                    <div style="font-size: 20px; font-weight: 800; color: var(--color-navy);">{{ count($assignedClasses) }}</div>
                    <div style="font-size: 11px; font-weight: 700; color: var(--color-text-muted); text-transform: uppercase;">Rombel Binaan</div>
                </div>
                <div style="background: #F8FAFD; border: 1px solid var(--color-border); border-radius: 10px; padding: 12px 18px; text-align: center;">
                    <div style="font-size: 20px; font-weight: 800; color: #0D9488;">{{ $counselingCount }}</div>
                    <div style="font-size: 11px; font-weight: 700; color: var(--color-text-muted); text-transform: uppercase;">Sesi Konseling</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Daftar Rombel / Kelas yang Ditugaskan -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header-clean">
        <div>
            <h2 class="card-title-clean">Kelas & Jurusan yang Ditugaskan</h2>
            <p class="card-subtitle-clean">
                Rombongan belajar yang siswa-siswinya dibimbing oleh {{ $counselor->name }}
            </p>
        </div>
    </div>
    <div class="card-body">
        @if(count($assignedClasses) > 0)
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px;">
                @foreach($assignedClasses as $cls)
                    <div style="border: 1px solid var(--color-border); border-radius: 8px; padding: 12px 16px; background: #FFFFFF; display: flex; align-items: center; justify-content: space-between;">
                        <div>
                            <span class="badge badge-info" style="font-size: 12px; font-weight: 700;">{{ $cls->name }}</span>
                            <div style="font-size: 11.5px; color: var(--color-text-muted); margin-top: 4px;">{{ $cls->major }}</div>
                        </div>
                        <span style="font-size: 11.5px; font-weight: 700; color: var(--color-navy);">Kelas {{ $cls->grade }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <div style="text-align: center; padding: 20px; color: var(--color-text-muted);">
                Guru BK ini belum memiliki kelas binaan resmi. Silakan bagikan tugas melalui menu <a href="{{ route('guru.penugasan.index') }}" style="color: var(--color-primary); font-weight: 700;">Pembagian Tugas</a>.
            </div>
        @endif
    </div>
</div>

<!-- Tabel Rincian Siswa yang Ditugaskan -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header-clean">
        <div>
            <h2 class="card-title-clean">Daftar Seluruh Siswa Binaan ({{ $assignedStudents->total() }} Siswa)</h2>
            <p class="card-subtitle-clean">
                Hanya siswa-siswa ini yang dapat diakses, dilihat, dan dikelola oleh {{ $counselor->name }}
            </p>
        </div>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table" style="width: 100%; border-collapse: collapse;">
                <thead class="table-thead-navy">
                    <tr>
                        <th style="padding: 13px 18px;">Nama Siswa</th>
                        <th style="padding: 13px 18px;">NISN / NIS</th>
                        <th style="padding: 13px 18px;">Kelas & Jurusan</th>
                        <th style="padding: 13px 18px; text-align: center;">Tingkat Perhatian</th>
                        <th style="padding: 13px 18px; text-align: center;">Status</th>
                        <th style="padding: 13px 18px; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignedStudents as $st)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <img src="{{ $st->avatar_url }}" alt="{{ $st->name }}" style="width: 34px; height: 34px; border-radius: 50%; object-fit: cover;" />
                                    <div>
                                        <a href="{{ route('guru.siswa.show', $st->id) }}" style="font-weight: 700; color: var(--color-navy); text-decoration: none;">
                                            {{ $st->name }}
                                        </a>
                                        <div style="font-size: 11px; color: var(--color-text-muted);">
                                            {{ $st->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span style="font-weight: 700; font-size: 12.5px;">{{ $st->nisn }}</span>
                                <div style="font-size: 11px; color: #64748B;">NIS: {{ $st->nis }}</div>
                            </td>
                            <td>
                                <span class="badge badge-info" style="font-size: 11px;">
                                    {{ $st->studentClass ? $st->studentClass->name : '-' }}
                                </span>
                                <div style="font-size: 11px; color: #64748B; margin-top: 2px;">
                                    {{ $st->studentClass ? $st->studentClass->major : '-' }}
                                </div>
                            </td>
                            <td>
                                @if($st->attention_level === 'segera_ditindaklanjuti')
                                    <span class="badge badge-danger" style="font-size: 11px;">Segera Ditindaklanjuti</span>
                                @elseif($st->attention_level === 'prioritas')
                                    <span class="badge badge-danger" style="font-size: 11px;">Prioritas</span>
                                @elseif($st->attention_level === 'perlu_perhatian')
                                    <span class="badge badge-warning" style="font-size: 11px;">Perlu Perhatian</span>
                                @else
                                    <span class="badge badge-success" style="font-size: 11px;">Normal</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-success" style="font-size: 11px;">{{ ucfirst($st->status) }}</span>
                            </td>
                            <td style="text-align: right;">
                                <a href="{{ route('guru.siswa.show', $st->id) }}" class="btn btn-secondary btn-sm" title="Lihat Profil 360 Siswa">
                                    Profil Siswa
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 32px 16px; color: var(--color-text-muted);">
                                Belum ada siswa yang ditugaskan kepada Guru BK ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($assignedStudents->hasPages())
        <div style="padding: 14px 20px; border-top: 1px solid var(--color-border);">
            {{ $assignedStudents->links() }}
        </div>
    @endif
</div>
@endsection
