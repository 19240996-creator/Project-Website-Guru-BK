@extends('layouts.siswa')

@section('title', 'Profil Saya - ' . $student->name)

@section('content')
<div style="margin-bottom: 24px;">
    <h2 style="font-size: 20px; font-weight: 800; color: var(--color-text-main);">Profil Pribadi Saya</h2>
    <p style="font-size: 13px; color: var(--color-text-muted);">
        Periksa kelengkapan data pribadi Anda dan pastikan kontak WhatsApp aktif untuk menerima kabar jadwal konseling.
    </p>
</div>

<div class="grid-2">
    <!-- Data Akademik & Sekolah -->
    <div class="card">
        <div class="card-header card-header-navy">
            <h3 class="card-title">Identitas Pokok Siswa</h3>
            <span class="badge badge-translucent">{{ ucfirst($student->status) }}</span>
        </div>
        <div class="card-body">
            <table class="table" style="font-size: 13px;">
                <tr>
                    <td style="width: 140px; font-weight: 600; color: var(--color-text-muted);">Nama Lengkap</td>
                    <td><strong>{{ $student->name }}</strong></td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--color-text-muted);">NISN</td>
                    <td>{{ $student->nisn }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--color-text-muted);">NIS</td>
                    <td>{{ $student->nis }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--color-text-muted);">Kelas Saat Ini</td>
                    <td>{{ $student->studentClass ? $student->studentClass->name : '-' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--color-text-muted);">Program Keahlian</td>
                    <td>{{ $student->studentClass ? $student->studentClass->major : '-' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--color-text-muted);">Tahun Ajaran</td>
                    <td>{{ $student->studentClass && $student->studentClass->academicYear ? $student->studentClass->academicYear->name : '-' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--color-text-muted);">Tempat, Tgl Lahir</td>
                    <td>{{ $student->birth_place ?: '-' }}, {{ $student->birth_date ? $student->birth_date->translatedFormat('d F Y') : '-' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--color-text-muted);">Nama Orang Tua</td>
                    <td>{{ $student->parent_name ?: '-' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Pembaruan Kontak Mandiri -->
    <div class="card">
        <div class="card-header card-header-navy">
            <h3 class="card-title">Perbarui Kontak Mandiri</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('siswa.profil.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label">Nomor HP / WhatsApp Aktif</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $student->phone) }}" placeholder="08..." required>
                    <div class="form-hint" style="font-size: 11.5px; color: var(--color-text-muted); margin-top: 4px;">
                        Digunakan guru BK untuk bimbingan &amp; otomatis menjadi kata sandi login Anda. Jika diganti, Anda wajib login memakai nomor baru ini.
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nomor Kontak Orang Tua / Wali</label>
                    <input type="text" name="parent_phone" class="form-control" value="{{ old('parent_phone', $student->parent_phone) }}" placeholder="08...">
                </div>

                <div class="form-group">
                    <label class="form-label">Alamat Tempat Tinggal Sekarang</label>
                    <textarea name="address" class="form-control" rows="3" placeholder="Jl. ...">{{ old('address', $student->address) }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    Simpan Perubahan Kontak
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
