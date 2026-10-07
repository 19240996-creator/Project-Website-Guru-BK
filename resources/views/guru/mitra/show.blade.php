@extends('layouts.app')

@section('title', 'Detail Mitra - ' . $partner->name)
@section('header_title', 'Profil Mitra & Rekam Kerja Sama')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
    <div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <h2 style="font-size: 20px; font-weight: 800; color: var(--color-text-main);">{{ $partner->name }}</h2>
            <span class="badge badge-{{ $partner->partnership_status === 'aktif' ? 'success' : 'warning' }}">
                {{ ucfirst(str_replace('_', ' ', $partner->partnership_status)) }}
            </span>
        </div>
        <p style="font-size: 13px; color: var(--color-text-muted); margin-top: 4px;">
            {{ ucfirst(str_replace('_', ' ', $partner->type)) }} | Kode: <strong>{{ $partner->code }}</strong>
        </p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('guru.mitra.index') }}" class="btn btn-secondary">
            Kembali ke Daftar
        </a>
        <button type="button" class="btn btn-primary" onclick="document.getElementById('addActivityModal').style.display = 'block';">
            + Jadwalkan Kegiatan Baru
        </button>
    </div>
</div>

<div class="grid-2" style="margin-bottom: 24px;">
    <!-- Informasi Detail Mitra -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Profil & Dokumen Kemitraan</h3>
        </div>
        <div class="card-body">
            <table class="table" style="font-size: 13px;">
                <tr>
                    <td style="width: 150px; font-weight: 600; color: var(--color-text-muted);">Nomor Dokumen</td>
                    <td>{{ $partner->partnership_doc_number ?: 'Belum ada nomor dokumen' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--color-text-muted);">Masa Berlaku</td>
                    <td>
                        {{ $partner->partnership_start_date ? $partner->partnership_start_date->translatedFormat('d M Y') : '-' }}
                        s.d.
                        {{ $partner->partnership_end_date ? $partner->partnership_end_date->translatedFormat('d M Y') : 'Selesai' }}
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--color-text-muted);">Kota / Lokasi</td>
                    <td>{{ $partner->city ?: '-' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--color-text-muted);">Website Resmi</td>
                    <td>
                        @if($partner->website)
                            <a href="{{ $partner->website }}" target="_blank">{{ $partner->website }}</a>
                        @else
                            -
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--color-text-muted);">Narahubung (PIC)</td>
                    <td>{{ $partner->contact_person ?: '-' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--color-text-muted);">Telepon / Email</td>
                    <td>{{ $partner->phone ?: '-' }} / {{ $partner->email ?: '-' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--color-text-muted);">Lingkup Kerja Sama</td>
                    <td>{{ $partner->notes ?: '-' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Peluang Terkait yang Dipublikasikan -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Peluang & Beasiswa Terkait ({{ $partner->opportunities->count() }})</h3>
        </div>
        <div class="card-body">
            @if($partner->opportunities->count() > 0)
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    @foreach($partner->opportunities as $op)
                        <div style="padding: 12px; background: #f8fafc; border: 1px solid var(--color-border); border-radius: var(--radius-sm);">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <strong style="font-size: 13px;">{{ $op->title }}</strong>
                                <span class="badge badge-primary">{{ ucfirst(str_replace('_', ' ', $op->type)) }}</span>
                            </div>
                            <p style="font-size: 12px; color: var(--color-text-muted); margin-top: 4px;">
                                Sasaran: {{ $op->target_audience ?: 'Umum' }} | Batas: {{ $op->deadline ? $op->deadline->translatedFormat('d M Y') : 'Terbuka' }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <p class="empty-state-title">Belum Ada Peluang Terdaftar</p>
                    <p class="empty-state-desc">Peluang magang atau beasiswa dari mitra ini belum dipublikasikan.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Agenda Kegiatan Bersama Mitra Ini -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Agenda Kegiatan Terjadwal Bersama Mitra ({{ $partner->activities->count() }})</h3>
    </div>
    <div class="card-body" style="padding: 0;">
        @if($partner->activities->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kode & Tanggal</th>
                            <th>Nama Kegiatan</th>
                            <th>Tipe Kegiatan</th>
                            <th>Sasaran Kelas</th>
                            <th>Waktu & Ruangan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($partner->activities as $act)
                            <tr>
                                <td>
                                    <strong>{{ $act->code }}</strong><br>
                                    <small>{{ $act->date->translatedFormat('d M Y') }}</small>
                                </td>
                                <td>{{ $act->title }}</td>
                                <td><span class="badge badge-secondary">{{ ucfirst(str_replace('_', ' ', $act->activity_type)) }}</span></td>
                                <td>{{ $act->targetClass ? $act->targetClass->name : 'Semua Kelas' }}</td>
                                <td>
                                    {{ substr($act->start_time, 0, 5) }} - {{ substr($act->end_time, 0, 5) }} WIB<br>
                                    <small style="color: var(--color-text-subtle);">Ruang: {{ $act->room_location }}</small>
                                </td>
                                <td>
                                    <span class="badge badge-success">{{ ucfirst($act->status) }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <p class="empty-state-title">Belum Ada Agenda Terjadwal</p>
                <p class="empty-state-desc">Klik tombol "Jadwalkan Kegiatan Baru" di bagian atas untuk mengatur jadwal sosialisasi atau seminar.</p>
            </div>
        @endif
    </div>
</div>

<!-- Modal Tambah Kegiatan -->
<div id="addActivityModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 50; overflow-y: auto;">
    <div style="max-width: 650px; margin: 60px auto; background: #fff; padding: 24px; border-radius: var(--radius-md);">
        <h3 style="font-size: 16px; font-weight: 800; margin-bottom: 16px;">Jadwalkan Agenda Kegiatan Baru</h3>
        
        <form action="{{ route('guru.mitra.activity.store') }}" method="POST">
            @csrf
            <input type="hidden" name="partner_id" value="{{ $partner->id }}">

            <div class="form-group">
                <label class="form-label">Nama Kegiatan / Agenda *</label>
                <input type="text" name="title" class="form-control" placeholder="Contoh: Sosialisasi PMB Jalur SNBP..." required>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Tipe Kegiatan *</label>
                    <select name="activity_type" class="form-select" required>
                        <option value="sosialisasi">Sosialisasi</option>
                        <option value="seminar">Seminar / Workshop</option>
                        <option value="campus_visit">Campus Visit</option>
                        <option value="kunjungan_industri">Kunjungan Industri</option>
                        <option value="magang">Program Magang</option>
                        <option value="rekrutmen">Rekrutmen Langsung</option>
                        <option value="pelatihan">Pelatihan / Sertifikasi</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Sasaran Kelas</label>
                    <select name="target_class_id" class="form-select">
                        <option value="">Semua Kelas</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid-3">
                <div class="form-group">
                    <label class="form-label">Tanggal Pelaksanaan *</label>
                    <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Jam Mulai *</label>
                    <input type="time" name="start_time" class="form-control" value="09:00" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Jam Selesai *</label>
                    <input type="time" name="end_time" class="form-control" value="11:30" required>
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Ruang / Tempat Pelaksanaan *</label>
                    <input type="text" name="room_location" class="form-control" placeholder="Contoh: Aula Utama, Lab Komputer..." required>
                    <div class="form-hint">Sistem otomatis mendeteksi jika ruang atau kelas telah terpakai di jam yang sama.</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Kapasitas Maksimal Peserta</label>
                    <input type="number" name="max_participants" class="form-control" value="100" min="1" required>
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Penanggung Jawab (PIC)</label>
                    <input type="text" name="pic_name" class="form-control" value="{{ auth()->user()->name }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Status Awal *</label>
                    <select name="status" class="form-select" required>
                        <option value="terkonfirmasi">Terkonfirmasi</option>
                        <option value="rencana">Rencana</option>
                        <option value="menunggu_konfirmasi">Menunggu Konfirmasi</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Catatan Teknis / Kebutuhan Perangkat</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="Proyektor, mic wireless, sound system..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('addActivityModal').style.display = 'none';">Batal</button>
                <button type="submit" class="btn btn-primary">Jadwalkan Kegiatan</button>
            </div>
        </form>
    </div>
</div>
@endsection
