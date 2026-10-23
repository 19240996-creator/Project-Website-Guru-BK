@extends('layouts.pemantau')

@section('title', 'Log Aktivitas Saya - Ruang BK Digital')

@section('content')
<!-- Page Header Bar -->
<div class="page-header-bar">
    <div>
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
            <span class="badge badge-primary" style="font-size: 11px; padding: 3px 8px; font-weight: 700;">
                LOG AKTIVITAS RESMI
            </span>
            <span style="font-size: 12px; color: var(--color-text-muted);">
                Riwayat Jejak Audit Khusus Akun Anda
            </span>
        </div>
        <h1 class="page-header-title">
            Log Aktivitas Akun {{ auth()->user()->role_label }}
        </h1>
    </div>
    <div class="page-header-date">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
        <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
    </div>
</div>

<!-- Privacy & Security Isolation Note (PRD Bagian 7) -->
<div class="alert alert-info" style="margin-bottom: 20px; display: flex; align-items: flex-start; gap: 12px;">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0; margin-top: 2px;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
    <div style="font-size: 13px; line-height: 1.5;">
        <strong>Kebijakan Pemisahan Log Aktivitas:</strong> Sesuai protokol keamanan dan spesifikasi PRD, halaman ini <strong>hanya menampilkan riwayat aktivitas yang dilakukan oleh akun Anda sendiri</strong> ({{ auth()->user()->name }}). Anda tidak dapat melihat log pengguna lain, dan pengguna dari role lain (Guru Koordinator BK, Guru BK, maupun akun pemantau lain) tidak memiliki akses terhadap riwayat aktivitas Anda.
    </div>
</div>

<div class="card" style="margin-bottom: 24px;">
    <div class="card-header card-header-navy" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; padding: 16px 20px;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(255, 255, 255, 0.12); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#60A5FA" stroke-width="2"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <h3 class="card-title" style="font-size: 16px; font-weight: 700; color: #FFFFFF; margin: 0 0 2px 0;">
                    Tabel Riwayat Aktivitas Akun
                </h3>
                <span style="font-size: 12.5px; color: rgba(255, 255, 255, 0.75);">
                    Rekaman log audit keamanan dan riwayat akses resmi
                </span>
            </div>
        </div>
        <span class="badge" style="background: rgba(255, 255, 255, 0.15); color: #FFFFFF; border: 1px solid rgba(255, 255, 255, 0.25); font-size: 11px; padding: 4px 10px;">
            Total: {{ $logs->total() }} Log
        </span>
    </div>

    <!-- Filter Section -->
    <div style="padding: 16px 20px; background: var(--color-surface-subtle); border-bottom: 1px solid var(--color-border);">
        <form method="GET" action="{{ route($rolePrefix . '.log.index') }}" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 1; min-width: 220px;">
                <label style="display: block; font-size: 12px; font-weight: 600; color: var(--color-text-muted); margin-bottom: 4px;">Pencarian Riwayat</label>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Kata kunci aktivitas, IP, atau deskripsi..." style="height: 38px;" />
            </div>

            <div style="width: 170px;">
                <label style="display: block; font-size: 12px; font-weight: 600; color: var(--color-text-muted); margin-bottom: 4px;">Kategori Aksi</label>
                <select name="action" class="form-control" style="height: 38px;">
                    <option value="">Semua Aksi</option>
                    @foreach($actions as $act)
                        <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>{{ $act }}</option>
                    @endforeach
                </select>
            </div>

            <div style="width: 160px;">
                <label style="display: block; font-size: 12px; font-weight: 600; color: var(--color-text-muted); margin-bottom: 4px;">Tanggal</label>
                <input type="date" name="date" value="{{ request('date') }}" class="form-control" style="height: 38px;" />
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-primary" style="height: 38px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <span>Filter</span>
                </button>
                <a href="{{ route($rolePrefix . '.log.index') }}" class="btn btn-secondary" style="height: 38px;" title="Reset Filter">
                    <span>Reset</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="table-responsive" style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead class="table-thead-navy">
                <tr>
                    <th style="width: 40px; text-align: center;">No</th>
                    <th style="width: 160px;">Waktu & Tanggal</th>
                    <th style="width: 140px;">Jenis Aksi</th>
                    <th>Deskripsi Aktivitas</th>
                    <th style="width: 120px; text-align: center;">Alamat IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $index => $log)
                    <tr>
                        <td style="text-align: center; color: var(--color-text-muted); font-size: 12px;">
                            {{ $logs->firstItem() + $index }}
                        </td>
                        <td>
                            <div style="font-weight: 600; color: var(--color-navy); font-size: 13px;">
                                {{ $log->created_at->format('d/m/Y H:i:s') }}
                            </div>
                            <div style="font-size: 11px; color: var(--color-text-muted);">
                                {{ $log->created_at->diffForHumans() }}
                            </div>
                        </td>
                        <td>
                            @php
                                $badgeClass = match ($log->action) {
                                    'LOGIN' => 'badge-success',
                                    'LOGOUT' => 'badge-secondary',
                                    'EKSPOR_SISWA' => 'badge-primary',
                                    'PERBARUI_PROFIL' => 'badge-info',
                                    default => 'badge-warning',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}" style="font-size: 11px;">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td style="font-size: 13px; color: var(--color-text-main);">
                            {{ $log->description }}
                        </td>
                        <td style="text-align: center; font-family: monospace; font-size: 12px; color: var(--color-text-muted);">
                            {{ $log->ip_address ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 36px 20px;">
                            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color: var(--color-text-subtle);"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg>
                                <span style="font-size: 14px; font-weight: 600; color: var(--color-navy);">Belum Ada Riwayat Aktivitas</span>
                                <span style="font-size: 12.5px; color: var(--color-text-muted);">Aktivitas Anda seperti login dan ekspor data siswa akan tercatat di sini.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->total() > 0)
        {{ $logs->links() }}
    @endif
</div>
@endsection
