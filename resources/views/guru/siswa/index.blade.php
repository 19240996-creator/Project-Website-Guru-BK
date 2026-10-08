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
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="{{ route('guru.siswa.edit', $students->first() ? $students->first()->id : 1) }}?tab=mass" class="btn btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            <span>Kenaikan Kelas Massal</span>
        </a>
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
    <div class="card-header card-header-navy">
        <h3 class="card-title">Daftar Data Siswa (Total: {{ $students->total() }})</h3>
    </div>
    <div class="card-body" style="padding: 0;">
        @if($students->count() > 0)
            <div class="table-responsive">
                <table class="table" style="min-width: 960px; width: 100%;">
                    <thead class="table-thead-navy">
                        <tr>
                            <th style="width: 22%; text-align: left;">Identitas Siswa</th>
                            <th style="width: 20%; text-align: left;">Kelas & Jurusan</th>
                            <th style="width: 16%; text-align: left;">Kontak</th>
                            <th style="width: 14%; text-align: center;">Status Perhatian</th>
                            <th style="width: 11%; text-align: center;">Status Siswa</th>
                            <th style="width: 17%; text-align: center;">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $s)
                            <tr>
                                <td style="text-align: left;">
                                    <div style="font-weight: 700; color: var(--color-text-main); font-size: 13px; line-height: 1.4;">{{ $s->name }}</div>
                                    <div style="font-size: 12px; color: var(--color-text-subtle); line-height: 1.4; margin-top: 2px;">NISN: {{ $s->nisn }} | NIS: {{ $s->nis }}</div>
                                </td>
                                <td style="text-align: left;">
                                    <div style="font-weight: 700; color: var(--color-text-main); font-size: 13px; line-height: 1.4;">{{ $s->studentClass ? $s->studentClass->name : '-' }}</div>
                                    <div style="font-size: 12px; color: var(--color-text-muted); line-height: 1.4; margin-top: 2px;">{{ $s->studentClass ? $s->studentClass->major : '-' }}</div>
                                </td>
                                <td style="text-align: left;">
                                    <div style="font-weight: 700; color: var(--color-text-main); font-size: 13px; line-height: 1.4;">{{ $s->phone ?: '-' }}</div>
                                    <div style="font-size: 12px; color: var(--color-text-subtle); line-height: 1.4; margin-top: 2px;">Ortu: {{ $s->parent_name ?: '-' }}</div>
                                </td>
                                <td style="text-align: center;">
                                    @if($s->attention_level === 'normal')
                                        <span class="badge badge-secondary" style="min-width: 80px; justify-content: center;">Normal</span>
                                    @elseif($s->attention_level === 'perlu_perhatian')
                                        <span class="badge badge-warning" style="min-width: 80px; justify-content: center;">Perlu Perhatian</span>
                                    @elseif($s->attention_level === 'prioritas')
                                        <span class="badge badge-danger" style="min-width: 80px; justify-content: center;">Prioritas</span>
                                    @else
                                        <span class="badge badge-danger" style="min-width: 80px; justify-content: center;">Segera Ditindaklanjuti</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <span class="badge badge-{{ $s->status === 'aktif' ? 'success' : 'secondary' }}" style="min-width: 68px; justify-content: center;">
                                        {{ ucfirst($s->status) }}
                                    </span>
                                </td>
                                <td style="text-align: center; white-space: nowrap;">
                                    <div style="display: inline-flex; gap: 6px; align-items: center; justify-content: center;">
                                        <a href="{{ route('guru.siswa.show', $s->id) }}" class="btn btn-secondary btn-sm" title="Lihat Profil">
                                            Profil
                                        </a>
                                        <a href="{{ route('guru.siswa.edit', $s->id) }}" class="btn btn-secondary btn-sm" title="Edit">
                                            Ubah
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="openDeleteStudentModal('{{ $s->id }}', '{{ addslashes($s->name) }}', '{{ $s->nisn }}')" title="Hapus Data Siswa">
                                            Hapus
                                        </button>
                                    </div>
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
<div id="importSection" class="card" style="margin-top: 32px;">
    <div class="card-header card-header-navy" style="display: flex; flex-direction: column; gap: 12px; padding: 20px 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <h3 class="card-title" style="margin: 0; font-size: 16px; font-weight: 700; color: #ffffff;">Impor Data Siswa Secara Massal (Format CSV / Excel)</h3>
            <a href="{{ route('guru.siswa.template') }}" class="btn btn-secondary btn-sm" title="Unduh Template Format Impor CSV / Excel">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                <span>Unduh Template CSV</span>
            </a>
        </div>
        <p style="font-size: 13px; color: #e2e8f0; margin: 0; line-height: 1.5; display: flex; align-items: flex-start; gap: 8px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: #93c5fd; flex-shrink: 0; margin-top: 2px;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
            <span>
                <strong style="color: #ffffff; font-weight: 700;">Petunjuk Aturan Impor Data:</strong>
                Sesuai aturan sistem, pilih kelas terlebih dahulu sebelum mengimpor file. Sistem akan otomatis membuatkan akun login siswa dengan username NISN dan kata sandi default <code style="background: rgba(255, 255, 255, 0.2); color: #ffffff; padding: 2px 6px; border-radius: var(--radius-sm); font-weight: 600;">password123</code>.
            </span>
        </p>
    </div>
    <div class="card-body">

        <form action="{{ route('guru.siswa.import') }}" method="POST" enctype="multipart/form-data" style="max-width: 600px;">
            @csrf
            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label" style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 13px;">Pilihan Kelas</label>
                <select name="grade" class="form-select" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-size: 13px;">
                    <option value="">-- Pilih Kelas --</option>
                    <option value="X">Kelas X</option>
                    <option value="XI">Kelas XI</option>
                    <option value="XII">Kelas XII</option>
                </select>
                <small style="color: var(--color-text-subtle); font-size: 11px; margin-top: 4px; display: block;">
                    Pilih tingkatan jenjang kelas tujuan siswa.
                </small>
            </div>

            <div class="form-group" style="margin-bottom: 16px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <label class="form-label" style="font-weight: 600; font-size: 13px; margin: 0;">Jurusan / Program Keahlian</label>
                    <button type="button" id="btnToggleManualMajor" class="btn btn-secondary btn-sm" onclick="toggleManualMajor()" style="padding: 2px 8px; font-size: 11px;">
                        Klik untuk memasukkan jurusan manual
                    </button>
                </div>

                <div id="selectMajorWrapper">
                    <select name="major" id="majorSelect" class="form-select" style="width: 100%; padding: 8px 12px; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-size: 13px;" required>
                        <option value="">-- Pilih Jurusan yang Tersedia --</option>
                        @foreach($majors as $m)
                            <option value="{{ $m }}">{{ $m }}</option>
                        @endforeach
                    </select>
                    <small style="color: var(--color-text-subtle); font-size: 11px; margin-top: 4px; display: block;">
                        Daftar jurusan yang telah tersimpan dari input sebelumnya.
                    </small>
                </div>

                <div id="manualMajorWrapper" style="display: none; margin-top: 6px;">
                    <input type="text" name="custom_major" id="customMajorInput" class="form-control" placeholder="Ketik nama jurusan baru (misal: Desain Komunikasi Visual)..." style="width: 100%; padding: 8px 12px; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-size: 13px;">
                    <small style="color: var(--color-text-subtle); font-size: 11px; margin-top: 4px; display: block;">
                        Jurusan baru akan otomatis tersimpan dalam daftar pilihan untuk impor berikutnya.
                    </small>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label class="form-label" style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 13px;">Pilih File CSV Siswa</label>
                <input type="file" name="csv_file" class="form-control" accept=".csv, .txt" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-size: 13px;">
                <div class="form-hint" style="font-size: 11px; color: var(--color-text-subtle); margin-top: 4px;">
                    Format kolom CSV: <code>NIS, NISN, Nama Lengkap, Jenis Kelamin (L/P), No. HP</code>.
                    Belum memiliki format? <a href="{{ route('guru.siswa.template') }}" style="color: var(--color-primary); font-weight: 600; text-decoration: underline;">Unduh template format di sini</a>.
                </div>
            </div>

            <button type="submit" class="btn btn-accent">
                Mulai Proses Impor Siswa
            </button>
        </form>

        <script>
            var isManualMajor = false;
            function toggleManualMajor() {
                isManualMajor = !isManualMajor;
                var selectWrapper = document.getElementById('selectMajorWrapper');
                var manualWrapper = document.getElementById('manualMajorWrapper');
                var majorSelect = document.getElementById('majorSelect');
                var customInput = document.getElementById('customMajorInput');
                var btnToggle = document.getElementById('btnToggleManualMajor');

                if (isManualMajor) {
                    selectWrapper.style.display = 'none';
                    manualWrapper.style.display = 'block';
                    majorSelect.value = '';
                    majorSelect.removeAttribute('required');
                    customInput.setAttribute('required', 'required');
                    customInput.focus();
                    btnToggle.textContent = 'Batal, pilih dari daftar yang ada';
                } else {
                    selectWrapper.style.display = 'block';
                    manualWrapper.style.display = 'none';
                    customInput.value = '';
                    customInput.removeAttribute('required');
                    majorSelect.setAttribute('required', 'required');
                    btnToggle.textContent = 'Klik untuk memasukkan jurusan manual';
                }
            }

            // Jika belum ada jurusan yang tersimpan, aktifkan mode manual langsung
            @if($majors->isEmpty())
                toggleManualMajor();
            @endif
        </script>
    </div>
</div>

<!-- Modal Konfirmasi Hapus Data Siswa (Antislop Compliant) -->
<div id="deleteStudentModal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="deleteStudentModalTitle" onclick="handleDeleteBackdropClick(event)">
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
            <h3 id="deleteStudentModalTitle" class="modal-title">Konfirmasi Hapus Data Siswa</h3>
            <p class="modal-desc">
                Apakah Anda yakin ingin menghapus data siswa <strong id="deleteStudentNameText" style="color: var(--color-text-main);"></strong> (<span id="deleteStudentNisnText" style="color: var(--color-text-subtle);"></span>)?
            </p>
            <div style="background-color: var(--color-danger-bg); border: 1px solid var(--color-danger-border); border-radius: var(--radius-sm); padding: 10px 14px; margin-top: 14px; font-size: 12px; color: var(--color-danger); display: flex; align-items: flex-start; gap: 8px; line-height: 1.45;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0; margin-top: 2px;">
                    <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
                <span>Perhatian: Seluruh riwayat bimbingan konseling, hasil asesmen, dan akun login siswa ini akan dihapus secara permanen dan tidak dapat dipulihkan.</span>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeDeleteStudentModal()">
                Batal
            </button>
            <form id="deleteStudentForm" method="POST" action="" style="display: inline; margin: 0;">
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
    function openDeleteStudentModal(studentId, studentName, studentNisn) {
        var modal = document.getElementById('deleteStudentModal');
        var form = document.getElementById('deleteStudentForm');
        var nameSpan = document.getElementById('deleteStudentNameText');
        var nisnSpan = document.getElementById('deleteStudentNisnText');

        if (modal && form) {
            form.action = '/guru/siswa/' + studentId;
            if (nameSpan) nameSpan.textContent = studentName;
            if (nisnSpan) nisnSpan.textContent = 'NISN: ' + studentNisn;
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeDeleteStudentModal() {
        var modal = document.getElementById('deleteStudentModal');
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    function handleDeleteBackdropClick(event) {
        if (event.target === document.getElementById('deleteStudentModal')) {
            closeDeleteStudentModal();
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDeleteStudentModal();
        }
    });
</script>
@endsection
