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
    <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
        <a href="{{ route('guru.peluang.index') }}" class="btn btn-secondary">
            Kembali ke Daftar
        </a>
        <button type="button" class="btn btn-secondary" onclick="document.getElementById('editOpportunityModal').style.display = 'block';" style="display: inline-flex; align-items: center; gap: 5px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
            <span>Ubah Peluang</span>
        </button>
        <button type="button" class="btn" style="background-color: var(--color-danger-bg); color: var(--color-danger); border: 1px solid var(--color-danger-border); border-radius: var(--radius-sm); font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; padding: 6px 12px;" onclick="document.getElementById('deleteOpportunityModal').style.display = 'flex';">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
            <span>Hapus</span>
        </button>
    </div>
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
                    <thead class="table-thead-navy">
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

<!-- Modal Ubah Data Peluang -->
<div id="editOpportunityModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 50; overflow-y: auto;">
    <div style="max-width: 650px; margin: 50px auto; background: var(--color-surface, #fff); padding: 24px; border-radius: var(--radius-md); box-shadow: var(--shadow-lg);">
        <h3 style="font-size: 16px; font-weight: 800; margin-bottom: 16px; color: var(--color-text-main);">
            Ubah Informasi Program Peluang
        </h3>
        
        <form action="{{ route('guru.peluang.update', $opportunity->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Judul Peluang / Program *</label>
                <input type="text" name="title" class="form-control" value="{{ $opportunity->title }}" required>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Tipe Peluang *</label>
                    <select name="type" class="form-select" required>
                        <option value="beasiswa" {{ $opportunity->type === 'beasiswa' ? 'selected' : '' }}>Beasiswa Pendidikan</option>
                        <option value="magang" {{ $opportunity->type === 'magang' ? 'selected' : '' }}>Program Magang Industri</option>
                        <option value="lowongan_kerja" {{ $opportunity->type === 'lowongan_kerja' ? 'selected' : '' }}>Lowongan Kerja Langsung</option>
                        <option value="pelatihan" {{ $opportunity->type === 'pelatihan' ? 'selected' : '' }}>Pelatihan Vokasi</option>
                        <option value="sertifikasi" {{ $opportunity->type === 'sertifikasi' ? 'selected' : '' }}>Sertifikasi Kompetensi</option>
                        <option value="kompetisi" {{ $opportunity->type === 'kompetisi' ? 'selected' : '' }}>Kompetisi & Lomba</option>
                        <option value="campus_visit" {{ $opportunity->type === 'campus_visit' ? 'selected' : '' }}>Campus Visit / Sosialisasi</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Mitra Penyelenggara (Opsional)</label>
                    <select name="partner_id" class="form-select">
                        <option value="">-- Tanpa Mitra / Mandiri --</option>
                        @foreach($partners as $p)
                            <option value="{{ $p->id }}" {{ $opportunity->partner_id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Sasaran Peserta</label>
                    <input type="text" name="target_audience" class="form-control" value="{{ $opportunity->target_audience }}" placeholder="Contoh: Kelas XII Jurusan TKJ dan RPL">
                </div>

                <div class="form-group">
                    <label class="form-label">Batas Akhir Pendaftaran</label>
                    <input type="date" name="deadline" class="form-control" value="{{ $opportunity->deadline ? $opportunity->deadline->format('Y-m-d') : '' }}">
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Kuota Peserta (Kosongkan bila bebas)</label>
                    <input type="number" name="quota" class="form-control" min="1" value="{{ $opportunity->quota }}" placeholder="Contoh: 20">
                </div>

                <div class="form-group">
                    <label class="form-label">Link Pendaftaran / Dokumen Eksternal</label>
                    <input type="url" name="registration_link" class="form-control" value="{{ $opportunity->registration_link }}" placeholder="https://...">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Deskripsi Lengkap Peluang *</label>
                <textarea name="description" class="form-control" rows="3" required>{{ $opportunity->description }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Persyaratan Pendaftaran</label>
                <textarea name="requirements" class="form-control" rows="2">{{ $opportunity->requirements }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Status Program *</label>
                <select name="status" class="form-select" required>
                    <option value="dipublikasikan" {{ $opportunity->status === 'dipublikasikan' ? 'selected' : '' }}>Dipublikasikan (Bisa Dilihat Siswa)</option>
                    <option value="draf" {{ $opportunity->status === 'draf' ? 'selected' : '' }}>Draf (Internal BK)</option>
                    <option value="ditutup" {{ $opportunity->status === 'ditutup' ? 'selected' : '' }}>Ditutup (Pendaftaran Berakhir)</option>
                    <option value="selesai" {{ $opportunity->status === 'selesai' ? 'selected' : '' }}>Selesai (Program Rampung)</option>
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('editOpportunityModal').style.display = 'none';">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Konfirmasi Hapus Peluang -->
<div id="deleteOpportunityModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.55); z-index: 100; backdrop-filter: blur(2px); align-items: center; justify-content: center;">
    <div style="max-width: 450px; width: 90%; background: var(--color-surface); border-radius: var(--radius-md); box-shadow: var(--shadow-lg); padding: 22px; border: 1px solid var(--color-border);">
        <div style="display: flex; align-items: flex-start; gap: 14px;">
            <div style="width: 42px; height: 42px; border-radius: 50%; background-color: var(--color-danger-bg); color: var(--color-danger); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    <line x1="10" y1="11" x2="10" y2="17"></line>
                    <line x1="14" y1="11" x2="14" y2="17"></line>
                </svg>
            </div>
            <div style="flex: 1;">
                <h4 style="font-size: 15px; font-weight: 700; color: var(--color-text-main); margin-bottom: 6px;">
                    Hapus Program Peluang?
                </h4>
                <p style="font-size: 13px; color: var(--color-text-muted); line-height: 1.5; margin-bottom: 10px;">
                    Apakah Anda yakin ingin menghapus peluang <strong style="color: var(--color-text-main);">"{{ $opportunity->title }}"</strong>?
                </p>
                @if($opportunity->registrations->count() > 0)
                    <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid var(--color-danger-border); border-radius: var(--radius-sm); padding: 8px 12px; margin-bottom: 12px; font-size: 12px; color: var(--color-danger);">
                        ⚠️ Terdapat <strong>{{ $opportunity->registrations->count() }}</strong> pendaftar siswa pada peluang ini yang juga akan dihapus.
                    </div>
                @endif
                <p style="font-size: 12px; color: var(--color-text-subtle); line-height: 1.4; margin-bottom: 14px;">
                    Tindakan ini bersifat permanen dan riwayat penghapusan akan dicatat ke dalam <strong>Audit Log Aktivitas</strong>.
                </p>
                <div style="display: flex; justify-content: flex-end; gap: 8px;">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('deleteOpportunityModal').style.display = 'none';" style="padding: 6px 14px;">
                        Batal
                    </button>
                    <form action="{{ route('guru.peluang.destroy', $opportunity->id) }}" method="POST" style="margin: 0;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm" style="padding: 6px 14px; background-color: var(--color-danger); color: #ffffff; border: 1px solid var(--color-danger); border-radius: var(--radius-sm); font-weight: 600; cursor: pointer;">
                            Ya, Hapus Peluang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('click', function(e) {
    const editModal = document.getElementById('editOpportunityModal');
    if (editModal && e.target === editModal) {
        editModal.style.display = 'none';
    }

    const delModal = document.getElementById('deleteOpportunityModal');
    if (delModal && e.target === delModal) {
        delModal.style.display = 'none';
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const delModal = document.getElementById('deleteOpportunityModal');
        if (delModal && delModal.style.display !== 'none') {
            delModal.style.display = 'none';
            return;
        }

        const editModal = document.getElementById('editOpportunityModal');
        if (editModal && editModal.style.display !== 'none') {
            editModal.style.display = 'none';
            return;
        }
    }
});
</script>
@endsection
