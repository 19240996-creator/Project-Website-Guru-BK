@extends('layouts.app')

@section('title', 'Manajemen Akun Guru BK - Ruang BK')
@section('header_title', 'Manajemen Akun Guru Bimbingan Konseling')

@section('content')
<!-- Page Header -->
<div class="page-header-bar">
    <div>
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
            <span class="badge badge-primary" style="font-size: 11px; padding: 3px 8px; font-weight: 700;">
                KOORDINATOR BK
            </span>
            <span style="font-size: 12px; color: var(--color-text-muted);">
                Pengelolaan Pendidik Bimbingan Konseling
            </span>
        </div>
        <h1 class="page-header-title">Daftar Guru Bimbingan Konseling (Guru BK)</h1>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('guru.penugasan.index') }}" class="btn btn-secondary btn-sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            <span>Menu Pembagian Tugas</span>
        </a>
        <a href="{{ route('guru.koordinator.guru_bk.create') }}" class="btn btn-primary btn-sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            <span>Tambah Guru BK Baru</span>
        </a>
    </div>
</div>

<!-- Stat Cards -->
<div class="grid-3" style="margin-bottom: 24px;">
    <div class="stat-card-clean">
        <div>
            <div class="stat-card-top">
                <div class="stat-icon-pill blue">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                </div>
            </div>
            <div class="stat-card-label">TOTAL GURU BK TERDAFTAR</div>
            <div class="stat-value-row">
                <span class="stat-card-number">{{ $totalGuruBk }}</span>
                <span class="stat-trend-tag">{{ $activeGuruBk }} Aktif</span>
            </div>
        </div>
        <p class="stat-card-desc">Akun pendidik BK dalam platform</p>
    </div>

    <div class="stat-card-clean">
        <div>
            <div class="stat-card-top">
                <div class="stat-icon-pill green">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                </div>
            </div>
            <div class="stat-card-label">TOTAL SISWA DITUGASKAN</div>
            <div class="stat-value-row">
                <span class="stat-card-number">{{ number_format($totalAssignedStudents) }}</span>
                <span class="stat-trend-tag">Terdistribusi</span>
            </div>
        </div>
        <p class="stat-card-desc">Siswa yang terikat resmi ke Guru BK</p>
    </div>

    <div class="stat-card-clean">
        <div>
            <div class="stat-card-top">
                <div class="stat-icon-pill amber">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                </div>
            </div>
            <div class="stat-card-label">HAK AKSES DATA SISWA</div>
            <div class="stat-value-row">
                <span class="stat-card-number" style="font-size: 20px;">Terkunci</span>
                <span class="stat-trend-tag">Aman</span>
            </div>
        </div>
        <p class="stat-card-desc">Tiap Guru BK hanya dapat melihat siswa yang ditugaskan</p>
    </div>
</div>

<!-- Table Card -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header-clean">
        <div>
            <h2 class="card-title-clean">Daftar Akun Guru BK & Beban Penugasan</h2>
            <p class="card-subtitle-clean">
                Kelola kredensial akun, NIP/NUPTK, kontak, dan awasi beban tugas siswa yang dibimbing
            </p>
        </div>
        <form method="GET" action="{{ route('guru.koordinator.guru_bk.index') }}" style="display: flex; align-items: center; gap: 8px; width: 100%; max-width: 320px;">
            <div class="form-search-box form-search-box-sm">
                <span class="form-search-icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </span>
                <input type="text" name="q" class="form-search-input" placeholder="Cari nama, NIP, username..." value="{{ $search }}" onkeydown="if(event.key==='Enter'){this.form.submit();}" />
                @if($search)
                    <a href="{{ route('guru.koordinator.guru_bk.index') }}" class="form-search-clear" title="Reset pencarian">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </a>
                @endif
            </div>
        </form>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table" style="width: 100%; border-collapse: collapse;">
                <thead class="table-thead-navy">
                    <tr>
                        <th style="padding: 13px 18px;">Guru BK</th>
                        <th style="padding: 13px 18px;">NIP / NUPTK</th>
                        <th style="padding: 13px 18px;">Username / Email</th>
                        <th style="padding: 13px 18px;">No. HP</th>
                        <th style="padding: 13px 18px; text-align: center;">Beban Siswa</th>
                        <th style="padding: 13px 18px; text-align: center;">Status</th>
                        <th style="padding: 13px 18px; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($counselors as $guru)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <img src="{{ $guru->avatar_url }}" alt="{{ $guru->name }}" style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover; flex-shrink: 0;" />
                                    <div>
                                        <a href="{{ route('guru.koordinator.guru_bk.show', $guru->id) }}" style="font-weight: 700; color: var(--color-navy); text-decoration: none; font-size: 13.5px;" title="Lihat profil dan daftar siswa binaan">
                                            {{ $guru->name }}
                                        </a>
                                        <div style="font-size: 11px; color: var(--color-text-muted);">
                                            Terdaftar sejak {{ $guru->created_at ? $guru->created_at->translatedFormat('d M Y') : '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span style="font-weight: 600; font-size: 12.5px; color: #334155;">
                                    {{ $guru->nip ?: '-' }}
                                </span>
                            </td>
                            <td>
                                <div style="font-weight: 600; font-size: 12.5px; color: var(--color-navy);">{{ $guru->username }}</div>
                                <div style="font-size: 11px; color: #64748B;">{{ $guru->email }}</div>
                            </td>
                            <td>
                                <span style="font-size: 12.5px; color: #334155;">{{ $guru->phone ?: '-' }}</span>
                            </td>
                            <td>
                                <a href="{{ route('guru.koordinator.guru_bk.show', $guru->id) }}" class="badge badge-info" style="font-size: 12px; font-weight: 700; text-decoration: none; padding: 4px 10px;" title="Lihat detail siswa binaan">
                                    {{ $guru->student_assignments_count }} Siswa
                                </a>
                            </td>
                            <td>
                                @if($guru->is_active)
                                    <span class="badge badge-success" style="font-size: 11px;">Aktif</span>
                                @else
                                    <span class="badge badge-danger" style="font-size: 11px;">Nonaktif</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 6px; justify-content: flex-end;">
                                    <a href="{{ route('guru.koordinator.guru_bk.show', $guru->id) }}" class="btn btn-secondary btn-sm" title="Lihat Detail & Daftar Siswa">
                                        Detail
                                    </a>
                                    <a href="{{ route('guru.koordinator.guru_bk.edit', $guru->id) }}" class="btn btn-secondary btn-sm" title="Ubah Akun & Data">
                                        Edit
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="openDeleteGuruModal('{{ $guru->id }}', '{{ addslashes($guru->name) }}', '{{ route('guru.koordinator.guru_bk.destroy', $guru->id) }}')" title="Hapus Akun">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 32px 16px; color: var(--color-text-muted);">
                                Tidak ada akun Guru BK ditemukan. Silakan tambahkan Guru BK baru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($counselors->hasPages())
        <div style="padding: 14px 20px; border-top: 1px solid var(--color-border);">
            {{ $counselors->links() }}
        </div>
    @endif
</div>

<!-- Modal Konfirmasi Hapus Akun Guru BK (Luxury EdTech Standard) -->
<div id="deleteGuruModal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="deleteGuruModalTitle" onclick="handleDeleteGuruBackdropClick(event)">
    <div class="modal-dialog" style="max-width: 480px;">
        <div class="modal-body">
            <div class="modal-icon-badge danger">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    <line x1="10" y1="11" x2="10" y2="17"></line>
                    <line x1="14" y1="11" x2="14" y2="17"></line>
                </svg>
            </div>
            <h3 id="deleteGuruModalTitle" class="modal-title">Konfirmasi Hapus Akun Guru BK</h3>
            <p class="modal-desc">
                Apakah Anda yakin ingin menghapus akun Guru BK <strong id="deleteGuruNameText" style="color: var(--color-navy);">-</strong>?
            </p>
            <div style="background-color: var(--color-danger-bg); border: 1px solid var(--color-danger-border); border-radius: var(--radius-sm); padding: 12px 14px; margin-top: 14px; font-size: 12px; color: var(--color-danger); display: flex; align-items: flex-start; gap: 8px; line-height: 1.45; text-align: left;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0; margin-top: 2px;">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <span>Tindakan ini permanen. Pastikan seluruh siswa binaan guru ini telah dialihkan atau dilepas sebelum menghapus akun.</span>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeDeleteGuruModal()">Batal</button>
            <form id="deleteGuruFormTarget" method="POST" action="" style="display:inline; margin:0;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Ya, Hapus Akun</button>
            </form>
        </div>
    </div>
</div>

<script>
function openDeleteGuruModal(guruId, guruName, actionUrl) {
    document.getElementById('deleteGuruNameText').textContent = guruName;
    document.getElementById('deleteGuruFormTarget').action = actionUrl;
    const modal = document.getElementById('deleteGuruModal');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeDeleteGuruModal() {
    const modal = document.getElementById('deleteGuruModal');
    modal.classList.remove('active');
    document.body.style.overflow = '';
}

function handleDeleteGuruBackdropClick(e) {
    if (e.target.id === 'deleteGuruModal') {
        closeDeleteGuruModal();
    }
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeleteGuruModal();
    }
});
</script>
@endsection
