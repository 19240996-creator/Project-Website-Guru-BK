@extends('layouts.app')

@section('title', 'Asesmen Siswa - Guru BK')
@section('header_title', 'Asesmen Minat, Bakat & Kepribadian')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
    <div>
        <h2 style="font-size: 20px; font-weight: 800; color: var(--color-text-main);">Asesmen Bimbingan & Pemetaan Diri</h2>
        <p style="font-size: 13px; color: var(--color-text-muted);">
            Instrumen asesmen untuk mengenali gaya belajar, tipologi minat karier, dan potensi masa depan siswa.
        </p>
    </div>
    <div>
        <button type="button" class="btn btn-primary" onclick="openCreateAssessmentModal()" style="display: inline-flex; align-items: center; gap: 6px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <span>+ Buat Asesmen Baru</span>
        </button>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success" style="margin-bottom: 20px; padding: 12px 16px; background: var(--color-success-bg, #ecfdf5); border: 1px solid var(--color-success-border, #a7f3d0); border-radius: var(--radius-sm); color: var(--color-success, #047857); font-size: 13px; font-weight: 500;">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger" style="margin-bottom: 20px; padding: 12px 16px; background: var(--color-danger-bg, #fef2f2); border: 1px solid var(--color-danger-border, #fecaca); border-radius: var(--radius-sm); color: var(--color-danger, #b91c1c); font-size: 13px; font-weight: 500;">
        {{ session('error') }}
    </div>
@endif

<!-- Daftar Instrumen Asesmen Aktif -->
<div class="grid-2" style="margin-bottom: 28px;">
    @foreach($assessments as $asm)
        <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div class="card-header card-header-navy">
                    <div style="flex: 1; padding-right: 10px;">
                        <h3 class="card-title">{{ $asm->title }}</h3>
                        <small style="color: #bfdbfe;">Kategori: {{ $asm->category }}</small>
                    </div>
                    <span class="badge badge-translucent">Aktif</span>
                </div>
                <div class="card-body">
                    <p style="font-size: 13px; color: var(--color-text-muted); line-height: 1.6; margin-bottom: 16px;">
                        {{ $asm->description }}
                    </p>

                    <div style="display: flex; gap: 20px; font-size: 13px; color: var(--color-text-main); margin-bottom: 20px;">
                        <div>
                            <span style="color: var(--color-text-subtle);">Total Soal:</span>
                            <strong>{{ $asm->questions_count }} Butir</strong>
                        </div>
                        <div>
                            <span style="color: var(--color-text-subtle);">Siswa Mengerjakan:</span>
                            <strong>{{ $asm->student_results_count }} Siswa</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div style="padding: 0 20px 20px 20px; display: flex; gap: 8px;">
                <a href="{{ route('guru.asesmen.show', $asm->id) }}" class="btn btn-secondary" style="flex: 1; text-align: center; font-size: 13px;">
                    Buka Butir Pertanyaan & Hasil Siswa
                </a>
                @if($asm->student_results_count === 0)
                    <button type="button" class="btn btn-secondary" onclick="openDeleteAssessmentModal({{ $asm->id }}, '{{ addslashes($asm->title) }}')" title="Hapus instrumen asesmen ini" style="color: var(--color-danger); padding: 8px 12px;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                    </button>
                @endif
            </div>
        </div>
    @endforeach
</div>

<!-- Filter Bar (Sesuai dengan layout horizontal di Daftar Siswa Bimbingan) -->
<div class="card" style="margin-bottom: 20px; overflow: visible; position: relative; z-index: 30;">
    <div class="card-body" style="padding: 16px 20px; overflow: visible;">
        <form action="{{ route('guru.asesmen.index') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)) 100px; gap: 12px; align-items: flex-end;">
            <div>
                <label class="form-label" style="font-size: 12px; margin-bottom: 6px; display: block;">Cari Siswa / NISN / Kelas</label>
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
                        <div style="padding: 8px 10px; border-bottom: 1px solid var(--color-border); background: var(--color-surface, #ffffff); position: sticky; top: 0; z-index: 10;">
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
                <label class="form-label" style="font-size: 12px; margin-bottom: 6px; display: block;">Instrumen Asesmen</label>
                <select name="assessment_id" class="form-select" style="min-height: 38px; font-size: 13px;">
                    <option value="">Semua Asesmen</option>
                    @foreach($assessments as $asm)
                        <option value="{{ $asm->id }}" {{ request('assessment_id') == $asm->id ? 'selected' : '' }}>
                            {{ $asm->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label" style="font-size: 12px; margin-bottom: 6px; display: block;">Status Publikasi</label>
                <select name="status" class="form-select" style="min-height: 38px; font-size: 13px;">
                    <option value="">Semua Status</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Terbuka Untuk Siswa</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draf BK</option>
                </select>
            </div>

            <div>
                <label class="form-label" style="font-size: 12px; margin-bottom: 6px; display: block;">Baris per Halaman</label>
                <select name="per_page" class="form-select" style="min-height: 38px; font-size: 13px;" onchange="this.form.submit()">
                    <option value="5" {{ request('per_page') == '5' ? 'selected' : '' }}>5 Hasil</option>
                    <option value="10" {{ request('per_page', '10') == '10' ? 'selected' : '' }}>10 Hasil</option>
                    <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25 Hasil</option>
                    <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 Hasil</option>
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

<!-- Hasil Pengerjaan Asesmen Siswa Terbaru -->
<div class="card">
    <div class="card-header card-header-navy" style="display: flex; justify-content: space-between; align-items: center;">
        <h3 class="card-title" style="margin: 0;">Hasil Pengerjaan Asesmen Siswa Terbaru (Total: {{ $recentResults->total() }})</h3>
        @if(request()->hasAny(['q', 'class_id', 'assessment_id', 'status']))
            <a href="{{ route('guru.asesmen.index') }}" class="btn btn-secondary btn-sm" style="font-size: 11px; padding: 4px 10px; color: #ffffff; border-color: rgba(255,255,255,0.3); background: rgba(255,255,255,0.1);" title="Reset semua filter">
                ✕ Reset Filter
            </a>
        @endif
    </div>

    <!-- Tabel Hasil Pengerjaan -->
    <div class="card-body" style="padding: 0;">
        @if($recentResults->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead class="table-thead-navy">
                        <tr>
                            <th>Siswa</th>
                            <th>Kelas</th>
                            <th>Asesmen</th>
                            <th>Kategori Hasil</th>
                            <th>Status Publikasi</th>
                            <th>Tanggal Pengerjaan</th>
                            <th style="text-align: center;">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentResults as $r)
                            <tr>
                                <td>
                                    <strong>{{ $r->student ? $r->student->name : '-' }}</strong>
                                    <div style="font-size: 11px; color: var(--color-text-subtle);">
                                        NISN: {{ $r->student ? $r->student->nisn : '-' }}
                                    </div>
                                </td>
                                <td>{{ $r->student && $r->student->studentClass ? $r->student->studentClass->name : '-' }}</td>
                                <td>{{ $r->assessment ? $r->assessment->title : '-' }}</td>
                                <td>
                                    <span class="badge badge-primary">{{ $r->result_category }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $r->is_published ? 'success' : 'secondary' }}">
                                        {{ $r->is_published ? 'Terbuka Untuk Siswa' : 'Draf BK' }}
                                    </span>
                                </td>
                                <td>{{ $r->created_at->translatedFormat('d M Y, H:i') }}</td>
                                <td style="text-align: center;">
                                    <a href="{{ route('guru.asesmen.result', $r->id) }}" class="btn btn-secondary btn-sm" style="font-size: 11px; padding: 4px 10px;">
                                        Lihat Analisis
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar -->
            <div style="padding: 16px 20px; border-top: 1px solid var(--color-border); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                <div style="font-size: 13px; color: var(--color-text-subtle);">
                    Menampilkan {{ $recentResults->firstItem() ?? 0 }} sampai {{ $recentResults->lastItem() ?? 0 }} dari {{ $recentResults->total() }} hasil pengerjaan siswa
                </div>
                <div>
                    {{ $recentResults->links() }}
                </div>
            </div>
        @else
            <div class="empty-state" style="padding: 40px 20px; text-align: center;">
                <p class="empty-state-title" style="font-weight: 700; color: var(--color-text-main); font-size: 15px; margin-bottom: 6px;">
                    Tidak Ada Hasil Pengerjaan Asesmen
                </p>
                <p class="empty-state-desc" style="font-size: 13px; color: var(--color-text-muted);">
                    @if(request()->hasAny(['q', 'class_id', 'assessment_id', 'status']))
                        Tidak ada data yang sesuai dengan filter pencarian Anda. Silakan coba kata kunci lain atau <a href="{{ route('guru.asesmen.index') }}" style="color: var(--color-primary); text-decoration: underline;">reset filter</a>.
                    @else
                        Belum ada siswa yang mengisi kuesioner asesmen.
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>

<!-- Modal Buat Asesmen Baru (Antislop Compliant) -->
<div id="createAssessmentModal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="createAssessmentModalTitle" onclick="handleCreateAssessmentBackdrop(event)">
    <div class="modal-dialog" style="max-width: 560px;">
        <form action="{{ route('guru.asesmen.store') }}" method="POST" style="margin: 0;">
            @csrf
            <div class="modal-body" style="padding: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                    <div>
                        <h3 id="createAssessmentModalTitle" class="modal-title" style="font-size: 17px; font-weight: 800; color: var(--color-text-main); margin-bottom: 4px;">
                            Buat Instrumen Asesmen Baru
                        </h3>
                        <p style="font-size: 12px; color: var(--color-text-subtle); margin: 0;">
                            Tambahkan kuesioner minat, bakat, kepribadian, atau modalitas belajar baru untuk siswa.
                        </p>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="closeCreateAssessmentModal()" style="padding: 4px 8px; font-size: 12px;">
                        ✕
                    </button>
                </div>

                <div class="form-group" style="margin-bottom: 14px;">
                    <label class="form-label" style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px;">
                        Judul Instrumen Asesmen <span style="color: var(--color-danger);">*</span>
                    </label>
                    <input type="text" name="title" class="form-control" placeholder="Contoh: Asesmen Gaya Belajar VAK (Visual, Auditori, Kinestetik)" required style="width: 100%; padding: 8px 12px; font-size: 13px; border: 1px solid var(--color-border); border-radius: var(--radius-sm);">
                </div>

                <div class="form-group" style="margin-bottom: 14px;">
                    <label class="form-label" style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px;">
                        Kategori Asesmen <span style="color: var(--color-danger);">*</span>
                    </label>
                    <select name="category" class="form-select" required style="width: 100%; padding: 8px 12px; font-size: 13px; border: 1px solid var(--color-border); border-radius: var(--radius-sm);">
                        <option value="Minat Karier">Minat Karier (Holland RIASEC / Jalur Studi)</option>
                        <option value="Gaya Belajar">Gaya Belajar (Visual, Auditori, Kinestetik)</option>
                        <option value="Kepribadian">Kepribadian & Karakteristik Siswa</option>
                        <option value="Kesiapan Kerja">Kesiapan Kerja & Mentalitas Industri</option>
                        <option value="Penjajakan Peminatan">Penjajakan Peminatan Jurusan</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 14px;">
                    <label class="form-label" style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px;">
                        Deskripsi Instrumen <span style="color: var(--color-danger);">*</span>
                    </label>
                    <textarea name="description" rows="3" class="form-control" placeholder="Jelaskan tujuan dan ruang lingkup instrumen asesmen ini..." required style="width: 100%; padding: 8px 12px; font-size: 13px; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-family: inherit;"></textarea>
                </div>

                <div class="form-group" style="margin-bottom: 14px;">
                    <label class="form-label" style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px;">
                        Petunjuk Pengerjaan untuk Siswa
                    </label>
                    <textarea name="instructions" rows="2" class="form-control" placeholder="Petunjuk pengerjaan bagi siswa..." style="width: 100%; padding: 8px 12px; font-size: 13px; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-family: inherit;">Pilihlah salah satu jawaban yang paling mencerminkan kondisi dan kecenderungan diri Anda secara jujur.</textarea>
                </div>
            </div>

            <div class="modal-footer" style="padding: 14px 24px; border-top: 1px solid var(--color-border); display: flex; justify-content: flex-end; gap: 8px;">
                <button type="button" class="btn btn-secondary" onclick="closeCreateAssessmentModal()">
                    Batal
                </button>
                <button type="submit" class="btn btn-primary">
                    Simpan & Lanjut Kelola Soal
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Konfirmasi Hapus Asesmen (Antislop Compliant) -->
<div id="deleteAssessmentModal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="deleteAssessmentModalTitle" onclick="handleDeleteAssessmentBackdrop(event)">
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
            <h3 id="deleteAssessmentModalTitle" class="modal-title">Konfirmasi Hapus Asesmen</h3>
            <p class="modal-desc">
                Apakah Anda yakin ingin menghapus instrumen asesmen <strong id="deleteAssessmentTitleText" style="color: var(--color-text-main);"></strong>?
            </p>
            <div style="background-color: var(--color-danger-bg); border: 1px solid var(--color-danger-border); border-radius: var(--radius-sm); padding: 10px 14px; margin-top: 14px; font-size: 12px; color: var(--color-danger); line-height: 1.45;">
                Perhatian: Seluruh butir pertanyaan dan opsi pilihan di dalam instrumen ini akan dihapus secara permanen.
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeDeleteAssessmentModal()">
                Batal
            </button>
            <form id="deleteAssessmentForm" method="POST" action="" style="display: inline; margin: 0;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    Ya, Hapus Asesmen
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function openCreateAssessmentModal() {
        var modal = document.getElementById('createAssessmentModal');
        if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
            var titleInput = modal.querySelector('input[name="title"]');
            if (titleInput) {
                setTimeout(function() { titleInput.focus(); }, 100);
            }
        }
    }

    function closeCreateAssessmentModal() {
        var modal = document.getElementById('createAssessmentModal');
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    function handleCreateAssessmentBackdrop(e) {
        if (e.target === document.getElementById('createAssessmentModal')) {
            closeCreateAssessmentModal();
        }
    }

    function openDeleteAssessmentModal(id, title) {
        var modal = document.getElementById('deleteAssessmentModal');
        var form = document.getElementById('deleteAssessmentForm');
        var titleSpan = document.getElementById('deleteAssessmentTitleText');

        if (modal && form) {
            form.action = '/guru/asesmen/' + id;
            if (titleSpan) titleSpan.textContent = title;
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeDeleteAssessmentModal() {
        var modal = document.getElementById('deleteAssessmentModal');
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    function handleDeleteAssessmentBackdrop(e) {
        if (e.target === document.getElementById('deleteAssessmentModal')) {
            closeDeleteAssessmentModal();
        }
    }

    // Logic Searchable Dropdown Filter Kelas
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

        var rows = document.querySelectorAll('.filter-class-option-row');
        var groups = document.querySelectorAll('.filter-class-group-block');
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
            var firstVisible = document.querySelector('.filter-class-option-row:not([style*="display: none"])');
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
            closeCreateAssessmentModal();
            closeDeleteAssessmentModal();
            closeFilterClassDropdown();
        }
    });
</script>
@endsection
