@extends('layouts.siswa')

@section('title', 'Detail Peluang - ' . $opportunity->title)

@section('content')
<div style="max-width: 760px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
        <div>
            <div style="display: flex; align-items: center; gap: 8px;">
                <span class="badge badge-primary">{{ ucfirst(str_replace('_', ' ', $opportunity->type)) }}</span>
                <span class="badge badge-secondary">{{ $opportunity->code }}</span>
            </div>
            <h2 style="font-size: 20px; font-weight: 800; color: var(--color-text-main); margin-top: 6px;">{{ $opportunity->title }}</h2>
            <p style="font-size: 13px; color: var(--color-text-muted); margin-top: 2px;">
                Penyelenggara: <strong>{{ $opportunity->partner ? $opportunity->partner->name : 'Program Sekolah' }}</strong>
            </p>
        </div>
        <a href="{{ route('siswa.peluang.index') }}" class="btn btn-secondary">
            Kembali ke Daftar
        </a>
    </div>

    <!-- Informasi Peluang -->
    <div class="card" style="margin-bottom: 24px;">
        <div class="card-body" style="padding: 24px;">
            <div style="font-size: 13px; font-weight: 700; color: var(--color-text-subtle); text-transform: uppercase;">Deskripsi Peluang:</div>
            <p style="font-size: 14px; color: var(--color-text-main); line-height: 1.6; margin-top: 6px; margin-bottom: 20px;">
                {{ $opportunity->description }}
            </p>

            <div style="font-size: 13px; font-weight: 700; color: var(--color-text-subtle); text-transform: uppercase;">Persyaratan & Kriteria:</div>
            <div style="margin-top: 6px; padding: 14px; background: #f8fafc; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-size: 13px; line-height: 1.6; margin-bottom: 20px;">
                {{ $opportunity->requirements ?: 'Terbuka untuk seluruh siswa aktif sesuai jenjang sasaran.' }}
            </div>

            <table class="table" style="font-size: 13px; margin-bottom: 20px;">
                <tr>
                    <td style="width: 160px; font-weight: 600; color: var(--color-text-muted);">Sasaran Peserta</td>
                    <td>{{ $opportunity->target_audience ?: 'Seluruh Siswa' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--color-text-muted);">Batas Pendaftaran</td>
                    <td><strong>{{ $opportunity->deadline ? $opportunity->deadline->translatedFormat('d F Y') : 'Terbuka Terus' }}</strong></td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--color-text-muted);">Kuota Disediakan</td>
                    <td>{{ $opportunity->quota ? $opportunity->quota . ' Peserta' : 'Tidak Dibatasi' }}</td>
                </tr>
                @if($opportunity->registration_link)
                    <tr>
                        <td style="font-weight: 600; color: var(--color-text-muted);">Link Berkas Eksternal</td>
                        <td><a href="{{ $opportunity->registration_link }}" target="_blank">Kunjungi Tautan Pendaftaran Resmi →</a></td>
                    </tr>
                @endif
            </table>

            <!-- Status Pendaftaran Anda -->
            @if($registration)
                <div style="padding: 16px; background: #ecfdf5; border: 1px solid var(--color-success-border); border-radius: var(--radius-md);">
                    <div style="font-size: 12px; font-weight: 700; color: var(--color-success); text-transform: uppercase;">Status Pendaftaran Anda:</div>
                    <div style="font-size: 16px; font-weight: 800; color: var(--color-success); margin-top: 4px;">
                        {{ ucfirst($registration->status) }}
                    </div>
                    <p style="font-size: 12px; color: var(--color-text-muted); margin-top: 4px;">
                        Anda mendaftar pada {{ $registration->registered_at->translatedFormat('d F Y, H:i') }} WIB. Pantau pembaruan status seleksi di halaman ini.
                    </p>
                </div>
            @else
                <form action="{{ route('siswa.peluang.register', $opportunity->id) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Catatan Singkat / Motivasi Mengikuti Peluang Ini (Opsional)</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Tuliskan motivasi atau kualifikasi Anda..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;" onclick="return confirm('Daftar pada peluang ini sekarang?')">
                        Daftar Program Ini Sekarang
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
