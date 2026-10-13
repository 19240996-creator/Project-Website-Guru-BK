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
        <button type="button" class="btn btn-primary" onclick="openAddPartnerModal()">
            + Tambah Mitra Baru
        </button>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success" style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if(session('warning'))
    <div class="alert alert-warning" style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
        <span>{{ session('warning') }}</span>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger" style="margin-bottom: 20px;">
        <ul style="margin: 0; padding-left: 20px; font-size: 13px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

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

<!-- Bilah Filter & Pencarian -->
<div class="card" style="margin-bottom: 20px;">
    <div class="card-body" style="padding: 16px 20px;">
        <form action="{{ route('guru.mitra.index') }}" method="GET" id="filterMitraForm">
            <div style="display: grid; grid-template-columns: minmax(220px, 2fr) minmax(160px, 1fr) minmax(160px, 1fr) minmax(160px, 1fr) auto auto; gap: 12px; align-items: flex-end;">
                <!-- Pencarian Kata Kunci -->
                <div>
                    <label class="form-label" style="font-size: 12px; margin-bottom: 6px; font-weight: 600;">Cari Mitra</label>
                    <div style="position: relative;">
                        <input type="text" 
                               name="q" 
                               value="{{ request('q') }}" 
                               class="form-control" 
                               placeholder="Nama, kode, kota, PIC, nomor MoU..." 
                               style="min-height: 38px; font-size: 13px; padding-left: 36px;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--color-text-subtle);">
                            <circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </div>
                </div>

                <!-- Filter Tipe Mitra -->
                <div>
                    <label class="form-label" style="font-size: 12px; margin-bottom: 6px; font-weight: 600;">Tipe Mitra</label>
                    <select name="type" class="form-select" style="min-height: 38px; font-size: 13px;">
                        <option value="all" {{ !request('type') || request('type') === 'all' ? 'selected' : '' }}>Semua Tipe</option>
                        <option value="perguruan_tinggi" {{ request('type') === 'perguruan_tinggi' ? 'selected' : '' }}>Perguruan Tinggi</option>
                        <option value="perusahaan" {{ request('type') === 'perusahaan' ? 'selected' : '' }}>Perusahaan Industri</option>
                    </select>
                </div>

                <!-- Filter Status Kerja Sama -->
                <div>
                    <label class="form-label" style="font-size: 12px; margin-bottom: 6px; font-weight: 600;">Status MoU</label>
                    <select name="status" class="form-select" style="min-height: 38px; font-size: 13px;">
                        <option value="all" {{ !request('status') || request('status') === 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="akan_berakhir" {{ request('status') === 'akan_berakhir' ? 'selected' : '' }}>Akan Berakhir</option>
                        <option value="berakhir" {{ request('status') === 'berakhir' ? 'selected' : '' }}>Berakhir</option>
                        <option value="tidak_aktif" {{ request('status') === 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>

                <!-- Filter Kategori / Bidang -->
                <div>
                    <label class="form-label" style="font-size: 12px; margin-bottom: 6px; font-weight: 600;">Kategori / Bidang</label>
                    <select name="category" class="form-select" style="min-height: 38px; font-size: 13px;">
                        <option value="all" {{ !request('category') || request('category') === 'all' ? 'selected' : '' }}>Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Tombol Terapkan Filter -->
                <div>
                    <button type="submit" class="btn btn-primary" style="min-height: 38px; padding: 0 16px; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                        <span>Filter</span>
                    </button>
                </div>

                <!-- Tombol Reset Filter -->
                @if(request()->hasAny(['q', 'status', 'category', 'city', 'type']) && (request('type') !== 'all' || request('status') !== 'all' || request('category') !== 'all' || filled(request('q'))))
                    <div>
                        <a href="{{ route('guru.mitra.index') }}" class="btn btn-secondary" style="min-height: 38px; padding: 0 14px; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; color: var(--color-text-muted);" title="Reset semua filter">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            <span>Reset</span>
                        </a>
                    </div>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="tab-nav" style="margin-bottom: 16px;">
    @php
        $queryAll = request()->except(['type', 'page']);
        $queryPT = array_merge(request()->except(['page']), ['type' => 'perguruan_tinggi']);
        $queryPerusahaan = array_merge(request()->except(['page']), ['type' => 'perusahaan']);
    @endphp
    <a href="{{ route('guru.mitra.index', $queryAll) }}" class="tab-btn {{ !request('type') || request('type') === 'all' ? 'active' : '' }}">
        Semua Mitra
    </a>
    <a href="{{ route('guru.mitra.index', $queryPT) }}" class="tab-btn {{ request('type') === 'perguruan_tinggi' ? 'active' : '' }}">
        Perguruan Tinggi
    </a>
    <a href="{{ route('guru.mitra.index', $queryPerusahaan) }}" class="tab-btn {{ request('type') === 'perusahaan' ? 'active' : '' }}">
        Perusahaan Industri
    </a>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        @if($partners->count() > 0)
            <div class="table-responsive">
                <table class="table" style="width: 100%; border-collapse: collapse;">
                    <thead class="table-thead-navy">
                        <tr>
                            <th style="width: 25%; text-align: left; padding: 12px 14px;">Kode & Nama Mitra</th>
                            <th style="width: 15%; text-align: left; padding: 12px 14px;">Tipe & Kategori</th>
                            <th style="width: 19%; text-align: left; padding: 12px 14px;">Kota & Kontak</th>
                            <th style="width: 13%; text-align: center; padding: 12px 14px;">Status MoU</th>
                            <th style="width: 12%; text-align: left; padding: 12px 14px;">Kegiatan & Peluang</th>
                            <th style="width: 16%; text-align: center; min-width: 190px; padding: 12px 14px;">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($partners as $p)
                            @php
                                $partnerData = [
                                    'id' => $p->id,
                                    'code' => $p->code,
                                    'type' => $p->type,
                                    'category' => $p->category ?: '',
                                    'name' => $p->name,
                                    'city' => $p->city ?: '',
                                    'address' => $p->address ?: '',
                                    'website' => $p->website ?: '',
                                    'contact_person' => $p->contact_person ?: '',
                                    'phone' => $p->phone ?: '',
                                    'email' => $p->email ?: '',
                                    'partnership_doc_number' => $p->partnership_doc_number ?: '',
                                    'partnership_status' => $p->partnership_status,
                                    'partnership_start_date' => $p->partnership_start_date ? $p->partnership_start_date->format('Y-m-d') : '',
                                    'partnership_end_date' => $p->partnership_end_date ? $p->partnership_end_date->format('Y-m-d') : '',
                                    'notes' => $p->notes ?: '',
                                ];
                            @endphp
                            <tr>
                                <td style="padding: 12px 14px; vertical-align: middle;">
                                    <strong style="color: var(--color-text-main); font-size: 14px;">{{ $p->name }}</strong><br>
                                    <small style="color: var(--color-text-subtle);">{{ $p->code }} | {{ $p->partnership_doc_number ?: 'Tanpa Dokumen' }}</small>
                                </td>
                                <td style="padding: 12px 14px; vertical-align: middle;">
                                    <span class="badge badge-{{ $p->type === 'perguruan_tinggi' ? 'primary' : 'success' }}" style="font-size: 11px;">
                                        {{ $p->type === 'perguruan_tinggi' ? 'Perguruan Tinggi' : 'Perusahaan' }}
                                    </span><br>
                                    <small style="color: var(--color-text-muted);">{{ $p->category ?: '-' }}</small>
                                </td>
                                <td style="padding: 12px 14px; vertical-align: middle;">
                                    <div style="font-weight: 500; color: var(--color-text-main);">{{ $p->city ?: '-' }}</div>
                                    <small style="color: var(--color-text-subtle);">PIC: {{ $p->contact_person ?: '-' }} ({{ $p->phone ?: '-' }})</small>
                                </td>
                                <td style="padding: 12px 14px; text-align: center; vertical-align: middle;">
                                    <span class="badge badge-{{ $p->partnership_status === 'aktif' ? 'success' : ($p->partnership_status === 'akan_berakhir' ? 'warning' : 'secondary') }}" style="font-size: 11px;">
                                        {{ ucfirst(str_replace('_', ' ', $p->partnership_status)) }}
                                    </span>
                                </td>
                                <td style="padding: 12px 14px; vertical-align: middle;">
                                    <small style="color: var(--color-text-main); line-height: 1.4; display: block;">
                                        <span style="font-weight: 600;">{{ $p->activities_count }}</span> Kegiatan Terdaftar<br>
                                        <span style="font-weight: 600;">{{ $p->opportunities_count }}</span> Peluang Publik
                                    </small>
                                </td>
                                <td style="padding: 12px 14px; text-align: center; vertical-align: middle; white-space: nowrap;">
                                    <div style="display: inline-flex; align-items: center; justify-content: center; gap: 5px;">
                                        <!-- Tindakan Profile -->
                                        <a href="{{ route('guru.mitra.show', $p->id) }}" 
                                           class="btn btn-secondary btn-sm" 
                                           style="padding: 4px 8px; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;" 
                                           title="Lihat Profil & Rekam Mitra">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                                <circle cx="12" cy="7" r="4"></circle>
                                            </svg>
                                            <span>Profile</span>
                                        </a>

                                        <!-- Tindakan Ubah -->
                                        <button type="button" 
                                                class="btn btn-secondary btn-sm" 
                                                style="padding: 4px 8px; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;" 
                                                onclick='openEditPartnerModal(@json($partnerData))' 
                                                title="Ubah Data Mitra">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                            </svg>
                                            <span>Ubah</span>
                                        </button>

                                        <!-- Tindakan Hapus -->
                                        <button type="button" 
                                                class="btn btn-sm" 
                                                style="padding: 4px 8px; font-size: 12px; background-color: var(--color-danger-bg); color: var(--color-danger); border: 1px solid var(--color-danger-border); border-radius: var(--radius-sm); cursor: pointer; display: inline-flex; align-items: center; gap: 4px;" 
                                                onclick="openDeletePartnerModal('{{ $p->id }}', '{{ addslashes($p->name) }}', '{{ $p->code }}', {{ $p->activities_count }}, {{ $p->opportunities_count }})" 
                                                title="Hapus Data Mitra">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                            </svg>
                                            <span>Hapus</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="padding: 16px 20px;">
                {{ $partners->links() }}
            </div>
        @else
            <div class="empty-state" style="padding: 48px 20px; text-align: center;">
                <p class="empty-state-title" style="font-size: 16px; font-weight: 700; color: var(--color-text-main); margin-bottom: 6px;">Tidak Ada Data Mitra</p>
                <p class="empty-state-desc" style="font-size: 13px; color: var(--color-text-muted); margin-bottom: 16px;">
                    @if(request()->hasAny(['q', 'status', 'category', 'city', 'type']))
                        Tidak ditemukan mitra yang sesuai dengan kriteria filter saat ini.
                    @else
                        Belum ada data perguruan tinggi atau perusahaan rekanan sekolah.
                    @endif
                </p>
                @if(request()->hasAny(['q', 'status', 'category', 'city', 'type']))
                    <a href="{{ route('guru.mitra.index') }}" class="btn btn-secondary btn-sm">
                        Reset Filter
                    </a>
                @else
                    <button type="button" class="btn btn-primary btn-sm" onclick="openAddPartnerModal()">
                        + Tambah Mitra Baru
                    </button>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Modal Tambah Mitra Baru -->
<div id="addPartnerModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.55); z-index: 90; backdrop-filter: blur(2px); overflow-y: auto;">
    <div style="max-width: 680px; margin: 40px auto; background: var(--color-surface); padding: 24px; border-radius: var(--radius-md); box-shadow: var(--shadow-lg); border: 1px solid var(--color-border);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; border-bottom: 1px solid var(--color-border); padding-bottom: 12px;">
            <div>
                <h3 style="font-size: 17px; font-weight: 800; color: var(--color-text-main); margin: 0;">Tambah Mitra Baru</h3>
                <p style="font-size: 12px; color: var(--color-text-muted); margin: 4px 0 0 0;">Daftarkan data perguruan tinggi atau perusahaan industri rekanan sekolah.</p>
            </div>
            <button type="button" onclick="closeAddPartnerModal()" style="background: none; border: none; font-size: 20px; color: var(--color-text-subtle); cursor: pointer; padding: 4px 8px;">&times;</button>
        </div>

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
                    <input type="text" name="category" class="form-control" placeholder="Contoh: PTN, PTS, BUMN, Manufaktur, IT">
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
                    <label class="form-label">Email Resmi</label>
                    <input type="email" name="email" class="form-control" placeholder="info@instansi.com">
                </div>

                <div class="form-group">
                    <label class="form-label">Nomor Dokumen MoU / PKS</label>
                    <input type="text" name="partnership_doc_number" class="form-control" placeholder="PKS/001/2026">
                </div>
            </div>

            <div class="grid-3">
                <div class="form-group">
                    <label class="form-label">Status Kerja Sama *</label>
                    <select name="partnership_status" class="form-select" required>
                        <option value="aktif">Aktif</option>
                        <option value="akan_berakhir">Akan Berakhir</option>
                        <option value="berakhir">Berakhir</option>
                        <option value="tidak_aktif">Tidak Aktif</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Mulai Berlaku MoU</label>
                    <input type="date" name="partnership_start_date" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">Berakhir MoU</label>
                    <input type="date" name="partnership_end_date" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Alamat Lengkap</label>
                <textarea name="address" class="form-control" rows="2" placeholder="Alamat kantor / kampus..."></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Catatan Lingkup Kerja Sama</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="Bidang kerja sama yang disepakati (sosialisasi, magang, rekrutmen)..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button type="button" class="btn btn-secondary" onclick="closeAddPartnerModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Mitra</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Ubah Data Mitra -->
<div id="editPartnerModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.55); z-index: 90; backdrop-filter: blur(2px); overflow-y: auto;">
    <div style="max-width: 680px; margin: 40px auto; background: var(--color-surface); padding: 24px; border-radius: var(--radius-md); box-shadow: var(--shadow-lg); border: 1px solid var(--color-border);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; border-bottom: 1px solid var(--color-border); padding-bottom: 12px;">
            <div>
                <h3 style="font-size: 17px; font-weight: 800; color: var(--color-text-main); margin: 0;">Ubah Data Mitra</h3>
                <p style="font-size: 12px; color: var(--color-text-muted); margin: 4px 0 0 0;">Perbarui profil lembaga, kontak PIC, dan masa berlaku kerja sama (Kode: <strong id="editPartnerCodeText"></strong>).</p>
            </div>
            <button type="button" onclick="closeEditPartnerModal()" style="background: none; border: none; font-size: 20px; color: var(--color-text-subtle); cursor: pointer; padding: 4px 8px;">&times;</button>
        </div>

        <form id="editPartnerForm" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Tipe Mitra *</label>
                    <select name="type" id="edit_type" class="form-select" required>
                        <option value="perguruan_tinggi">Perguruan Tinggi (Kampus)</option>
                        <option value="perusahaan">Perusahaan / Industri (DUDI)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Kategori Spesifik</label>
                    <input type="text" name="category" id="edit_category" class="form-control" placeholder="Contoh: PTN, PTS, BUMN, Manufaktur, IT">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Nama Lembaga / Perguruan Tinggi / Perusahaan *</label>
                <input type="text" name="name" id="edit_name" class="form-control" placeholder="Nama lengkap instansi..." required>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Kota / Lokasi</label>
                    <input type="text" name="city" id="edit_city" class="form-control" placeholder="Contoh: Bandung, Jakarta">
                </div>

                <div class="form-group">
                    <label class="form-label">Alamat Website</label>
                    <input type="url" name="website" id="edit_website" class="form-control" placeholder="https://...">
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Nama Kontak PIC</label>
                    <input type="text" name="contact_person" id="edit_contact_person" class="form-control" placeholder="Nama narahubung">
                </div>

                <div class="form-group">
                    <label class="form-label">No. Telepon / WhatsApp</label>
                    <input type="text" name="phone" id="edit_phone" class="form-control" placeholder="08...">
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Email Resmi</label>
                    <input type="email" name="email" id="edit_email" class="form-control" placeholder="info@instansi.com">
                </div>

                <div class="form-group">
                    <label class="form-label">Nomor Dokumen MoU / PKS</label>
                    <input type="text" name="partnership_doc_number" id="edit_partnership_doc_number" class="form-control" placeholder="PKS/001/2026">
                </div>
            </div>

            <div class="grid-3">
                <div class="form-group">
                    <label class="form-label">Status Kerja Sama *</label>
                    <select name="partnership_status" id="edit_partnership_status" class="form-select" required>
                        <option value="aktif">Aktif</option>
                        <option value="akan_berakhir">Akan Berakhir</option>
                        <option value="berakhir">Berakhir</option>
                        <option value="tidak_aktif">Tidak Aktif</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Mulai Berlaku MoU</label>
                    <input type="date" name="partnership_start_date" id="edit_partnership_start_date" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">Berakhir MoU</label>
                    <input type="date" name="partnership_end_date" id="edit_partnership_end_date" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Alamat Lengkap</label>
                <textarea name="address" id="edit_address" class="form-control" rows="2" placeholder="Alamat kantor / kampus..."></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Catatan Lingkup Kerja Sama</label>
                <textarea name="notes" id="edit_notes" class="form-control" rows="2" placeholder="Bidang kerja sama yang disepakati (sosialisasi, magang, rekrutmen)..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button type="button" class="btn btn-secondary" onclick="closeEditPartnerModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Konfirmasi Hapus Mitra -->
<div id="deletePartnerModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.55); z-index: 100; backdrop-filter: blur(2px); align-items: center; justify-content: center;">
    <div style="max-width: 460px; width: 92%; background: var(--color-surface); border-radius: var(--radius-md); box-shadow: var(--shadow-lg); padding: 24px; border: 1px solid var(--color-border);">
        <div style="display: flex; align-items: flex-start; gap: 14px;">
            <div style="width: 44px; height: 44px; border-radius: 50%; background-color: var(--color-danger-bg); color: var(--color-danger); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    <line x1="10" y1="11" x2="10" y2="17"></line>
                    <line x1="14" y1="11" x2="14" y2="17"></line>
                </svg>
            </div>
            <div style="flex: 1;">
                <h4 style="font-size: 16px; font-weight: 700; color: var(--color-text-main); margin-bottom: 6px;">
                    Hapus Data Mitra?
                </h4>
                <p style="font-size: 13px; color: var(--color-text-muted); line-height: 1.5; margin-bottom: 8px;">
                    Apakah Anda yakin ingin menghapus data mitra <strong id="deletePartnerNameText" style="color: var(--color-text-main);"></strong> (<span id="deletePartnerCodeText" style="font-family: monospace;"></span>)?
                </p>
                <div id="deletePartnerWarningBox" style="display: none; margin-bottom: 12px; padding: 10px 12px; background: #fff1f2; border: 1px solid #fecdd3; border-radius: var(--radius-sm); font-size: 12px; color: #9f1239; line-height: 1.4;">
                    <span id="deletePartnerWarningText"></span>
                </div>
                <p style="font-size: 12px; color: var(--color-text-subtle); margin-bottom: 16px;">
                    * Tindakan ini bersifat permanen dan riwayat penghapusan akan dicatat ke dalam <strong>Audit Log Sistem</strong>.
                </p>
                <div style="display: flex; justify-content: flex-end; gap: 8px;">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="closeDeletePartnerModal()" style="padding: 6px 14px;">
                        Batal
                    </button>
                    <form id="deletePartnerForm" method="POST" style="margin: 0;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm" style="padding: 6px 14px; background-color: var(--color-danger); color: #ffffff; border: 1px solid var(--color-danger); border-radius: var(--radius-sm); font-weight: 600; cursor: pointer;">
                            Ya, Hapus Mitra
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function openAddPartnerModal() {
    const modal = document.getElementById('addPartnerModal');
    if (modal) {
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
}

function closeAddPartnerModal() {
    const modal = document.getElementById('addPartnerModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
}

function openEditPartnerModal(data) {
    const form = document.getElementById('editPartnerForm');
    if (form) {
        form.action = '/guru/mitra/' + data.id;
    }

    const codeSpan = document.getElementById('editPartnerCodeText');
    if (codeSpan) codeSpan.textContent = data.code;

    document.getElementById('edit_type').value = data.type || 'perguruan_tinggi';
    document.getElementById('edit_category').value = data.category || '';
    document.getElementById('edit_name').value = data.name || '';
    document.getElementById('edit_city').value = data.city || '';
    document.getElementById('edit_website').value = data.website || '';
    document.getElementById('edit_contact_person').value = data.contact_person || '';
    document.getElementById('edit_phone').value = data.phone || '';
    document.getElementById('edit_email').value = data.email || '';
    document.getElementById('edit_address').value = data.address || '';
    document.getElementById('edit_partnership_doc_number').value = data.partnership_doc_number || '';
    document.getElementById('edit_partnership_status').value = data.partnership_status || 'aktif';
    document.getElementById('edit_partnership_start_date').value = data.partnership_start_date || '';
    document.getElementById('edit_partnership_end_date').value = data.partnership_end_date || '';
    document.getElementById('edit_notes').value = data.notes || '';

    const modal = document.getElementById('editPartnerModal');
    if (modal) {
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
}

function closeEditPartnerModal() {
    const modal = document.getElementById('editPartnerModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
}

function openDeletePartnerModal(id, name, code, actCount, oppCount) {
    const form = document.getElementById('deletePartnerForm');
    if (form) {
        form.action = '/guru/mitra/' + id;
    }

    const nameEl = document.getElementById('deletePartnerNameText');
    if (nameEl) nameEl.textContent = name;

    const codeEl = document.getElementById('deletePartnerCodeText');
    if (codeEl) codeEl.textContent = code;

    const warningBox = document.getElementById('deletePartnerWarningBox');
    const warningText = document.getElementById('deletePartnerWarningText');
    if (warningBox && warningText) {
        if (actCount > 0 || oppCount > 0) {
            let msg = 'Perhatian: Mitra ini memiliki ';
            let parts = [];
            if (actCount > 0) parts.push(actCount + ' agenda kegiatan terjadwal');
            if (oppCount > 0) parts.push(oppCount + ' peluang/beasiswa terdaftar');
            msg += parts.join(' dan ') + ' yang terkait.';
            warningText.textContent = msg;
            warningBox.style.display = 'block';
        } else {
            warningBox.style.display = 'none';
        }
    }

    const modal = document.getElementById('deletePartnerModal');
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

function closeDeletePartnerModal() {
    const modal = document.getElementById('deletePartnerModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
}

// Global click & Escape handlers
document.addEventListener('click', function(e) {
    const addModal = document.getElementById('addPartnerModal');
    if (addModal && e.target === addModal) {
        closeAddPartnerModal();
    }

    const editModal = document.getElementById('editPartnerModal');
    if (editModal && e.target === editModal) {
        closeEditPartnerModal();
    }

    const deleteModal = document.getElementById('deletePartnerModal');
    if (deleteModal && e.target === deleteModal) {
        closeDeletePartnerModal();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAddPartnerModal();
        closeEditPartnerModal();
        closeDeletePartnerModal();
    }
});
</script>
@endsection
