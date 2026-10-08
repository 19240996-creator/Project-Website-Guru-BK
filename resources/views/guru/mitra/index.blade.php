@extends('layouts.app')

@section('title', 'Mitra Kampus & Dunia Kerja - Guru BK')
@section('header_title', 'Perguruan Tinggi & Dunia Usaha/Industri Mitra')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
    <div>
        <h2 style="font-size: 20px; font-weight: 800; color: var(--color-text-main);">Mitra Strategis Sekolah</h2>
        <p style="font-size: 13px; color: var(--color-text-muted);">
            Hubungan kemitraan dengan perguruan tinggi (PTN/PTS) dan perusahaan industri untuk sosialisasi, magang, beasiswa, dan penyerapan kerja.
        </p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('guru.mitra.activities') }}" class="btn btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            <span>Jadwal Kegiatan Mitra</span>
        </a>
        <button type="button" class="btn btn-primary" onclick="document.getElementById('addPartnerModal').style.display = 'block';">
            + Tambah Mitra Baru
        </button>
    </div>
</div>

<div class="grid-4" style="margin-bottom: 24px;">
    <div class="stat-card">
        <span class="stat-label">Perguruan Tinggi</span>
        <span class="stat-value">{{ $stats['total_pt'] }}</span>
        <span class="stat-desc">PTN dan PTS Rekanan</span>
    </div>

    <div class="stat-card">
        <span class="stat-label">Perusahaan Industri</span>
        <span class="stat-value">{{ $stats['total_perusahaan'] }}</span>
        <span class="stat-desc">Dunia Usaha & Industri</span>
    </div>

    <div class="stat-card">
        <span class="stat-label">Kerja Sama Aktif</span>
        <span class="stat-value">{{ $stats['total_aktif'] }}</span>
        <span class="stat-desc">Memiliki MoU / PKS Berlaku</span>
    </div>

    <div class="stat-card">
        <span class="stat-label">Total Agenda Kegiatan</span>
        <span class="stat-value">{{ $stats['total_kegiatan'] }}</span>
        <span class="stat-desc">Sosialisasi, Seminar, Magang</span>
    </div>
</div>

<div class="tab-nav">
    <a href="{{ route('guru.mitra.index') }}" class="tab-btn {{ !request('type') || request('type') === 'all' ? 'active' : '' }}">Semua Mitra</a>
    <a href="{{ route('guru.mitra.index', ['type' => 'perguruan_tinggi']) }}" class="tab-btn {{ request('type') === 'perguruan_tinggi' ? 'active' : '' }}">Perguruan Tinggi</a>
    <a href="{{ route('guru.mitra.index', ['type' => 'perusahaan']) }}" class="tab-btn {{ request('type') === 'perusahaan' ? 'active' : '' }}">Perusahaan Industri</a>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        @if($partners->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead class="table-thead-navy">
                        <tr>
                            <th style="width: 26%;">Kode & Nama Mitra</th>
                            <th style="width: 15%;">Tipe & Kategori</th>
                            <th style="width: 20%;">Kota & Kontak</th>
                            <th style="width: 14%; text-align: center;">Status Kerja Sama (MoU)</th>
                            <th style="width: 13%;">Kegiatan & Peluang</th>
                            <th style="width: 12%; text-align: center; min-width: 130px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($partners as $p)
                            <tr>
                                <td>
                                    <strong>{{ $p->name }}</strong><br>
                                    <small style="color: var(--color-text-subtle);">{{ $p->code }} | {{ $p->partnership_doc_number ?: 'Tanpa Dokumen' }}</small>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $p->type === 'perguruan_tinggi' ? 'primary' : 'success' }}">
                                        {{ $p->type === 'perguruan_tinggi' ? 'Perguruan Tinggi' : 'Perusahaan' }}
                                    </span><br>
                                    <small style="color: var(--color-text-muted);">{{ $p->category ?: '-' }}</small>
                                </td>
                                <td>
                                    {{ $p->city ?: '-' }}<br>
                                    <small style="color: var(--color-text-subtle);">PIC: {{ $p->contact_person ?: '-' }} ({{ $p->phone ?: '-' }})</small>
                                </td>
                                <td style="text-align: center;">
                                    <span class="badge badge-{{ $p->partnership_status === 'aktif' ? 'success' : 'warning' }}">
                                        {{ ucfirst(str_replace('_', ' ', $p->partnership_status)) }}
                                    </span>
                                </td>
                                <td>
                                    <small>
                                        {{ $p->activities_count }} Kegiatan Terdaftar<br>
                                        {{ $p->opportunities_count }} Peluang Dipublikasikan
                                    </small>
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <a href="{{ route('guru.mitra.show', $p->id) }}" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; justify-content: center; white-space: nowrap;">
                                        Detail & Agenda
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $partners->links() }}
        @else
            <div class="empty-state">
                <p class="empty-state-title">Belum Ada Data Mitra</p>
                <p class="empty-state-desc">Tambahkan data perguruan tinggi atau perusahaan rekanan sekolah.</p>
            </div>
        @endif
    </div>
</div>

<!-- Modal Tambah Mitra -->
<div id="addPartnerModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 50; overflow-y: auto;">
    <div style="max-width: 650px; margin: 60px auto; background: #fff; padding: 24px; border-radius: var(--radius-md);">
        <h3 style="font-size: 16px; font-weight: 800; margin-bottom: 16px;">Tambah Mitra Baru</h3>
        <form action="{{ route('guru.mitra.store') }}" method="POST">
            @csrf
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Tipe Mitra *</label>
                    <select name="type" class="form-select" required>
                        <option value="perguruan_tinggi">Perguruan Tinggi (Kampus)</option>
                        <option value="perusahaan">Perusahaan / Industri (DUDI)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Kategori Spesifik</label>
                    <input type="text" name="category" class="form-control" placeholder="Contoh: PTN, PTS, Industri Otomotif, BUMN">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Nama Lembaga / Perguruan Tinggi / Perusahaan *</label>
                <input type="text" name="name" class="form-control" placeholder="Nama lengkap instansi..." required>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Kota / Lokasi</label>
                    <input type="text" name="city" class="form-control" placeholder="Contoh: Bandung, Jakarta">
                </div>

                <div class="form-group">
                    <label class="form-label">Alamat Website</label>
                    <input type="url" name="website" class="form-control" placeholder="https://...">
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Nama Kontak PIC</label>
                    <input type="text" name="contact_person" class="form-control" placeholder="Nama narahubung">
                </div>

                <div class="form-group">
                    <label class="form-label">No. Telepon / WhatsApp</label>
                    <input type="text" name="phone" class="form-control" placeholder="08...">
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Nomor Dokumen MoU / PKS</label>
                    <input type="text" name="partnership_doc_number" class="form-control" placeholder="PKS/001/2025">
                </div>

                <div class="form-group">
                    <label class="form-label">Status Kerja Sama *</label>
                    <select name="partnership_status" class="form-select" required>
                        <option value="aktif">Aktif</option>
                        <option value="akan_berakhir">Akan Berakhir</option>
                        <option value="berakhir">Berakhir</option>
                        <option value="tidak_aktif">Tidak Aktif</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Catatan Lingkup Kerja Sama</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="Bidang kerja sama yang disepakati..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('addPartnerModal').style.display = 'none';">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Mitra</button>
            </div>
        </form>
    </div>
</div>
@endsection
