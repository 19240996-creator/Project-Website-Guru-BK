@extends('layouts.app')

@section('title', 'Data & Profil Siswa - Guru BK')
@section('header_title', 'Data & Administrasi Siswa')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
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
<div class="card" style="margin-bottom: 20px; overflow: visible; position: relative; z-index: 30;">
    <div class="card-body" style="padding: 16px 20px; overflow: visible;">
        <form action="{{ route('guru.siswa.index') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)) 100px; gap: 12px; align-items: flex-end;">
            <div>
                <label class="form-label" style="font-size: 12px; margin-bottom: 6px; display: block;">Cari Nama / NISN / NIS</label>
                <input type="text" name="q" class="form-control" style="min-height: 38px; font-size: 13px;" value="{{ request('q') }}" placeholder="Ketik kata kunci...">
            </div>

            <div>
                <label class="form-label" style="font-size: 12px; margin-bottom: 6px; display: block;">Kelas</label>
                @php
                    $selectedClassText = 'Semua Kelas';
                    if (request('class_id')) {
                        if (str_starts_with(request('class_id'), 'major:')) {
                            $selectedClassText = 'Semua Kelas ' . substr(request('class_id'), 6);
                        } else {
                            $foundClass = $classes->firstWhere('id', request('class_id'));
                            if ($foundClass) {
                                $selectedClassText = $foundClass->name;
                            }
                        }
                    }
                @endphp
                <div style="position: relative; width: 100%;">
                    <input type="hidden" name="class_id" id="filterClassInput" value="{{ request('class_id') }}">
                    <div id="filterClassTrigger" class="form-select custom-dropdown-trigger" tabindex="0" onclick="toggleFilterClassDropdown(event)" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();toggleFilterClassDropdown(event);}" style="width: 100%; min-height: 38px; padding: 8px 12px; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-size: 13px; text-align: left; background-color: var(--color-surface); color: var(--color-text-main); display: flex; justify-content: space-between; align-items: center; cursor: pointer; user-select: none;">
                        <span id="filterClassSelectedText" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $selectedClassText }}</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" id="filterClassChevron" style="transition: transform 0.2s ease; flex-shrink: 0; margin-left: 6px;"><path d="m6 9 6 6 6-6"/></svg>
                    </div>

                    <!-- Dropdown Menu dengan Sticky Search -->
                    <div id="filterClassMenu" style="display: none; position: absolute; top: calc(100% + 4px); left: 0; min-width: 260px; max-width: 360px; width: 100%; background: var(--color-surface, #ffffff); border: 1px solid var(--color-border); border-radius: var(--radius-sm); box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1); z-index: 100; max-height: 280px; overflow-y: auto;">
                        <!-- Sticky Search Box -->
                        <div style="padding: 8px 10px; border-bottom: 1px solid var(--color-border); background: var(--color-surface); position: sticky; top: 0; z-index: 10;">
                            <div style="position: relative; display: flex; align-items: center;">
                                <span style="position: absolute; left: 10px; color: var(--color-text-subtle); display: flex; align-items: center; pointer-events: none;">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                </span>
                                <input type="text" id="filterClassSearchInput" placeholder="Cari nama kelas / jurusan..." oninput="filterClassDropdownSearch()" onkeydown="handleFilterClassSearchKeyDown(event)" onclick="event.stopPropagation()" style="width: 100%; padding: 6px 28px 6px 30px; font-size: 12px; border: 1px solid var(--color-border); border-radius: var(--radius-sm); outline: none; background: var(--color-bg, #f8fafc); color: var(--color-text-main); font-family: inherit;">
                                <button type="button" id="btnClearFilterClassSearch" onclick="clearFilterClassSearch(event)" style="position: absolute; right: 6px; display: none; background: none; border: none; padding: 2px 4px; color: var(--color-text-subtle); cursor: pointer; border-radius: 4px;" title="Reset pencarian">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                </button>
                            </div>
                        </div>

                        <div id="filterClassOptionList">
                            <!-- Pilihan Semua Kelas -->
                            <div class="filter-class-option-row" data-search-text="semua kelas" onclick="selectFilterClassOption('', 'Semua Kelas')" style="padding: 8px 12px; cursor: pointer; font-size: 13px; font-weight: 600; color: var(--color-text-main); border-bottom: 1px solid var(--color-border); {{ !request('class_id') ? 'background: var(--color-surface-hover); color: var(--color-primary);' : '' }}" onmouseover="this.style.background='var(--color-surface-hover)'" onmouseout="this.style.background='{{ !request('class_id') ? 'var(--color-surface-hover)' : 'transparent' }}'">
                                Semua Kelas
                            </div>

                            @foreach($majors as $m)
                                @php
                                    $majorClasses = $classes->where('major', $m);
                                @endphp
                                <div class="filter-class-group-block" data-group-name="{{ strtolower($m) }}">
                                    <div style="padding: 6px 12px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--color-text-subtle); background: var(--color-surface-hover, #f1f5f9); border-bottom: 1px solid var(--color-border);">
                                        Jurusan: {{ $m }}
                                    </div>
                                    <div class="filter-class-option-row" data-search-text="{{ strtolower('semua kelas ' . $m) }}" onclick="selectFilterClassOption('major:{{ addslashes($m) }}', 'Semua Kelas {{ addslashes($m) }}')" style="padding: 8px 14px; cursor: pointer; font-size: 12.5px; font-weight: 600; color: var(--color-text-main); border-bottom: 1px solid var(--color-border); {{ request('class_id') === 'major:'.$m ? 'background: var(--color-surface-hover); color: var(--color-primary);' : '' }}" onmouseover="this.style.background='var(--color-surface-hover)'" onmouseout="this.style.background='{{ request('class_id') === 'major:'.$m ? 'var(--color-surface-hover)' : 'transparent' }}'">
                                        Semua Kelas {{ $m }}
                                    </div>
                                    @foreach($majorClasses as $c)
                                        <div class="filter-class-option-row" data-search-text="{{ strtolower($c->name . ' ' . $m) }}" onclick="selectFilterClassOption('{{ $c->id }}', '{{ addslashes($c->name) }}')" style="padding: 8px 18px; cursor: pointer; font-size: 12.5px; color: var(--color-text-main); border-bottom: 1px solid var(--color-border); display: flex; justify-content: space-between; align-items: center; {{ request('class_id') == $c->id ? 'background: var(--color-surface-hover); color: var(--color-primary); font-weight: 600;' : '' }}" onmouseover="this.style.background='var(--color-surface-hover)'" onmouseout="this.style.background='{{ request('class_id') == $c->id ? 'var(--color-surface-hover)' : 'transparent' }}'">
                                            <span>{{ $c->name }}</span>
                                            <span style="font-size: 11px; color: var(--color-text-subtle);">Kelas {{ $c->grade }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach

                            @php
                                $otherClasses = $classes->filter(fn($c) => empty($c->major) || !$majors->contains($c->major));
                            @endphp
                            @if($otherClasses->count() > 0)
                                <div class="filter-class-group-block" data-group-name="lainnya">
                                    <div style="padding: 6px 12px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--color-text-subtle); background: var(--color-surface-hover, #f1f5f9); border-bottom: 1px solid var(--color-border);">
                                        Lainnya
                                    </div>
                                    @foreach($otherClasses as $c)
                                        <div class="filter-class-option-row" data-search-text="{{ strtolower($c->name) }}" onclick="selectFilterClassOption('{{ $c->id }}', '{{ addslashes($c->name) }}')" style="padding: 8px 18px; cursor: pointer; font-size: 12.5px; color: var(--color-text-main); border-bottom: 1px solid var(--color-border); {{ request('class_id') == $c->id ? 'background: var(--color-surface-hover); color: var(--color-primary); font-weight: 600;' : '' }}" onmouseover="this.style.background='var(--color-surface-hover)'" onmouseout="this.style.background='{{ request('class_id') == $c->id ? 'var(--color-surface-hover)' : 'transparent' }}'">
                                            {{ $c->name }}
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div id="filterClassNotFoundMsg" style="display: none; padding: 14px 12px; text-align: center; font-size: 12px; color: var(--color-text-subtle);">
                            Tidak ada kelas atau jurusan yang cocok.
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <label class="form-label" style="font-size: 12px; margin-bottom: 6px; display: block;">Tingkat Perhatian</label>
                <select name="attention_level" class="form-select" style="min-height: 38px; font-size: 13px;">
                    <option value="">Semua Tingkat</option>
                    <option value="normal" {{ request('attention_level') == 'normal' ? 'selected' : '' }}>Normal</option>
                    <option value="perlu_perhatian" {{ request('attention_level') == 'perlu_perhatian' ? 'selected' : '' }}>Perlu Perhatian</option>
                    <option value="prioritas" {{ request('attention_level') == 'prioritas' ? 'selected' : '' }}>Prioritas</option>
                    <option value="segera_ditindaklanjuti" {{ request('attention_level') == 'segera_ditindaklanjuti' ? 'selected' : '' }}>Segera Ditindaklanjuti</option>
                </select>
            </div>

            <div>
                <label class="form-label" style="font-size: 12px; margin-bottom: 6px; display: block;">Status Siswa</label>
                <select name="status" class="form-select" style="min-height: 38px; font-size: 13px;">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="lulus" {{ request('status') == 'lulus' ? 'selected' : '' }}>Lulus</option>
                    <option value="pindah" {{ request('status') == 'pindah' ? 'selected' : '' }}>Pindah</option>
                </select>
            </div>

            <div>
                <label class="form-label" style="font-size: 12px; margin-bottom: 6px; display: block;">Baris per Halaman</label>
                <select name="per_page" class="form-select" style="min-height: 38px; font-size: 13px;" onchange="this.form.submit()">
                    <option value="5" {{ request('per_page', '5') == '5' ? 'selected' : '' }}>5 Siswa</option>
                    <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10 Siswa</option>
                    <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25 Siswa</option>
                    <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 Siswa</option>
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

            {{ $students->links() }}
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
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; flex-wrap: wrap; gap: 8px;">
                    <label class="form-label" style="font-weight: 600; font-size: 13px; margin: 0;">Jurusan / Program Keahlian</label>
                    <button type="button" id="btnToggleManualMajor" class="btn btn-secondary btn-sm" onclick="toggleAddMajorBox()" style="padding: 3px 10px; font-size: 12px; display: inline-flex; align-items: center; gap: 5px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span>+ Tambah Jurusan Baru</span>
                        <span style="font-size: 11px; opacity: 0.85;">(Klik untuk memasukkan jurusan manual)</span>
                    </button>
                </div>

                <div id="selectMajorWrapper">
                    <!-- Real form select (disinkronkan untuk kompatibilitas form & validasi) -->
                    <select name="major" id="majorSelect" style="display: none;" required>
                        <option value="">-- Pilih Jurusan yang Tersedia --</option>
                        @foreach($majors as $m)
                            <option value="{{ $m }}">{{ $m }}</option>
                        @endforeach
                    </select>

                    <!-- Custom Dropdown Container dengan tombol hapus (X) per baris -->
                    <div id="customMajorDropdownWrapper" style="position: relative; width: 100%;">
                        <div id="majorDropdownTrigger" class="form-select custom-dropdown-trigger" tabindex="0" onclick="toggleMajorDropdown(event)" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();toggleMajorDropdown(event);}" style="width: 100%; min-height: 40px; padding: 8px 12px; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-size: 13px; text-align: left; background-color: var(--color-surface); color: var(--color-text-main); display: flex; justify-content: space-between; align-items: center; cursor: pointer; user-select: none;">
                            <span id="majorSelectedText" style="color: var(--color-text-subtle);">-- Pilih Jurusan yang Tersedia --</span>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" id="majorDropdownChevron" style="transition: transform 0.2s ease;"><path d="m6 9 6 6 6-6"/></svg>
                        </div>

                        <!-- Menu Pilihan Dropdown -->
                        <div id="majorDropdownMenu" style="display: none; position: absolute; top: calc(100% + 4px); left: 0; right: 0; background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--radius-sm); box-shadow: var(--shadow-md); z-index: 60; max-height: 280px; overflow-y: auto;">
                            <!-- Sticky Search Box -->
                            <div style="padding: 8px 10px; border-bottom: 1px solid var(--color-border); background: var(--color-surface); position: sticky; top: 0; z-index: 10;">
                                <div style="position: relative; display: flex; align-items: center;">
                                    <span style="position: absolute; left: 10px; color: var(--color-text-subtle); display: flex; align-items: center; pointer-events: none;">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                    </span>
                                    <input type="text" id="majorSearchInput" placeholder="Cari nama jurusan / kelas..." oninput="filterMajorOptions()" onkeydown="handleMajorSearchKeyDown(event)" onclick="event.stopPropagation()" style="width: 100%; padding: 6px 28px 6px 30px; font-size: 12px; border: 1px solid var(--color-border); border-radius: var(--radius-sm); outline: none; background: var(--color-bg, #f8fafc); color: var(--color-text-main); font-family: inherit;">
                                    <button type="button" id="btnClearMajorSearch" onclick="clearMajorSearch(event)" style="position: absolute; right: 6px; display: none; background: none; border: none; padding: 2px 4px; color: var(--color-text-subtle); cursor: pointer; border-radius: 4px;" title="Reset pencarian">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                    </button>
                                </div>
                            </div>

                            <div id="majorOptionList">
                                <div class="major-option-row" onclick="selectMajorOption('')" style="padding: 9px 12px; cursor: pointer; font-size: 13px; color: var(--color-text-subtle); border-bottom: 1px solid var(--color-border); transition: background 0.15s ease;" onmouseover="this.style.background='var(--color-surface-hover)'" onmouseout="this.style.background='transparent'">
                                    <em>-- Pilih Jurusan yang Tersedia --</em>
                                </div>
                                @foreach($majors as $m)
                                    <div class="major-option-row" data-major="{{ $m }}" onclick="selectMajorOption('{{ addslashes($m) }}')" style="display: flex; justify-content: space-between; align-items: center; padding: 9px 12px; cursor: pointer; font-size: 13px; color: var(--color-text-main); border-bottom: 1px solid var(--color-border); transition: background 0.15s ease;" onmouseover="this.style.background='var(--color-surface-hover)'" onmouseout="this.style.background='transparent'">
                                        <span class="major-name" style="flex: 1; font-weight: 500;">{{ $m }}</span>
                                        <button type="button" class="btn-delete-major-item" title="Hapus jurusan {{ $m }}" aria-label="Hapus jurusan {{ $m }}" onclick="promptDeleteMajor(event, '{{ addslashes($m) }}')" style="background: none; border: none; padding: 4px 6px; border-radius: 4px; cursor: pointer; color: var(--color-text-subtle); display: inline-flex; align-items: center; justify-content: center; transition: all 0.15s ease;" onmouseover="this.style.color='var(--color-danger)'; this.style.background='var(--color-danger-bg)';" onmouseout="this.style.color='var(--color-text-subtle)'; this.style.background='transparent';">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Pesan jika pencarian tidak ditemukan -->
                            <div id="majorNotFoundMsg" style="display: none; padding: 14px 12px; text-align: center; font-size: 12px; color: var(--color-text-subtle);">
                                <div>Tidak ada jurusan/kelas yang cocok.</div>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="useQueryAsNewMajor()" style="margin-top: 8px; font-size: 11px; padding: 3px 10px;">
                                    + Tambahkan sebagai jurusan baru
                                </button>
                            </div>
                        </div>
                    </div>
                    <small style="color: var(--color-text-subtle); font-size: 11px; margin-top: 4px; display: block;">
                        Ketik pada kolom pencarian di menu untuk menemukan cepat, atau klik tanda <strong>✕</strong> untuk menghapus.
                    </small>
                </div>

                <!-- Input Box Tambah Jurusan Baru (Antislop Compliant) -->
                <div id="newMajorBox" style="display: none; margin-top: 10px; padding: 12px 14px; background: var(--color-surface-hover, #f8fafc); border: 1px solid var(--color-border); border-radius: var(--radius-sm);">
                    <label for="customMajorInput" style="display: block; font-size: 12px; font-weight: 600; color: var(--color-text-main); margin-bottom: 6px;">
                        Input Nama Jurusan Baru:
                    </label>
                    <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                        <input type="text" name="custom_major" id="customMajorInput" class="form-control" placeholder="Ketik nama jurusan baru (misal: Desain Komunikasi Visual)..." style="flex: 1; min-width: 220px; padding: 7px 12px; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-size: 13px;" onkeydown="handleNewMajorKeyDown(event)">
                        <button type="button" id="btnSubmitNewMajor" class="btn btn-primary btn-sm" onclick="saveAndSelectNewMajor()" style="padding: 7px 14px; font-size: 12px; font-weight: 600;">
                            Oke, Masukkan ke Pilihan
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="toggleAddMajorBox()" style="padding: 7px 10px; font-size: 12px;">
                            Batal
                        </button>
                    </div>
                    <div id="newMajorFeedback" style="display: none; margin-top: 8px; font-size: 12px; font-weight: 500;"></div>
                    <small style="color: var(--color-text-subtle); font-size: 11px; margin-top: 6px; display: block;">
                        Setelah klik 'Oke', jurusan baru akan otomatis masuk ke daftar pilihan di atas dan langsung terpilih untuk seterusnya.
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
            var isAddMajorBoxOpen = false;
            var majorToDelete = null;

            function toggleMajorDropdown(e) {
                if (e) e.stopPropagation();
                var menu = document.getElementById('majorDropdownMenu');
                var chevron = document.getElementById('majorDropdownChevron');
                var searchInput = document.getElementById('majorSearchInput');
                if (!menu) return;
                if (menu.style.display === 'none' || menu.style.display === '') {
                    menu.style.display = 'block';
                    if (chevron) chevron.style.transform = 'rotate(180deg)';
                    if (searchInput) {
                        setTimeout(function() { searchInput.focus(); }, 60);
                    }
                } else {
                    closeMajorDropdown();
                }
            }

            function closeMajorDropdown() {
                var menu = document.getElementById('majorDropdownMenu');
                var chevron = document.getElementById('majorDropdownChevron');
                if (menu) menu.style.display = 'none';
                if (chevron) chevron.style.transform = 'rotate(0deg)';
                var searchInput = document.getElementById('majorSearchInput');
                if (searchInput && searchInput.value) {
                    searchInput.value = '';
                    filterMajorOptions();
                }
            }

            function filterMajorOptions() {
                var searchInput = document.getElementById('majorSearchInput');
                var query = searchInput ? searchInput.value.toLowerCase().trim() : '';
                var clearBtn = document.getElementById('btnClearMajorSearch');
                var notFoundMsg = document.getElementById('majorNotFoundMsg');
                var rows = document.querySelectorAll('#majorOptionList .major-option-row');
                var visibleCount = 0;

                if (clearBtn) {
                    clearBtn.style.display = query ? 'block' : 'none';
                }

                rows.forEach(function(row) {
                    var major = row.getAttribute('data-major');
                    if (!major) {
                        row.style.display = query ? 'none' : 'block';
                        return;
                    }
                    if (major.toLowerCase().includes(query)) {
                        row.style.display = 'flex';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                if (notFoundMsg) {
                    notFoundMsg.style.display = (query && visibleCount === 0) ? 'block' : 'none';
                }
            }

            function clearMajorSearch(e) {
                if (e) e.stopPropagation();
                var searchInput = document.getElementById('majorSearchInput');
                if (searchInput) {
                    searchInput.value = '';
                    searchInput.focus();
                }
                filterMajorOptions();
            }

            function handleMajorSearchKeyDown(event) {
                if (event.key === 'Escape') {
                    closeMajorDropdown();
                } else if (event.key === 'Enter') {
                    event.preventDefault();
                    var visibleRows = Array.from(document.querySelectorAll('#majorOptionList .major-option-row')).filter(function(r) {
                        return r.style.display !== 'none' && r.getAttribute('data-major');
                    });
                    if (visibleRows.length === 1) {
                        var majorVal = visibleRows[0].getAttribute('data-major');
                        selectMajorOption(majorVal);
                    }
                }
            }

            function useQueryAsNewMajor() {
                var searchInput = document.getElementById('majorSearchInput');
                var query = searchInput ? searchInput.value.trim() : '';
                closeMajorDropdown();
                toggleAddMajorBox();
                var customInput = document.getElementById('customMajorInput');
                if (customInput && query) {
                    customInput.value = query;
                    customInput.focus();
                }
            }

            function selectMajorOption(val) {
                var select = document.getElementById('majorSelect');
                var textSpan = document.getElementById('majorSelectedText');
                if (select) select.value = val;
                if (textSpan) {
                    if (val) {
                        textSpan.textContent = val;
                        textSpan.style.color = 'var(--color-text-main)';
                        textSpan.style.fontWeight = '600';
                    } else {
                        textSpan.textContent = '-- Pilih Jurusan yang Tersedia --';
                        textSpan.style.color = 'var(--color-text-subtle)';
                        textSpan.style.fontWeight = 'normal';
                    }
                }
                closeMajorDropdown();
            }

            function promptDeleteMajor(e, majorName) {
                if (e) e.stopPropagation();
                majorToDelete = majorName;
                var modal = document.getElementById('deleteMajorModal');
                var nameSpan = document.getElementById('deleteMajorNameText');
                var errAlert = document.getElementById('deleteMajorErrorAlert');
                var btn = document.getElementById('btnConfirmDeleteMajor');

                if (errAlert) errAlert.style.display = 'none';
                if (nameSpan) nameSpan.textContent = majorName;
                if (btn) {
                    btn.disabled = false;
                    btn.textContent = 'Ya, Hapus Pilihan';
                }

                if (modal) {
                    modal.classList.add('active');
                    document.body.style.overflow = 'hidden';
                }
                closeMajorDropdown();
            }

            function closeDeleteMajorModal() {
                var modal = document.getElementById('deleteMajorModal');
                if (modal) {
                    modal.classList.remove('active');
                    document.body.style.overflow = '';
                }
                majorToDelete = null;
            }

            function handleDeleteMajorBackdropClick(e) {
                if (e.target === document.getElementById('deleteMajorModal')) {
                    closeDeleteMajorModal();
                }
            }

            function executeDeleteMajor() {
                if (!majorToDelete) return;
                var btn = document.getElementById('btnConfirmDeleteMajor');
                var errAlert = document.getElementById('deleteMajorErrorAlert');
                btn.disabled = true;
                btn.textContent = 'Menghapus...';

                fetch('{{ route("guru.siswa.destroy_major") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ major: majorToDelete })
                })
                .then(function(res) {
                    return res.json().then(function(data) {
                        return { ok: res.ok, status: res.status, data: data };
                    });
                })
                .then(function(result) {
                    if (!result.ok || !result.data.success) {
                        btn.disabled = false;
                        btn.textContent = 'Ya, Hapus Pilihan';
                        if (errAlert) {
                            errAlert.style.display = 'block';
                            errAlert.textContent = result.data.message || 'Gagal menghapus jurusan.';
                        }
                        return;
                    }

                    // Hapus elemen baris dari dropdown
                    var rows = document.querySelectorAll('#majorOptionList .major-option-row');
                    rows.forEach(function(r) {
                        if (r.getAttribute('data-major') === majorToDelete) {
                            r.remove();
                        }
                    });

                    // Hapus opsi dari select hidden
                    var select = document.getElementById('majorSelect');
                    if (select) {
                        for (var i = 0; i < select.options.length; i++) {
                            if (select.options[i].value === majorToDelete) {
                                select.remove(i);
                                break;
                            }
                        }
                        if (select.value === majorToDelete || !select.value) {
                            selectMajorOption('');
                        }
                    }

                    closeDeleteMajorModal();
                })
                .catch(function(err) {
                    btn.disabled = false;
                    btn.textContent = 'Ya, Hapus Pilihan';
                    if (errAlert) {
                        errAlert.style.display = 'block';
                        errAlert.textContent = 'Terjadi kendala jaringan saat menghubungi server.';
                    }
                });
            }

            function toggleAddMajorBox() {
                isAddMajorBoxOpen = !isAddMajorBoxOpen;
                var box = document.getElementById('newMajorBox');
                var input = document.getElementById('customMajorInput');
                var feedback = document.getElementById('newMajorFeedback');

                if (isAddMajorBoxOpen) {
                    box.style.display = 'block';
                    feedback.style.display = 'none';
                    input.focus();
                } else {
                    box.style.display = 'none';
                }
            }

            function handleNewMajorKeyDown(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    saveAndSelectNewMajor();
                } else if (event.key === 'Escape') {
                    toggleAddMajorBox();
                }
            }

            function saveAndSelectNewMajor() {
                var input = document.getElementById('customMajorInput');
                var val = input.value.trim();
                var feedback = document.getElementById('newMajorFeedback');
                var btn = document.getElementById('btnSubmitNewMajor');
                var select = document.getElementById('majorSelect');
                var gradeSelect = document.querySelector('select[name="grade"]');

                if (!val) {
                    feedback.style.display = 'block';
                    feedback.style.color = 'var(--color-danger, #b91c1c)';
                    feedback.textContent = 'Harap ketik nama jurusan terlebih dahulu.';
                    input.focus();
                    return;
                }

                btn.disabled = true;
                btn.textContent = 'Menyimpan...';

                // Simpan jurusan ke database via endpoint agar tersimpan permanen
                fetch('{{ route("guru.siswa.store_major") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        major: val,
                        grade: gradeSelect ? gradeSelect.value : 'X'
                    })
                })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    btn.disabled = false;
                    btn.textContent = 'Oke, Masukkan ke Pilihan';

                    // Cek apakah opsi sudah ada di select
                    var exists = false;
                    for (var i = 0; i < select.options.length; i++) {
                        if (select.options[i].value.toLowerCase() === val.toLowerCase()) {
                            select.selectedIndex = i;
                            exists = true;
                            break;
                        }
                    }

                    if (!exists) {
                        var opt = document.createElement('option');
                        opt.value = val;
                        opt.textContent = val;
                        opt.selected = true;
                        select.appendChild(opt);

                        // Tambahkan juga ke custom dropdown
                        var optionList = document.getElementById('majorOptionList');
                        if (optionList) {
                            var newRow = document.createElement('div');
                            newRow.className = 'major-option-row';
                            newRow.setAttribute('data-major', val);
                            newRow.style.cssText = 'display: flex; justify-content: space-between; align-items: center; padding: 9px 12px; cursor: pointer; font-size: 13px; color: var(--color-text-main); border-bottom: 1px solid var(--color-border); transition: background 0.15s ease;';
                            newRow.onmouseover = function() { this.style.background = 'var(--color-surface-hover)'; };
                            newRow.onmouseout = function() { this.style.background = 'transparent'; };

                            var valEsc = val.replace(/'/g, "\\'");
                            newRow.innerHTML = '<span class="major-name" style="flex: 1; font-weight: 500;">' + val + '</span>' +
                                '<button type="button" class="btn-delete-major-item" title="Hapus jurusan ' + val + '" aria-label="Hapus jurusan ' + val + '" onclick="promptDeleteMajor(event, \'' + valEsc + '\')" style="background: none; border: none; padding: 4px 6px; border-radius: 4px; cursor: pointer; color: var(--color-text-subtle); display: inline-flex; align-items: center; justify-content: center; transition: all 0.15s ease;" onmouseover="this.style.color=\'var(--color-danger)\'; this.style.background=\'var(--color-danger-bg)\';" onmouseout="this.style.color=\'var(--color-text-subtle)\'; this.style.background=\'transparent\';">' +
                                '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>' +
                                '</button>';

                            newRow.onclick = function() { selectMajorOption(val); };
                            optionList.appendChild(newRow);
                        }
                    }

                    selectMajorOption(val);

                    feedback.style.display = 'block';
                    feedback.style.color = 'var(--color-success, #047857)';
                    feedback.textContent = '✓ Jurusan "' + val + '" berhasil ditambahkan ke pilihan dan langsung terpilih!';

                    setTimeout(function() {
                        toggleAddMajorBox();
                    }, 1000);
                })
                .catch(function(err) {
                    btn.disabled = false;
                    btn.textContent = 'Oke, Masukkan ke Pilihan';

                    var opt = document.createElement('option');
                    opt.value = val;
                    opt.textContent = val;
                    opt.selected = true;
                    select.appendChild(opt);
                    selectMajorOption(val);

                    toggleAddMajorBox();
                });
            }

            // Klik di luar dropdown untuk menutup menu
            window.addEventListener('click', function(e) {
                var wrapper = document.getElementById('customMajorDropdownWrapper');
                if (wrapper && !wrapper.contains(e.target)) {
                    closeMajorDropdown();
                }
            });

            // Kompatibilitas fungsi jika dipanggil
            function toggleManualMajor() {
                toggleAddMajorBox();
            }

            @if($majors->isEmpty())
                toggleAddMajorBox();
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

<!-- Modal Konfirmasi Hapus Pilihan Jurusan / Kelas (Antislop Compliant) -->
<div id="deleteMajorModal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="deleteMajorModalTitle" onclick="handleDeleteMajorBackdropClick(event)">
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
            <h3 id="deleteMajorModalTitle" class="modal-title">Konfirmasi Hapus Pilihan Jurusan</h3>
            <p class="modal-desc">
                Apakah Anda yakin ingin menghapus pilihan jurusan <strong id="deleteMajorNameText" style="color: var(--color-text-main);"></strong> dari daftar sistem?
            </p>
            <div id="deleteMajorErrorAlert" style="display: none; background-color: var(--color-danger-bg); border: 1px solid var(--color-danger-border); border-radius: var(--radius-sm); padding: 10px 14px; margin-top: 14px; font-size: 12px; color: var(--color-danger); line-height: 1.45;"></div>
            <div id="deleteMajorNoticeBox" style="background-color: var(--color-surface-hover); border: 1px solid var(--color-border); border-radius: var(--radius-sm); padding: 10px 14px; margin-top: 14px; font-size: 12px; color: var(--color-text-muted); display: flex; align-items: flex-start; gap: 8px; line-height: 1.45;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0; margin-top: 2px;">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <span>Pilihan ini akan dihapus dari seluruh dropdown jurusan yang tersedia. Kelas yang sudah memiliki data siswa terdaftar tidak dapat dihapus.</span>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeDeleteMajorModal()">
                Batal
            </button>
            <button type="button" id="btnConfirmDeleteMajor" class="btn btn-danger" onclick="executeDeleteMajor()">
                Ya, Hapus Pilihan
            </button>
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

    // Logic Searchable Dropdown Filter Kelas di Header
    function toggleFilterClassDropdown(e) {
        if (e) e.stopPropagation();
        var menu = document.getElementById('filterClassMenu');
        var chevron = document.getElementById('filterClassChevron');
        if (!menu) return;
        var isOpen = menu.style.display === 'block';
        if (isOpen) {
            closeFilterClassDropdown();
        } else {
            menu.style.display = 'block';
            if (chevron) chevron.style.transform = 'rotate(180deg)';
            var input = document.getElementById('filterClassSearchInput');
            if (input) {
                setTimeout(function() {
                    input.focus({ preventScroll: true });
                }, 50);
            }
        }
    }

    function closeFilterClassDropdown() {
        var menu = document.getElementById('filterClassMenu');
        var chevron = document.getElementById('filterClassChevron');
        if (menu) menu.style.display = 'none';
        if (chevron) chevron.style.transform = 'rotate(0deg)';
    }

    function selectFilterClassOption(value, text) {
        var hiddenInput = document.getElementById('filterClassInput');
        var labelSpan = document.getElementById('filterClassSelectedText');
        if (hiddenInput) hiddenInput.value = value;
        if (labelSpan) labelSpan.textContent = text;
        closeFilterClassDropdown();
    }

    function filterClassDropdownSearch() {
        var input = document.getElementById('filterClassSearchInput');
        var clearBtn = document.getElementById('btnClearFilterClassSearch');
        var notFound = document.getElementById('filterClassNotFoundMsg');
        if (!input) return;

        var query = input.value.trim().toLowerCase();
        if (clearBtn) clearBtn.style.display = query ? 'block' : 'none';

        var rows = document.querySelectorAll('#filterClassOptionList .filter-class-option-row');
        var groups = document.querySelectorAll('#filterClassOptionList .filter-class-group-block');
        var anyMatch = false;

        rows.forEach(function(row) {
            var text = row.getAttribute('data-search-text') || '';
            if (!query || text.indexOf(query) !== -1) {
                row.style.display = '';
                anyMatch = true;
            } else {
                row.style.display = 'none';
            }
        });

        groups.forEach(function(group) {
            var visibleChildren = group.querySelectorAll('.filter-class-option-row:not([style*="display: none"])');
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

    function clearFilterClassSearch(e) {
        if (e) e.stopPropagation();
        var input = document.getElementById('filterClassSearchInput');
        if (input) {
            input.value = '';
            filterClassDropdownSearch();
            input.focus();
        }
    }

    function handleFilterClassSearchKeyDown(e) {
        if (e.key === 'Escape') {
            closeFilterClassDropdown();
        } else if (e.key === 'Enter') {
            e.preventDefault();
            var firstVisible = document.querySelector('#filterClassOptionList .filter-class-option-row:not([style*="display: none"])');
            if (firstVisible) {
                firstVisible.click();
            }
        }
    }

    document.addEventListener('click', function(e) {
        var trigger = document.getElementById('filterClassTrigger');
        var menu = document.getElementById('filterClassMenu');
        if (menu && menu.style.display === 'block') {
            if (!menu.contains(e.target) && !trigger.contains(e.target)) {
                closeFilterClassDropdown();
            }
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDeleteStudentModal();
            closeDeleteMajorModal();
            closeMajorDropdown();
            closeFilterClassDropdown();
        }
    });
</script>
@endsection
