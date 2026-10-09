@extends('layouts.app')

@section('title', 'Peminatan & Karier - Guru BK')
@section('header_title', 'Peminatan & Peta Rencana Masa Depan')

@section('content')
<div style="margin-bottom: 24px;">
    <h2 style="font-size: 20px; font-weight: 800; color: var(--color-text-main);">Peta Peminatan & Rencana Masa Depan</h2>
    <p style="font-size: 13px; color: var(--color-text-muted);">
        Pemantauan pilihan karier siswa kelas X, XI, dan XII untuk mendukung persiapan studi lanjut, penyerapan kerja, dan wirausaha.
    </p>
</div>

<!-- Distribution Highlights -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px; margin-bottom: 24px;">
    <div class="stat-card">
        <span class="stat-label">Target Kuliah</span>
        <span class="stat-value">{{ $countKuliah }}</span>
        <span class="stat-desc">PTN, PTS, Kedinasan</span>
    </div>

    <div class="stat-card">
        <span class="stat-label">Target Bekerja</span>
        <span class="stat-value">{{ $countKerja }}</span>
        <span class="stat-desc">Industri, Swasta, BUMN</span>
    </div>

    <div class="stat-card">
        <span class="stat-label">Kuliah Sambil Kerja</span>
        <span class="stat-value">{{ $countKuliahKerja }}</span>
        <span class="stat-desc">Kuliah Fleksibel & Karier</span>
    </div>

    <div class="stat-card">
        <span class="stat-label">Target Wirausaha</span>
        <span class="stat-value">{{ $countWirausaha }}</span>
        <span class="stat-desc">Bisnis Mandiri & Usaha</span>
    </div>

    <div class="stat-card">
        <span class="stat-label">Belum Menentukan</span>
        <span class="stat-value">{{ $countUndecided }}</span>
        <span class="stat-desc">Belum Mengisi Formulir</span>
    </div>
</div>

<!-- Filter Bar -->
<div class="card" style="margin-bottom: 20px; overflow: visible;">
    <div class="card-body" style="padding: 16px 20px; overflow: visible;">
        <form action="{{ route('guru.peminatan.index') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)) 100px; gap: 12px; align-items: flex-end;">
            <div>
                <label class="form-label" style="font-size: 12px; margin-bottom: 6px; display: block;">Tingkat Jenjang</label>
                <select name="grade" id="filterGradeSelect" class="form-select" style="min-height: 38px; font-size: 13px;">
                    <option value="">Semua Tingkat (X, XI, XII)</option>
                    <option value="X" {{ request('grade') === 'X' ? 'selected' : '' }}>Kelas X</option>
                    <option value="XI" {{ request('grade') === 'XI' ? 'selected' : '' }}>Kelas XI</option>
                    <option value="XII" {{ request('grade') === 'XII' ? 'selected' : '' }}>Kelas XII (Kelas Akhir)</option>
                </select>
            </div>

            <div>
                <label class="form-label" style="font-size: 12px; margin-bottom: 6px; display: block;">Kelas</label>
                <select name="class_id" id="filterClassSelect" class="form-select" style="min-height: 38px; font-size: 13px;">
                    <option value="">Semua Rombel</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" data-grade="{{ $c->grade }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label" style="font-size: 12px; margin-bottom: 6px; display: block;">Arah Pilihan Rencana</label>
                <select name="goal" class="form-select" style="min-height: 38px; font-size: 13px;">
                    <option value="">Semua Pilihan</option>
                    <option value="kuliah" {{ request('goal') === 'kuliah' ? 'selected' : '' }}>Kuliah</option>
                    <option value="bekerja" {{ request('goal') === 'bekerja' ? 'selected' : '' }}>Bekerja</option>
                    <option value="kuliah_kerja" {{ request('goal') === 'kuliah_kerja' ? 'selected' : '' }}>Kuliah Sambil Bekerja</option>
                    <option value="wirausaha" {{ request('goal') === 'wirausaha' ? 'selected' : '' }}>Wirausaha</option>
                    <option value="belum_menentukan" {{ request('goal') === 'belum_menentukan' ? 'selected' : '' }}>Belum Mengisi (Prioritas)</option>
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

<!-- Table Peta Rencana -->
<div class="card">
    <div class="card-header card-header-navy" style="display: flex; justify-content: space-between; align-items: center;">
        <h3 class="card-title" style="margin: 0;">Daftar Pilihan Rencana Siswa ({{ $students->total() }} Siswa)</h3>
        @if(request()->hasAny(['grade', 'class_id', 'goal']))
            <a href="{{ route('guru.peminatan.index') }}" class="btn btn-secondary btn-sm" style="font-size: 11px; padding: 4px 10px; color: #ffffff; border-color: rgba(255,255,255,0.3); background: rgba(255,255,255,0.1);" title="Reset semua filter">
                ✕ Reset Filter
            </a>
        @endif
    </div>
    <div class="card-body" style="padding: 0;">
        @if($students->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead class="table-thead-navy">
                        <tr>
                            <th>Nama Siswa & Kelas</th>
                            <th>Arah Pilihan Utama</th>
                            <th>Target Kampus / Perusahaan / Usaha</th>
                            <th>Jalur / Bidang Spesifik</th>
                            <th>Versi & Update</th>
                            <th style="text-align: center; width: 110px; min-width: 90px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $s)
                            @php
                                $plan = $s->futurePlan;
                                $goal = $plan ? $plan->primary_goal : 'belum_menentukan';
                            @endphp
                            <tr style="{{ $goal === 'belum_menentukan' ? 'background-color: #fffdf5;' : '' }}">
                                <td>
                                    <strong>{{ $s->name }}</strong><br>
                                    <small style="color: var(--color-text-muted);">{{ $s->studentClass ? $s->studentClass->name : '-' }} | NISN: {{ $s->nisn }}</small>
                                </td>
                                <td>
                                    @if($goal === 'kuliah')
                                        <span class="badge badge-primary">Kuliah</span>
                                    @elseif($goal === 'bekerja')
                                        <span class="badge badge-success">Bekerja</span>
                                    @elseif($goal === 'kuliah_kerja')
                                        <span class="badge badge-info">Kuliah & Kerja</span>
                                    @elseif($goal === 'wirausaha')
                                        <span class="badge badge-warning">Wirausaha</span>
                                    @else
                                        <span class="badge badge-danger">Belum Mengisi</span>
                                    @endif
                                </td>
                                <td>
                                    @if($plan)
                                        @if($goal === 'kuliah')
                                            <strong>{{ $plan->college_target ?: '-' }}</strong><br>
                                            <small style="color: var(--color-text-subtle);">Prodi: {{ $plan->study_program ?: '-' }}</small>
                                        @elseif($goal === 'bekerja')
                                            <strong>{{ $plan->work_target_company ?: '-' }}</strong><br>
                                            <small style="color: var(--color-text-subtle);">Bidang: {{ $plan->work_target_field ?: '-' }}</small>
                                        @elseif($goal === 'kuliah_kerja')
                                            <strong>{{ $plan->college_target ?: '-' }}</strong><br>
                                            <small style="color: var(--color-text-subtle);">Kerja: {{ $plan->work_target_field ?: ($plan->work_target_company ?: '-') }}</small>
                                        @elseif($goal === 'wirausaha')
                                            <strong>{{ $plan->business_field ?: '-' }}</strong>
                                        @else
                                            <span style="color: var(--color-danger); font-style: italic;">Memerlukan konsultasi penjajakan minat</span>
                                        @endif
                                    @else
                                        <span style="color: var(--color-danger); font-style: italic;">Belum mengisi formulir</span>
                                    @endif
                                </td>
                                <td>
                                    @if($plan && $goal === 'kuliah')
                                        Jalur: {{ $plan->entry_path ?: '-' }}
                                    @elseif($plan && $goal === 'kuliah_kerja')
                                        Prodi: {{ $plan->study_program ?: '-' }}
                                    @elseif($plan && $goal === 'wirausaha')
                                        {{ \Illuminate\Support\Str::limit($plan->business_idea, 40) ?: '-' }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($plan)
                                        <small style="color: var(--color-text-subtle);">
                                            Versi ke-{{ $plan->version }}<br>
                                            {{ $plan->updated_at->translatedFormat('d M Y') }}
                                        </small>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <a href="{{ route('guru.siswa.show', $s->id) }}" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; justify-content: center; white-space: nowrap;">
                                        Profil
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $students->links() }}
        @else
            <div class="empty-state">
                <p class="empty-state-title">Data Peminatan Tidak Ditemukan</p>
                <p class="empty-state-desc">Belum ada siswa yang sesuai dengan filter jenjang atau pilihan rencana yang dipilih.</p>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var gradeSelect = document.getElementById('filterGradeSelect');
        var classSelect = document.getElementById('filterClassSelect');
        if (!gradeSelect || !classSelect) return;

        // Simpan data master opsi kelas
        var allOptions = Array.from(classSelect.options).map(function(opt) {
            return {
                value: opt.value,
                text: opt.text,
                grade: opt.getAttribute('data-grade') || '',
                selected: opt.selected
            };
        });

        function filterClassesByGrade(selectedGrade) {
            var currentClassVal = classSelect.value;
            classSelect.innerHTML = '';
            var hasMatchingSelection = false;

            allOptions.forEach(function(item) {
                if (!item.value) {
                    var defaultOpt = document.createElement('option');
                    defaultOpt.value = '';
                    defaultOpt.textContent = item.text;
                    classSelect.appendChild(defaultOpt);
                } else if (!selectedGrade || item.grade === selectedGrade) {
                    var opt = document.createElement('option');
                    opt.value = item.value;
                    opt.textContent = item.text;
                    opt.setAttribute('data-grade', item.grade);
                    if (item.value === currentClassVal) {
                        opt.selected = true;
                        hasMatchingSelection = true;
                    }
                    classSelect.appendChild(opt);
                }
            });

            if (!hasMatchingSelection && currentClassVal) {
                classSelect.value = '';
            }
        }

        gradeSelect.addEventListener('change', function() {
            filterClassesByGrade(this.value);
        });

        // Jalankan saat halaman pertama kali dimuat jika ada grade yang sedang aktif
        if (gradeSelect.value) {
            filterClassesByGrade(gradeSelect.value);
        }
    });
</script>
@endsection
