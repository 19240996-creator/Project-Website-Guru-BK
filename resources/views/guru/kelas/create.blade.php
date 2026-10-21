@extends('layouts.app')

@section('title', 'Buat Kelas BK Online Baru - Guru BK')
@section('header_title', 'Buat Kelas BK Online')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
        <a href="{{ route('guru.kelas.index') }}" class="btn btn-secondary" style="padding: 7px 12px; display: inline-flex; align-items: center; gap: 6px;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
            <span>Kembali ke Daftar</span>
        </a>
        <div>
            <h2 style="font-size: 20px; font-weight: 800; color: var(--color-navy); margin: 0;">Buat Kelas BK Online Baru</h2>
            <p style="font-size: 13px; color: var(--color-text-muted); margin: 0;">
                Tentukan sasaran peserta, jadwal pembelajaran, dan rentang waktu presensi siswa.
            </p>
        </div>
    </div>

    @if($errors->any())
    <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 14px 18px; margin-bottom: 20px;">
        <div style="font-size: 13px; font-weight: 700; color: #991b1b; margin-bottom: 6px;">Terdapat kesalahan pada isian formulir:</div>
        <ul style="margin: 0; padding-left: 20px; font-size: 12.5px; color: #b91c1c;">
            @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('guru.kelas.store') }}" method="POST">
        @csrf

        <!-- 1. Informasi Umum Pembelajaran -->
        <div class="card" style="margin-bottom: 20px; border: 1px solid var(--color-border); border-radius: 10px; overflow: hidden; background: #fff;">
            <div style="padding: 14px 20px; background: var(--color-surface-subtle, #f8fafd); border-bottom: 1px solid var(--color-border);">
                <h3 style="font-size: 14px; font-weight: 700; color: var(--color-navy); margin: 0;">
                    1. Identitas Pembelajaran BK
                </h3>
            </div>
            <div class="card-body" style="padding: 20px;">
                <div style="margin-bottom: 16px;">
                    <label class="form-label" style="font-size: 12.5px; font-weight: 600; margin-bottom: 5px;">
                        Judul / Nama Kelas Pembelajaran <span style="color: #dc2626;">*</span>
                    </label>
                    <input type="text" 
                           name="title" 
                           value="{{ old('title') }}" 
                           class="form-control" 
                           required 
                           placeholder="Contoh: Bimbingan Klasikal: Memilih Rencana Karier & Kuliah Lanjutan" 
                           style="min-height: 40px; font-size: 13.5px;">
                </div>

                <div>
                    <label class="form-label" style="font-size: 12.5px; font-weight: 600; margin-bottom: 5px;">
                        Deskripsi / Petunjuk Awal Pembelajaran
                    </label>
                    <textarea name="description" 
                              rows="3" 
                              class="form-control" 
                              placeholder="Tuliskan tujuan materi, instruksi umum, atau hal yang perlu dipersiapkan siswa..." 
                              style="font-size: 13px; line-height: 1.5;">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        <!-- 2. Sasaran Rombongan Belajar (Cakupan Penugasan) -->
        <div class="card" style="margin-bottom: 20px; border: 1px solid var(--color-border); border-radius: 10px; overflow: hidden; background: #fff;">
            <div style="padding: 14px 20px; background: var(--color-surface-subtle, #f8fafd); border-bottom: 1px solid var(--color-border); display: flex; justify-content: space-between; align-items: center;">
                <h3 style="font-size: 14px; font-weight: 700; color: var(--color-navy); margin: 0;">
                    2. Sasaran Rombongan Belajar &amp; Siswa Binaan
                </h3>
                <span style="font-size: 11.5px; color: var(--color-text-muted);">
                    @if(auth()->user()->isKoordinatorBk())
                        Akses Koordinator: Seluruh Rombel Sekolah
                    @else
                        Dibatasi pada Rombel Binaan Resmi Anda
                    @endif
                </span>
            </div>
            <div class="card-body" style="padding: 20px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 18px;">
                    <div>
                        <label class="form-label" style="font-size: 12.5px; font-weight: 600; margin-bottom: 5px;">Tingkat Kelas</label>
                        <select name="target_grade" id="selectTargetGrade" class="form-select" style="min-height: 38px; font-size: 13px;" onchange="filterRombelList()">
                            <option value="">Semua Tingkat (Atau tentukan spesifik di bawah)</option>
                            @foreach($grades as $g)
                            <option value="{{ $g }}" {{ old('target_grade') === $g ? 'selected' : '' }}>Kelas {{ $g }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label" style="font-size: 12.5px; font-weight: 600; margin-bottom: 5px;">Jurusan / Program Keahlian</label>
                        <select name="target_major" id="selectTargetMajor" class="form-select" style="min-height: 38px; font-size: 13px;" onchange="filterRombelList()">
                            <option value="">Semua Jurusan</option>
                            @foreach($majors as $m)
                            <option value="{{ $m }}" {{ old('target_major') === $m ? 'selected' : '' }}>{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; flex-wrap: wrap; gap: 8px;">
                        <div>
                            <label class="form-label" style="font-size: 12.5px; font-weight: 600; margin: 0; display: inline-block;">
                                Pilih Rombongan Belajar Sasaran Spesifik (Opsional)
                            </label>
                            <span id="rombelFilterBadge" style="font-size: 11.5px; color: var(--color-navy); font-weight: 600; margin-left: 6px;"></span>
                        </div>
                        <div style="display: flex; gap: 6px;">
                            <button type="button" class="btn btn-secondary" onclick="selectAllVisibleRombel()" style="font-size: 11px; padding: 4px 10px; border-radius: 4px;" title="Pilih semua rombel yang sedang tampil sesuai filter">
                                Pilih Semua yang Tampil
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="clearSelectedRombel()" style="font-size: 11px; padding: 4px 10px; border-radius: 4px; color: #dc2626;" title="Kosongkan seluruh pilihan rombel">
                                Kosongkan
                            </button>
                        </div>
                    </div>
                    <p style="font-size: 11.5px; color: var(--color-text-muted); margin: 0 0 10px 0;">
                        Centang rombel yang menjadi peserta kelas. Jika tidak ada yang dicentang, kelas terbuka bagi seluruh siswa binaan Anda pada tingkat/jurusan di atas.
                    </p>

                    <div id="rombelCheckboxGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(210px, 1fr)); gap: 10px; max-height: 220px; overflow-y: auto; padding: 12px; border: 1px solid var(--color-border); border-radius: 8px; background: #fafbfc;">
                        @forelse($availableClasses as $ac)
                        <label class="rombel-item" 
                               data-id="{{ $ac->id }}" 
                               data-grade="{{ $ac->grade }}" 
                               data-major="{{ $ac->major }}" 
                               data-name="{{ $ac->name }}"
                               data-students="{{ $ac->students_count }}"
                               style="display: flex; align-items: center; gap: 8px; font-size: 12.5px; cursor: pointer; padding: 6px 8px; border-radius: 6px; background: #fff; border: 1px solid var(--color-border); transition: all 0.15s ease;">
                            <input type="checkbox" 
                                   name="target_class_ids[]" 
                                   value="{{ $ac->id }}"
                                   class="rombel-checkbox"
                                   onchange="updateSelectedRombelDisplay()"
                                   {{ is_array(old('target_class_ids')) && in_array($ac->id, old('target_class_ids')) ? 'checked' : '' }}
                                   style="cursor: pointer; width: 15px; height: 15px; accent-color: var(--color-navy);">
                            <span style="flex: 1; font-weight: 500;">
                                {{ $ac->name }} 
                                <small style="color: var(--color-text-muted); font-size: 11px;">({{ $ac->students_count }} siswa)</small>
                            </span>
                        </label>
                        @empty
                        <div style="grid-column: 1 / -1; font-size: 12px; color: #b91c1c;">
                            Belum ada pembagian rombel siswa untuk akun Anda. Hubungi Koordinator BK untuk penugasan kelas.
                        </div>
                        @endforelse
                        <div id="noRombelMatchNotice" style="display: none; grid-column: 1 / -1; font-size: 12px; color: var(--color-text-muted); text-align: center; padding: 16px;">
                            Tidak ada rombel yang sesuai dengan filter tingkat atau jurusan yang dipilih.
                        </div>
                    </div>

                    <!-- Panel Dinamis: Pilihan Kelas yang Telah Dipilih -->
                    <div id="selectedRombelContainer" style="margin-top: 12px; padding: 12px 16px; border-radius: 8px; border: 1.5px dashed var(--color-border); background: #f8fafc; transition: all 0.2s ease;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; flex-wrap: wrap; gap: 8px;">
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--color-navy)" stroke-width="2.2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                <strong style="font-size: 12.5px; color: var(--color-navy);">Pilihan Kelas yang Telah Dipilih:</strong>
                            </div>
                            <span id="selectedRombelCounterBadge" style="font-size: 11.5px; font-weight: 700; color: #1e40af; background: #dbeafe; padding: 2px 10px; border-radius: 20px;">
                                0 Kelas Dipilih (0 Siswa)
                            </span>
                        </div>
                        <div id="selectedRombelChips" style="display: flex; flex-wrap: wrap; gap: 8px;">
                            <!-- Injeksi chip rombel terpilih -->
                        </div>
                        <div id="selectedRombelEmptyNotice" style="font-size: 12px; color: var(--color-text-muted); line-height: 1.4;">
                            Belum ada rombel spesifik yang dipilih. Jika tidak ada yang dicentang, sesi ini otomatis berlaku bagi <strong>seluruh siswa binaan Anda</strong> pada tingkat/jurusan yang dipilih.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Jadwal Pelaksanaan & Waktu Absensi -->
        <div class="card" style="margin-bottom: 24px; border: 1px solid var(--color-border); border-radius: 10px; overflow: hidden; background: #fff;">
            <div style="padding: 14px 20px; background: var(--color-surface-subtle, #f8fafd); border-bottom: 1px solid var(--color-border);">
                <h3 style="font-size: 14px; font-weight: 700; color: var(--color-navy); margin: 0;">
                    3. Jadwal Pembelajaran &amp; Rentang Presensi (WIB)
                </h3>
            </div>
            <div class="card-body" style="padding: 20px;">
                <div style="margin-bottom: 18px;">
                    <label class="form-label" style="font-size: 12.5px; font-weight: 600; margin-bottom: 5px;">
                        Tanggal Pelaksanaan Kelas <span style="color: #dc2626;">*</span>
                    </label>
                    <input type="date" 
                           name="class_date" 
                           value="{{ old('class_date', \Carbon\Carbon::now('Asia/Jakarta')->toDateString()) }}" 
                           class="form-control" 
                           required 
                           style="min-height: 40px; font-size: 13.5px; max-width: 300px;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 18px;">
                    <!-- Jam Pembelajaran -->
                    <div style="padding: 14px; background: #f8fafc; border: 1px solid var(--color-border); border-radius: 8px;">
                        <div style="font-size: 12.5px; font-weight: 700; color: var(--color-navy); margin-bottom: 8px;">
                            Jam Pembelajaran Kelas BK
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <div>
                                <label style="font-size: 11.5px; color: var(--color-text-muted); display: block; margin-bottom: 4px;">Mulai (WIB)</label>
                                <input type="time" name="start_time" value="{{ old('start_time', '08:00') }}" class="form-control" required style="font-size: 13px;">
                            </div>
                            <div>
                                <label style="font-size: 11.5px; color: var(--color-text-muted); display: block; margin-bottom: 4px;">Selesai (WIB)</label>
                                <input type="time" name="end_time" value="{{ old('end_time', '09:30') }}" class="form-control" required style="font-size: 13px;">
                            </div>
                        </div>
                    </div>

                    <!-- Jam Presensi -->
                    <div style="padding: 14px; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px;">
                        <div style="font-size: 12.5px; font-weight: 700; color: #1e40af; margin-bottom: 8px;">
                            Rentang Presensi Siswa
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <div>
                                <label style="font-size: 11.5px; color: #1e40af; display: block; margin-bottom: 4px;">Dibuka (WIB)</label>
                                <input type="time" name="attendance_start_time" value="{{ old('attendance_start_time', '07:55') }}" class="form-control" required style="font-size: 13px;">
                            </div>
                            <div>
                                <label style="font-size: 11.5px; color: #1e40af; display: block; margin-bottom: 4px;">Batas Akhir (WIB)</label>
                                <input type="time" name="attendance_end_time" value="{{ old('attendance_end_time', '08:15') }}" class="form-control" required style="font-size: 13px;">
                            </div>
                        </div>
                    </div>
                </div>

                <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 12px 14px; font-size: 12px; color: #166534; line-height: 1.5;">
                    <strong>Ketentuan Jam Server:</strong> Seluruh validasi absensi diproses melalui waktu server Ruang BK (WIB). Siswa hanya dapat menekan tombol absensi selama rentang waktu di atas. Setelah waktu presensi ditutup, siswa yang belum absen tidak dapat melakukan absensi mandiri, namun tetap dapat mempelajari materi dan mengumpulkan tugas.
                </div>
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-bottom: 40px;">
            <a href="{{ route('guru.kelas.index') }}" class="btn btn-secondary" style="padding: 10px 20px; font-weight: 600;">
                Batal
            </a>
            <button type="submit" class="btn btn-primary" style="padding: 10px 24px; font-weight: 700; display: inline-flex; align-items: center; gap: 8px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span>Simpan &amp; Jadwalkan Kelas BK</span>
            </button>
        </div>
    </form>
</div>

<script>
function normalizeGrade(val) {
    if (!val) return '';
    var v = String(val).trim().toUpperCase();
    if (v === '10' || v === 'X' || v === 'KELAS X' || v === 'KELAS 10') return 'X';
    if (v === '11' || v === 'XI' || v === 'KELAS XI' || v === 'KELAS 11') return 'XI';
    if (v === '12' || v === 'XII' || v === 'KELAS XII' || v === 'KELAS 12') return 'XII';
    return v;
}

function filterRombelList() {
    var gradeSelect = document.getElementById('selectTargetGrade');
    var majorSelect = document.getElementById('selectTargetMajor');
    var selectedGrade = normalizeGrade(gradeSelect ? gradeSelect.value : '');
    var selectedMajor = majorSelect ? majorSelect.value.trim().toLowerCase() : '';

    var items = document.querySelectorAll('.rombel-item');
    var visibleCount = 0;

    items.forEach(function(item) {
        var itemGrade = normalizeGrade(item.getAttribute('data-grade'));
        var itemName = (item.getAttribute('data-name') || '').toUpperCase();
        var itemMajor = (item.getAttribute('data-major') || '').trim().toLowerCase();

        var gradeMatch = true;
        if (selectedGrade !== '') {
            var matchesGradeDirect = (itemGrade === selectedGrade);
            var matchesNamePattern = itemName.startsWith(selectedGrade + ' ') || 
                                     itemName.startsWith('KELAS ' + selectedGrade) ||
                                     itemName.indexOf(selectedGrade + ' ') === 0;
            if (selectedGrade === 'X') matchesNamePattern = matchesNamePattern || itemName.startsWith('10 ');
            if (selectedGrade === 'XI') matchesNamePattern = matchesNamePattern || itemName.startsWith('11 ');
            if (selectedGrade === 'XII') matchesNamePattern = matchesNamePattern || itemName.startsWith('12 ');

            gradeMatch = matchesGradeDirect || matchesNamePattern;
        }

        var majorMatch = true;
        if (selectedMajor !== '') {
            majorMatch = (itemMajor === selectedMajor) || (itemName.toLowerCase().indexOf(selectedMajor) !== -1);
        }

        if (gradeMatch && majorMatch) {
            item.style.display = 'flex';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });

    var badge = document.getElementById('rombelFilterBadge');
    if (badge) {
        if (selectedGrade !== '' && selectedMajor !== '') {
            badge.textContent = '(Filter: Kelas ' + selectedGrade + ' • ' + selectedMajor + ' — ' + visibleCount + ' rombel)';
        } else if (selectedGrade !== '') {
            badge.textContent = '(Filter: Kelas ' + selectedGrade + ' — ' + visibleCount + ' rombel)';
        } else if (selectedMajor !== '') {
            badge.textContent = '(Filter: ' + selectedMajor + ' — ' + visibleCount + ' rombel)';
        } else {
            badge.textContent = '(Menampilkan seluruh ' + visibleCount + ' rombel)';
        }
    }

    var noMatch = document.getElementById('noRombelMatchNotice');
    if (noMatch) {
        noMatch.style.display = (visibleCount === 0) ? 'block' : 'none';
    }

    updateSelectedRombelDisplay();
}

function updateSelectedRombelDisplay() {
    var checkedBoxes = document.querySelectorAll('.rombel-checkbox:checked');
    var container = document.getElementById('selectedRombelChips');
    var emptyNotice = document.getElementById('selectedRombelEmptyNotice');
    var badge = document.getElementById('selectedRombelCounterBadge');

    if (!container) return;

    container.innerHTML = '';
    var totalStudents = 0;

    if (checkedBoxes.length === 0) {
        if (emptyNotice) emptyNotice.style.display = 'block';
        if (badge) badge.textContent = '0 Kelas Dipilih (0 Siswa)';
        return;
    }

    if (emptyNotice) emptyNotice.style.display = 'none';

    checkedBoxes.forEach(function(cb) {
        var parent = cb.closest('.rombel-item');
        var id = cb.value;
        var name = parent ? parent.getAttribute('data-name') : ('Kelas #' + id);
        var students = parent ? parseInt(parent.getAttribute('data-students') || '0', 10) : 0;
        totalStudents += students;

        var chip = document.createElement('div');
        chip.style.display = 'inline-flex';
        chip.style.alignItems = 'center';
        chip.style.gap = '6px';
        chip.style.background = '#1a365d';
        chip.style.color = '#ffffff';
        chip.style.padding = '5px 12px';
        chip.style.borderRadius = '20px';
        chip.style.fontSize = '12px';
        chip.style.fontWeight = '600';
        chip.style.boxShadow = '0 1px 3px rgba(0,0,0,0.1)';

        chip.innerHTML = '<span>' + name + '</span>' +
            '<span style="background: rgba(255,255,255,0.22); padding: 1px 7px; border-radius: 10px; font-size: 11px;">' + students + ' siswa</span>' +
            '<button type="button" onclick="uncheckRombel(\'' + id + '\')" style="background: transparent; border: none; color: #fff; cursor: pointer; padding: 0; font-size: 14px; line-height: 1; margin-left: 3px; display: inline-flex; align-items: center;" title="Hapus pilihan ini">&times;</button>';

        container.appendChild(chip);
    });

    if (badge) {
        badge.textContent = checkedBoxes.length + ' Kelas Dipilih (' + totalStudents + ' Siswa)';
    }
}

function uncheckRombel(id) {
    var cb = document.querySelector('.rombel-checkbox[value="' + id + '"]');
    if (cb) {
        cb.checked = false;
        updateSelectedRombelDisplay();
    }
}

function selectAllVisibleRombel() {
    var items = document.querySelectorAll('.rombel-item');
    items.forEach(function(item) {
        if (item.style.display !== 'none') {
            var cb = item.querySelector('.rombel-checkbox');
            if (cb) cb.checked = true;
        }
    });
    updateSelectedRombelDisplay();
}

function clearSelectedRombel() {
    var checkboxes = document.querySelectorAll('.rombel-checkbox');
    checkboxes.forEach(function(cb) {
        cb.checked = false;
    });
    updateSelectedRombelDisplay();
}

document.addEventListener('DOMContentLoaded', function() {
    filterRombelList();
    updateSelectedRombelDisplay();
});
</script>
@endsection
