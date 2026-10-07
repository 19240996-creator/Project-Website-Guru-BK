@extends('layouts.app')

@section('title', 'Detail Peluang - ' . $opportunity->title)
@section('header_title', 'Detail Peluang & Pendaftar Siswa')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
    <div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <h2 style="font-size: 20px; font-weight: 800; color: var(--color-text-main);">{{ $opportunity->title }}</h2>
            <span class="badge badge-success">{{ ucfirst($opportunity->status) }}</span>
            <span class="badge badge-primary">{{ ucfirst(str_replace('_', ' ', $opportunity->type)) }}</span>
        </div>
        <p style="font-size: 13px; color: var(--color-text-muted); margin-top: 4px;">
            Mitra: <strong>{{ $opportunity->partner ? $opportunity->partner->name : 'Program Sekolah' }}</strong> | Kode: <strong>{{ $opportunity->code }}</strong>
        </p>
    </div>
    <a href="{{ route('guru.peluang.index') }}" class="btn btn-secondary">
        Kembali ke Daftar
    </a>
</div>

<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h3 class="card-title">Informasi & Ketentuan Peluang</h3>
    </div>
    <div class="card-body">
        <div class="grid-2">
            <div>
                <div style="font-size: 12px; font-weight: 700; color: var(--color-text-subtle); text-transform: uppercase;">Deskripsi:</div>
                <p style="font-size: 13px; color: var(--color-text-main); margin-top: 4px; line-height: 1.6;">
                    {{ $opportunity->description }}
                </p>
            </div>
            <div>
                <div style="font-size: 12px; font-weight: 700; color: var(--color-text-subtle); text-transform: uppercase;">Persyaratan Pendaftaran:</div>
                <p style="font-size: 13px; color: var(--color-text-main); margin-top: 4px; line-height: 1.6;">
                    {{ $opportunity->requirements ?: 'Tidak ada syarat khusus.' }}
                </p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Siswa yang Mendaftar ({{ $opportunity->registrations->count() }} Siswa)</h3>
    </div>
    <div class="card-body" style="padding: 0;">
        @if($opportunity->registrations->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Identitas Siswa</th>
                            <th>Kelas</th>
                            <th>Tanggal Mendaftar</th>
                            <th>Catatan Pendaftaran</th>
                            <th>Status Pendaftaran</th>
                            <th style="text-align: right;">Perbarui Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($opportunity->registrations as $reg)
                            <tr>
                                <td>
                                    <strong>{{ $reg->student ? $reg->student->name : '-' }}</strong><br>
                                    <small style="color: var(--color-text-subtle);">NISN: {{ $reg->student ? $reg->student->nisn : '-' }}</small>
                                </td>
                                <td>{{ $reg->student && $reg->student->studentClass ? $reg->student->studentClass->name : '-' }}</td>
                                <td>{{ $reg->registered_at->translatedFormat('d M Y, H:i') }}</td>
                                <td>{{ $reg->notes ?: '-' }}</td>
                                <td>
                                    @if($reg->status === 'diterima')
                                        <span class="badge badge-success">Diterima</span>
                                    @elseif($reg->status === 'ditolak')
                                        <span class="badge badge-danger">Ditolak</span>
                                    @elseif($reg->status === 'menunggu')
                                        <span class="badge badge-warning">Menunggu Seleksi</span>
                                    @else
                                        <span class="badge badge-primary">Terdaftar</span>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    <form action="{{ route('guru.peluang.reg_status', $reg->id) }}" method="POST" style="display: inline-flex; gap: 6px;">
                                        @csrf
                                        <select name="status" class="form-select" style="min-height: 32px; font-size: 12px; width: 130px;" onchange="this.form.submit()">
                                            <option value="terdaftar" {{ $reg->status === 'terdaftar' ? 'selected' : '' }}>Terdaftar</option>
                                            <option value="menunggu" {{ $reg->status === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                                            <option value="diterima" {{ $reg->status === 'diterima' ? 'selected' : '' }}>Diterima</option>
                                            <option value="ditolak" {{ $reg->status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                            <option value="selesai" {{ $reg->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <p class="empty-state-title">Belum Ada Siswa yang Mendaftar</p>
                <p class="empty-state-desc">Pendaftaran dari siswa akan muncul di tabel ini.</p>
            </div>
        @endif
    </div>
</div>
@endsection
