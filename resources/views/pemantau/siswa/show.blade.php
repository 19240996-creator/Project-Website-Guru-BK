@extends('layouts.pemantau')

@section('title', 'Detail Profil Siswa - Ruang BK Digital')

@section('content')
<!-- Page Header Bar -->
<div class="page-header-bar">
    <div>
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
            <a href="{{ route($rolePrefix . '.siswa.index') }}" style="font-size: 12px; color: var(--color-primary); text-decoration: none; font-weight: 600;">
                &larr; Kembali ke Data Siswa
            </a>
            <span style="font-size: 12px; color: var(--color-text-subtle);">/</span>
            <span style="font-size: 12px; color: var(--color-text-muted);">
                Profil Siswa
            </span>
        </div>
        <h1 class="page-header-title">
            {{ $student->name }}
        </h1>
    </div>
    <div style="display: flex; align-items: center; gap: 10px;">
        <span class="badge {{ $student->status === 'aktif' ? 'badge-success' : 'badge-secondary' }}" style="font-size: 12px; padding: 6px 12px;">
            Status: {{ ucfirst((string) $student->status) }}
        </span>
    </div>
</div>

<!-- Profil Card & Informasi Akademik -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px; margin-bottom: 24px;">
    <!-- Card Identitas Utama -->
    <div class="card">
        <div class="card-body" style="padding: 24px; text-align: center;">
            <img src="{{ $student->avatar_url }}" alt="{{ $student->name }}" style="width: 96px; height: 96px; border-radius: 50%; object-fit: cover; border: 3px solid var(--color-primary-light); box-shadow: var(--shadow-sm); margin-bottom: 12px;" />
            <h2 style="font-size: 18px; font-weight: 800; color: var(--color-navy); margin-bottom: 4px;">{{ $student->name }}</h2>
            <div style="font-size: 13px; color: var(--color-text-muted); margin-bottom: 16px;">
                NISN: <span style="font-family: monospace; font-weight: 700; color: var(--color-navy);">{{ $student->nisn ?? '-' }}</span> | NIS: {{ $student->nis ?? '-' }}
            </div>

            <div style="display: flex; justify-content: center; gap: 8px; flex-wrap: wrap; margin-bottom: 20px;">
                <span class="badge badge-primary" style="font-size: 12px; padding: 5px 12px;">
                    {{ $student->studentClass->name ?? 'Belum ada kelas' }}
                </span>
                <span class="badge badge-info" style="font-size: 12px; padding: 5px 12px;">
                    {{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                </span>
            </div>

            <hr style="border: none; border-top: 1px solid var(--color-border); margin: 16px 0;" />

            <div style="text-align: left; display: flex; flex-direction: column; gap: 12px; font-size: 13px;">
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--color-text-muted);">Tempat, Tanggal Lahir</span>
                    <span style="font-weight: 600; color: var(--color-navy);">
                        {{ $student->birth_place ?? '-' }}, {{ $student->birth_date ? $student->birth_date->translatedFormat('d F Y') : '-' }}
                    </span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--color-text-muted);">Nomor Handphone</span>
                    <span style="font-weight: 600; color: var(--color-navy);">{{ $student->phone ?? '-' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--color-text-muted);">Alamat Domisili</span>
                    <span style="font-weight: 600; color: var(--color-navy); text-align: right; max-width: 60%;">
                        {{ $student->address ?? '-' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Pembina BK & Rombel Sekolah -->
    <div style="display: flex; flex-direction: column; gap: 20px;">
        <div class="card">
            <div class="card-header" style="padding: 16px 20px;">
                <h3 style="font-size: 15px; font-weight: 700; color: var(--color-navy); margin: 0;">Penugasan Pembina BK</h3>
            </div>
            <div class="card-body" style="padding: 20px;">
                @if($student->counselor)
                    <div style="display: flex; align-items: center; gap: 14px;">
                        <img src="{{ $student->counselor->avatar_url }}" alt="{{ $student->counselor->name }}" style="width: 46px; height: 46px; border-radius: 50%; object-fit: cover; border: 1px solid var(--color-border);" />
                        <div>
                            <div style="font-size: 14.5px; font-weight: 700; color: var(--color-navy);">{{ $student->counselor->name }}</div>
                            <div style="font-size: 12px; color: var(--color-text-muted);">Guru BK Penanggung Jawab Resmi</div>
                            <div style="font-size: 12px; color: var(--color-primary); font-weight: 600; margin-top: 2px;">
                                NIP: {{ $student->counselor->nip ?? '-' }} | Telp: {{ $student->counselor->phone ?? '-' }}
                            </div>
                        </div>
                    </div>
                @else
                    <div style="padding: 12px 16px; background: var(--color-surface-subtle); border-radius: 8px; color: var(--color-text-muted); font-size: 13px;">
                        Siswa ini belum memiliki penugasan Guru BK khusus. Koordinasi dilakukan oleh Guru Koordinator BK.
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header" style="padding: 16px 20px;">
                <h3 style="font-size: 15px; font-weight: 700; color: var(--color-navy); margin: 0;">Data Orang Tua / Wali</h3>
            </div>
            <div class="card-body" style="padding: 20px; display: flex; flex-direction: column; gap: 12px; font-size: 13px;">
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--color-text-muted);">Nama Orang Tua / Wali</span>
                    <span style="font-weight: 600; color: var(--color-navy);">{{ $student->parent_name ?? '-' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--color-text-muted);">Nomor Telepon Orang Tua</span>
                    <span style="font-weight: 600; color: var(--color-navy);">{{ $student->parent_phone ?? '-' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--color-text-muted);">Pekerjaan Orang Tua</span>
                    <span style="font-weight: 600; color: var(--color-navy);">{{ $student->parent_job ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
