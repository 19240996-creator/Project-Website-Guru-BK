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
            <!-- Pengelolaan Foto Profil Siswa -->
            <div style="margin-bottom: 24px; padding: 16px; background-color: var(--color-surface-subtle); border: 1px solid var(--color-border); border-radius: var(--radius-md);">
                <div class="avatar-profile-header">
                    <div class="avatar-current-wrapper" style="width: 80px; height: 80px;">
                        <img src="{{ $student->avatar_url }}" alt="{{ $student->name }}" id="studentEditAvatarImg">
                    </div>
                    <div class="avatar-profile-info">
                        @if($student->hasCustomAvatar())
                            <div class="avatar-status-badge custom">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                <span>Foto Khusus Terpasang</span>
                            </div>
                        @else
                            <div class="avatar-status-badge default">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                <span>Menggunakan Avatar Default</span>
                            </div>
                        @endif
                        <h4 style="font-size: 14.5px; font-weight: 700; color: var(--color-text-main); margin-bottom: 4px;">Foto Profil Siswa</h4>
                        <p style="font-size: 12px; color: var(--color-text-muted); margin-bottom: 10px;">
                            Guru BK dapat mengunggah foto baru, memotong (crop) dengan live preview, atau menghapus foto agar kembali ke avatar default.
                        </p>
                        <div class="avatar-btn-group">
                            <input type="file" id="studentEditPhotoInput" accept=".jpg,.jpeg,.png,.webp" style="display: none;" onchange="handleStudentEditPhotoSelect(this)">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('studentEditPhotoInput').click()" title="Unggah foto baru dengan crop preview">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                                <span>{{ $student->hasCustomAvatar() ? 'Ganti Foto' : 'Unggah Foto Baru' }}</span>
                            </button>
                            @if($student->hasCustomAvatar())
                                <button type="button" class="btn btn-danger-subtle" onclick="handleStudentEditPhotoDelete()" title="Hapus foto siswa dan kembali ke avatar default">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                    <span>Hapus Foto</span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

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
    <div class="card" style="overflow: visible;">
        <div class="card-header card-header-navy" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: #93c5fd;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                <h3 class="card-title" style="margin: 0; font-size: 15px; font-weight: 700; color: #ffffff;">Fitur Kenaikan Kelas & Kelulusan Alumni</h3>
            </div>
            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                <button type="button" class="btn btn-secondary btn-sm" onclick="openDeleteByClassModal()" style="border-color: #fecaca; color: #b91c1c; font-size: 12px; background: #ffffff;" title="Hapus seluruh data siswa di kelas tertentu (antisipasi salah upload data)">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                    <span>Hapus Siswa per Kelas</span>
                </button>
                <span class="badge badge-translucent" id="massSelectedCountBadge">0 siswa dipilih</span>
            </div>
        </div>
        <div class="card-body" style="overflow: visible;">
            <form action="{{ route('guru.siswa.mass_promote') }}" method="POST" id="massPromoteForm">
                @csrf

                <!-- Filter Kelas & Jurusan Asal -->
                <div style="background-color: #f8fafc; border: 1px solid var(--color-border); border-radius: var(--radius-sm); padding: 18px; margin-bottom: 20px; position: relative; z-index: 30; overflow: visible;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 8px;">
                        <div>
                            <label class="form-label" style="margin-bottom: 2px; font-weight: 700; font-size: 13.5px; color: var(--color-text-main);">
                                1. Filter Siswa Berdasarkan Kelas & Jurusan Asal
                            </label>
                            <p style="margin: 0; font-size: 12px; color: var(--color-text-muted);">
                                Pilih atau cari kelas dan jurusan terlebih dahulu agar data siswa yang tampil sesuai dan akurat.
                            </p>
                        </div>
                        <span style="font-size: 12px; font-weight: 600; color: var(--color-primary); background: #eff6ff; padding: 4px 10px; border-radius: 9999px; border: 1px solid #bfdbfe;" id="massVisibleCount">
                            Pilih filter terlebih dahulu
                        </span>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 14px; position: relative;">
                        <!-- Custom Searchable Dropdown: Tingkat / Kelas Asal (Selalu ke bawah) -->
                        <div style="position: relative; width: 100%;">
                            <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 5px; color: var(--color-text-main);">
                                Tingkat / Kelas Asal *
                            </label>

                            <!-- Hidden Select untuk penyimpanan state & kompatibilitas form/test -->
                            <select id="massFilterClassSelect" style="display: none;">
                                <option value="">-- Pilih Tingkat / Kelas --</option>
                                <option value="all">Semua Tingkat (Tampilkan Keseluruhan)</option>
                                <optgroup label="Berdasarkan Tingkat Jenjang">
                                    <option value="grade:X">Kelas 10 (Semua Tingkat X)</option>
                                    <option value="grade:XI">Kelas 11 (Semua Tingkat XI)</option>
                                    <option value="grade:XII">Kelas 12 (Semua Tingkat XII / Siap Jadi Alumni)</option>
                                </optgroup>
                                <optgroup label="Berdasarkan Rombel / Kelas Spesifik">
                                    @foreach($classes as $c)
                                        <option value="class:{{ $c->id }}" data-grade="{{ $c->grade }}" data-major="{{ $c->major }}">
                                            {{ $c->name }} (Kelas {{ $c->grade }})
                                        </option>
                                    @endforeach
                                </optgroup>
                            </select>

                            <!-- Trigger Button -->
                            <div id="massClassDropdownTrigger" class="form-select custom-dropdown-trigger" tabindex="0" onclick="toggleMassClassDropdown(event)" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();toggleMassClassDropdown(event);}" style="width: 100%; min-height: 40px; padding: 9px 12px; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-size: 13px; text-align: left; background-color: var(--color-surface); color: var(--color-text-main); display: flex; justify-content: space-between; align-items: center; cursor: pointer; user-select: none;">
                                <span id="massClassSelectedText" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: var(--color-text-subtle);">-- Pilih Tingkat / Kelas --</span>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" id="massClassChevron" style="transition: transform 0.2s ease; flex-shrink: 0; margin-left: 6px;"><path d="m6 9 6 6 6-6"/></svg>
                            </div>

                            <!-- Menu Dropdown dengan Sticky Search (Muncul Selalu Ke Bawah) -->
                            <div id="massClassDropdownMenu" style="display: none; position: absolute; top: calc(100% + 4px); left: 0; right: 0; width: 100%; min-width: 260px; background: var(--color-surface, #ffffff); border: 1px solid var(--color-border); border-radius: var(--radius-sm); box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1); z-index: 100; max-height: 280px; overflow-y: auto;">
                                <!-- Sticky Search Box -->
                                <div style="padding: 8px 10px; border-bottom: 1px solid var(--color-border); background: var(--color-surface); position: sticky; top: 0; z-index: 10;">
                                    <div style="position: relative; display: flex; align-items: center;">
                                        <span style="position: absolute; left: 10px; color: var(--color-text-subtle); display: flex; align-items: center; pointer-events: none;">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                        </span>
                                        <input type="text" id="massClassSearchInput" placeholder="Cari nama kelas atau tingkat..." oninput="filterMassClassSearch()" onkeydown="handleMassClassSearchKeyDown(event)" onclick="event.stopPropagation()" style="width: 100%; padding: 6px 28px 6px 30px; font-size: 12px; border: 1px solid var(--color-border); border-radius: var(--radius-sm); outline: none; background: var(--color-bg, #f8fafc); color: var(--color-text-main); font-family: inherit;">
                                        <button type="button" id="btnClearMassClassSearch" onclick="clearMassClassSearch(event)" style="position: absolute; right: 6px; display: none; background: none; border: none; padding: 2px 4px; color: var(--color-text-subtle); cursor: pointer; border-radius: 4px;" title="Reset pencarian">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                        </button>
                                    </div>
                                </div>

                                <div id="massClassOptionList">
                                    <div class="mass-class-option-row" data-search-text="semua tingkat keseluruhan" onclick="selectMassClassOption('all', 'Semua Tingkat (Tampilkan Keseluruhan)')" style="padding: 8px 12px; cursor: pointer; font-size: 12.5px; font-weight: 600; color: var(--color-text-main); border-bottom: 1px solid var(--color-border); transition: background 0.15s ease;" onmouseover="this.style.background='var(--color-surface-hover)'" onmouseout="this.style.background='transparent'">
                                        Semua Tingkat (Tampilkan Keseluruhan)
                                    </div>

                                    <!-- Group Tingkat Jenjang -->
                                    <div class="mass-class-group-block" data-group-name="tingkat jenjang">
                                        <div style="padding: 6px 12px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--color-text-subtle); background: var(--color-surface-hover, #f1f5f9); border-bottom: 1px solid var(--color-border);">
                                            Berdasarkan Tingkat Jenjang
                                        </div>
                                        <div class="mass-class-option-row" data-search-text="kelas 10 semua tingkat x" onclick="selectMassClassOption('grade:X', 'Kelas 10 (Semua Tingkat X)')" style="padding: 8px 16px; cursor: pointer; font-size: 12.5px; color: var(--color-text-main); border-bottom: 1px solid var(--color-border); transition: background 0.15s ease;" onmouseover="this.style.background='var(--color-surface-hover)'" onmouseout="this.style.background='transparent'">
                                            Kelas 10 (Semua Tingkat X)
                                        </div>
                                        <div class="mass-class-option-row" data-search-text="kelas 11 semua tingkat xi" onclick="selectMassClassOption('grade:XI', 'Kelas 11 (Semua Tingkat XI)')" style="padding: 8px 16px; cursor: pointer; font-size: 12.5px; color: var(--color-text-main); border-bottom: 1px solid var(--color-border); transition: background 0.15s ease;" onmouseover="this.style.background='var(--color-surface-hover)'" onmouseout="this.style.background='transparent'">
                                            Kelas 11 (Semua Tingkat XI)
                                        </div>
                                        <div class="mass-class-option-row" data-search-text="kelas 12 semua tingkat xii alumni" onclick="selectMassClassOption('grade:XII', 'Kelas 12 (Semua Tingkat XII / Siap Jadi Alumni)')" style="padding: 8px 16px; cursor: pointer; font-size: 12.5px; color: var(--color-text-main); border-bottom: 1px solid var(--color-border); transition: background 0.15s ease;" onmouseover="this.style.background='var(--color-surface-hover)'" onmouseout="this.style.background='transparent'">
                                            Kelas 12 (Semua Tingkat XII / Siap Jadi Alumni)
                                        </div>
                                    </div>

                                    <!-- Group Rombel / Kelas Spesifik -->
                                    <div class="mass-class-group-block" data-group-name="rombel spesifik">
                                        <div style="padding: 6px 12px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--color-text-subtle); background: var(--color-surface-hover, #f1f5f9); border-bottom: 1px solid var(--color-border);">
                                            Berdasarkan Rombel / Kelas Spesifik
                                        </div>
                                        @foreach($classes as $c)
                                            <div class="mass-class-option-row" data-search-text="{{ strtolower($c->name . ' kelas ' . $c->grade . ' ' . $c->major) }}" onclick="selectMassClassOption('class:{{ $c->id }}', '{{ addslashes($c->name) }} (Kelas {{ $c->grade }})')" style="padding: 8px 16px; cursor: pointer; font-size: 12.5px; color: var(--color-text-main); border-bottom: 1px solid var(--color-border); display: flex; justify-content: space-between; align-items: center; transition: background 0.15s ease;" onmouseover="this.style.background='var(--color-surface-hover)'" onmouseout="this.style.background='transparent'">
                                                <span style="font-weight: 500;">{{ $c->name }}</span>
                                                <span style="font-size: 11px; color: var(--color-text-subtle);">Kelas {{ $c->grade }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div id="massClassNotFoundMsg" style="display: none; padding: 14px 12px; text-align: center; font-size: 12px; color: var(--color-text-subtle);">
                                    Tidak ada tingkat atau kelas yang cocok.
                                </div>
                            </div>
                        </div>

                        <!-- Custom Searchable Dropdown: Jurusan / Program Keahlian (Selalu ke bawah) -->
                        <div style="position: relative; width: 100%;">
                            <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 5px; color: var(--color-text-main);">
                                Jurusan / Program Keahlian
                            </label>

                            <!-- Hidden Select untuk penyimpanan state & kompatibilitas form/test -->
                            <select id="massFilterMajorSelect" style="display: none;">
                                <option value="all">-- Semua Jurusan --</option>
                                @foreach($majors as $m)
                                    <option value="{{ $m }}">{{ $m }}</option>
                                @endforeach
                            </select>

                            <!-- Trigger Button -->
                            <div id="massMajorDropdownTrigger" class="form-select custom-dropdown-trigger" tabindex="0" onclick="toggleMassMajorDropdown(event)" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();toggleMassMajorDropdown(event);}" style="width: 100%; min-height: 40px; padding: 9px 12px; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-size: 13px; text-align: left; background-color: var(--color-surface); color: var(--color-text-main); display: flex; justify-content: space-between; align-items: center; cursor: pointer; user-select: none;">
                                <span id="massMajorSelectedText" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: var(--color-text-main);">-- Semua Jurusan --</span>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" id="massMajorChevron" style="transition: transform 0.2s ease; flex-shrink: 0; margin-left: 6px;"><path d="m6 9 6 6 6-6"/></svg>
                            </div>

                            <!-- Menu Dropdown dengan Sticky Search (Muncul Selalu Ke Bawah) -->
                            <div id="massMajorDropdownMenu" style="display: none; position: absolute; top: calc(100% + 4px); left: 0; right: 0; width: 100%; min-width: 260px; background: var(--color-surface, #ffffff); border: 1px solid var(--color-border); border-radius: var(--radius-sm); box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1); z-index: 100; max-height: 280px; overflow-y: auto;">
                                <!-- Sticky Search Box -->
                                <div style="padding: 8px 10px; border-bottom: 1px solid var(--color-border); background: var(--color-surface); position: sticky; top: 0; z-index: 10;">
                                    <div style="position: relative; display: flex; align-items: center;">
                                        <span style="position: absolute; left: 10px; color: var(--color-text-subtle); display: flex; align-items: center; pointer-events: none;">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                        </span>
                                        <input type="text" id="massMajorSearchInput" placeholder="Cari nama jurusan..." oninput="filterMassMajorSearch()" onkeydown="handleMassMajorSearchKeyDown(event)" onclick="event.stopPropagation()" style="width: 100%; padding: 6px 28px 6px 30px; font-size: 12px; border: 1px solid var(--color-border); border-radius: var(--radius-sm); outline: none; background: var(--color-bg, #f8fafc); color: var(--color-text-main); font-family: inherit;">
                                        <button type="button" id="btnClearMassMajorSearch" onclick="clearMassMajorSearch(event)" style="position: absolute; right: 6px; display: none; background: none; border: none; padding: 2px 4px; color: var(--color-text-subtle); cursor: pointer; border-radius: 4px;" title="Reset pencarian">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                        </button>
                                    </div>
                                </div>

                                <div id="massMajorOptionList">
                                    <div class="mass-major-option-row" data-search-text="semua jurusan" onclick="selectMassMajorOption('all', '-- Semua Jurusan --')" style="padding: 8px 12px; cursor: pointer; font-size: 12.5px; font-weight: 600; color: var(--color-text-main); border-bottom: 1px solid var(--color-border); transition: background 0.15s ease;" onmouseover="this.style.background='var(--color-surface-hover)'" onmouseout="this.style.background='transparent'">
                                        -- Semua Jurusan --
                                    </div>

                                    @foreach($majors as $m)
                                        <div class="mass-major-option-row" data-search-text="{{ strtolower($m) }}" onclick="selectMassMajorOption('{{ addslashes($m) }}', '{{ addslashes($m) }}')" style="padding: 8px 14px; cursor: pointer; font-size: 12.5px; color: var(--color-text-main); border-bottom: 1px solid var(--color-border); transition: background 0.15s ease;" onmouseover="this.style.background='var(--color-surface-hover)'" onmouseout="this.style.background='transparent'">
                                            {{ $m }}
                                        </div>
                                    @endforeach
                                </div>

                                <div id="massMajorNotFoundMsg" style="display: none; padding: 14px 12px; text-align: center; font-size: 12px; color: var(--color-text-subtle);">
                                    Tidak ada jurusan yang cocok.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabel Pemilihan Siswa -->
                <div style="margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; flex-wrap: wrap; gap: 8px;">
                        <div>
                            <label class="form-label" style="margin-bottom: 0; font-weight: 700;">2. Pilih Siswa (Pilih 1 per 1 atau Massal Sekaligus)</label>
                            <small style="display: block; color: var(--color-text-muted); font-size: 11.5px;">Gunakan tombol "Pilih Semua Yang Tampil" untuk mencentang seluruh siswa hasil filter.</small>
                        </div>
                        <div style="display: flex; gap: 8px; font-size: 12px;">
                            <button type="button" class="btn btn-secondary btn-sm" id="btnSelectAllVisible" onclick="toggleSelectAllMass(true)" style="padding: 4px 10px; font-size: 12px; font-weight: 600;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right: 4px;"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                <span>Pilih Semua Yang Tampil</span>
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="toggleSelectAllMass(false)" style="padding: 4px 10px; font-size: 12px;">
                                Batal Pilih
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive" style="max-height: 360px; overflow-y: auto; border: 1px solid var(--color-border); border-radius: var(--radius-sm);">
                        <table class="table" style="width: 100%; font-size: 13px;">
                            <thead class="table-thead-navy" style="position: sticky; top: 0; z-index: 10;">
                                <tr>
                                    <th style="width: 12%; text-align: center;">
                                        <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAllMass(this.checked)" title="Pilih Semua yang Ditampilkan" style="cursor: pointer; width: 16px; height: 16px;">
                                    </th>
                                    <th style="width: 44%; text-align: left;">Nama & NISN Siswa</th>
                                    <th style="width: 44%; text-align: left;">Kelas Saat Ini</th>
                                </tr>
                            </thead>
                            <tbody id="massStudentTableBody">
                                <!-- Baris petunjuk filter jika belum difilter -->
                                <tr id="massFilterPromptRow">
                                    <td colspan="3" style="text-align: center; padding: 36px 16px; color: var(--color-text-muted);">
                                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin: 0 auto 10px auto; color: var(--color-text-subtle); display: block;"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                                        <div style="font-weight: 700; font-size: 14px; color: var(--color-text-main);">Silakan Pilih Filter Kelas & Jurusan Asal</div>
                                        <div style="font-size: 12px; margin-top: 4px; color: var(--color-text-muted);">Pilih tingkat/kelas atau jurusan di atas untuk menampilkan siswa yang akan dinaikkan kelas.</div>
                                    </td>
                                </tr>

                                <!-- Baris jika hasil filter kosong -->
                                <tr id="massEmptyStateRow" style="display: none;">
                                    <td colspan="3" style="text-align: center; padding: 36px 16px; color: var(--color-text-muted);">
                                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin: 0 auto 10px auto; color: var(--color-text-subtle); display: block;"><circle cx="12" cy="12" r="10"></circle><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                                        <div style="font-weight: 700; font-size: 14px; color: var(--color-text-main);">Tidak Ada Siswa Sesuai Filter</div>
                                        <div style="font-size: 12px; margin-top: 4px; color: var(--color-text-muted);">Tidak ditemukan data siswa aktif untuk kombinasi kelas dan jurusan yang dipilih.</div>
                                    </td>
                                </tr>

                                @foreach($allStudents as $s)
                                    <tr class="mass-student-row" data-grade="{{ $s->studentClass ? $s->studentClass->grade : '' }}" data-class-id="{{ $s->student_class_id }}" data-major="{{ $s->studentClass ? $s->studentClass->major : '' }}" style="display: none;">
                                        <td style="text-align: center;">
                                            <input type="checkbox" name="student_ids[]" value="{{ $s->id }}" class="mass-student-checkbox" onchange="updateMassSelectedCount()" style="cursor: pointer; width: 16px; height: 16px;">
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
                <div style="background-color: #eff6ff; border: 1px solid var(--color-primary-border); border-radius: var(--radius-sm); padding: 18px; margin-bottom: 24px;">
                    <label for="massTargetClassSelect" class="form-label" style="font-weight: 700; color: var(--color-primary); font-size: 13.5px; margin-bottom: 6px;">
                        3. Pilih Kelas Tujuan Kenaikan Kelas / Alumni *
                    </label>
                    <select name="target_class" id="massTargetClassSelect" class="form-select" required style="border-color: var(--color-primary-border); font-weight: 600; font-size: 14px; min-height: 42px;">
                        <option value="">-- Pilih Kelas Tujuan --</option>
                        <option value="10">Kelas 10 (Tingkat X)</option>
                        <option value="11">Kelas 11 (Tingkat XI)</option>
                        <option value="12">Kelas 12 (Tingkat XII)</option>
                        <option value="alumni">Alumni (Lulus)</option>
                    </select>
                    <div class="form-hint" style="color: var(--color-text-muted); margin-top: 8px; font-size: 12px; line-height: 1.5;">
                        Pilih <strong>Kelas 10, 11, atau 12</strong> untuk kenaikan jenjang (sistem otomatis menyesuaikan rombel sesuai jurusan siswa), atau pilih <strong>Alumni</strong> untuk meluluskan siswa ke Tracer Study.
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

<!-- Modal Konfirmasi Hapus Data Siswa Berdasarkan Kelas (Antisipasi Salah Upload) -->
<div id="deleteByClassModal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="deleteByClassModalTitle" onclick="handleDeleteByClassBackdropClick(event)">
    <div class="modal-dialog" style="max-width: 520px; overflow: visible;">
        <div class="modal-body" style="overflow: visible;">
            <div class="modal-icon-badge danger">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    <line x1="10" y1="11" x2="10" y2="17"></line>
                    <line x1="14" y1="11" x2="14" y2="17"></line>
                </svg>
            </div>
            <h3 id="deleteByClassModalTitle" class="modal-title">Hapus Data Siswa Berdasarkan Kelas</h3>
            <p class="modal-desc">
                Fitur ini disediakan khusus untuk membersihkan data siswa pada satu kelas sekaligus (misalnya untuk mengantisipasi salah upload file Excel/CSV atau dobel data).
            </p>

            <form id="deleteByClassForm" method="POST" action="{{ route('guru.siswa.destroy_by_class') }}" style="margin-top: 16px;">
                @csrf
                @method('DELETE')

                <div class="form-group" style="margin-bottom: 14px; text-align: left; position: relative;">
                    <label style="font-weight: 700; font-size: 13px; color: var(--color-text-main); display: block; margin-bottom: 6px;">
                        Pilih Kelas Sasaran *
                    </label>

                    <!-- Hidden Select untuk validasi & kirim form -->
                    <select name="student_class_id" id="deleteClassSelect" style="display: none;" required>
                        <option value="">-- Pilih Kelas yang Ingin Dihapus Siswanya --</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" data-name="{{ $c->name }}" data-count="{{ $c->students_count ?? 0 }}">
                                {{ $c->name }} ({{ $c->students_count ?? 0 }} Siswa)
                            </option>
                        @endforeach
                    </select>

                    <!-- Trigger Dropdown Kustom -->
                    <div id="deleteClassDropdownTrigger" class="form-select custom-dropdown-trigger" tabindex="0" onclick="toggleDeleteClassDropdown(event)" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();toggleDeleteClassDropdown(event);}" style="width: 100%; min-height: 40px; padding: 9px 12px; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-size: 13px; text-align: left; background-color: var(--color-surface); color: var(--color-text-main); display: flex; justify-content: space-between; align-items: center; cursor: pointer; user-select: none;">
                        <span id="deleteClassSelectedText" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: var(--color-text-subtle);">-- Pilih Kelas yang Ingin Dihapus Siswanya --</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" id="deleteClassChevron" style="transition: transform 0.2s ease; flex-shrink: 0; margin-left: 6px;"><path d="m6 9 6 6 6-6"/></svg>
                    </div>

                    <!-- Dropdown Menu dengan Sticky Search (Selalu ke bawah) -->
                    <div id="deleteClassDropdownMenu" style="display: none; position: absolute; top: calc(100% + 4px); left: 0; right: 0; width: 100%; background: var(--color-surface, #ffffff); border: 1px solid var(--color-border); border-radius: var(--radius-sm); box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1); z-index: 120; max-height: 240px; overflow-y: auto;">
                        <!-- Sticky Search Box -->
                        <div style="padding: 8px 10px; border-bottom: 1px solid var(--color-border); background: var(--color-surface); position: sticky; top: 0; z-index: 10;">
                            <div style="position: relative; display: flex; align-items: center;">
                                <span style="position: absolute; left: 10px; color: var(--color-text-subtle); display: flex; align-items: center; pointer-events: none;">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                </span>
                                <input type="text" id="deleteClassSearchInput" placeholder="Ketik untuk mencari nama kelas..." oninput="filterDeleteClassSearch()" onkeydown="handleDeleteClassSearchKeyDown(event)" onclick="event.stopPropagation()" style="width: 100%; padding: 6px 28px 6px 30px; font-size: 12px; border: 1px solid var(--color-border); border-radius: var(--radius-sm); outline: none; background: var(--color-bg, #f8fafc); color: var(--color-text-main); font-family: inherit;">
                                <button type="button" id="btnClearDeleteClassSearch" onclick="clearDeleteClassSearch(event)" style="position: absolute; right: 6px; display: none; background: none; border: none; padding: 2px 4px; color: var(--color-text-subtle); cursor: pointer; border-radius: 4px;" title="Reset pencarian">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                </button>
                            </div>
                        </div>

                        <div id="deleteClassOptionList">
                            <div class="delete-class-option-row" data-search-text="" onclick="selectDeleteClassOption('', '-- Pilih Kelas yang Ingin Dihapus Siswanya --')" style="padding: 8px 12px; cursor: pointer; font-size: 12.5px; color: var(--color-text-subtle); border-bottom: 1px solid var(--color-border); transition: background 0.15s ease;" onmouseover="this.style.background='var(--color-surface-hover)'" onmouseout="this.style.background='transparent'">
                                <em>-- Pilih Kelas yang Ingin Dihapus Siswanya --</em>
                            </div>

                            @foreach($classes as $c)
                                <div class="delete-class-option-row" data-search-text="{{ strtolower($c->name . ' kelas ' . $c->grade . ' ' . $c->major) }}" onclick="selectDeleteClassOption('{{ $c->id }}', '{{ addslashes($c->name) }} ({{ $c->students_count ?? 0 }} Siswa)')" style="padding: 8px 14px; cursor: pointer; font-size: 12.5px; color: var(--color-text-main); border-bottom: 1px solid var(--color-border); display: flex; justify-content: space-between; align-items: center; transition: background 0.15s ease;" onmouseover="this.style.background='var(--color-surface-hover)'" onmouseout="this.style.background='transparent'">
                                    <span style="font-weight: 500;">{{ $c->name }}</span>
                                    <span style="font-size: 11px; font-weight: 600; color: {{ ($c->students_count ?? 0) > 0 ? 'var(--color-primary)' : 'var(--color-text-subtle)' }}; background: {{ ($c->students_count ?? 0) > 0 ? '#eff6ff' : 'var(--color-surface-hover)' }}; padding: 2px 7px; border-radius: 9999px;">
                                        {{ $c->students_count ?? 0 }} Siswa
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        <div id="deleteClassNotFoundMsg" style="display: none; padding: 14px 12px; text-align: center; font-size: 12px; color: var(--color-text-subtle);">
                            Tidak ada kelas yang cocok dengan pencarian.
                        </div>
                    </div>
                </div>

                <!-- Live Summary Info Box -->
                <div id="deleteClassInfoBox" style="display: none; background-color: #fef2f2; border: 1px solid #fee2e2; border-radius: var(--radius-sm); padding: 12px 14px; margin-bottom: 14px; font-size: 12.5px; color: #991b1b; text-align: left; line-height: 1.45;">
                    <div style="font-weight: 700; margin-bottom: 3px;">
                        Rangkuman Tindakan:
                    </div>
                    <div>
                        Sebanyak <strong id="deleteClassCountText">0</strong> siswa pada kelas <strong id="deleteClassNameText">-</strong> beserta seluruh akun login dan riwayat terkait akan dihapus secara permanen.
                    </div>
                </div>

                <div style="text-align: left; margin-bottom: 14px; padding: 10px 12px; background: var(--color-surface-hover); border: 1px solid var(--color-border); border-radius: var(--radius-sm);">
                    <label style="display: flex; align-items: flex-start; gap: 8px; font-size: 12px; color: var(--color-text-main); cursor: pointer; user-select: none;">
                        <input type="checkbox" name="delete_class_too" value="1" style="margin-top: 2px; cursor: pointer;">
                        <span>Sekaligus hapus rombel / nama kelas ini dari daftar sistem (opsional jika kelas baru dibuat saat salah impor).</span>
                    </label>
                </div>

                <div style="background-color: var(--color-danger-bg); border: 1px solid var(--color-danger-border); border-radius: var(--radius-sm); padding: 10px 14px; font-size: 12px; color: var(--color-danger); display: flex; align-items: flex-start; gap: 8px; text-align: left; line-height: 1.45;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0; margin-top: 2px;">
                        <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                    <span>Perhatian: Seluruh akun user login dan data rekam jejak siswa pada kelas ini akan dihapus secara permanen dan tidak dapat dipulihkan.</span>
                </div>

                <div class="modal-footer" style="padding-top: 18px; padding-bottom: 0; padding-left: 0; padding-right: 0;">
                    <button type="button" class="btn btn-secondary" onclick="closeDeleteByClassModal()">
                        Batal
                    </button>
                    <button type="submit" id="btnConfirmDeleteByClass" class="btn btn-danger" disabled>
                        Ya, Hapus Data Siswa di Kelas Ini
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
    const classFilter = document.getElementById('massFilterClassSelect') ? document.getElementById('massFilterClassSelect').value : '';
    const majorFilter = document.getElementById('massFilterMajorSelect') ? document.getElementById('massFilterMajorSelect').value : 'all';
    const rows = document.querySelectorAll('.mass-student-row');
    const promptRow = document.getElementById('massFilterPromptRow');
    const emptyRow = document.getElementById('massEmptyStateRow');
    const visibleCountEl = document.getElementById('massVisibleCount');
    const selectAllCb = document.getElementById('selectAllCheckbox');
    const btnSelectAll = document.getElementById('btnSelectAllVisible');

    // Jika filter kelas belum dipilih sama sekali
    if (!classFilter) {
        if (promptRow) promptRow.style.display = '';
        if (emptyRow) emptyRow.style.display = 'none';
        rows.forEach(r => {
            r.style.display = 'none';
            const cb = r.querySelector('.mass-student-checkbox');
            if (cb) cb.checked = false;
        });
        if (visibleCountEl) visibleCountEl.textContent = 'Pilih filter terlebih dahulu';
        if (selectAllCb) {
            selectAllCb.checked = false;
            selectAllCb.disabled = true;
        }
        if (btnSelectAll) btnSelectAll.disabled = true;
        updateMassSelectedCount();
        return;
    }

    if (promptRow) promptRow.style.display = 'none';
    if (selectAllCb) selectAllCb.disabled = false;
    if (btnSelectAll) btnSelectAll.disabled = false;

    let visibleCount = 0;

    rows.forEach(row => {
        const grade = row.getAttribute('data-grade');
        const classId = row.getAttribute('data-class-id');
        const major = row.getAttribute('data-major');

        // Evaluasi filter kelas/tingkat
        let matchClass = false;
        if (classFilter === 'all') {
            matchClass = true;
        } else if (classFilter.startsWith('grade:')) {
            matchClass = (grade === classFilter.replace('grade:', ''));
        } else if (classFilter.startsWith('class:')) {
            matchClass = (classId === classFilter.replace('class:', ''));
        }

        // Evaluasi filter jurusan
        let matchMajor = false;
        if (!majorFilter || majorFilter === 'all') {
            matchMajor = true;
        } else {
            matchMajor = (major === majorFilter);
        }

        const show = matchClass && matchMajor;
        row.style.display = show ? '' : 'none';

        if (show) {
            visibleCount++;
        } else {
            // Uncheck baris yang tidak tampil agar tidak terproses secara tidak sengaja
            const cb = row.querySelector('.mass-student-checkbox');
            if (cb) cb.checked = false;
        }
    });

    if (emptyRow) {
        emptyRow.style.display = (visibleCount === 0) ? '' : 'none';
    }

    if (visibleCountEl) {
        visibleCountEl.textContent = visibleCount + ' siswa ditampilkan sesuai filter';
    }

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
    const selectAllCb = document.getElementById('selectAllCheckbox');
    if (selectAllCb) selectAllCb.checked = checked;
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

// Logika Searchable Dropdown: Tingkat / Kelas Asal
function toggleMassClassDropdown(e) {
    if (e) e.stopPropagation();
    closeMassMajorDropdown();
    var menu = document.getElementById('massClassDropdownMenu');
    var chevron = document.getElementById('massClassChevron');
    if (!menu) return;
    if (menu.style.display === 'none' || menu.style.display === '') {
        menu.style.display = 'block';
        if (chevron) chevron.style.transform = 'rotate(180deg)';
        var input = document.getElementById('massClassSearchInput');
        if (input) {
            setTimeout(function() { input.focus({ preventScroll: true }); }, 50);
        }
    } else {
        closeMassClassDropdown();
    }
}

function closeMassClassDropdown() {
    var menu = document.getElementById('massClassDropdownMenu');
    var chevron = document.getElementById('massClassChevron');
    if (menu) menu.style.display = 'none';
    if (chevron) chevron.style.transform = 'rotate(0deg)';
}

function selectMassClassOption(value, text) {
    var hiddenSelect = document.getElementById('massFilterClassSelect');
    var labelSpan = document.getElementById('massClassSelectedText');
    if (hiddenSelect) {
        hiddenSelect.value = value;
    }
    if (labelSpan) {
        labelSpan.textContent = text;
        labelSpan.style.color = value ? 'var(--color-text-main)' : 'var(--color-text-subtle)';
    }
    closeMassClassDropdown();
    filterMassStudents();
}

function filterMassClassSearch() {
    var input = document.getElementById('massClassSearchInput');
    var clearBtn = document.getElementById('btnClearMassClassSearch');
    var notFound = document.getElementById('massClassNotFoundMsg');
    if (!input) return;

    var query = input.value.trim().toLowerCase();
    if (clearBtn) clearBtn.style.display = query ? 'block' : 'none';

    var rows = document.querySelectorAll('#massClassOptionList .mass-class-option-row');
    var groups = document.querySelectorAll('#massClassOptionList .mass-class-group-block');
    var anyMatch = false;

    rows.forEach(function(row) {
        var text = (row.getAttribute('data-search-text') || row.textContent).toLowerCase();
        if (!query || text.indexOf(query) !== -1) {
            row.style.display = '';
            anyMatch = true;
        } else {
            row.style.display = 'none';
        }
    });

    groups.forEach(function(group) {
        var visibleChildren = group.querySelectorAll('.mass-class-option-row:not([style*="display: none"])');
        if (visibleChildren.length > 0) {
            group.style.display = '';
        } else {
            group.style.display = 'none';
        }
    });

    if (notFound) {
        notFound.style.display = anyMatch ? 'none' : 'block';
    }
}

function clearMassClassSearch(e) {
    if (e) e.stopPropagation();
    var input = document.getElementById('massClassSearchInput');
    if (input) {
        input.value = '';
        filterMassClassSearch();
        input.focus();
    }
}

function handleMassClassSearchKeyDown(e) {
    if (e.key === 'Escape') {
        closeMassClassDropdown();
    } else if (e.key === 'Enter') {
        e.preventDefault();
        var firstVisible = document.querySelector('#massClassOptionList .mass-class-option-row:not([style*="display: none"])');
        if (firstVisible) {
            firstVisible.click();
        }
    }
}

// Logika Searchable Dropdown: Jurusan / Program Keahlian
function toggleMassMajorDropdown(e) {
    if (e) e.stopPropagation();
    closeMassClassDropdown();
    var menu = document.getElementById('massMajorDropdownMenu');
    var chevron = document.getElementById('massMajorChevron');
    if (!menu) return;
    if (menu.style.display === 'none' || menu.style.display === '') {
        menu.style.display = 'block';
        if (chevron) chevron.style.transform = 'rotate(180deg)';
        var input = document.getElementById('massMajorSearchInput');
        if (input) {
            setTimeout(function() { input.focus({ preventScroll: true }); }, 50);
        }
    } else {
        closeMassMajorDropdown();
    }
}

function closeMassMajorDropdown() {
    var menu = document.getElementById('massMajorDropdownMenu');
    var chevron = document.getElementById('massMajorChevron');
    if (menu) menu.style.display = 'none';
    if (chevron) chevron.style.transform = 'rotate(0deg)';
}

function selectMassMajorOption(value, text) {
    var hiddenSelect = document.getElementById('massFilterMajorSelect');
    var labelSpan = document.getElementById('massMajorSelectedText');
    if (hiddenSelect) {
        hiddenSelect.value = value;
    }
    if (labelSpan) {
        labelSpan.textContent = text;
    }
    closeMassMajorDropdown();
    filterMassStudents();
}

function filterMassMajorSearch() {
    var input = document.getElementById('massMajorSearchInput');
    var clearBtn = document.getElementById('btnClearMassMajorSearch');
    var notFound = document.getElementById('massMajorNotFoundMsg');
    if (!input) return;

    var query = input.value.trim().toLowerCase();
    if (clearBtn) clearBtn.style.display = query ? 'block' : 'none';

    var rows = document.querySelectorAll('#massMajorOptionList .mass-major-option-row');
    var anyMatch = false;

    rows.forEach(function(row) {
        var text = (row.getAttribute('data-search-text') || row.textContent).toLowerCase();
        if (!query || text.indexOf(query) !== -1) {
            row.style.display = '';
            anyMatch = true;
        } else {
            row.style.display = 'none';
        }
    });

    if (notFound) {
        notFound.style.display = anyMatch ? 'none' : 'block';
    }
}

function clearMassMajorSearch(e) {
    if (e) e.stopPropagation();
    var input = document.getElementById('massMajorSearchInput');
    if (input) {
        input.value = '';
        filterMassMajorSearch();
        input.focus();
    }
}

function handleMassMajorSearchKeyDown(e) {
    if (e.key === 'Escape') {
        closeMassMajorDropdown();
    } else if (e.key === 'Enter') {
        e.preventDefault();
        var firstVisible = document.querySelector('#massMajorOptionList .mass-major-option-row:not([style*="display: none"])');
        if (firstVisible) {
            firstVisible.click();
        }
    }
}

// Global click & Escape handling
document.addEventListener('click', function(e) {
    var classTrigger = document.getElementById('massClassDropdownTrigger');
    var classMenu = document.getElementById('massClassDropdownMenu');
    if (classMenu && classMenu.style.display === 'block') {
        if (!classMenu.contains(e.target) && !classTrigger.contains(e.target)) {
            closeMassClassDropdown();
        }
    }

    var majorTrigger = document.getElementById('massMajorDropdownTrigger');
    var majorMenu = document.getElementById('massMajorDropdownMenu');
    if (majorMenu && majorMenu.style.display === 'block') {
        if (!majorMenu.contains(e.target) && !majorTrigger.contains(e.target)) {
            closeMassMajorDropdown();
        }
    }

    var delTrigger = document.getElementById('deleteClassDropdownTrigger');
    var delMenu = document.getElementById('deleteClassDropdownMenu');
    if (delMenu && delMenu.style.display === 'block') {
        if (!delMenu.contains(e.target) && !delTrigger.contains(e.target)) {
            closeDeleteClassDropdown();
        }
    }
});

function openDeleteByClassModal() {
    var modal = document.getElementById('deleteByClassModal');
    var select = document.getElementById('deleteClassSelect');
    var labelSpan = document.getElementById('deleteClassSelectedText');
    var searchInput = document.getElementById('deleteClassSearchInput');
    if (modal) {
        if (select) {
            select.value = '';
        }
        if (labelSpan) {
            labelSpan.textContent = '-- Pilih Kelas yang Ingin Dihapus Siswanya --';
            labelSpan.style.color = 'var(--color-text-subtle)';
        }
        if (searchInput) {
            searchInput.value = '';
            filterDeleteClassSearch();
        }
        closeDeleteClassDropdown();
        updateDeleteClassSummary();
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeDeleteByClassModal() {
    var modal = document.getElementById('deleteByClassModal');
    if (modal) {
        closeDeleteClassDropdown();
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

function handleDeleteByClassBackdropClick(event) {
    if (event.target === document.getElementById('deleteByClassModal')) {
        closeDeleteByClassModal();
    }
}

function toggleDeleteClassDropdown(e) {
    if (e) e.stopPropagation();
    var menu = document.getElementById('deleteClassDropdownMenu');
    var chevron = document.getElementById('deleteClassChevron');
    if (!menu) return;
    if (menu.style.display === 'none' || menu.style.display === '') {
        menu.style.display = 'block';
        if (chevron) chevron.style.transform = 'rotate(180deg)';
        var input = document.getElementById('deleteClassSearchInput');
        if (input) {
            setTimeout(function() { input.focus({ preventScroll: true }); }, 50);
        }
    } else {
        closeDeleteClassDropdown();
    }
}

function closeDeleteClassDropdown() {
    var menu = document.getElementById('deleteClassDropdownMenu');
    var chevron = document.getElementById('deleteClassChevron');
    if (menu) menu.style.display = 'none';
    if (chevron) chevron.style.transform = 'rotate(0deg)';
}

function selectDeleteClassOption(value, text) {
    var select = document.getElementById('deleteClassSelect');
    var labelSpan = document.getElementById('deleteClassSelectedText');
    if (select) {
        select.value = value;
    }
    if (labelSpan) {
        labelSpan.textContent = text;
        labelSpan.style.color = value ? 'var(--color-text-main)' : 'var(--color-text-subtle)';
    }
    closeDeleteClassDropdown();
    updateDeleteClassSummary();
}

function filterDeleteClassSearch() {
    var input = document.getElementById('deleteClassSearchInput');
    var clearBtn = document.getElementById('btnClearDeleteClassSearch');
    var notFound = document.getElementById('deleteClassNotFoundMsg');
    if (!input) return;

    var query = input.value.trim().toLowerCase();
    if (clearBtn) clearBtn.style.display = query ? 'block' : 'none';

    var rows = document.querySelectorAll('#deleteClassOptionList .delete-class-option-row');
    var anyMatch = false;

    rows.forEach(function(row) {
        var text = (row.getAttribute('data-search-text') || row.textContent).toLowerCase();
        if (!query || text.indexOf(query) !== -1) {
            row.style.display = 'flex';
            anyMatch = true;
        } else {
            row.style.display = 'none';
        }
    });

    if (notFound) {
        notFound.style.display = anyMatch ? 'none' : 'block';
    }
}

function clearDeleteClassSearch(e) {
    if (e) e.stopPropagation();
    var input = document.getElementById('deleteClassSearchInput');
    if (input) {
        input.value = '';
        filterDeleteClassSearch();
        input.focus();
    }
}

function handleDeleteClassSearchKeyDown(e) {
    if (e.key === 'Escape') {
        closeDeleteClassDropdown();
    } else if (e.key === 'Enter') {
        e.preventDefault();
        var firstVisible = document.querySelector('#deleteClassOptionList .delete-class-option-row:not([style*="display: none"])');
        if (firstVisible) {
            firstVisible.click();
        }
    }
}

function updateDeleteClassSummary() {
    var select = document.getElementById('deleteClassSelect');
    var infoBox = document.getElementById('deleteClassInfoBox');
    var countSpan = document.getElementById('deleteClassCountText');
    var nameSpan = document.getElementById('deleteClassNameText');
    var submitBtn = document.getElementById('btnConfirmDeleteByClass');

    if (!select || !select.value) {
        if (infoBox) infoBox.style.display = 'none';
        if (submitBtn) submitBtn.disabled = true;
        return;
    }

    var selectedOpt = select.querySelector('option[value="' + select.value + '"]');
    var count = selectedOpt ? (selectedOpt.getAttribute('data-count') || '0') : '0';
    var name = selectedOpt ? (selectedOpt.getAttribute('data-name') || select.value) : select.value;

    if (countSpan) countSpan.textContent = count;
    if (nameSpan) nameSpan.textContent = name;
    if (infoBox) infoBox.style.display = 'block';
    if (submitBtn) submitBtn.disabled = false;
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeMassClassDropdown();
        closeMassMajorDropdown();
        closeDeleteClassDropdown();
        closeDeleteByClassModal();
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('tab') === 'mass') {
        switchEditTab('mass');
    }
    filterMassStudents();
});

function handleStudentEditPhotoSelect(input) {
    if (!input.files || !input.files[0]) return;
    window.AvatarCropper.openForFile(input.files[0], {
        uploadUrl: '{{ route('guru.siswa.photo.update', $student->id) }}',
        csrf: '{{ csrf_token() }}'
    });
    input.value = '';
}

function handleStudentEditPhotoDelete() {
    window.AvatarCropper.openDeleteModal({
        deleteUrl: '{{ route('guru.siswa.photo.destroy', $student->id) }}',
        csrf: '{{ csrf_token() }}'
    });
}
</script>
@endsection
