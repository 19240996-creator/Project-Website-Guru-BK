@extends('layouts.app')

@section('title', 'Ubah Data Siswa - ' . $student->name)
@section('header_title', 'Ubah Data Siswa')

@section('content')
<div style="max-width: 860px; margin: 0 auto 20px auto; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
    <div class="tab-nav" style="margin-bottom: 0;">
        <button type="button" class="tab-btn active" id="tabBtnSingle" onclick="switchEditTab('single')">
            Perbarui Profil: {{ $student->name }}
        </button>
        <button type="button" class="tab-btn" id="tabBtnMass" onclick="switchEditTab('mass')">
            Kenaikan Kelas Massal
        </button>
    </div>
    <a href="{{ route('guru.siswa.show', $student->id) }}" class="btn btn-secondary btn-sm">
        Kembali ke Profil Siswa
    </a>
</div>

<!-- Pane 1: Edit Profil Individu Siswa (Tampilan Utama Tidak Berubah) -->
<div id="paneSingle" style="max-width: 860px; margin: 0 auto;">
    <div class="card">
        <div class="card-header card-header-navy" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <h3 class="card-title">Perbarui Profil: {{ $student->name }}</h3>
            <span class="badge badge-translucent">NISN: {{ $student->nisn }}</span>
        </div>
        <div class="card-body">
            <form action="{{ route('guru.siswa.update', $student->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">NIS *</label>
                        <input type="text" name="nis" class="form-control" value="{{ old('nis', $student->nis) }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">NISN *</label>
                        <input type="text" name="nisn" class="form-control" value="{{ old('nisn', $student->nisn) }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nama Lengkap Siswa *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $student->name) }}" required>
                </div>

                <div class="grid-3">
                    <div class="form-group">
                        <label class="form-label">Jenis Kelamin *</label>
                        <select name="gender" class="form-select" required>
                            <option value="L" {{ old('gender', $student->gender) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('gender', $student->gender) === 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Kelas & Jurusan *</label>
                        <select name="student_class_id" class="form-select" required>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" {{ old('student_class_id', $student->student_class_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Status Siswa / Alumni *</label>
                        <select name="status" class="form-select" required>
                            <option value="aktif" {{ old('status', $student->status) === 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="lulus" {{ old('status', $student->status) === 'lulus' ? 'selected' : '' }}>Alumni (Lulus)</option>
                            <option value="pindah" {{ old('status', $student->status) === 'pindah' ? 'selected' : '' }}>Pindah</option>
                            <option value="tidak_aktif" {{ old('status', $student->status) === 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Tempat Lahir</label>
                        <input type="text" name="birth_place" class="form-control" value="{{ old('birth_place', $student->birth_place) }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tanggal Lahir</label>
                        <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date', $student->birth_date ? $student->birth_date->format('Y-m-d') : '') }}">
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">No. Telepon / HP Siswa</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $student->phone) }}">
                        <small style="display: block; margin-top: 4px; font-size: 11px; color: var(--color-text-muted);">Kata sandi login siswa otomatis mengikuti nomor ini. Jika diganti, siswa wajib login menggunakan nomor baru.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tingkat Perhatian *</label>
                        <select name="attention_level" class="form-select" required>
                            <option value="normal" {{ old('attention_level', $student->attention_level) === 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="perlu_perhatian" {{ old('attention_level', $student->attention_level) === 'perlu_perhatian' ? 'selected' : '' }}>Perlu Perhatian</option>
                            <option value="prioritas" {{ old('attention_level', $student->attention_level) === 'prioritas' ? 'selected' : '' }}>Prioritas</option>
                            <option value="segera_ditindaklanjuti" {{ old('attention_level', $student->attention_level) === 'segera_ditindaklanjuti' ? 'selected' : '' }}>Segera Ditindaklanjuti</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Alamat Rumah</label>
                    <textarea name="address" class="form-control" rows="2">{{ old('address', $student->address) }}</textarea>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Nama Orang Tua / Wali</label>
                        <input type="text" name="parent_name" class="form-control" value="{{ old('parent_name', $student->parent_name) }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">No. Telepon Orang Tua</label>
                        <input type="text" name="parent_phone" class="form-control" value="{{ old('parent_phone', $student->parent_phone) }}">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Pekerjaan Orang Tua</label>
                    <input type="text" name="parent_job" class="form-control" value="{{ old('parent_job', $student->parent_job) }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Catatan Khusus Bimbingan</label>
                    <textarea name="special_notes" class="form-control" rows="3">{{ old('special_notes', $student->special_notes) }}</textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px;">
                    <a href="{{ route('guru.siswa.show', $student->id) }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Pane 2: Tampilan Tambahan Kenaikan Kelas Massal -->
<div id="paneMass" style="display: none; max-width: 860px; margin: 0 auto;">
    <div class="card">
        <div class="card-header card-header-navy" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: #93c5fd;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                <h3 class="card-title" style="margin: 0; font-size: 15px; font-weight: 700; color: #ffffff;">Fitur Kenaikan Kelas & Kelulusan Alumni</h3>
            </div>
            <span class="badge badge-translucent" id="massSelectedCountBadge">0 siswa dipilih</span>
        </div>
        <div class="card-body">
            <form action="{{ route('guru.siswa.mass_promote') }}" method="POST" id="massPromoteForm">
                @csrf

                <!-- Filter Kelas / Tingkat Asal -->
                <div style="background-color: #f8fafc; border: 1px solid var(--color-border); border-radius: var(--radius-sm); padding: 16px; margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; flex-wrap: wrap; gap: 8px;">
                        <label class="form-label" style="margin-bottom: 0; font-weight: 700;">1. Filter Tampilkan Siswa Berdasarkan Tingkat / Kelas Asal</label>
                        <span style="font-size: 12px; color: var(--color-text-subtle);" id="massVisibleCount">{{ $allStudents->count() }} siswa ditampilkan</span>
                    </div>
                    <select id="massFilterSelect" class="form-select" onchange="filterMassStudents()">
                        <option value="all">-- Semua Siswa (Aktif) --</option>
                        <option value="grade:X">Kelas 10</option>
                        <option value="grade:XI">Kelas 11</option>
                        <option value="grade:XII">Kelas 12 (Siap Jadi Alumni)</option>
                    </select>
                </div>

                <!-- Tabel Pemilihan Siswa -->
                <div style="margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; flex-wrap: wrap; gap: 8px;">
                        <label class="form-label" style="margin-bottom: 0; font-weight: 700;">2. Pilih Siswa (Bisa Pilih 1 per 1 atau Massal Sekaligus)</label>
                        <div style="display: flex; gap: 10px; font-size: 12px;">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="toggleSelectAllMass(true)" style="padding: 3px 8px; font-size: 11px;">Pilih Semua Yang Tampil</button>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="toggleSelectAllMass(false)" style="padding: 3px 8px; font-size: 11px;">Batal Pilih</button>
                        </div>
                    </div>

                    <div class="table-responsive" style="max-height: 340px; overflow-y: auto; border: 1px solid var(--color-border); border-radius: var(--radius-sm);">
                        <table class="table" style="width: 100%; font-size: 13px;">
                            <thead class="table-thead-navy" style="position: sticky; top: 0; z-index: 10;">
                                <tr>
                                    <th style="width: 12%; text-align: center;">
                                        <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAllMass(this.checked)" title="Pilih Semua" style="cursor: pointer;">
                                    </th>
                                    <th style="width: 44%; text-align: left;">Nama & NISN Siswa</th>
                                    <th style="width: 44%; text-align: left;">Kelas Saat Ini</th>
                                </tr>
                            </thead>
                            <tbody id="massStudentTableBody">
                                @foreach($allStudents as $s)
                                    <tr class="mass-student-row" data-grade="{{ $s->studentClass ? $s->studentClass->grade : '' }}" data-class-id="{{ $s->student_class_id }}">
                                        <td style="text-align: center;">
                                            <input type="checkbox" name="student_ids[]" value="{{ $s->id }}" class="mass-student-checkbox" onchange="updateMassSelectedCount()" style="cursor: pointer;">
                                        </td>
                                        <td style="text-align: left;">
                                            <div style="font-weight: 700; color: var(--color-text-main);">{{ $s->name }}</div>
                                            <small style="color: var(--color-text-subtle);">NISN: {{ $s->nisn }} | NIS: {{ $s->nis }}</small>
                                        </td>
                                        <td style="text-align: left;">
                                            <span class="badge badge-secondary">
                                                {{ $s->studentClass ? $s->studentClass->name : 'Tanpa Kelas' }}
                                            </span>
                                            <small style="display: block; color: var(--color-text-muted); margin-top: 2px;">
                                                {{ $s->studentClass ? $s->studentClass->major : '-' }}
                                            </small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pilihan Kelas Tujuan Baru (10, 11, 12, Alumni) -->
                <div style="background-color: #eff6ff; border: 1px solid var(--color-primary-border); border-radius: var(--radius-sm); padding: 16px; margin-bottom: 24px;">
                    <label class="form-label" style="font-weight: 700; color: var(--color-primary);">3. Pilih Kelas Tujuan Kenaikan Kelas / Alumni</label>
                    <select name="target_class" class="form-select" required style="border-color: var(--color-primary-border); font-weight: 600; font-size: 14px;">
                        <option value="">-- Pilih Kelas Tujuan --</option>
                        <option value="10">Kelas 10</option>
                        <option value="11">Kelas 11</option>
                        <option value="12">Kelas 12</option>
                        <option value="alumni">Alumni</option>
                    </select>
                    <div class="form-hint" style="color: var(--color-text-muted); margin-top: 6px;">
                        Pilih <strong>Kelas 10, 11, atau 12</strong> untuk kenaikan tingkat, atau pilih <strong>Alumni</strong> untuk meluluskan siswa (terutama kelas 12) ke Tracer Study alumni. Sistem otomatis memperbarui data di profil dan seluruh sistem.
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="btn btn-secondary" onclick="switchEditTab('single')">Batal / Kembali</button>
                    <button type="submit" class="btn btn-primary" id="massSubmitBtn" disabled>
                        Terapkan Kenaikan Kelas / Kelulusan Alumni
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function switchEditTab(tab) {
    const paneSingle = document.getElementById('paneSingle');
    const paneMass = document.getElementById('paneMass');
    const btnSingle = document.getElementById('tabBtnSingle');
    const btnMass = document.getElementById('tabBtnMass');

    if (tab === 'mass') {
        paneSingle.style.display = 'none';
        paneMass.style.display = 'block';
        btnSingle.classList.remove('active');
        btnMass.classList.add('active');
    } else {
        paneSingle.style.display = 'block';
        paneMass.style.display = 'none';
        btnSingle.classList.add('active');
        btnMass.classList.remove('active');
    }
}

function filterMassStudents() {
    const filterVal = document.getElementById('massFilterSelect').value;
    const rows = document.querySelectorAll('.mass-student-row');
    let visibleCount = 0;

    rows.forEach(row => {
        const grade = row.getAttribute('data-grade');
        const classId = row.getAttribute('data-class-id');
        let show = false;

        if (!filterVal || filterVal === 'all') {
            show = true;
        } else if (filterVal.startsWith('grade:')) {
            show = (grade === filterVal.replace('grade:', ''));
        } else if (filterVal.startsWith('class:')) {
            show = (classId === filterVal.replace('class:', ''));
        }

        row.style.display = show ? '' : 'none';
        if (show) visibleCount++;
    });

    const visibleCountEl = document.getElementById('massVisibleCount');
    if (visibleCountEl) {
        visibleCountEl.textContent = visibleCount + ' siswa ditampilkan';
    }

    const selectAllCb = document.getElementById('selectAllCheckbox');
    if (selectAllCb) selectAllCb.checked = false;
    updateMassSelectedCount();
}

function toggleSelectAllMass(checked) {
    const rows = document.querySelectorAll('.mass-student-row');
    rows.forEach(row => {
        if (row.style.display !== 'none') {
            const cb = row.querySelector('.mass-student-checkbox');
            if (cb) cb.checked = checked;
        }
    });
    updateMassSelectedCount();
}

function updateMassSelectedCount() {
    const checkedBoxes = document.querySelectorAll('.mass-student-checkbox:checked');
    const badge = document.getElementById('massSelectedCountBadge');
    const submitBtn = document.getElementById('massSubmitBtn');

    if (badge) {
        badge.textContent = checkedBoxes.length + ' siswa dipilih';
    }
    if (submitBtn) {
        submitBtn.disabled = (checkedBoxes.length === 0);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('tab') === 'mass') {
        switchEditTab('mass');
    }
    filterMassStudents();
});
</script>
@endsection
