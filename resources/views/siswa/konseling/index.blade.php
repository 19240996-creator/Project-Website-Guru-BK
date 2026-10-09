@extends('layouts.siswa')

@section('title', 'Riwayat Konseling Saya')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
    <div>
        <h2 style="font-size: 20px; font-weight: 800; color: var(--color-text-main);">Layanan Konseling Saya</h2>
        <p style="font-size: 13px; color: var(--color-text-muted); margin-top: 4px;">
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
                <table class="table" style="width: 100%; border-collapse: collapse;">
                    <thead class="table-thead-navy">
                        <tr>
                            <th style="width: 160px; text-align: left; white-space: nowrap;">Permohonan</th>
                            <th style="min-width: 250px; text-align: left;">Topik Konsultasi</th>
                            <th style="width: 210px; text-align: left; white-space: nowrap;">Kategori</th>
                            <th style="width: 120px; text-align: center; white-space: nowrap;">Urgensi</th>
                            <th style="width: 140px; text-align: center; white-space: nowrap;">Status</th>
                            <th style="width: 110px; text-align: center; white-space: nowrap;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($counselings as $c)
                            <tr>
                                <td style="text-align: left; white-space: nowrap; vertical-align: middle;">
                                    <div style="font-weight: 700; color: var(--color-primary); font-size: 13px;">{{ $c->code }}</div>
                                    <div style="font-size: 11px; color: var(--color-text-subtle); margin-top: 2px;">{{ $c->created_at->translatedFormat('d M Y') }}</div>
                                </td>
                                <td style="text-align: left; vertical-align: middle;">
                                    <div style="font-weight: 600; color: var(--color-text-main); font-size: 13px; line-height: 1.4;">
                                        {{ $c->topic }}
                                    </div>
                                </td>
                                <td style="text-align: left; white-space: nowrap; vertical-align: middle;">
                                    <span style="font-weight: 500; color: var(--color-text-muted); font-size: 13px;">
                                        {{ $c->category ? $c->category->name : 'Konseling Umum' }}
                                    </span>
                                </td>
                                <td style="text-align: center; white-space: nowrap; vertical-align: middle;">
                                    <div style="display: inline-flex; align-items: center; gap: 7px; width: 80px; text-align: left;">
                                        @if($c->urgency === 'mendesak' || $c->urgency === 'tinggi')
                                            <span style="width: 7px; height: 7px; border-radius: 50%; background-color: var(--color-danger); flex-shrink: 0; display: inline-block;"></span>
                                            <span style="font-weight: 600; font-size: 13px; color: var(--color-danger);">{{ ucfirst($c->urgency) }}</span>
                                        @elseif($c->urgency === 'sedang')
                                            <span style="width: 7px; height: 7px; border-radius: 50%; background-color: var(--color-warning); flex-shrink: 0; display: inline-block;"></span>
                                            <span style="font-weight: 600; font-size: 13px; color: var(--color-warning);">Sedang</span>
                                        @else
                                            <span style="width: 7px; height: 7px; border-radius: 50%; background-color: var(--color-text-subtle); flex-shrink: 0; display: inline-block;"></span>
                                            <span style="font-weight: 600; font-size: 13px; color: var(--color-text-subtle);">Rendah</span>
                                        @endif
                                    </div>
                                </td>
                                <td style="text-align: center; white-space: nowrap; vertical-align: middle;">
                                    <div style="display: inline-flex; align-items: center; gap: 7px; width: 100px; text-align: left;">
                                        @if($c->status === 'dijadwalkan')
                                            <span style="width: 7px; height: 7px; border-radius: 50%; background-color: var(--color-primary); flex-shrink: 0; display: inline-block;"></span>
                                            <span style="font-weight: 600; font-size: 13px; color: var(--color-primary);">Terjadwal</span>
                                        @elseif($c->status === 'selesai')
                                            <span style="width: 7px; height: 7px; border-radius: 50%; background-color: var(--color-success); flex-shrink: 0; display: inline-block;"></span>
                                            <span style="font-weight: 600; font-size: 13px; color: var(--color-success);">Selesai</span>
                                        @elseif($c->status === 'dilaksanakan')
                                            <span style="width: 7px; height: 7px; border-radius: 50%; background-color: var(--color-info); flex-shrink: 0; display: inline-block;"></span>
                                            <span style="font-weight: 600; font-size: 13px; color: var(--color-info);">Berlangsung</span>
                                        @else
                                            <span style="width: 7px; height: 7px; border-radius: 50%; background-color: var(--color-text-subtle); flex-shrink: 0; display: inline-block;"></span>
                                            <span style="font-weight: 600; font-size: 13px; color: var(--color-text-muted);">Diajukan</span>
                                        @endif
                                    </div>
                                </td>
                                <td style="text-align: center; white-space: nowrap; vertical-align: middle;">
                                    <a href="{{ route('siswa.konseling.show', $c->id) }}" class="btn btn-secondary btn-sm" style="font-size: 12px; padding: 6px 14px;">
                                        Lihat Sesi
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
