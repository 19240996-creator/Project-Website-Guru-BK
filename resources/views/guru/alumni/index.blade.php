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

<div class="card">
    <div class="card-header card-header-navy">
        <h3 class="card-title">Daftar Pelacakan Alumni ({{ $alumni->total() }})</h3>
        <a href="{{ route('guru.laporan.generate', ['package' => 'paket_c']) }}" target="_blank" class="btn btn-secondary btn-sm">
            Cetak Rekap Lulusan Kepala Sekolah
        </a>
    </div>
    <div class="card-body" style="padding: 0;">
        @if($alumni->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead class="table-thead-navy">
                        <tr>
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
                            @endphp
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        @if($als->photo)
                                             <img src="{{ asset('storage/' . $als->photo) }}" alt="{{ $als->student ? $als->student->name : 'Alumni' }}" style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover; border: 1px solid var(--color-border); flex-shrink: 0;">
                                        @elseif($als->student && $als->student->avatar)
                                            <img src="{{ asset('storage/' . $als->student->avatar) }}" alt="{{ $als->student->name }}" style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover; border: 1px solid var(--color-border); flex-shrink: 0;">
                                        @else
                                            <div class="user-avatar-initial" style="width: 38px; height: 38px; font-size: 13px; flex-shrink: 0;">
                                                {{ strtoupper(substr($als->student ? $als->student->name : 'A', 0, 1)) }}
                                            </div>
                                        @endif
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
                <p class="empty-state-title">Belum Ada Data Pelacakan Alumni</p>
                <p class="empty-state-desc">Ubah status siswa kelas XII menjadi lulus melalui menu Profil Siswa 360° untuk mencatat pelacakan alumni.</p>
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

<script>
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
        }
    });
</script>
@endsection
