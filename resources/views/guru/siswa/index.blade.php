@extends('layouts.app')

@section('title', 'Data & Profil Siswa - Guru BK')
@section('header_title', 'Data & Administrasi Siswa')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: gap: 12px;">
    <div>
        <h2 style="font-size: 20px; font-weight: 800; color: var(--color-text-main);">Daftar Siswa Bimbingan</h2>
        <p style="font-size: 13px; color: var(--color-text-muted);">
            Basis data siswa terpadu untuk pencatatan profil, rekam jejak konseling, dan perencanaan karier.
        </p>
    </div>
    <div style="display: flex; gap: 10px;">
        <button type="button" class="btn btn-secondary" onclick="document.getElementById('importSection').scrollIntoView({ behavior: 'smooth' });">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            <span>Impor Data Siswa</span>
        </button>
        <a href="{{ route('guru.siswa.create') }}" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            <span>Tambah Siswa Baru</span>
        </a>
    </div>
</div>

<!-- Filter Bar -->
<div class="card" style="margin-bottom: 20px;">
    <div class="card-body" style="padding: 16px 20px;">
        <form action="{{ route('guru.siswa.index') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)) 100px; gap: 12px; align-items: flex-end;">
            <div>
                <label class="form-label" style="font-size: 12px;">Cari Nama / NISN / NIS</label>
                <input type="text" name="q" class="form-control" style="min-height: 38px; font-size: 13px;" value="{{ request('q') }}" placeholder="Ketik kata kunci...">
            </div>

            <div>
                <label class="form-label" style="font-size: 12px;">Kelas</label>
                <select name="class_id" class="form-select" style="min-height: 38px; font-size: 13px;">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label" style="font-size: 12px;">Tingkat Perhatian</label>
                <select name="attention_level" class="form-select" style="min-height: 38px; font-size: 13px;">
                    <option value="">Semua Tingkat</option>
                    <option value="normal" {{ request('attention_level') == 'normal' ? 'selected' : '' }}>Normal</option>
                    <option value="perlu_perhatian" {{ request('attention_level') == 'perlu_perhatian' ? 'selected' : '' }}>Perlu Perhatian</option>
                    <option value="prioritas" {{ request('attention_level') == 'prioritas' ? 'selected' : '' }}>Prioritas</option>
                    <option value="segera_ditindaklanjuti" {{ request('attention_level') == 'segera_ditindaklanjuti' ? 'selected' : '' }}>Segera Ditindaklanjuti</option>
                </select>
            </div>

            <div>
                <label class="form-label" style="font-size: 12px;">Status Siswa</label>
                <select name="status" class="form-select" style="min-height: 38px; font-size: 13px;">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="lulus" {{ request('status') == 'lulus' ? 'selected' : '' }}>Lulus</option>
                    <option value="pindah" {{ request('status') == 'pindah' ? 'selected' : '' }}>Pindah</option>
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

<!-- Table Daftar Siswa -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Data Siswa (Total: {{ $students->total() }})</h3>
    </div>
    <div class="card-body" style="padding: 0;">
        @if($students->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Identitas Siswa</th>
                            <th>Kelas & Jurusan</th>
                            <th>Kontak</th>
                            <th>Status Perhatian</th>
                            <th>Status Siswa</th>
                            <th style="text-align: right;">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $s)
                            <tr>
                                <td>
                                    <div style="font-weight: 700; color: var(--color-text-main);">{{ $s->name }}</div>
                                    <small style="color: var(--color-text-subtle);">NISN: {{ $s->nisn }} | NIS: {{ $s->nis }}</small>
                                </td>
                                <td>
                                    <strong>{{ $s->studentClass ? $s->studentClass->name : '-' }}</strong><br>
                                    <small style="color: var(--color-text-muted);">{{ $s->studentClass ? $s->studentClass->major : '-' }}</small>
                                </td>
                                <td>
                                    {{ $s->phone ?: '-' }}<br>
                                    <small style="color: var(--color-text-subtle);">Ortu: {{ $s->parent_name ?: '-' }}</small>
                                </td>
                                <td>
                                    @if($s->attention_level === 'normal')
                                        <span class="badge badge-secondary">Normal</span>
                                    @elseif($s->attention_level === 'perlu_perhatian')
                                        <span class="badge badge-warning">Perlu Perhatian</span>
                                    @elseif($s->attention_level === 'prioritas')
                                        <span class="badge badge-danger">Prioritas</span>
                                    @else
                                        <span class="badge badge-danger">Segera Ditindaklanjuti</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $s->status === 'aktif' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($s->status) }}
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <a href="{{ route('guru.siswa.show', $s->id) }}" class="btn btn-secondary btn-sm" title="Lihat Profil 360">
                                        Profil 360°
                                    </a>
                                    <a href="{{ route('guru.siswa.edit', $s->id) }}" class="btn btn-secondary btn-sm" title="Edit">
                                        Ubah
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="padding: 16px 20px;">
                {{ $students->links() }}
            </div>
        @else
            <div class="empty-state">
                <p class="empty-state-title">Data Siswa Tidak Ditemukan</p>
                <p class="empty-state-desc">Belum ada siswa yang sesuai dengan filter pencarian yang Anda pilih.</p>
            </div>
        @endif
    </div>
</div>

<!-- Section Impor CSV / Excel (Section 2 Blueprint) -->
<div id="importSection" class="card" style="margin-top: 32px; border-top: 3px solid var(--color-accent);">
    <div class="card-header">
        <h3 class="card-title">Impor Data Siswa Secara Massal (Format CSV / Excel)</h3>
    </div>
    <div class="card-body">
        <p style="font-size: 13px; color: var(--color-text-muted); margin-bottom: 16px;">
            Sesuai aturan sistem, pilih kelas terlebih dahulu sebelum mengimpor file. Sistem akan otomatis membuatkan akun login siswa dengan username NISN dan kata sandi default <code>password123</code>.
        </p>

        <form action="{{ route('guru.siswa.import') }}" method="POST" enctype="multipart/form-data" style="max-width: 600px;">
            @csrf
            <div class="form-group">
                <label class="form-label">Pilih Kelas Tujuan Impor</label>
                <select name="student_class_id" class="form-select" required>
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->major }})</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Pilih File CSV Siswa</label>
                <input type="file" name="csv_file" class="form-control" accept=".csv, .txt" required>
                <div class="form-hint">
                    Format kolom CSV: <code>NIS, NISN, Nama Lengkap, Jenis Kelamin (L/P), No. HP</code>
                </div>
            </div>

            <button type="submit" class="btn btn-accent">
                Mulai Proses Impor Siswa
            </button>
        </form>
    </div>
</div>
@endsection
