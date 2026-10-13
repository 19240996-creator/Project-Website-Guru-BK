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

<!-- Filter Bar (Horizontal) -->
<div class="card" style="margin-bottom: 20px;">
    <div class="card-body" style="padding: 16px 20px;">
        <form action="{{ route('guru.peluang.index') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)) 100px; gap: 12px; align-items: flex-end;">
            <div>
                <label class="form-label" style="font-size: 12px; margin-bottom: 6px; display: block;">Cari Peluang / Mitra / Kode</label>
                <input type="text" name="q" class="form-control" style="min-height: 38px; font-size: 13px;" value="{{ request('q') }}" placeholder="Ketik kata kunci...">
            </div>

            <div>
                <label class="form-label" style="font-size: 12px; margin-bottom: 6px; display: block;">Tipe Peluang</label>
                <select name="type" class="form-select" style="min-height: 38px; font-size: 13px;">
                    <option value="">Semua Tipe</option>
                    <option value="beasiswa" {{ request('type') === 'beasiswa' ? 'selected' : '' }}>Beasiswa</option>
                    <option value="magang" {{ request('type') === 'magang' ? 'selected' : '' }}>Magang Industri</option>
                    <option value="lowongan_kerja" {{ request('type') === 'lowongan_kerja' ? 'selected' : '' }}>Lowongan Kerja</option>
                    <option value="pelatihan" {{ request('type') === 'pelatihan' ? 'selected' : '' }}>Pelatihan & Workshop</option>
                    <option value="sertifikasi" {{ request('type') === 'sertifikasi' ? 'selected' : '' }}>Sertifikasi</option>
                    <option value="kompetisi" {{ request('type') === 'kompetisi' ? 'selected' : '' }}>Kompetisi</option>
                    <option value="campus_visit" {{ request('type') === 'campus_visit' ? 'selected' : '' }}>Campus Visit</option>
                </select>
            </div>

            <div>
                <label class="form-label" style="font-size: 12px; margin-bottom: 6px; display: block;">Mitra Penyelenggara</label>
                <select name="partner_id" class="form-select" style="min-height: 38px; font-size: 13px;">
                    <option value="">Semua Mitra</option>
                    @foreach($partners as $p)
                        <option value="{{ $p->id }}" {{ request('partner_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label" style="font-size: 12px; margin-bottom: 6px; display: block;">Status</label>
                <select name="status" class="form-select" style="min-height: 38px; font-size: 13px;">
                    <option value="">Semua Status</option>
                    <option value="dipublikasikan" {{ request('status') === 'dipublikasikan' ? 'selected' : '' }}>Dipublikasikan</option>
                    <option value="draf" {{ request('status') === 'draf' ? 'selected' : '' }}>Draf</option>
                    <option value="ditutup" {{ request('status') === 'ditutup' ? 'selected' : '' }}>Ditutup</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-secondary" style="flex: 1; min-height: 38px;">
                    Filter
                </button>
                @if(request()->hasAny(['q', 'type', 'partner_id', 'status']))
                    <a href="{{ route('guru.peluang.index') }}" class="btn btn-secondary" style="min-height: 38px; display: inline-flex; align-items: center; justify-content: center; padding: 0 12px;" title="Reset Filter">
                        ✕
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header card-header-navy" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            <h3 class="card-title" style="margin: 0;">Daftar Peluang & Program Karier (Total: {{ $opportunities->total() }})</h3>
        </div>
        @if(request()->hasAny(['q', 'type', 'partner_id', 'status']))
            <a href="{{ route('guru.peluang.index') }}" class="btn btn-secondary btn-sm" style="font-size: 11px; padding: 4px 10px; color: #ffffff; border-color: rgba(255,255,255,0.3); background: rgba(255,255,255,0.1);" title="Reset semua filter">
                ✕ Reset Filter
            </a>
        @endif
    </div>
    <div class="card-body" style="padding: 0;">
        @if($opportunities->count() > 0)
            <div class="table-responsive">
                <table class="table" style="width: 100%; border-collapse: collapse;">
                    <thead class="table-thead-navy">
                        <tr>
                            <th style="min-width: 170px;">Kode & Judul Peluang</th>
                            <th style="min-width: 150px;">Tipe & Mitra Penyelenggara</th>
                            <th style="min-width: 130px;">Sasaran & Kuota</th>
                            <th style="min-width: 120px;">Batas Pendaftaran</th>
                            <th style="min-width: 110px;">Jumlah Pendaftar</th>
                            <th style="min-width: 95px; text-align: center;">Status</th>
                            <th style="text-align: center; width: 195px; min-width: 190px;">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($opportunities as $op)
                            @php
                                $opData = [
                                    'id' => $op->id,
                                    'code' => $op->code,
                                    'title' => $op->title,
                                    'type' => $op->type,
                                    'partner_id' => $op->partner_id,
                                    'description' => $op->description,
                                    'requirements' => $op->requirements,
                                    'target_audience' => $op->target_audience,
                                    'deadline' => $op->deadline ? $op->deadline->format('Y-m-d') : '',
                                    'quota' => $op->quota,
                                    'registration_link' => $op->registration_link,
                                    'status' => $op->status,
                                ];
                            @endphp
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
                                <td style="text-align: center;">
                                    <span class="badge badge-{{ $op->status === 'dipublikasikan' ? 'success' : ($op->status === 'ditutup' ? 'danger' : 'secondary') }}">
                                        {{ ucfirst($op->status) }}
                                    </span>
                                </td>
                                <td style="text-align: center; vertical-align: middle; white-space: nowrap; padding: 12px 10px;">
                                    <div style="display: inline-flex; align-items: center; justify-content: center; gap: 5px;">
                                        <a href="{{ route('guru.peluang.show', $op->id) }}" 
                                           class="btn btn-secondary btn-sm" 
                                           style="padding: 4px 8px; font-size: 11px; display: inline-flex; align-items: center; gap: 4px; white-space: nowrap;" 
                                           title="Lihat Profil & Pendaftar">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                            <span>Profile</span>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-secondary btn-sm" 
                                                style="padding: 4px 8px; font-size: 11px; display: inline-flex; align-items: center; gap: 4px; white-space: nowrap;" 
                                                onclick='openEditOpportunityModal(@json($opData))' 
                                                title="Ubah Data Peluang">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                            <span>Ubah</span>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-sm" 
                                                style="padding: 4px 8px; font-size: 11px; background-color: var(--color-danger-bg); color: var(--color-danger); border: 1px solid var(--color-danger-border); border-radius: var(--radius-sm); cursor: pointer; display: inline-flex; align-items: center; gap: 4px; white-space: nowrap;" 
                                                onclick="openDeleteOpportunityModal({{ $op->id }}, '{{ addslashes($op->title) }}', {{ $op->registrations->count() }})" 
                                                title="Hapus Program Peluang">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                            <span>Hapus</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $opportunities->links() }}
        @else
            <div class="empty-state" style="padding: 40px 20px; text-align: center;">
                <p class="empty-state-title" style="font-weight: 700; color: var(--color-text-main); font-size: 15px; margin-bottom: 6px;">Data Peluang Tidak Ditemukan</p>
                <p class="empty-state-desc" style="font-size: 13px; color: var(--color-text-muted);">
                    @if(request()->hasAny(['q', 'type', 'partner_id', 'status']))
                        Tidak ada data peluang yang sesuai dengan filter pencarian Anda. Silakan coba kata kunci lain atau <a href="{{ route('guru.peluang.index') }}" style="color: var(--color-primary); text-decoration: underline;">reset filter</a>.
                    @else
                        Publikasikan informasi beasiswa atau magang industri untuk siswa.
                    @endif
                </p>
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

<!-- Modal Ubah Data Peluang -->
<div id="editOpportunityModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 50; overflow-y: auto;">
    <div style="max-width: 650px; margin: 50px auto; background: var(--color-surface, #fff); padding: 24px; border-radius: var(--radius-md); box-shadow: var(--shadow-lg);">
        <h3 style="font-size: 16px; font-weight: 800; margin-bottom: 16px; color: var(--color-text-main);">
            Ubah Informasi Program Peluang
        </h3>
        
        <form id="editOpportunityForm" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Judul Peluang / Program *</label>
                <input type="text" name="title" id="edit_title" class="form-control" placeholder="Contoh: Beasiswa Pendidikan Prestasi..." required>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Tipe Peluang *</label>
                    <select name="type" id="edit_type" class="form-select" required>
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
                    <select name="partner_id" id="edit_partner_id" class="form-select">
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
                    <input type="text" name="target_audience" id="edit_target_audience" class="form-control" placeholder="Contoh: Kelas XII Jurusan TKJ dan RPL">
                </div>

                <div class="form-group">
                    <label class="form-label">Batas Akhir Pendaftaran</label>
                    <input type="date" name="deadline" id="edit_deadline" class="form-control">
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Kuota Peserta (Kosongkan bila bebas)</label>
                    <input type="number" name="quota" id="edit_quota" class="form-control" min="1" placeholder="Contoh: 20">
                </div>

                <div class="form-group">
                    <label class="form-label">Link Pendaftaran / Dokumen Eksternal</label>
                    <input type="url" name="registration_link" id="edit_registration_link" class="form-control" placeholder="https://...">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Deskripsi Lengkap Peluang *</label>
                <textarea name="description" id="edit_description" class="form-control" rows="3" placeholder="Informasi manfaat, benefit, atau skema program..." required></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Persyaratan Pendaftaran</label>
                <textarea name="requirements" id="edit_requirements" class="form-control" rows="2" placeholder="Nilai rata-rata, sertifikat prestasi, berkas CV..."></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Status Program *</label>
                <select name="status" id="edit_status" class="form-select" required>
                    <option value="dipublikasikan">Dipublikasikan (Bisa Dilihat Siswa)</option>
                    <option value="draf">Draf (Internal BK)</option>
                    <option value="ditutup">Ditutup (Pendaftaran Berakhir)</option>
                    <option value="selesai">Selesai (Program Rampung)</option>
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button type="button" class="btn btn-secondary" onclick="closeEditOpportunityModal()">Batal</button>
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
                    Apakah Anda yakin ingin menghapus peluang <strong id="deleteOpportunityTitle" style="color: var(--color-text-main);"></strong>?
                </p>
                <div id="deleteRegWarning" style="display: none; background: rgba(239, 68, 68, 0.1); border: 1px solid var(--color-danger-border); border-radius: var(--radius-sm); padding: 8px 12px; margin-bottom: 12px; font-size: 12px; color: var(--color-danger);">
                    ⚠️ Terdapat <strong id="deleteRegCount">0</strong> pendaftar siswa pada peluang ini yang juga akan dihapus.
                </div>
                <p style="font-size: 12px; color: var(--color-text-subtle); line-height: 1.4; margin-bottom: 14px;">
                    Tindakan ini bersifat permanen dan riwayat penghapusan akan dicatat ke dalam <strong>Audit Log Aktivitas</strong>.
                </p>
                <div style="display: flex; justify-content: flex-end; gap: 8px;">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="closeDeleteOpportunityModal()" style="padding: 6px 14px;">
                        Batal
                    </button>
                    <form id="deleteOpportunityForm" method="POST" style="margin: 0;">
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
function openEditOpportunityModal(data) {
    const form = document.getElementById('editOpportunityForm');
    form.action = '/guru/peluang/' + data.id;

    document.getElementById('edit_title').value = data.title || '';
    document.getElementById('edit_type').value = data.type || 'beasiswa';
    document.getElementById('edit_partner_id').value = data.partner_id || '';
    document.getElementById('edit_target_audience').value = data.target_audience || '';
    document.getElementById('edit_deadline').value = data.deadline || '';
    document.getElementById('edit_quota').value = data.quota || '';
    document.getElementById('edit_registration_link').value = data.registration_link || '';
    document.getElementById('edit_description').value = data.description || '';
    document.getElementById('edit_requirements').value = data.requirements || '';
    document.getElementById('edit_status').value = data.status || 'dipublikasikan';

    const modal = document.getElementById('editOpportunityModal');
    if (modal) modal.style.display = 'block';
}

function closeEditOpportunityModal() {
    const modal = document.getElementById('editOpportunityModal');
    if (modal) modal.style.display = 'none';
}

function openDeleteOpportunityModal(id, title, regCount) {
    const form = document.getElementById('deleteOpportunityForm');
    form.action = '/guru/peluang/' + id;

    const titleEl = document.getElementById('deleteOpportunityTitle');
    if (titleEl) titleEl.textContent = title ? '"' + title + '"' : '';

    const warningEl = document.getElementById('deleteRegWarning');
    const countEl = document.getElementById('deleteRegCount');
    if (warningEl && countEl) {
        if (regCount > 0) {
            countEl.textContent = regCount;
            warningEl.style.display = 'block';
        } else {
            warningEl.style.display = 'none';
        }
    }

    const modal = document.getElementById('deleteOpportunityModal');
    if (modal) modal.style.display = 'flex';
}

function closeDeleteOpportunityModal() {
    const modal = document.getElementById('deleteOpportunityModal');
    if (modal) modal.style.display = 'none';
}

document.addEventListener('click', function(e) {
    const addModal = document.getElementById('addOpportunityModal');
    if (addModal && e.target === addModal) {
        addModal.style.display = 'none';
    }

    const editModal = document.getElementById('editOpportunityModal');
    if (editModal && e.target === editModal) {
        closeEditOpportunityModal();
    }

    const delModal = document.getElementById('deleteOpportunityModal');
    if (delModal && e.target === delModal) {
        closeDeleteOpportunityModal();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const delModal = document.getElementById('deleteOpportunityModal');
        if (delModal && delModal.style.display !== 'none') {
            closeDeleteOpportunityModal();
            return;
        }

        const editModal = document.getElementById('editOpportunityModal');
        if (editModal && editModal.style.display !== 'none') {
            closeEditOpportunityModal();
            return;
        }

        const addModal = document.getElementById('addOpportunityModal');
        if (addModal && addModal.style.display !== 'none') {
            addModal.style.display = 'none';
            return;
        }
    }
});
</script>
@endsection
