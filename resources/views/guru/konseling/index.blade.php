@extends('layouts.app')

@section('title', 'Layanan Bimbingan & Konseling - Guru BK')
@section('header_title', 'Bimbingan & Konseling Siswa')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
    <div>
        <h2 style="font-size: 20px; font-weight: 800; color: var(--color-text-main);">Daftar Permohonan & Sesi Konseling</h2>
        <p style="font-size: 13px; color: var(--color-text-muted);">
            Pengelolaan seluruh siklus layanan bimbingan mulai dari pengajuan mandiri siswa sampai evaluasi tindak lanjut.
        </p>
    </div>
</div>

<!-- Status Tab Filter -->
<div class="tab-nav">
    <a href="{{ route('guru.konseling.index') }}" class="tab-btn {{ !request('status') ? 'active' : '' }}">Semua Layanan</a>
    <a href="{{ route('guru.konseling.index', ['status' => 'diajukan']) }}" class="tab-btn {{ request('status') === 'diajukan' ? 'active' : '' }}">
        Pengajuan Baru
    </a>
    <a href="{{ route('guru.konseling.index', ['status' => 'dijadwalkan']) }}" class="tab-btn {{ request('status') === 'dijadwalkan' ? 'active' : '' }}">
        Terjadwal
    </a>
    <a href="{{ route('guru.konseling.index', ['status' => 'tindak_lanjut']) }}" class="tab-btn {{ request('status') === 'tindak_lanjut' ? 'active' : '' }}">
        Tindak Lanjut
    </a>
    <a href="{{ route('guru.konseling.index', ['status' => 'selesai']) }}" class="tab-btn {{ request('status') === 'selesai' ? 'active' : '' }}">
        Selesai
    </a>
</div>

<!-- Search & Category Filter -->
<div class="card" style="margin-bottom: 20px;">
    <div class="card-body" style="padding: 16px 20px;">
        <form action="{{ route('guru.konseling.index') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) 100px; gap: 12px; align-items: flex-end;">
            <div>
                <label class="form-label" style="font-size: 12px;">Cari Siswa / Topik / Kode</label>
                <input type="text" name="q" class="form-control" style="min-height: 38px; font-size: 13px;" value="{{ request('q') }}" placeholder="Contoh: KSL-2026 atau nama siswa...">
            </div>

            <div>
                <label class="form-label" style="font-size: 12px;">Kategori Konseling</label>
                <select name="category_id" class="form-select" style="min-height: 38px; font-size: 13px;">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label" style="font-size: 12px;">Tingkat Urgensi</label>
                <select name="urgency" class="form-select" style="min-height: 38px; font-size: 13px;">
                    <option value="">Semua Urgensi</option>
                    <option value="rendah" {{ request('urgency') === 'rendah' ? 'selected' : '' }}>Rendah</option>
                    <option value="sedang" {{ request('urgency') === 'sedang' ? 'selected' : '' }}>Sedang</option>
                    <option value="tinggi" {{ request('urgency') === 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                    <option value="mendesak" {{ request('urgency') === 'mendesak' ? 'selected' : '' }}>Mendesak</option>
                </select>
            </div>

            <div>
                <button type="submit" class="btn btn-secondary" style="width: 100%; min-height: 38px;">
                    Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Table Konseling -->
<div class="card">
    <div class="card-body" style="padding: 0;">
        @if($counselings->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead class="table-thead-navy">
                        <tr>
                            <th>Nomor & Tanggal</th>
                            <th>Identitas Siswa</th>
                            <th>Topik & Kategori</th>
                            <th>Urgensi</th>
                            <th>Status & Jadwal</th>
                            <th style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($counselings as $c)
                            <tr>
                                <td>
                                    <strong>{{ $c->code }}</strong><br>
                                    <small style="color: var(--color-text-subtle);">{{ $c->created_at->translatedFormat('d M Y, H:i') }}</small>
                                </td>
                                <td>
                                    <strong>{{ $c->student ? $c->student->name : '-' }}</strong><br>
                                    <small style="color: var(--color-text-muted);">{{ $c->student && $c->student->studentClass ? $c->student->studentClass->name : '-' }}</small>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: var(--color-text-main);">{{ $c->topic }}</div>
                                    <small style="color: var(--color-text-subtle);">{{ $c->category ? $c->category->name : 'Umum' }}</small>
                                </td>
                                <td>
                                    @if($c->urgency === 'mendesak' || $c->urgency === 'tinggi')
                                        <span class="badge badge-danger">{{ ucfirst($c->urgency) }}</span>
                                    @elseif($c->urgency === 'sedang')
                                        <span class="badge badge-warning">Sedang</span>
                                    @else
                                        <span class="badge badge-secondary">Rendah</span>
                                    @endif
                                </td>
                                <td>
                                    @if($c->status === 'diajukan')
                                        <span class="badge badge-warning">Diajukan Siswa</span>
                                    @elseif($c->status === 'dijadwalkan')
                                        <span class="badge badge-primary">
                                            Dijadwalkan: {{ $c->scheduled_date ? $c->scheduled_date->format('d/m/Y') : '-' }} ({{ substr($c->scheduled_time, 0, 5) }} WIB)
                                        </span>
                                    @elseif($c->status === 'tindak_lanjut')
                                        <span class="badge badge-danger">Tindak Lanjut</span>
                                    @elseif($c->status === 'selesai')
                                        <span class="badge badge-success">Selesai</span>
                                    @else
                                        <span class="badge badge-secondary">{{ ucfirst(str_replace('_', ' ', $c->status)) }}</span>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    <a href="{{ route('guru.konseling.show', $c->id) }}" class="btn btn-secondary btn-sm">
                                        Proses & Catatan
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="padding: 16px 20px;">
                {{ $counselings->links() }}
            </div>
        @else
            <div class="empty-state">
                <p class="empty-state-title">Tidak Ada Catatan Konseling</p>
                <p class="empty-state-desc">Belum ada sesi bimbingan yang sesuai dengan filter yang Anda tentukan.</p>
            </div>
        @endif
    </div>
</div>
@endsection
