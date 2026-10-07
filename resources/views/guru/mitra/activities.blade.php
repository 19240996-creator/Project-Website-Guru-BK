@extends('layouts.app')

@section('title', 'Jadwal Kegiatan Mitra - Guru BK')
@section('header_title', 'Jadwal Kegiatan Perguruan Tinggi & Industri')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
    <div>
        <h2 style="font-size: 20px; font-weight: 800; color: var(--color-text-main);">Agenda Kegiatan Kampus & Industri</h2>
        <p style="font-size: 13px; color: var(--color-text-muted);">
            Sistem penjadwalan terpadu dengan deteksi benturan otomatis (ruang, jam, dan sasaran kelas) untuk koordinasi Wakasek Kurikulum.
        </p>
    </div>
    <div>
        <button type="button" class="btn btn-primary" onclick="document.getElementById('addActivityModal').style.display = 'block';">
            + Jadwalkan Kegiatan Baru
        </button>
    </div>
</div>

@if($errors->has('schedule_conflict'))
    <div class="alert alert-danger" style="border-left: 4px solid var(--color-danger);">
        <strong>PERINGATAN BENTURAN JADWAL:</strong><br>
        {{ $errors->first('schedule_conflict') }}
    </div>
@endif

<div class="card" style="margin-bottom: 20px;">
    <div class="card-body" style="padding: 16px 20px;">
        <form action="{{ route('guru.mitra.activities') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) 100px; gap: 12px; align-items: flex-end;">
            <div>
                <label class="form-label" style="font-size: 12px;">Tipe Kegiatan</label>
                <select name="type" class="form-select" style="min-height: 38px; font-size: 13px;">
                    <option value="">Semua Tipe</option>
                    <option value="sosialisasi" {{ request('type') === 'sosialisasi' ? 'selected' : '' }}>Sosialisasi</option>
                    <option value="seminar" {{ request('type') === 'seminar' ? 'selected' : '' }}>Seminar</option>
                    <option value="campus_visit" {{ request('type') === 'campus_visit' ? 'selected' : '' }}>Campus Visit</option>
                    <option value="kunjungan_industri" {{ request('type') === 'kunjungan_industri' ? 'selected' : '' }}>Kunjungan Industri</option>
                    <option value="magang" {{ request('type') === 'magang' ? 'selected' : '' }}>Magang</option>
                </select>
            </div>

            <div>
                <label class="form-label" style="font-size: 12px;">Status Jadwal</label>
                <select name="status" class="form-select" style="min-height: 38px; font-size: 13px;">
                    <option value="">Semua Status</option>
                    <option value="terkonfirmasi" {{ request('status') === 'terkonfirmasi' ? 'selected' : '' }}>Terkonfirmasi</option>
                    <option value="rencana" {{ request('status') === 'rencana' ? 'selected' : '' }}>Rencana</option>
                    <option value="terlaksana" {{ request('status') === 'terlaksana' ? 'selected' : '' }}>Terlaksana</option>
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

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Jadwal Kegiatan Kemitraan ({{ $activities->total() }})</h3>
        <a href="{{ route('guru.laporan.generate', ['package' => 'paket_b']) }}" target="_blank" class="btn btn-secondary btn-sm">
            Cetak Laporan Kurikulum (Paket B)
        </a>
    </div>
    <div class="card-body" style="padding: 0;">
        @if($activities->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tanggal & Waktu</th>
                            <th>Mitra Pelaksana</th>
                            <th>Kegiatan</th>
                            <th>Tipe</th>
                            <th>Sasaran Kelas</th>
                            <th>Ruang / Tempat</th>
                            <th>Penanggung Jawab</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activities as $act)
                            <tr>
                                <td>
                                    <strong>{{ $act->date->translatedFormat('d M Y') }}</strong><br>
                                    <small>{{ substr($act->start_time, 0, 5) }} - {{ substr($act->end_time, 0, 5) }} WIB</small>
                                </td>
                                <td>
                                    <strong>{{ $act->partner ? $act->partner->name : '-' }}</strong><br>
                                    <small style="color: var(--color-text-subtle);">{{ $act->partner ? ucfirst(str_replace('_', ' ', $act->partner->type)) : '' }}</small>
                                </td>
                                <td>{{ $act->title }}</td>
                                <td><span class="badge badge-secondary">{{ ucfirst(str_replace('_', ' ', $act->activity_type)) }}</span></td>
                                <td>{{ $act->targetClass ? $act->targetClass->name : 'Semua Kelas' }}</td>
                                <td><strong>{{ $act->room_location }}</strong></td>
                                <td>{{ $act->pic_name ?: '-' }}</td>
                                <td>
                                    <span class="badge badge-success">{{ ucfirst($act->status) }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="padding: 16px 20px;">
                {{ $activities->links() }}
            </div>
        @else
            <div class="empty-state">
                <p class="empty-state-title">Tidak Ada Agenda Terjadwal</p>
                <p class="empty-state-desc">Belum ada kegiatan kemitraan yang dijadwalkan.</p>
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
            <div class="form-group">
                <label class="form-label">Mitra Penyelenggara *</label>
                <select name="partner_id" class="form-select" required>
                    <option value="">-- Pilih Mitra --</option>
                    @foreach($partners as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} ({{ ucfirst(str_replace('_', ' ', $p->type)) }})</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Nama Kegiatan / Agenda *</label>
                <input type="text" name="title" class="form-control" placeholder="Contoh: Workshop Cloud Computing..." required>
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
                <label class="form-label">Catatan Teknis / Kebutuhan Ruang</label>
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
