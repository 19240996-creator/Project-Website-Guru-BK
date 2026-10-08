@extends('layouts.app')

@section('title', 'Detail Konseling - ' . $counseling->code)
@section('header_title', 'Penanganan Sesi Konseling')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
    <div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <h2 style="font-size: 20px; font-weight: 800; color: var(--color-text-main);">{{ $counseling->code }}</h2>
            <span class="badge badge-primary">{{ ucfirst(str_replace('_', ' ', $counseling->status)) }}</span>
            <span class="badge badge-warning">Urgensi: {{ ucfirst($counseling->urgency) }}</span>
        </div>
        <p style="font-size: 13px; color: var(--color-text-muted); margin-top: 4px;">
            Diajukan pada {{ $counseling->created_at->translatedFormat('l, d F Y - H:i') }} WIB
        </p>
    </div>
    <div>
        <a href="{{ route('guru.konseling.index') }}" class="btn btn-secondary">
            Kembali ke Daftar
        </a>
    </div>
</div>

<div class="grid-2" style="margin-bottom: 24px;">
    <!-- Kartu Informasi Siswa & Permohonan -->
    <div class="card">
        <div class="card-header card-header-navy" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <h3 class="card-title">Permohonan Siswa</h3>
            <a href="{{ route('guru.siswa.show', $counseling->student_id) }}" class="btn btn-secondary btn-sm" target="_blank">
                Lihat Profil Siswa
            </a>
        </div>
        <div class="card-body">
            <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid var(--color-border);">
                <div class="user-avatar-initial" style="width: 44px; height: 44px; font-size: 16px;">
                    {{ strtoupper(substr($counseling->student->name, 0, 1)) }}
                </div>
                <div>
                    <div style="font-weight: 700; font-size: 14px; color: var(--color-text-main);">{{ $counseling->student->name }}</div>
                    <div style="font-size: 12px; color: var(--color-text-subtle);">
                        Kelas: {{ $counseling->student->studentClass ? $counseling->student->studentClass->name : '-' }} | NISN: {{ $counseling->student->nisn }}
                    </div>
                </div>
            </div>

            <table class="table" style="font-size: 13px;">
                <tr>
                    <td style="width: 140px; font-weight: 600; color: var(--color-text-muted);">Kategori Masalah</td>
                    <td><span class="badge badge-secondary">{{ $counseling->category ? $counseling->category->name : 'Umum' }}</span></td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--color-text-muted);">Topik Konseling</td>
                    <td><strong>{{ $counseling->topic }}</strong></td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--color-text-muted);">Pilihan Waktu Siswa</td>
                    <td>{{ $counseling->preferred_schedule ?: 'Tidak mencantumkan waktu khusus' }}</td>
                </tr>
            </table>

            <div style="margin-top: 16px;">
                <div style="font-size: 12px; font-weight: 700; color: var(--color-text-subtle); text-transform: uppercase;">Deskripsi / Cerita Siswa:</div>
                <div style="margin-top: 6px; padding: 12px; background: #f8fafc; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-size: 13px; line-height: 1.6; color: var(--color-text-main);">
                    {{ $counseling->story }}
                </div>
            </div>
        </div>
    </div>

    <!-- Panel Penjadwalan & Waktu Sesi -->
    <div class="card">
        <div class="card-header card-header-navy" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <h3 class="card-title">Penjadwalan Sesi Konseling</h3>
            @if($counseling->scheduled_date)
                <span class="badge badge-success">Sudah Ditetapkan</span>
            @else
                <span class="badge badge-warning">Belum Dijadwalkan</span>
            @endif
        </div>
        <div class="card-body">
            <form action="{{ route('guru.konseling.schedule', $counseling->id) }}" method="POST">
                @csrf
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Tanggal Sesi Pertemuan *</label>
                        <input type="date" name="scheduled_date" class="form-control" value="{{ old('scheduled_date', $counseling->scheduled_date ? $counseling->scheduled_date->format('Y-m-d') : date('Y-m-d')) }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Waktu Pertemuan (WIB) *</label>
                        <input type="time" name="scheduled_time" class="form-control" value="{{ old('scheduled_time', $counseling->scheduled_time ? substr($counseling->scheduled_time, 0, 5) : '09:00') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Lokasi / Ruang Pertemuan *</label>
                    <input type="text" name="scheduled_location" class="form-control" value="{{ old('scheduled_location', $counseling->scheduled_location ?: 'Ruang Konseling BK 1') }}" placeholder="Contoh: Ruang Konseling BK 1" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Tindakan / Rencana Awal Siswa (Dapat dilihat siswa)</label>
                    <textarea name="student_action_plan" class="form-control" rows="2" placeholder="Instruksi awal untuk siswa sebelum hadir sesi...">{{ old('student_action_plan', $counseling->student_action_plan) }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    {{ $counseling->scheduled_date ? 'Perbarui Jadwal & Kirim Notifikasi' : 'Jadwalkan Sesi Pertemuan' }}
                </button>
            </form>
        </div>
    </div>
</div>

<!-- SECTION 11: CATATAN INTERNAL GURU BK & STATUS WORKFLOW -->
<div class="card" style="border-top: 3px solid var(--color-primary);">
    <div class="card-header card-header-navy" style="display: flex; flex-direction: column; gap: 12px; padding: 20px 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: #93c5fd;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                <h3 class="card-title" style="margin: 0; font-size: 16px; font-weight: 700; color: #ffffff;">Catatan Internal Konseling & Privasi Rahasia Guru BK</h3>
            </div>
            <span class="badge badge-danger">Kerahasiaan: {{ strtoupper($counseling->confidential_level) }}</span>
        </div>
        <p style="font-size: 13px; color: #e2e8f0; margin: 0; line-height: 1.5; display: flex; align-items: flex-start; gap: 8px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: #93c5fd; flex-shrink: 0; margin-top: 2px;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
            <span>
                <strong style="color: #ffffff; font-weight: 700;">Prinsip Privasi BK:</strong> Catatan interpretasi internal, kronologi kasus, dan analisis risiko profesional yang Anda simpan di bawah ini <strong style="color: #ffffff;">TIDAK AKAN PERNAH</strong> ditampilkan kepada siswa pada tampilan antarmuka mereka.
            </span>
        </p>
    </div>
    <div class="card-body">

        <form action="{{ route('guru.konseling.notes', $counseling->id) }}" method="POST">
            @csrf
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Status Alur Konseling Saat Ini *</label>
                    <select name="status" class="form-select" required>
                        <option value="diajukan" {{ $counseling->status === 'diajukan' ? 'selected' : '' }}>Diajukan (Menunggu)</option>
                        <option value="ditinjau" {{ $counseling->status === 'ditinjau' ? 'selected' : '' }}>Ditinjau BK</option>
                        <option value="dijadwalkan" {{ $counseling->status === 'dijadwalkan' ? 'selected' : '' }}>Dijadwalkan</option>
                        <option value="dilaksanakan" {{ $counseling->status === 'dilaksanakan' ? 'selected' : '' }}>Dilaksanakan (Sesi Berjalan)</option>
                        <option value="tindak_lanjut" {{ $counseling->status === 'tindak_lanjut' ? 'selected' : '' }}>Membutuhkan Tindak Lanjut</option>
                        <option value="selesai" {{ $counseling->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="dialihkan" {{ $counseling->status === 'dialihkan' ? 'selected' : '' }}>Dialihkan / Rujukan Luar</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Tingkat Kerahasiaan Dokumen *</label>
                    <select name="confidential_level" class="form-select" required>
                        <option value="rahasia" {{ $counseling->confidential_level === 'rahasia' ? 'selected' : '' }}>Rahasia (Default BK)</option>
                        <option value="terbatas" {{ $counseling->confidential_level === 'terbatas' ? 'selected' : '' }}>Terbatas</option>
                        <option value="umum" {{ $counseling->confidential_level === 'umum' ? 'selected' : '' }}>Umum</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Catatan Hasil Pertemuan & Interpretasi Internal (Rahasia)</label>
                <textarea name="counselor_notes" class="form-control" rows="5" placeholder="Tuliskan dinamika wawancara, observasi perilaku, akar masalah tersembunyi, dan penilaian profesional...">{{ old('counselor_notes', $counseling->counselor_notes) }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Kesepakatan & Komitmen Siswa (Dapat dilihat siswa untuk panduan)</label>
                <textarea name="student_action_plan" class="form-control" rows="3" placeholder="Rencana aksi mandiri yang disepakati siswa bersama guru BK...">{{ old('student_action_plan', $counseling->student_action_plan) }}</textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="submit" class="btn btn-primary">Simpan Catatan & Pembaruan Status</button>
            </div>
        </form>
    </div>
</div>

<!-- SECTION 12: TINDAK LANJUT OTOMATIS (FOLLOW-UP ITEMS) -->
<div class="card">
    <div class="card-header card-header-navy" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <h3 class="card-title">Agenda Tindak Lanjut Kasus ({{ $counseling->followUps->count() }})</h3>
        <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('addFollowUpModal').style.display = 'block';">
            + Tambah Tindak Lanjut
        </button>
    </div>
    <div class="card-body" style="padding: 0;">
        @if($counseling->followUps->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead class="table-thead-navy">
                        <tr>
                            <th>Tindakan yang Perlu Dilakukan</th>
                            <th>Target Tanggal</th>
                            <th>Status Tindak Lanjut</th>
                            <th>Catatan</th>
                            <th style="text-align: right;">Perbarui Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($counseling->followUps as $fu)
                            @php
                                $isOverdue = $fu->status === 'belum_dilakukan' && $fu->target_date <= date('Y-m-d');
                            @endphp
                            <tr style="{{ $isOverdue ? 'background-color: #fef2f2;' : '' }}">
                                <td>
                                    <strong>{{ $fu->action_description }}</strong>
                                    @if($isOverdue)
                                        <br><span class="badge badge-danger">Jatuh Tempo Hari Ini</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($fu->target_date)->translatedFormat('d F Y') }}</td>
                                <td>
                                    @if($fu->status === 'selesai')
                                        <span class="badge badge-success">Selesai</span>
                                    @elseif($fu->status === 'sedang_dilakukan')
                                        <span class="badge badge-primary">Sedang Dilakukan</span>
                                    @elseif($fu->status === 'ditunda')
                                        <span class="badge badge-secondary">Ditunda</span>
                                    @else
                                        <span class="badge badge-warning">Belum Dilakukan</span>
                                    @endif
                                </td>
                                <td>{{ $fu->notes ?: '-' }}</td>
                                <td style="text-align: right;">
                                    <form action="{{ route('guru.konseling.follow_up.update', $fu->id) }}" method="POST" style="display: inline-flex; gap: 6px;">
                                        @csrf
                                        <select name="status" class="form-select" style="min-height: 32px; font-size: 12px; width: 140px;" onchange="this.form.submit()">
                                            <option value="belum_dilakukan" {{ $fu->status === 'belum_dilakukan' ? 'selected' : '' }}>Belum Selesai</option>
                                            <option value="sedang_dilakukan" {{ $fu->status === 'sedang_dilakukan' ? 'selected' : '' }}>Sedang Proses</option>
                                            <option value="selesai" {{ $fu->status === 'selesai' ? 'selected' : '' }}>Tandai Selesai</option>
                                            <option value="ditunda" {{ $fu->status === 'ditunda' ? 'selected' : '' }}>Ditunda</option>
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
                <p class="empty-state-title">Belum Ada Agenda Tindak Lanjut</p>
                <p class="empty-state-desc">Tambahkan butir tindakan lanjutan jika kasus ini memerlukan observasi berkala atau verifikasi data.</p>
            </div>
        @endif
    </div>
</div>

<!-- Modal Tambah Tindak Lanjut -->
<div id="addFollowUpModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 50; overflow-y: auto;">
    <div style="max-width: 520px; margin: 80px auto; background: #fff; padding: 24px; border-radius: var(--radius-md);">
        <h3 style="font-size: 16px; font-weight: 800; margin-bottom: 14px;">Tambah Agenda Tindak Lanjut</h3>
        <form action="{{ route('guru.konseling.follow_up.store', $counseling->id) }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Deskripsi Tindakan yang Harus Dilakukan *</label>
                <textarea name="action_description" class="form-control" rows="3" placeholder="Contoh: Pemanggilan orang tua, verifikasi nilai rapor eligible, kunjungan rumah..." required></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Target Tanggal Selesai (Jatuh Tempo) *</label>
                <input type="date" name="target_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                <div class="form-hint">Jika target tanggal tiba dan belum selesai, agenda otomatis masuk ke kotak "Yang Perlu Dikerjakan".</div>
            </div>

            <div class="form-group">
                <label class="form-label">Catatan Tambahan</label>
                <input type="text" name="notes" class="form-control" placeholder="Koordinator atau pihak terkait">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('addFollowUpModal').style.display = 'none';">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Tindak Lanjut</button>
            </div>
        </form>
    </div>
</div>
@endsection
