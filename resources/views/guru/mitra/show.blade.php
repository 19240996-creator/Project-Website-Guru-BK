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
        <div class="card-header card-header-navy">
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
        <div class="card-header card-header-navy">
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
    <div class="card-header card-header-navy" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <h3 class="card-title">Agenda Kegiatan Terjadwal Bersama Mitra ({{ $partner->activities->count() }})</h3>
        <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('addActivityModal').style.display = 'block';">
            + Jadwalkan Kegiatan Baru
        </button>
    </div>
    <div class="card-body" style="padding: 0;">
        @if($partner->activities->count() > 0)
            <div class="table-responsive">
                <table class="table" style="width: 100%; border-collapse: collapse;">
                    <thead class="table-thead-navy">
                        <tr>
                            <th style="width: 14%; min-width: 115px; text-align: left; vertical-align: middle; padding: 12px 14px;">Kode & Tanggal</th>
                            <th style="width: 22%; min-width: 150px; text-align: left; vertical-align: middle; padding: 12px 14px;">Nama Kegiatan</th>
                            <th style="width: 13%; min-width: 100px; text-align: left; vertical-align: middle; padding: 12px 14px;">Tipe Kegiatan</th>
                            <th style="width: 16%; min-width: 115px; text-align: left; vertical-align: middle; padding: 12px 14px;">Sasaran Kelas</th>
                            <th style="width: 16%; min-width: 120px; text-align: left; vertical-align: middle; padding: 12px 14px;">Waktu & Ruangan</th>
                            <th style="width: 9%; min-width: 80px; text-align: center; vertical-align: middle; padding: 12px 14px;">Status</th>
                            <th style="width: 10%; min-width: 95px; text-align: center; vertical-align: middle; padding: 12px 14px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($partner->activities as $act)
                            @php
                                $actData = [
                                    'id' => $act->id,
                                    'partner_id' => $partner->id,
                                    'title' => $act->title,
                                    'activity_type' => $act->activity_type,
                                    'date' => $act->date->format('Y-m-d'),
                                    'start_time' => substr($act->start_time, 0, 5),
                                    'end_time' => substr($act->end_time, 0, 5),
                                    'room_location' => $act->room_location,
                                    'target_class_ids' => !empty($act->target_class_ids) ? $act->target_class_ids : ($act->target_class_id ? [$act->target_class_id] : []),
                                    'max_participants' => $act->max_participants,
                                    'pic_name' => $act->pic_name,
                                    'status' => $act->status,
                                    'notes' => $act->notes,
                                ];
                            @endphp
                            <tr>
                                <td style="vertical-align: top; text-align: left; padding: 12px 14px;">
                                    <strong style="color: var(--color-text-main); font-weight: 700;">{{ $act->code }}</strong>
                                    <div style="font-size: 11px; color: var(--color-text-subtle); margin-top: 2px;">{{ $act->date->translatedFormat('d M Y') }}</div>
                                </td>
                                <td style="vertical-align: top; text-align: left; padding: 12px 14px;">
                                    <div style="font-weight: 500; color: var(--color-text-main); line-height: 1.4;">{{ $act->title }}</div>
                                </td>
                                <td style="vertical-align: top; text-align: left; padding: 12px 14px; color: var(--color-text-main); font-size: 13px;">
                                    {{ ucfirst(str_replace('_', ' ', $act->activity_type)) }}
                                </td>
                                <td style="vertical-align: top; text-align: left; padding: 12px 14px; color: var(--color-text-main); font-size: 13px; line-height: 1.4;">
                                    @if(!empty($act->target_class_ids) && count($act->target_class_ids) > 0)
                                        @php
                                            $targetClasses = \App\Models\StudentClass::whereIn('id', $act->target_class_ids)->get();
                                        @endphp
                                        @if($targetClasses->count() > 0)
                                            {{ $targetClasses->pluck('name')->join(', ') }}
                                        @else
                                            <span style="color: var(--color-text-subtle);">Semua Kelas</span>
                                        @endif
                                    @elseif($act->targetClass)
                                        {{ $act->targetClass->name }}
                                    @else
                                        <span style="color: var(--color-text-subtle);">Semua Kelas</span>
                                    @endif
                                </td>
                                <td style="vertical-align: top; text-align: left; padding: 12px 14px;">
                                    <div style="font-size: 13px; font-weight: 500; color: var(--color-text-main);">{{ substr($act->start_time, 0, 5) }} - {{ substr($act->end_time, 0, 5) }} WIB</div>
                                    <div style="font-size: 11px; color: var(--color-text-subtle); margin-top: 2px;">Ruang: {{ $act->room_location }}</div>
                                </td>
                                <td style="vertical-align: top; text-align: center; padding: 12px 14px;">
                                    <span class="badge badge-success" style="font-size: 11px;">{{ ucfirst($act->status) }}</span>
                                </td>
                                <td style="vertical-align: top; text-align: center; padding: 12px 14px;">
                                    <div style="display: inline-flex; align-items: center; gap: 5px;">
                                        <button type="button" 
                                                class="btn btn-secondary btn-sm" 
                                                style="padding: 3px 7px; font-size: 11px; display: inline-flex; align-items: center; gap: 3px;" 
                                                onclick='openEditActivityModal(@json($actData))' 
                                                title="Edit Agenda Kegiatan">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                            <span>Edit</span>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-sm" 
                                                style="padding: 3px 7px; font-size: 11px; background-color: var(--color-danger-bg); color: var(--color-danger); border: 1px solid var(--color-danger-border); border-radius: var(--radius-sm); cursor: pointer; display: inline-flex; align-items: center; gap: 3px;" 
                                                onclick="openDeleteConfirmModal('{{ $act->id }}', '{{ addslashes($act->title) }}')" 
                                                title="Hapus Agenda Kegiatan">
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

                <div class="form-group" style="position: relative;">
                    <label class="form-label">Sasaran Kelas (Pilih Kelas)</label>
                    <button type="button" 
                            id="targetClassDropdownBtn" 
                            class="form-select" 
                            style="text-align: left; display: flex; align-items: center; justify-content: space-between; cursor: pointer; padding-right: 12px; background-color: var(--color-surface);"
                            aria-haspopup="listbox" 
                            aria-expanded="false"
                            onclick="toggleTargetClassDropdown(event)">
                        <span id="targetClassLabelText" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; padding-right: 6px; color: var(--color-text-main);">
                            Semua Kelas
                        </span>
                        <svg id="targetClassChevron" width="16" height="16" viewBox="0 0 20 20" fill="currentColor" style="flex-shrink: 0; color: var(--color-text-subtle); transition: transform 0.2s ease;">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <!-- Floating Checkbox Dropdown Panel -->
                    <div id="targetClassDropdownMenu" 
                         style="display: none; position: absolute; top: calc(100% + 4px); left: 0; right: 0; z-index: 60; background: var(--color-surface); border: 1px solid var(--color-border-strong); border-radius: var(--radius-sm); box-shadow: var(--shadow-lg); padding: 8px; max-height: 230px; overflow-y: auto;">
                        <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 6px; margin-bottom: 6px; border-bottom: 1px solid var(--color-border);">
                            <span style="font-size: 11px; font-weight: 600; color: var(--color-text-muted);">Pilih Sasaran Kelas:</span>
                            <div style="display: flex; gap: 6px;">
                                <button type="button" class="btn btn-secondary btn-sm" onclick="toggleAllTargetClasses(true)" style="padding: 1px 7px; font-size: 10px;">Semua</button>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="toggleAllTargetClasses(false)" style="padding: 1px 7px; font-size: 10px;">Kosongkan</button>
                            </div>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 4px;">
                            @foreach($classes as $c)
                                <label style="display: flex; align-items: center; gap: 8px; font-size: 12px; color: var(--color-text-main); cursor: pointer; margin: 0; padding: 6px 8px; border-radius: var(--radius-sm); transition: background-color 0.15s ease;" onmouseover="this.style.backgroundColor='var(--color-surface-hover)'" onmouseout="this.style.backgroundColor='transparent'">
                                    <input type="checkbox" name="target_class_ids[]" value="{{ $c->id }}" data-class-name="{{ $c->name }}" class="target-class-checkbox" onchange="updateTargetClassLabel()" style="cursor: pointer; accent-color: var(--color-primary);">
                                    <span>{{ $c->name }} <small style="color: var(--color-text-muted);">({{ $c->grade }} - {{ $c->major }})</small></span>
                                </label>
                            @endforeach
                        </div>
                        <div style="border-top: 1px solid var(--color-border); margin-top: 6px; padding-top: 6px;">
                            <small style="color: var(--color-text-subtle); font-size: 10px; display: block; line-height: 1.3;">
                                * Centang kelas sasaran. Jika tidak ada yang dicentang, kegiatan terbuka untuk Semua Kelas.
                            </small>
                        </div>
                    </div>
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

<!-- Modal Edit Kegiatan -->
<div id="editActivityModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 50; overflow-y: auto;">
    <div style="max-width: 650px; margin: 60px auto; background: #fff; padding: 24px; border-radius: var(--radius-md);">
        <h3 style="font-size: 16px; font-weight: 800; margin-bottom: 16px; color: var(--color-text-main);">Edit Agenda Kegiatan Kemitraan</h3>
        
        <form id="editActivityForm" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="partner_id" value="{{ $partner->id }}">

            <div class="form-group">
                <label class="form-label">Nama Kegiatan / Agenda *</label>
                <input type="text" name="title" id="edit_title" class="form-control" placeholder="Contoh: Sosialisasi PMB Jalur SNBP..." required>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Tipe Kegiatan *</label>
                    <select name="activity_type" id="edit_activity_type" class="form-select" required>
                        <option value="sosialisasi">Sosialisasi</option>
                        <option value="seminar">Seminar / Workshop</option>
                        <option value="campus_visit">Campus Visit</option>
                        <option value="kunjungan_industri">Kunjungan Industri</option>
                        <option value="magang">Program Magang</option>
                        <option value="rekrutmen">Rekrutmen Langsung</option>
                        <option value="pelatihan">Pelatihan / Sertifikasi</option>
                    </select>
                </div>

                <div class="form-group" style="position: relative;">
                    <label class="form-label">Sasaran Kelas (Pilih Kelas)</label>
                    <button type="button" 
                            id="editTargetClassDropdownBtn" 
                            class="form-select" 
                            style="text-align: left; display: flex; align-items: center; justify-content: space-between; cursor: pointer; padding-right: 12px; background-color: var(--color-surface);"
                            aria-haspopup="listbox" 
                            aria-expanded="false"
                            onclick="toggleEditTargetClassDropdown(event)">
                        <span id="editTargetClassLabelText" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; padding-right: 6px; color: var(--color-text-main);">
                            Semua Kelas
                        </span>
                        <svg id="editTargetClassChevron" width="16" height="16" viewBox="0 0 20 20" fill="currentColor" style="flex-shrink: 0; color: var(--color-text-subtle); transition: transform 0.2s ease;">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <!-- Floating Checkbox Dropdown Panel for Edit -->
                    <div id="editTargetClassDropdownMenu" 
                         style="display: none; position: absolute; top: calc(100% + 4px); left: 0; right: 0; z-index: 60; background: var(--color-surface); border: 1px solid var(--color-border-strong); border-radius: var(--radius-sm); box-shadow: var(--shadow-lg); padding: 8px; max-height: 230px; overflow-y: auto;">
                        <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 6px; margin-bottom: 6px; border-bottom: 1px solid var(--color-border);">
                            <span style="font-size: 11px; font-weight: 600; color: var(--color-text-muted);">Pilih Sasaran Kelas:</span>
                            <div style="display: flex; gap: 6px;">
                                <button type="button" class="btn btn-secondary btn-sm" onclick="toggleAllEditTargetClasses(true)" style="padding: 1px 7px; font-size: 10px;">Semua</button>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="toggleAllEditTargetClasses(false)" style="padding: 1px 7px; font-size: 10px;">Kosongkan</button>
                            </div>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 4px;">
                            @foreach($classes as $c)
                                <label style="display: flex; align-items: center; gap: 8px; font-size: 12px; color: var(--color-text-main); cursor: pointer; margin: 0; padding: 6px 8px; border-radius: var(--radius-sm); transition: background-color 0.15s ease;" onmouseover="this.style.backgroundColor='var(--color-surface-hover)'" onmouseout="this.style.backgroundColor='transparent'">
                                    <input type="checkbox" name="target_class_ids[]" value="{{ $c->id }}" data-class-name="{{ $c->name }}" class="edit-target-class-checkbox" onchange="updateEditTargetClassLabel()" style="cursor: pointer; accent-color: var(--color-primary);">
                                    <span>{{ $c->name }} <small style="color: var(--color-text-muted);">({{ $c->grade }} - {{ $c->major }})</small></span>
                                </label>
                            @endforeach
                        </div>
                        <div style="border-top: 1px solid var(--color-border); margin-top: 6px; padding-top: 6px;">
                            <small style="color: var(--color-text-subtle); font-size: 10px; display: block; line-height: 1.3;">
                                * Centang kelas sasaran. Jika tidak ada yang dicentang, kegiatan terbuka untuk Semua Kelas.
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid-3">
                <div class="form-group">
                    <label class="form-label">Tanggal Pelaksanaan *</label>
                    <input type="date" name="date" id="edit_date" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Jam Mulai *</label>
                    <input type="time" name="start_time" id="edit_start_time" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Jam Selesai *</label>
                    <input type="time" name="end_time" id="edit_end_time" class="form-control" required>
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Ruang / Tempat Pelaksanaan *</label>
                    <input type="text" name="room_location" id="edit_room_location" class="form-control" placeholder="Contoh: Aula Utama, Lab Komputer..." required>
                    <div class="form-hint">Sistem otomatis mendeteksi jika ruang atau kelas telah terpakai di jam yang sama.</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Kapasitas Maksimal Peserta</label>
                    <input type="number" name="max_participants" id="edit_max_participants" class="form-control" min="1" required>
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Penanggung Jawab (PIC)</label>
                    <input type="text" name="pic_name" id="edit_pic_name" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">Status *</label>
                    <select name="status" id="edit_status" class="form-select" required>
                        <option value="terkonfirmasi">Terkonfirmasi</option>
                        <option value="rencana">Rencana</option>
                        <option value="menunggu_konfirmasi">Menunggu Konfirmasi</option>
                        <option value="terlaksana">Terlaksana</option>
                        <option value="ditunda">Ditunda</option>
                        <option value="dibatalkan">Dibatalkan</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Catatan Teknis / Kebutuhan Perangkat</label>
                <textarea name="notes" id="edit_notes" class="form-control" rows="2" placeholder="Proyektor, mic wireless, sound system..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('editActivityModal').style.display = 'none';">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Konfirmasi Hapus Kegiatan (Custom Designed) -->
<div id="deleteConfirmModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.55); z-index: 100; backdrop-filter: blur(2px); align-items: center; justify-content: center;">
    <div style="max-width: 440px; width: 90%; background: var(--color-surface); border-radius: var(--radius-md); box-shadow: var(--shadow-lg); padding: 22px; border: 1px solid var(--color-border);">
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
                    Hapus Agenda Kegiatan?
                </h4>
                <p style="font-size: 13px; color: var(--color-text-muted); line-height: 1.5; margin-bottom: 12px;">
                    Apakah Anda yakin ingin menghapus kegiatan <strong id="deleteActivityTitle" style="color: var(--color-text-main);"></strong>? Tindakan ini bersifat permanen dan seluruh riwayat akan dicatat ke dalam <strong>Audit Log Aktivitas</strong>.
                </p>
                <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px;">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="closeDeleteConfirmModal()" style="padding: 6px 14px;">
                        Batal
                    </button>
                    <form id="deleteActivityForm" method="POST" style="margin: 0;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm" style="padding: 6px 14px; background-color: var(--color-danger); color: #ffffff; border: 1px solid var(--color-danger); border-radius: var(--radius-sm); font-weight: 600; cursor: pointer;">
                            Ya, Hapus Kegiatan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleTargetClassDropdown(e) {
    if (e) {
        e.stopPropagation();
        e.preventDefault();
    }
    const menu = document.getElementById('targetClassDropdownMenu');
    const btn = document.getElementById('targetClassDropdownBtn');
    const chevron = document.getElementById('targetClassChevron');
    if (!menu) return;

    const isOpen = menu.style.display !== 'none';
    if (isOpen) {
        menu.style.display = 'none';
        if (btn) btn.setAttribute('aria-expanded', 'false');
        if (chevron) chevron.style.transform = 'rotate(0deg)';
    } else {
        menu.style.display = 'block';
        if (btn) btn.setAttribute('aria-expanded', 'true');
        if (chevron) chevron.style.transform = 'rotate(180deg)';
    }
}

function updateTargetClassLabel() {
    const checkboxes = document.querySelectorAll('.target-class-checkbox');
    const checked = Array.from(checkboxes).filter(cb => cb.checked);
    const label = document.getElementById('targetClassLabelText');
    if (!label) return;

    if (checked.length === 0) {
        label.textContent = 'Semua Kelas';
        label.style.fontWeight = 'normal';
        label.style.color = 'var(--color-text-main)';
    } else if (checked.length === checkboxes.length && checkboxes.length > 0) {
        label.textContent = 'Semua Kelas (' + checked.length + ' Kelas)';
        label.style.fontWeight = '600';
        label.style.color = 'var(--color-primary)';
    } else if (checked.length <= 2) {
        const names = checked.map(cb => cb.getAttribute('data-class-name') || cb.value);
        label.textContent = names.join(', ');
        label.style.fontWeight = '600';
        label.style.color = 'var(--color-primary)';
    } else {
        label.textContent = checked.length + ' Kelas Terpilih';
        label.style.fontWeight = '600';
        label.style.color = 'var(--color-primary)';
    }
}

function toggleAllTargetClasses(check) {
    document.querySelectorAll('.target-class-checkbox').forEach(function(cb) {
        cb.checked = check;
    });
    updateTargetClassLabel();
}

function openEditActivityModal(data) {
    const form = document.getElementById('editActivityForm');
    form.action = '/guru/mitra-kegiatan/' + data.id;

    document.getElementById('edit_title').value = data.title;
    document.getElementById('edit_activity_type').value = data.activity_type;
    document.getElementById('edit_date').value = data.date;
    document.getElementById('edit_start_time').value = data.start_time;
    document.getElementById('edit_end_time').value = data.end_time;
    document.getElementById('edit_room_location').value = data.room_location;
    document.getElementById('edit_max_participants').value = data.max_participants;
    document.getElementById('edit_pic_name').value = data.pic_name || '';
    document.getElementById('edit_status').value = data.status;
    document.getElementById('edit_notes').value = data.notes || '';

    const targetClassIds = Array.isArray(data.target_class_ids) ? data.target_class_ids.map(Number) : [];
    document.querySelectorAll('.edit-target-class-checkbox').forEach(cb => {
        cb.checked = targetClassIds.includes(Number(cb.value));
    });
    updateEditTargetClassLabel();

    document.getElementById('editActivityModal').style.display = 'block';
}

function toggleEditTargetClassDropdown(e) {
    if (e) {
        e.stopPropagation();
        e.preventDefault();
    }
    const menu = document.getElementById('editTargetClassDropdownMenu');
    const btn = document.getElementById('editTargetClassDropdownBtn');
    const chevron = document.getElementById('editTargetClassChevron');
    if (!menu) return;

    const isOpen = menu.style.display !== 'none';
    if (isOpen) {
        menu.style.display = 'none';
        if (btn) btn.setAttribute('aria-expanded', 'false');
        if (chevron) chevron.style.transform = 'rotate(0deg)';
    } else {
        menu.style.display = 'block';
        if (btn) btn.setAttribute('aria-expanded', 'true');
        if (chevron) chevron.style.transform = 'rotate(180deg)';
    }
}

function updateEditTargetClassLabel() {
    const checkboxes = document.querySelectorAll('.edit-target-class-checkbox');
    const checked = Array.from(checkboxes).filter(cb => cb.checked);
    const label = document.getElementById('editTargetClassLabelText');
    if (!label) return;

    if (checked.length === 0) {
        label.textContent = 'Semua Kelas';
        label.style.fontWeight = 'normal';
        label.style.color = 'var(--color-text-main)';
    } else if (checked.length === checkboxes.length && checkboxes.length > 0) {
        label.textContent = 'Semua Kelas (' + checked.length + ' Kelas)';
        label.style.fontWeight = '600';
        label.style.color = 'var(--color-primary)';
    } else if (checked.length <= 2) {
        const names = checked.map(cb => cb.getAttribute('data-class-name') || cb.value);
        label.textContent = names.join(', ');
        label.style.fontWeight = '600';
        label.style.color = 'var(--color-primary)';
    } else {
        label.textContent = checked.length + ' Kelas Terpilih';
        label.style.fontWeight = '600';
        label.style.color = 'var(--color-primary)';
    }
}

function toggleAllEditTargetClasses(check) {
    document.querySelectorAll('.edit-target-class-checkbox').forEach(function(cb) {
        cb.checked = check;
    });
    updateEditTargetClassLabel();
}

function openDeleteConfirmModal(id, title) {
    const form = document.getElementById('deleteActivityForm');
    form.action = '/guru/mitra-kegiatan/' + id;
    const titleEl = document.getElementById('deleteActivityTitle');
    if (titleEl) {
        titleEl.textContent = title ? '"' + title + '"' : '';
    }
    const modal = document.getElementById('deleteConfirmModal');
    if (modal) {
        modal.style.display = 'flex';
    }
}

function closeDeleteConfirmModal() {
    const modal = document.getElementById('deleteConfirmModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

document.addEventListener('click', function(e) {
    const menu = document.getElementById('targetClassDropdownMenu');
    const btn = document.getElementById('targetClassDropdownBtn');
    if (menu && btn && !menu.contains(e.target) && !btn.contains(e.target)) {
        menu.style.display = 'none';
        btn.setAttribute('aria-expanded', 'false');
        const chevron = document.getElementById('targetClassChevron');
        if (chevron) chevron.style.transform = 'rotate(0deg)';
    }

    const editMenu = document.getElementById('editTargetClassDropdownMenu');
    const editBtn = document.getElementById('editTargetClassDropdownBtn');
    if (editMenu && editBtn && !editMenu.contains(e.target) && !editBtn.contains(e.target)) {
        editMenu.style.display = 'none';
        editBtn.setAttribute('aria-expanded', 'false');
        const chevron = document.getElementById('editTargetClassChevron');
        if (chevron) chevron.style.transform = 'rotate(0deg)';
    }

    const deleteModal = document.getElementById('deleteConfirmModal');
    if (deleteModal && e.target === deleteModal) {
        closeDeleteConfirmModal();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const deleteModal = document.getElementById('deleteConfirmModal');
        if (deleteModal && deleteModal.style.display !== 'none') {
            closeDeleteConfirmModal();
            return;
        }

        const menu = document.getElementById('targetClassDropdownMenu');
        const btn = document.getElementById('targetClassDropdownBtn');
        if (menu && menu.style.display !== 'none') {
            menu.style.display = 'none';
            if (btn) {
                btn.setAttribute('aria-expanded', 'false');
                btn.focus();
            }
            const chevron = document.getElementById('targetClassChevron');
            if (chevron) chevron.style.transform = 'rotate(0deg)';
        }

        const editMenu = document.getElementById('editTargetClassDropdownMenu');
        const editBtn = document.getElementById('editTargetClassDropdownBtn');
        if (editMenu && editMenu.style.display !== 'none') {
            editMenu.style.display = 'none';
            if (editBtn) {
                editBtn.setAttribute('aria-expanded', 'false');
                editBtn.focus();
            }
            const chevron = document.getElementById('editTargetClassChevron');
            if (chevron) chevron.style.transform = 'rotate(0deg)';
        }
    }
});

document.addEventListener('DOMContentLoaded', function() {
    updateTargetClassLabel();
});
</script>
@endsection
