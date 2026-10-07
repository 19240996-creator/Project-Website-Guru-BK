@extends('layouts.app')

@section('title', 'Peluang & Beasiswa Siswa - Guru BK')
@section('header_title', 'Peluang, Magang & Beasiswa Siswa')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
    <div>
        <h2 style="font-size: 20px; font-weight: 800; color: var(--color-text-main);">Peluang & Pengembangan Karier</h2>
        <p style="font-size: 13px; color: var(--color-text-muted);">
            Publikasi informasi beasiswa perguruan tinggi, program magang industri bersertifikat, dan rekrutmen kerja bagi siswa.
        </p>
    </div>
    <div>
        <button type="button" class="btn btn-primary" onclick="document.getElementById('addOpportunityModal').style.display = 'block';">
            + Buat Peluang Baru
        </button>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        @if($opportunities->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead class="table-thead-navy">
                        <tr>
                            <th>Kode & Judul Peluang</th>
                            <th>Tipe & Mitra Penyelenggara</th>
                            <th>Sasaran & Kuota</th>
                            <th>Batas Pendaftaran</th>
                            <th>Jumlah Pendaftar</th>
                            <th>Status</th>
                            <th style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($opportunities as $op)
                            <tr>
                                <td>
                                    <strong>{{ $op->title }}</strong><br>
                                    <small style="color: var(--color-text-subtle);">{{ $op->code }}</small>
                                </td>
                                <td>
                                    <span class="badge badge-secondary">{{ ucfirst(str_replace('_', ' ', $op->type)) }}</span><br>
                                    <small style="color: var(--color-text-muted);">{{ $op->partner ? $op->partner->name : 'Program Mandiri Sekolah' }}</small>
                                </td>
                                <td>
                                    {{ $op->target_audience ?: 'Semua Siswa' }}<br>
                                    <small style="color: var(--color-text-subtle);">Kuota: {{ $op->quota ? $op->quota . ' Siswa' : 'Tidak Dibatasi' }}</small>
                                </td>
                                <td>
                                    {{ $op->deadline ? $op->deadline->translatedFormat('d M Y') : 'Terbuka Terus' }}
                                </td>
                                <td>
                                    <strong>{{ $op->registrations->count() }} Pendaftar</strong>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $op->status === 'dipublikasikan' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($op->status) }}
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <a href="{{ route('guru.peluang.show', $op->id) }}" class="btn btn-secondary btn-sm">
                                        Pendaftar & Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="padding: 16px 20px;">
                {{ $opportunities->links() }}
            </div>
        @else
            <div class="empty-state">
                <p class="empty-state-title">Belum Ada Peluang Dipublikasikan</p>
                <p class="empty-state-desc">Publikasikan informasi beasiswa atau magang industri untuk siswa.</p>
            </div>
        @endif
    </div>
</div>

<!-- Modal Tambah Peluang -->
<div id="addOpportunityModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 50; overflow-y: auto;">
    <div style="max-width: 650px; margin: 60px auto; background: #fff; padding: 24px; border-radius: var(--radius-md);">
        <h3 style="font-size: 16px; font-weight: 800; margin-bottom: 16px;">Publikasikan Peluang Baru</h3>
        
        <form action="{{ route('guru.peluang.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Judul Peluang / Program *</label>
                <input type="text" name="title" class="form-control" placeholder="Contoh: Beasiswa Pendidikan Prestasi PTN 2026..." required>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Tipe Peluang *</label>
                    <select name="type" class="form-select" required>
                        <option value="beasiswa">Beasiswa Pendidikan</option>
                        <option value="magang">Program Magang Industri</option>
                        <option value="lowongan_kerja">Lowongan Kerja Langsung</option>
                        <option value="pelatihan">Pelatihan Vokasi</option>
                        <option value="sertifikasi">Sertifikasi Kompetensi</option>
                        <option value="kompetisi">Kompetisi & Lomba</option>
                        <option value="campus_visit">Campus Visit / Sosialisasi</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Mitra Penyelenggara (Opsional)</label>
                    <select name="partner_id" class="form-select">
                        <option value="">-- Tanpa Mitra / Mandiri --</option>
                        @foreach($partners as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Sasaran Peserta</label>
                    <input type="text" name="target_audience" class="form-control" placeholder="Contoh: Kelas XII Jurusan TKJ dan RPL">
                </div>

                <div class="form-group">
                    <label class="form-label">Batas Akhir Pendaftaran</label>
                    <input type="date" name="deadline" class="form-control">
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Kuota Peserta (Kosongkan bila bebas)</label>
                    <input type="number" name="quota" class="form-control" min="1" placeholder="Contoh: 20">
                </div>

                <div class="form-group">
                    <label class="form-label">Link Pendaftaran / Dokumen Eksternal</label>
                    <input type="url" name="registration_link" class="form-control" placeholder="https://...">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Deskripsi Lengkap Peluang *</label>
                <textarea name="description" class="form-control" rows="3" placeholder="Informasi manfaat, benefit, atau skema program..." required></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Persyaratan Pendaftaran</label>
                <textarea name="requirements" class="form-control" rows="2" placeholder="Nilai rata-rata, sertifikat prestasi, berkas CV..."></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Status Awal *</label>
                <select name="status" class="form-select" required>
                    <option value="dipublikasikan">Dipublikasikan (Bisa Dilihat & Didaftar Siswa)</option>
                    <option value="draf">Draf (Internal BK)</option>
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('addOpportunityModal').style.display = 'none';">Batal</button>
                <button type="submit" class="btn btn-primary">Publikasikan Peluang</button>
            </div>
        </form>
    </div>
</div>
@endsection
