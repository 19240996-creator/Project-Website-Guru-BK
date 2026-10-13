@extends('layouts.app')

@section('title', 'Alumni & Pelacakan Lulusan - Guru BK')
@section('header_title', 'Kelulusan & Pelacakan Alumni (Tracer Study)')

@section('content')
<div style="margin-bottom: 24px;">
    <h2 style="font-size: 20px; font-weight: 800; color: var(--color-text-main);">Pelacakan Alumni & Jejak Karier</h2>
    <p style="font-size: 13px; color: var(--color-text-muted);">
        Pemantauan penyerapan lulusan berkala (3, 6, 12, dan 24 bulan setelah kelulusan) dan kurasi inspirasi karier untuk adik kelas.
    </p>
</div>

<div class="grid-4" style="margin-bottom: 24px;">
    <div class="stat-card">
        <span class="stat-label">Total Alumni Terdata</span>
        <span class="stat-value">{{ $stats['total'] }}</span>
        <span class="stat-desc">Siswa berstatus lulus</span>
    </div>

    <div class="stat-card">
        <span class="stat-label">Alumni Bekerja</span>
        <span class="stat-value">{{ $stats['bekerja'] }}</span>
        <span class="stat-desc">Terserap di industri</span>
    </div>

    <div class="stat-card">
        <span class="stat-label">Alumni Kuliah</span>
        <span class="stat-value">{{ $stats['kuliah'] }}</span>
        <span class="stat-desc">Studi lanjut PTN/PTS</span>
    </div>

    <div class="stat-card">
        <span class="stat-label">Alumni Wirausaha</span>
        <span class="stat-value">{{ $stats['wirausaha'] }}</span>
        <span class="stat-desc">Mendirikan usaha mandiri</span>
    </div>
</div>

<!-- Filter Bar (Horizontal) -->
<div class="card" style="margin-bottom: 20px;">
    <div class="card-body" style="padding: 16px 20px;">
        <form action="{{ route('guru.alumni.index') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)) 100px; gap: 12px; align-items: flex-end;">
            <div>
                <label class="form-label" style="font-size: 12px; margin-bottom: 6px; display: block;">Cari Alumni / Instansi / NISN</label>
                <input type="text" name="q" class="form-control" style="min-height: 38px; font-size: 13px;" value="{{ request('q') }}" placeholder="Ketik kata kunci...">
            </div>

            <div>
                <label class="form-label" style="font-size: 12px; margin-bottom: 6px; display: block;">Status Terkini</label>
                <select name="status" class="form-select" style="min-height: 38px; font-size: 13px;">
                    <option value="">Semua Status</option>
                    <option value="bekerja" {{ request('status') === 'bekerja' ? 'selected' : '' }}>Bekerja</option>
                    <option value="kuliah" {{ request('status') === 'kuliah' ? 'selected' : '' }}>Kuliah</option>
                    <option value="wirausaha" {{ request('status') === 'wirausaha' ? 'selected' : '' }}>Wirausaha</option>
                    <option value="mencari_kerja" {{ request('status') === 'mencari_kerja' ? 'selected' : '' }}>Mencari Kerja</option>
                    <option value="belum_bekerja" {{ request('status') === 'belum_bekerja' ? 'selected' : '' }}>Belum Bekerja</option>
                    <option value="belum_terlacak" {{ request('status') === 'belum_terlacak' ? 'selected' : '' }}>Belum Terlacak</option>
                </select>
            </div>

            <div>
                <label class="form-label" style="font-size: 12px; margin-bottom: 6px; display: block;">Tahun Lulus</label>
                <select name="year" class="form-select" style="min-height: 38px; font-size: 13px;">
                    <option value="">Semua Tahun</option>
                    @foreach($years as $yr)
                        <option value="{{ $yr }}" {{ request('year') == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label" style="font-size: 12px; margin-bottom: 6px; display: block;">Jurusan</label>
                <select name="major" class="form-select" style="min-height: 38px; font-size: 13px;">
                    <option value="">Semua Jurusan</option>
                    @foreach($majors as $mj)
                        <option value="{{ $mj }}" {{ request('major') === $mj ? 'selected' : '' }}>{{ $mj }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label" style="font-size: 12px; margin-bottom: 6px; display: block;">Periode Pelacakan</label>
                <select name="tracking_period" class="form-select" style="min-height: 38px; font-size: 13px;">
                    <option value="">Semua Periode</option>
                    <option value="3_bulan" {{ request('tracking_period') === '3_bulan' ? 'selected' : '' }}>3 Bulan</option>
                    <option value="6_bulan" {{ request('tracking_period') === '6_bulan' ? 'selected' : '' }}>6 Bulan</option>
                    <option value="12_bulan" {{ request('tracking_period') === '12_bulan' ? 'selected' : '' }}>12 Bulan</option>
                    <option value="24_bulan" {{ request('tracking_period') === '24_bulan' ? 'selected' : '' }}>24 Bulan</option>
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
    <div class="card-header card-header-navy" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
            <h3 class="card-title" style="margin: 0;">Daftar Pelacakan Alumni ({{ $alumni->total() }})</h3>
            <span id="headerSelectedBadge" style="display: none; margin-left: 4px; font-size: 11px; padding: 2px 8px; background: rgba(37, 99, 235, 0.12); color: var(--color-primary); border-radius: 999px; font-weight: 700; align-items: center;"><span id="headerSelectedCount">0</span> Dipilih</span>
            @if(request()->hasAny(['q', 'status', 'year', 'major', 'tracking_period']))
                <a href="{{ route('guru.alumni.index') }}" class="btn btn-secondary btn-sm" style="font-size: 11px; padding: 4px 10px; color: #ffffff; border-color: rgba(255,255,255,0.3); background: rgba(255,255,255,0.1);">
                    ✕ Reset Filter
                </a>
            @endif
        </div>
        <a href="{{ route('guru.laporan.generate', ['package' => 'paket_c']) }}" target="_blank" class="btn btn-secondary btn-sm">
            Cetak Rekap Lulusan Kepala Sekolah
        </a>
    </div>
    <div class="card-body" style="padding: 0;">
        <!-- In-place Bulk Action Toolbar -->
        <div id="bulkActionBar" style="display: none; background: var(--color-surface-hover, #f1f5f9); border-bottom: 2px solid var(--color-primary); padding: 10px 18px; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; transition: all 0.2s ease;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 50%; background: var(--color-primary); color: #ffffff;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </span>
                <span style="font-size: 13px; font-weight: 700; color: var(--color-text-main);">
                    <span id="bulkSelectedCount">0</span> Alumni Terpilih
                </span>
            </div>
            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                <button type="button" class="btn btn-secondary btn-sm" onclick="openBulkStatusModal()" style="font-size: 12px; padding: 5px 12px; display: inline-flex; align-items: center; gap: 5px;" title="Ubah status pelacakan semua alumni yang dipilih">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    <span>Ubah Status Massal</span>
                </button>
                <button type="button" class="btn btn-sm" onclick="openBulkDeleteModal()" style="font-size: 12px; background-color: var(--color-danger-bg); color: var(--color-danger); border: 1px solid var(--color-danger-border); border-radius: var(--radius-sm); font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; padding: 5px 12px;" title="Hapus semua data alumni yang dipilih">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                    <span>Hapus Terpilih</span>
                </button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="clearAlumniSelection()" style="font-size: 12px; padding: 5px 10px;" title="Batalkan pilihan checklist">
                    Batal
                </button>
            </div>
        </div>

        @if($alumni->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead class="table-thead-navy">
                        <tr>
                            <th style="width: 44px; text-align: center; vertical-align: middle; padding: 12px 10px;">
                                <input type="checkbox" id="selectAllAlumni" onchange="toggleSelectAllAlumni(this)" title="Pilih Semua Alumni pada Halaman Ini" style="cursor: pointer; width: 16px; height: 16px; accent-color: var(--color-primary);" />
                            </th>
                            <th>Identitas Alumni</th>
                            <th>Tahun Lulus & Jurusan</th>
                            <th>Periode Pelacakan</th>
                            <th>Status Terkini</th>
                            <th>Instansi / Perusahaan / Kampus</th>
                            <th>Jejak Inspirasi Publik</th>
                            <th style="text-align: center; width: 230px; min-width: 210px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($alumni as $als)
                            @php
                                $alsData = [
                                    'id' => $als->id,
                                    'name' => $als->student ? $als->student->name : 'Alumni',
                                    'nisn' => $als->student ? $als->student->nisn : '-',
                                    'major' => $als->student && $als->student->studentClass ? $als->student->studentClass->major : '',
                                    'code' => $als->code,
                                    'graduation_year' => $als->graduation_year,
                                    'tracking_period' => $als->tracking_period,
                                    'current_status' => $als->current_status,
                                    'institution_or_company' => $als->institution_or_company,
                                    'major_or_position' => $als->major_or_position,
                                    'monthly_income_range' => $als->monthly_income_range,
                                    'notes' => $als->notes,
                                    'allow_public_showcase' => (bool) $als->allow_public_showcase,
                                ];
                                $photoSrc = null;
                                if (!empty($als->photo)) {
                                    $photoSrc = str_starts_with($als->photo, 'http') ? $als->photo : asset('storage/' . ltrim($als->photo, '/'));
                                } elseif (!empty($als->student?->avatar)) {
                                    $photoSrc = str_starts_with($als->student->avatar, 'http') ? $als->student->avatar : asset('storage/' . ltrim($als->student->avatar, '/'));
                                }
                                $initial = strtoupper(substr($als->student ? $als->student->name : 'A', 0, 1));
                            @endphp
                            <tr id="alumni-row-{{ $als->id }}">
                                <td style="width: 44px; text-align: center; vertical-align: middle; padding: 12px 10px;">
                                    <input type="checkbox" 
                                           class="alumni-checkbox" 
                                           value="{{ $als->id }}" 
                                           data-name="{{ $als->student ? $als->student->name : 'Alumni' }}" 
                                           data-code="{{ $als->code }}" 
                                           onchange="updateSelectedAlumniCount()" 
                                           title="Pilih {{ $als->student ? $als->student->name : 'Alumni' }}"
                                           style="cursor: pointer; width: 16px; height: 16px; accent-color: var(--color-primary);" />
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div style="position: relative; width: 38px; height: 38px; flex-shrink: 0;">
                                            @if($photoSrc)
                                                <img src="{{ $photoSrc }}" 
                                                     alt="{{ $als->student ? $als->student->name : 'Alumni' }}" 
                                                     style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover; border: 1px solid var(--color-border); display: block;"
                                                     onerror="this.style.display='none'; if(this.nextElementSibling){ this.nextElementSibling.style.display='flex'; }">
                                                <div class="user-avatar-initial" style="width: 38px; height: 38px; font-size: 13px; display: none; align-items: center; justify-content: center;">
                                                    {{ $initial }}
                                                </div>
                                            @else
                                                <div class="user-avatar-initial" style="width: 38px; height: 38px; font-size: 13px; display: flex; align-items: center; justify-content: center;">
                                                    {{ $initial }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <strong>{{ $als->student ? $als->student->name : '-' }}</strong><br>
                                            <small style="color: var(--color-text-subtle);">{{ $als->code }} | NISN: {{ $als->student ? $als->student->nisn : '-' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    Lulus Tahun: <strong>{{ $als->graduation_year }}</strong><br>
                                    <small style="color: var(--color-text-muted);">{{ $als->student && $als->student->studentClass ? $als->student->studentClass->major : '-' }}</small>
                                </td>
                                <td>
                                    <span class="badge badge-secondary">{{ str_replace('_', ' ', $als->tracking_period) }}</span>
                                </td>
                                <td>
                                    @if($als->current_status === 'bekerja')
                                        <span class="badge badge-success">Bekerja</span>
                                    @elseif($als->current_status === 'kuliah')
                                        <span class="badge badge-primary">Kuliah</span>
                                    @elseif($als->current_status === 'wirausaha')
                                        <span class="badge badge-warning">Wirausaha</span>
                                    @else
                                        <span class="badge badge-secondary">{{ ucfirst(str_replace('_', ' ', $als->current_status)) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $als->institution_or_company ?: '-' }}</strong><br>
                                    <small style="color: var(--color-text-subtle);">{{ $als->major_or_position ?: '-' }}</small>
                                </td>
                                <td>
                                    @if($als->allow_public_showcase)
                                        <span class="badge badge-success">Tampil di Siswa</span>
                                    @else
                                        <span class="badge badge-secondary">Tidak Ditampilkan</span>
                                    @endif
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <div style="display: inline-flex; align-items: center; justify-content: center; gap: 6px; flex-wrap: nowrap;">
                                        <button type="button" class="btn btn-secondary btn-sm" onclick='openEditAlumniModal(@json($alsData))' title="Ubah Data Pelacakan Alumni">
                                            Ubah
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="openDeleteAlumniModal('{{ $als->id }}', '{{ addslashes($als->student ? $als->student->name : 'Alumni') }}', '{{ $als->code }}')" title="Hapus Data Pelacakan Alumni">
                                            Hapus
                                        </button>
                                        <form action="{{ route('guru.alumni.toggle_showcase', $als->id) }}" method="POST" style="display: inline-block; margin: 0;">
                                            @csrf
                                            <button type="submit" class="btn btn-secondary btn-sm" style="white-space: nowrap;" title="Ubah status tampil publik">
                                                {{ $als->allow_public_showcase ? 'Sembunyikan' : 'Inspirasi' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $alumni->links() }}
        @else
            <div class="empty-state">
                <p class="empty-state-title">
                    @if(request()->hasAny(['q', 'status', 'year', 'major', 'tracking_period']))
                        Tidak Ada Data Alumni yang Sesuai Filter
                    @else
                        Belum Ada Data Pelacakan Alumni
                    @endif
                </p>
                <p class="empty-state-desc">
                    @if(request()->hasAny(['q', 'status', 'year', 'major', 'tracking_period']))
                        Coba sesuaikan kata kunci pencarian atau ubah kriteria filter yang Anda pilih.
                    @else
                        Ubah status siswa kelas XII menjadi lulus melalui menu Profil Siswa 360° untuk mencatat pelacakan alumni.
                    @endif
                </p>
                @if(request()->hasAny(['q', 'status', 'year', 'major', 'tracking_period']))
                    <div style="margin-top: 14px;">
                        <a href="{{ route('guru.alumni.index') }}" class="btn btn-secondary btn-sm">Reset Filter</a>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Modal Ubah Data Alumni -->
<div id="editAlumniModal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="editAlumniModalTitle" onclick="handleEditBackdropClick(event)">
    <div class="modal-dialog" style="max-width: 620px; max-height: 90vh; display: flex; flex-direction: column;">
        <div style="display: flex; align-items: center; justify-content: space-between; padding: 18px 24px; border-bottom: 1px solid var(--color-border); background-color: var(--color-surface);">
            <div>
                <h3 id="editAlumniModalTitle" style="font-size: 16px; font-weight: 800; color: var(--color-text-main); margin: 0;">Ubah Data Pelacakan Alumni</h3>
                <p style="font-size: 12px; color: var(--color-text-muted); margin-top: 2px; margin-bottom: 0;">
                    Perbarui status karier, kampus/perusahaan, dan catatan tracer study alumni.
                </p>
            </div>
            <button type="button" onclick="closeEditAlumniModal()" style="background: none; border: none; font-size: 20px; line-height: 1; cursor: pointer; color: var(--color-text-subtle); padding: 4px;">
                &times;
            </button>
        </div>

        <form id="editAlumniForm" action="" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; overflow: hidden; margin: 0;">
            @csrf
            @method('PUT')
            
            <div class="modal-body" style="padding: 20px 24px; overflow-y: auto; max-height: calc(90vh - 140px);">
                <!-- Ringkasan Info Siswa -->
                <div style="background-color: var(--color-primary-light); border: 1px solid var(--color-primary-border); border-radius: var(--radius-sm); padding: 12px 16px; margin-bottom: 18px; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="font-weight: 700; color: var(--color-primary); font-size: 14px;" id="editAlumniStudentName">-</div>
                        <div style="font-size: 12px; color: var(--color-text-muted); margin-top: 2px;" id="editAlumniStudentMeta">-</div>
                    </div>
                    <span class="badge badge-primary" id="editAlumniCodeBadge">-</span>
                </div>

                <div class="grid-2" style="gap: 14px; margin-bottom: 14px;">
                    <div class="form-group">
                        <label class="form-label" style="font-size: 12px; font-weight: 700;">Tahun Lulus *</label>
                        <input type="number" name="graduation_year" id="editGraduationYear" class="form-control" style="font-size: 13px;" required min="2000" max="2099">
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-size: 12px; font-weight: 700;">Periode Pelacakan *</label>
                        <select name="tracking_period" id="editTrackingPeriod" class="form-select" style="font-size: 13px;" required>
                            <option value="3_bulan">3 Bulan Setelah Lulus</option>
                            <option value="6_bulan">6 Bulan Setelah Lulus</option>
                            <option value="12_bulan">12 Bulan (1 Tahun)</option>
                            <option value="24_bulan">24 Bulan (2 Tahun)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 14px;">
                    <label class="form-label" style="font-size: 12px; font-weight: 700;">Status Aktivitas Terkini *</label>
                    <select name="current_status" id="editCurrentStatus" class="form-select" style="font-size: 13px;" required>
                        <option value="bekerja">Bekerja di Industri / Perusahaan</option>
                        <option value="kuliah">Melanjutkan Kuliah (PTN / PTS / Kedinasan)</option>
                        <option value="wirausaha">Membangun Usaha Mandiri / Wirausaha</option>
                        <option value="mencari_kerja">Sedang Mencari Kerja</option>
                        <option value="belum_bekerja">Belum Bekerja / Gap Year</option>
                        <option value="belum_terlacak">Belum Terlacak (Menunggu Konfirmasi)</option>
                    </select>
                </div>

                <div class="grid-2" style="gap: 14px; margin-bottom: 14px;">
                    <div class="form-group">
                        <label class="form-label" style="font-size: 12px; font-weight: 700;">Instansi / Perusahaan / Kampus</label>
                        <input type="text" name="institution_or_company" id="editInstitutionOrCompany" class="form-control" style="font-size: 13px;" placeholder="Contoh: PT Telkom, ITB, Usaha Kopi...">
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-size: 12px; font-weight: 700;">Jabatan / Program Studi / Bidang</label>
                        <input type="text" name="major_or_position" id="editMajorOrPosition" class="form-control" style="font-size: 13px;" placeholder="Contoh: Software Engineer, Teknik Informatika...">
                    </div>
                </div>

                <div class="grid-2" style="gap: 14px; margin-bottom: 14px;">
                    <div class="form-group">
                        <label class="form-label" style="font-size: 12px; font-weight: 700;">Rentang Penghasilan Bulanan</label>
                        <select name="monthly_income_range" id="editMonthlyIncomeRange" class="form-select" style="font-size: 13px;">
                            <option value="">-- Pilih Rentang --</option>
                            <option value="Belum Berpenghasilan">Belum Berpenghasilan</option>
                            <option value="Di bawah Rp 2.000.000">Di bawah Rp 2.000.000</option>
                            <option value="Rp 2.000.000 - Rp 4.000.000">Rp 2.000.000 - Rp 4.000.000</option>
                            <option value="Rp 4.000.000 - Rp 7.000.000">Rp 4.000.000 - Rp 7.000.000</option>
                            <option value="Di atas Rp 7.000.000">Di atas Rp 7.000.000</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-size: 12px; font-weight: 700;">Perbarui Foto Profil (Opsional)</label>
                        <input type="file" name="photo" class="form-control" accept="image/*" style="font-size: 12px; padding: 6px 10px;">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label" style="font-size: 12px; font-weight: 700;">Catatan Perkembangan / Tracer Study</label>
                    <textarea name="notes" id="editNotes" class="form-control" rows="2" style="font-size: 13px;" placeholder="Catatan tambahan mengenai testimoni, saran, atau riwayat alumni..."></textarea>
                </div>

                <div style="background-color: #f8fafc; border: 1px solid var(--color-border); border-radius: var(--radius-sm); padding: 12px 14px;">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 13px; font-weight: 600; color: var(--color-text-main); margin: 0;">
                        <input type="checkbox" name="allow_public_showcase" id="editAllowPublicShowcase" value="1" style="width: 16px; height: 16px; accent-color: var(--color-primary);">
                        <span>Tampilkan sebagai Jejak Inspirasi Publik (Dapat dilihat siswa aktif di portal siswa)</span>
                    </label>
                </div>
            </div>

            <div class="modal-footer" style="padding: 14px 24px; border-top: 1px solid var(--color-border);">
                <button type="button" class="btn btn-secondary" onclick="closeEditAlumniModal()">
                    Batal
                </button>
                <button type="submit" class="btn btn-primary">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Konfirmasi Hapus Data Alumni -->
<div id="deleteAlumniModal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="deleteAlumniModalTitle" onclick="handleDeleteBackdropClick(event)">
    <div class="modal-dialog">
        <div class="modal-body">
            <div class="modal-icon-badge danger">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    <line x1="10" y1="11" x2="10" y2="17"></line>
                    <line x1="14" y1="11" x2="14" y2="17"></line>
                </svg>
            </div>
            <h3 id="deleteAlumniModalTitle" class="modal-title">Hapus Data Pelacakan Alumni</h3>
            <p class="modal-desc">
                Apakah Anda yakin ingin menghapus data pelacakan alumni <strong id="deleteAlumniNameText" style="color: var(--color-text-main);"></strong> (<span id="deleteAlumniCodeText" style="color: var(--color-text-subtle);"></span>)?
            </p>
            <div style="background-color: var(--color-warning-bg); border: 1px solid var(--color-warning-border); border-radius: var(--radius-sm); padding: 10px 12px; margin-top: 14px; font-size: 12px; color: var(--color-warning); display: flex; align-items: flex-start; gap: 8px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0; margin-top: 1px;">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <span>Catatan: Riwayat pelacakan ini akan dihapus dari sistem Tracer Study dan dicatat ke dalam Audit Log.</span>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeDeleteAlumniModal()">
                Batal
            </button>
            <form id="deleteAlumniForm" method="POST" action="" style="display: inline; margin: 0;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    Ya, Hapus Data
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus Masal Alumni -->
<div id="bulkDeleteModal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="bulkDeleteModalTitle" onclick="handleBulkDeleteBackdropClick(event)">
    <div class="modal-dialog" style="max-width: 480px;">
        <div class="modal-body">
            <div class="modal-icon-badge danger">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    <line x1="10" y1="11" x2="10" y2="17"></line>
                    <line x1="14" y1="11" x2="14" y2="17"></line>
                </svg>
            </div>
            <h3 id="bulkDeleteModalTitle" class="modal-title">Hapus Data Alumni Terpilih</h3>
            <p class="modal-desc">
                Anda akan menghapus <strong id="bulkDeleteCountText" style="color: var(--color-danger);">0</strong> data pelacakan alumni yang dipilih. Tindakan ini bersifat permanen dan status siswa bersangkutan akan dikembalikan ke status aktif.
            </p>
            <div id="bulkDeleteListContainer" style="max-height: 140px; overflow-y: auto; background: var(--color-surface-hover, #f8fafc); border: 1px solid var(--color-border); border-radius: var(--radius-sm); padding: 8px 12px; margin-top: 12px; font-size: 12px; color: var(--color-text-main);">
                <ul id="bulkDeleteList" style="margin: 0; padding-left: 18px; line-height: 1.6;"></ul>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeBulkDeleteModal()">
                Batal
            </button>
            <form id="bulkDeleteForm" action="{{ route('guru.alumni.bulk_destroy') }}" method="POST" style="display: inline; margin: 0;">
                @csrf
                @method('DELETE')
                <div id="bulkDeleteInputs"></div>
                <button type="submit" class="btn btn-danger">
                    Ya, Hapus Semua Terpilih
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ubah Status Masal Alumni -->
<div id="bulkStatusModal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="bulkStatusModalTitle" onclick="handleBulkStatusBackdropClick(event)">
    <div class="modal-dialog" style="max-width: 480px;">
        <div style="display: flex; align-items: center; justify-content: space-between; padding: 18px 24px; border-bottom: 1px solid var(--color-border); background-color: var(--color-surface);">
            <div>
                <h3 id="bulkStatusModalTitle" style="font-size: 16px; font-weight: 700; margin: 0; color: var(--color-text-main);">
                    Ubah Status Pelacakan Alumni Terpilih
                </h3>
                <small style="color: var(--color-text-subtle);">Perbarui status pelacakan untuk <strong id="bulkStatusCountText" style="color: var(--color-primary);">0</strong> alumni terpilih sekaligus</small>
            </div>
            <button type="button" onclick="closeBulkStatusModal()" style="border: none; background: transparent; cursor: pointer; color: var(--color-text-subtle); padding: 4px;" title="Tutup Modal" aria-label="Tutup Modal">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        <form id="bulkStatusForm" action="{{ route('guru.alumni.bulk_status') }}" method="POST" style="margin: 0;">
            @csrf
            <div id="bulkStatusInputs"></div>
            <div class="modal-body" style="padding: 20px 24px;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" style="font-size: 13px; font-weight: 600;">Pilih Status Terkini Baru *</label>
                    <select name="status" id="bulkTargetStatus" class="form-select" required style="min-height: 40px; font-size: 13px;">
                        <option value="belum_terlacak">Belum Terlacak</option>
                        <option value="bekerja">Bekerja</option>
                        <option value="kuliah">Kuliah (Pendidikan Tinggi)</option>
                        <option value="wirausaha">Wirausaha</option>
                        <option value="mencari_kerja">Mencari Kerja</option>
                        <option value="belum_bekerja">Belum Bekerja</option>
                    </select>
                    <div class="form-hint" style="margin-top: 6px; font-size: 11px; color: var(--color-text-subtle);">Semua alumni yang dipilih akan diperbarui ke status ini dan dicatat di Audit Log.</div>
                </div>
            </div>
            <div class="modal-footer" style="padding: 14px 24px; border-top: 1px solid var(--color-border); display: flex; justify-content: flex-end; gap: 8px;">
                <button type="button" class="btn btn-secondary" onclick="closeBulkStatusModal()">
                    Batal
                </button>
                <button type="submit" class="btn btn-primary" style="font-weight: 600;">
                    Simpan Perubahan Status
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function toggleSelectAllAlumni(master) {
        var isChecked = master.checked;
        var checkboxes = document.querySelectorAll('.alumni-checkbox');
        checkboxes.forEach(function(cb) {
            cb.checked = isChecked;
            var row = document.getElementById('alumni-row-' + cb.value);
            if (row) {
                row.style.backgroundColor = isChecked ? 'rgba(37, 99, 235, 0.06)' : '';
            }
        });
        updateSelectedAlumniCount();
    }

    function updateSelectedAlumniCount() {
        var checkboxes = document.querySelectorAll('.alumni-checkbox');
        var checked = Array.from(checkboxes).filter(function(cb) { return cb.checked; });
        var count = checked.length;
        var total = checkboxes.length;

        var master = document.getElementById('selectAllAlumni');
        if (master) {
            master.checked = total > 0 && count === total;
            master.indeterminate = count > 0 && count < total;
        }

        checkboxes.forEach(function(cb) {
            var row = document.getElementById('alumni-row-' + cb.value);
            if (row) {
                row.style.backgroundColor = cb.checked ? 'rgba(37, 99, 235, 0.06)' : '';
            }
        });

        var bulkBar = document.getElementById('bulkActionBar');
        var bulkCountEl = document.getElementById('bulkSelectedCount');
        var headerBadge = document.getElementById('headerSelectedBadge');
        var headerCountEl = document.getElementById('headerSelectedCount');

        if (bulkBar) {
            bulkBar.style.display = count > 0 ? 'flex' : 'none';
        }
        if (bulkCountEl) {
            bulkCountEl.textContent = count;
        }
        if (headerBadge && headerCountEl) {
            headerBadge.style.display = count > 0 ? 'inline-flex' : 'none';
            headerCountEl.textContent = count;
        }
    }

    function clearAlumniSelection() {
        var checkboxes = document.querySelectorAll('.alumni-checkbox');
        checkboxes.forEach(function(cb) {
            cb.checked = false;
            var row = document.getElementById('alumni-row-' + cb.value);
            if (row) row.style.backgroundColor = '';
        });
        var master = document.getElementById('selectAllAlumni');
        if (master) {
            master.checked = false;
            master.indeterminate = false;
        }
        updateSelectedAlumniCount();
    }

    function openBulkDeleteModal() {
        var checked = Array.from(document.querySelectorAll('.alumni-checkbox:checked'));
        if (checked.length === 0) return;

        var countText = document.getElementById('bulkDeleteCountText');
        if (countText) countText.textContent = checked.length;

        var list = document.getElementById('bulkDeleteList');
        if (list) {
            list.innerHTML = '';
            checked.forEach(function(cb) {
                var li = document.createElement('li');
                var name = cb.getAttribute('data-name') || ('Alumni #' + cb.value);
                var code = cb.getAttribute('data-code') || '';
                li.innerHTML = '<strong>' + escapeHtml(name) + '</strong>' + (code ? ' <span style="color: var(--color-text-subtle);">(' + escapeHtml(code) + ')</span>' : '');
                list.appendChild(li);
            });
        }

        var inputsContainer = document.getElementById('bulkDeleteInputs');
        if (inputsContainer) {
            inputsContainer.innerHTML = '';
            checked.forEach(function(cb) {
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = cb.value;
                inputsContainer.appendChild(input);
            });
        }

        var modal = document.getElementById('bulkDeleteModal');
        if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeBulkDeleteModal() {
        var modal = document.getElementById('bulkDeleteModal');
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    function handleBulkDeleteBackdropClick(e) {
        if (e.target.id === 'bulkDeleteModal') {
            closeBulkDeleteModal();
        }
    }

    function openBulkStatusModal() {
        var checked = Array.from(document.querySelectorAll('.alumni-checkbox:checked'));
        if (checked.length === 0) return;

        var countText = document.getElementById('bulkStatusCountText');
        if (countText) countText.textContent = checked.length;

        var inputsContainer = document.getElementById('bulkStatusInputs');
        if (inputsContainer) {
            inputsContainer.innerHTML = '';
            checked.forEach(function(cb) {
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = cb.value;
                inputsContainer.appendChild(input);
            });
        }

        var modal = document.getElementById('bulkStatusModal');
        if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeBulkStatusModal() {
        var modal = document.getElementById('bulkStatusModal');
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    function handleBulkStatusBackdropClick(e) {
        if (e.target.id === 'bulkStatusModal') {
            closeBulkStatusModal();
        }
    }

    function openEditAlumniModal(data) {
        var modal = document.getElementById('editAlumniModal');
        var form = document.getElementById('editAlumniForm');
        if (!modal || !form) return;

        form.action = '/guru/alumni/' + data.id;
        document.getElementById('editAlumniStudentName').textContent = data.name;
        document.getElementById('editAlumniStudentMeta').textContent = 'NISN: ' + data.nisn + (data.major ? ' • ' + data.major : '');
        document.getElementById('editAlumniCodeBadge').textContent = data.code;
        document.getElementById('editGraduationYear').value = data.graduation_year || '';
        document.getElementById('editTrackingPeriod').value = data.tracking_period || '6_bulan';
        document.getElementById('editCurrentStatus').value = data.current_status || 'belum_terlacak';
        document.getElementById('editInstitutionOrCompany').value = data.institution_or_company || '';
        document.getElementById('editMajorOrPosition').value = data.major_or_position || '';
        document.getElementById('editMonthlyIncomeRange').value = data.monthly_income_range || '';
        document.getElementById('editNotes').value = data.notes || '';
        document.getElementById('editAllowPublicShowcase').checked = !!data.allow_public_showcase;

        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeEditAlumniModal() {
        var modal = document.getElementById('editAlumniModal');
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    function handleEditBackdropClick(e) {
        if (e.target.id === 'editAlumniModal') {
            closeEditAlumniModal();
        }
    }

    function openDeleteAlumniModal(id, name, code) {
        var modal = document.getElementById('deleteAlumniModal');
        var form = document.getElementById('deleteAlumniForm');
        if (!modal || !form) return;

        form.action = '/guru/alumni/' + id;
        document.getElementById('deleteAlumniNameText').textContent = name;
        document.getElementById('deleteAlumniCodeText').textContent = code;

        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeDeleteAlumniModal() {
        var modal = document.getElementById('deleteAlumniModal');
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    function handleDeleteBackdropClick(e) {
        if (e.target.id === 'deleteAlumniModal') {
            closeDeleteAlumniModal();
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeEditAlumniModal();
            closeDeleteAlumniModal();
            closeBulkDeleteModal();
            closeBulkStatusModal();
        }
    });
</script>
@endsection
