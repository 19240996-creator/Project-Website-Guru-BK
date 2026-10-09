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

<!-- Filter Bar (Horizontal) -->
<div class="card" style="margin-bottom: 20px;">
    <div class="card-body" style="padding: 16px 20px;">
        <form action="{{ route('guru.audit.index') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)) 100px; gap: 12px; align-items: flex-end;">
            <div>
                <label class="form-label" style="font-size: 12px; margin-bottom: 6px; display: block;">Cari Deskripsi / IP / Pelaksana</label>
                <input type="text" name="q" class="form-control" style="min-height: 38px; font-size: 13px;" value="{{ request('q') }}" placeholder="Ketik kata kunci...">
            </div>

            <div>
                <label class="form-label" style="font-size: 12px; margin-bottom: 6px; display: block;">Kategori Aksi</label>
                <select name="action" class="form-select" style="min-height: 38px; font-size: 13px;">
                    <option value="">Semua Aksi</option>
                    @foreach($actions as $act)
                        <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>{{ str_replace('_', ' ', $act) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label" style="font-size: 12px; margin-bottom: 6px; display: block;">Entitas Data</label>
                <select name="entity_type" class="form-select" style="min-height: 38px; font-size: 13px;">
                    <option value="">Semua Entitas</option>
                    @foreach($entityTypes as $ent)
                        <option value="{{ $ent }}" {{ request('entity_type') === $ent ? 'selected' : '' }}>{{ $ent }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label" style="font-size: 12px; margin-bottom: 6px; display: block;">Tanggal Aktivitas</label>
                <input type="date" name="date" class="form-control" style="min-height: 38px; font-size: 13px;" value="{{ request('date') }}">
            </div>

            <div>
                <button type="submit" class="btn btn-secondary" style="width: 100%; min-height: 38px;">
                    Filter
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header card-header-navy" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
            <h3 class="card-title" style="margin: 0;">Catatan Riwayat Aktivitas Sistem</h3>
            @if(request()->hasAny(['q', 'action', 'entity_type', 'date']))
                <a href="{{ route('guru.audit.index') }}" class="btn btn-secondary btn-sm" style="font-size: 11px; padding: 4px 10px; color: #ffffff; border-color: rgba(255,255,255,0.3); background: rgba(255,255,255,0.1);">
                    ✕ Reset Filter
                </a>
            @endif
        </div>
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
                <p class="empty-state-title">
                    @if(request()->hasAny(['q', 'action', 'entity_type', 'date']))
                        Tidak Ada Log Aktivitas yang Sesuai Filter
                    @else
                        Belum Ada Aktivitas
                    @endif
                </p>
                <p class="empty-state-desc">
                    @if(request()->hasAny(['q', 'action', 'entity_type', 'date']))
                        Coba sesuaikan kata kunci pencarian atau ubah kriteria filter yang Anda pilih.
                    @else
                        Aktivitas penting sistem akan terekam secara otomatis di sini.
                    @endif
                </p>
                @if(request()->hasAny(['q', 'action', 'entity_type', 'date']))
                    <div style="margin-top: 14px;">
                        <a href="{{ route('guru.audit.index') }}" class="btn btn-secondary btn-sm">Reset Filter</a>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
