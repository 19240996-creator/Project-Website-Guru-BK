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
                            <th style="width: 150px; min-width: 130px;">Waktu & Tanggal</th>
                            <th style="width: 180px; min-width: 160px;">Pengguna Pelaksana</th>
                            <th style="width: 160px; min-width: 140px; text-align: center;">Aksi</th>
                            <th style="width: 160px; min-width: 140px;">Entitas Data</th>
                            <th>Deskripsi Perubahan</th>
                            <th style="width: 110px; min-width: 90px;">Alamat IP</th>
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
                                <td style="text-align: center; vertical-align: middle;">
                                    <span class="badge badge-secondary" style="display: inline-flex; align-items: center; justify-content: center;">{{ $log->action }}</span>
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
