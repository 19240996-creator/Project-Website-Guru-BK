@extends('layouts.app')

@section('title', 'Audit Log Aktivitas - Guru BK')
@section('header_title', 'Audit Log & Rekam Jejak Sistem')

@section('content')
<div style="margin-bottom: 24px;">
    <h2 style="font-size: 20px; font-weight: 800; color: var(--color-text-main);">Audit Log Aktivitas & Perubahan Data</h2>
    <p style="font-size: 13px; color: var(--color-text-muted);">
        Rekam jejak setiap tindakan krusial pada data konseling, penjadwalan, dan profil siswa untuk menjamin akuntabilitas dan privasi data.
    </p>
</div>

<div class="card">
    <div class="card-header card-header-navy">
        <h3 class="card-title">Catatan Riwayat Aktivitas Sistem</h3>
        <span class="badge badge-translucent">Total: {{ $logs->total() }} Log</span>
    </div>
    <div class="card-body" style="padding: 0;">
        @if($logs->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead class="table-thead-navy">
                        <tr>
                            <th>Waktu & Tanggal</th>
                            <th>Pengguna Pelaksana</th>
                            <th>Aksi</th>
                            <th>Entitas Data</th>
                            <th>Deskripsi Perubahan</th>
                            <th>Alamat IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr>
                                <td>
                                    <strong>{{ $log->created_at->translatedFormat('d M Y') }}</strong><br>
                                    <small class="text-muted">{{ $log->created_at->format('H:i:s') }} WIB</small>
                                </td>
                                <td>
                                    <strong>{{ $log->user ? $log->user->name : 'Sistem' }}</strong><br>
                                    <small style="color: var(--color-text-subtle);">{{ $log->user ? ucfirst(str_replace('_', ' ', $log->user->role)) : '-' }}</small>
                                </td>
                                <td>
                                    <span class="badge badge-secondary">{{ $log->action }}</span>
                                </td>
                                <td>
                                    <code>{{ $log->entity_type }} #{{ $log->entity_id ?: '-' }}</code>
                                </td>
                                <td style="font-size: 13px; color: var(--color-text-main);">
                                    {{ $log->description }}
                                </td>
                                <td>
                                    <small style="color: var(--color-text-subtle);">{{ $log->ip_address ?: '127.0.0.1' }}</small>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $logs->links() }}
        @else
            <div class="empty-state">
                <p class="empty-state-title">Belum Ada Aktivitas</p>
                <p class="empty-state-desc">Aktivitas penting sistem akan terekam secara otomatis di sini.</p>
            </div>
        @endif
    </div>
</div>
@endsection
