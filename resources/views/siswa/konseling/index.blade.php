@extends('layouts.siswa')

@section('title', 'Riwayat Konseling Saya')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
    <div>
        <h2 style="font-size: 20px; font-weight: 800; color: var(--color-text-main);">Layanan Konseling Saya</h2>
        <p style="font-size: 13px; color: var(--color-text-muted);">
            Ajukan konsultasi pribadi dengan Guru BK tanpa perlu merasa ragu. Segala cerita Anda terjaga kerahasiaannya.
        </p>
    </div>
    <div>
        <a href="{{ route('siswa.konseling.create') }}" class="btn btn-primary">
            + Ajukan Konseling Baru
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        @if($counselings->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nomor Permohonan</th>
                            <th>Topik Konsultasi</th>
                            <th>Kategori</th>
                            <th>Urgensi</th>
                            <th>Status Penanganan</th>
                            <th style="text-align: right;">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($counselings as $c)
                            <tr>
                                <td>
                                    <strong>{{ $c->code }}</strong><br>
                                    <small style="color: var(--color-text-subtle);">{{ $c->created_at->translatedFormat('d M Y') }}</small>
                                </td>
                                <td>
                                    <strong style="color: var(--color-text-main);">{{ $c->topic }}</strong>
                                </td>
                                <td>
                                    <span class="badge badge-secondary">{{ $c->category ? $c->category->name : 'Umum' }}</span>
                                </td>
                                <td>
                                    @if($c->urgency === 'mendesak' || $c->urgency === 'tinggi')
                                        <span class="badge badge-danger">{{ ucfirst($c->urgency) }}</span>
                                    @else
                                        <span class="badge badge-warning">{{ ucfirst($c->urgency) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($c->status === 'dijadwalkan')
                                        <span class="badge badge-primary">
                                            Terjadwal: {{ $c->scheduled_date ? $c->scheduled_date->format('d/m/Y') : '-' }} ({{ substr($c->scheduled_time, 0, 5) }} WIB)
                                        </span>
                                    @elseif($c->status === 'selesai')
                                        <span class="badge badge-success">Selesai</span>
                                    @else
                                        <span class="badge badge-secondary">{{ ucfirst(str_replace('_', ' ', $c->status)) }}</span>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    <a href="{{ route('siswa.konseling.show', $c->id) }}" class="btn btn-secondary btn-sm">
                                        Buka Jadwal & Progres
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
                <p class="empty-state-title">Belum Pernah Mengajukan Konseling</p>
                <p class="empty-state-desc">Jika Anda membutuhkan teman bicara mengenai akademik, masa depan, atau tantangan pribadi, jangan ragu untuk mengajukan konseling.</p>
                <a href="{{ route('siswa.konseling.create') }}" class="btn btn-primary btn-sm">Mulai Ajukan Konseling</a>
            </div>
        @endif
    </div>
</div>
@endsection
