@extends('layouts.app')

@section('title', 'Alumni & Pelacakan Lulusan - Guru BK')
@section('header_title', 'Kelulusan & Pelacakan Alumni (Tracer Study)')

@section('content')
<div style="margin-bottom: 24px;">
    <h2 style="font-size: 20px; font-weight: 800; color: var(--color-text-main);">Pelacakan Alumni & Jejak Karier</h2>
    <p style="font-size: 13px; color: var(--color-text-muted);">
        Pemantauan penyerapan lulusan berkala (3, 6, 12, dan 24 bulan setelah kelulusan) dan kurasi inspirasi karier untuk adik kelas.
    </p>
</div>

<div class="grid-4" style="margin-bottom: 24px;">
    <div class="stat-card">
        <span class="stat-label">Total Alumni Terdata</span>
        <span class="stat-value">{{ $stats['total'] }}</span>
        <span class="stat-desc">Siswa berstatus lulus</span>
    </div>

    <div class="stat-card">
        <span class="stat-label">Alumni Bekerja</span>
        <span class="stat-value">{{ $stats['bekerja'] }}</span>
        <span class="stat-desc">Terserap di industri</span>
    </div>

    <div class="stat-card">
        <span class="stat-label">Alumni Kuliah</span>
        <span class="stat-value">{{ $stats['kuliah'] }}</span>
        <span class="stat-desc">Studi lanjut PTN/PTS</span>
    </div>

    <div class="stat-card">
        <span class="stat-label">Alumni Wirausaha</span>
        <span class="stat-value">{{ $stats['wirausaha'] }}</span>
        <span class="stat-desc">Mendirikan usaha mandiri</span>
    </div>
</div>

<div class="card">
    <div class="card-header card-header-navy">
        <h3 class="card-title">Daftar Pelacakan Alumni ({{ $alumni->total() }})</h3>
        <a href="{{ route('guru.laporan.generate', ['package' => 'paket_c']) }}" target="_blank" class="btn btn-secondary btn-sm">
            Cetak Rekap Lulusan Kepala Sekolah
        </a>
    </div>
    <div class="card-body" style="padding: 0;">
        @if($alumni->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead class="table-thead-navy">
                        <tr>
                            <th>Identitas Alumni</th>
                            <th>Tahun Lulus & Jurusan</th>
                            <th>Periode Pelacakan</th>
                            <th>Status Terkini</th>
                            <th>Instansi / Perusahaan / Kampus</th>
                            <th>Jejak Inspirasi Publik</th>
                            <th style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($alumni as $als)
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        @if($als->photo)
                                            <img src="{{ asset('storage/' . $als->photo) }}" alt="{{ $als->student ? $als->student->name : 'Alumni' }}" style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover; border: 1px solid var(--color-border); flex-shrink: 0;">
                                        @elseif($als->student && $als->student->avatar)
                                            <img src="{{ asset('storage/' . $als->student->avatar) }}" alt="{{ $als->student->name }}" style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover; border: 1px solid var(--color-border); flex-shrink: 0;">
                                        @else
                                            <div class="user-avatar-initial" style="width: 38px; height: 38px; font-size: 13px; flex-shrink: 0;">
                                                {{ strtoupper(substr($als->student ? $als->student->name : 'A', 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <strong>{{ $als->student ? $als->student->name : '-' }}</strong><br>
                                            <small style="color: var(--color-text-subtle);">{{ $als->code }} | NISN: {{ $als->student ? $als->student->nisn : '-' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    Lulus Tahun: <strong>{{ $als->graduation_year }}</strong><br>
                                    <small style="color: var(--color-text-muted);">{{ $als->student && $als->student->studentClass ? $als->student->studentClass->major : '-' }}</small>
                                </td>
                                <td>
                                    <span class="badge badge-secondary">{{ str_replace('_', ' ', $als->tracking_period) }}</span>
                                </td>
                                <td>
                                    @if($als->current_status === 'bekerja')
                                        <span class="badge badge-success">Bekerja</span>
                                    @elseif($als->current_status === 'kuliah')
                                        <span class="badge badge-primary">Kuliah</span>
                                    @elseif($als->current_status === 'wirausaha')
                                        <span class="badge badge-warning">Wirausaha</span>
                                    @else
                                        <span class="badge badge-secondary">{{ ucfirst(str_replace('_', ' ', $als->current_status)) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $als->institution_or_company ?: '-' }}</strong><br>
                                    <small style="color: var(--color-text-subtle);">{{ $als->major_or_position ?: '-' }}</small>
                                </td>
                                <td>
                                    @if($als->allow_public_showcase)
                                        <span class="badge badge-success">Tampil di Siswa</span>
                                    @else
                                        <span class="badge badge-secondary">Tidak Ditampilkan</span>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    <form action="{{ route('guru.alumni.toggle_showcase', $als->id) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        <button type="submit" class="btn btn-secondary btn-sm" title="Ubah status tampil">
                                            {{ $als->allow_public_showcase ? 'Sembunyikan' : 'Jadikan Inspirasi' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="padding: 16px 20px;">
                {{ $alumni->links() }}
            </div>
        @else
            <div class="empty-state">
                <p class="empty-state-title">Belum Ada Data Pelacakan Alumni</p>
                <p class="empty-state-desc">Ubah status siswa kelas XII menjadi lulus melalui menu Profil Siswa 360° untuk mencatat pelacakan alumni.</p>
            </div>
        @endif
    </div>
</div>
@endsection
