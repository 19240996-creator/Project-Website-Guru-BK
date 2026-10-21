@extends('layouts.app')

@section('title', $class->title . ' - Kelas BK Online')
@section('header_title', 'Kelas BK: ' . $class->title)

@section('content')
<div style="margin-bottom: 24px;">
    <!-- Breadcrumb & Header Bar -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; flex-wrap: wrap; gap: 12px;">
        <div>
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                <a href="{{ route('guru.kelas.index') }}" style="font-size: 12.5px; color: var(--color-primary); text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    <span>Daftar Kelas BK</span>
                </a>
                <span style="color: var(--color-text-muted); font-size: 12px;">/</span>
                <span style="font-size: 12.5px; color: var(--color-text-muted);">Ruang Pembelajaran</span>
            </div>
            <h2 style="font-size: 22px; font-weight: 800; color: var(--color-navy); margin: 0 0 6px 0;">
                {{ $class->title }}
            </h2>
            <div style="display: flex; align-items: center; gap: 12px; font-size: 12.5px; color: var(--color-text-muted); flex-wrap: wrap;">
                <span>Pengampu: <strong style="color: var(--color-text-main);">{{ $class->counselor->name }}</strong></span>
                <span>•</span>
                <span>Sasaran: <strong style="color: var(--color-navy);">{{ $class->target_classes_text }}</strong></span>
                <span>•</span>
                <span>{{ \Carbon\Carbon::parse($class->class_date)->translatedFormat('l, d F Y') }}</span>
                <span>•</span>
                <span>Pukul {{ substr($class->start_time, 0, 5) }} - {{ substr($class->end_time, 0, 5) }} WIB</span>
            </div>
        </div>

        <div style="display: flex; gap: 8px;">
            <a href="{{ route('guru.kelas.edit', $class->id) }}" class="btn btn-secondary" style="font-size: 12.5px; padding: 8px 14px; display: inline-flex; align-items: center; gap: 6px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                <span>Edit Jadwal</span>
            </a>
            <button type="button" class="btn btn-secondary" style="font-size: 12.5px; padding: 8px 12px; color: #dc2626; border-color: #fecaca;" title="Hapus Kelas" onclick="openDeleteClassDetailModal('{{ $class->id }}', '{{ addslashes($class->title) }}')">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
            </button>
        </div>
    </div>

    <!-- Live Status & Info Bar -->
    @php
        $compStatus = $class->computed_status;
        $attStatus = $class->attendance_status;
    @endphp
    <div class="card" style="margin-bottom: 20px; border: 1px solid var(--color-border); border-radius: 10px; background: #fff; padding: 14px 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                <div>
                    <span style="font-size: 11px; color: var(--color-text-muted); display: block; margin-bottom: 2px;">Status Pembelajaran:</span>
                    @if($compStatus === 'berlangsung')
                        <span style="display: inline-flex; align-items: center; gap: 5px; background: #dcfce7; color: #166534; font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 5px; border: 1px solid #86efac;">
                            <span style="width: 7px; height: 7px; border-radius: 50%; background: #16a34a;"></span>
                            Sedang Berlangsung
                        </span>
                    @elseif($compStatus === 'terjadwal')
                        <span style="display: inline-flex; align-items: center; gap: 5px; background: #dbeafe; color: #1e40af; font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 5px; border: 1px solid #93c5fd;">
                            <span style="width: 7px; height: 7px; border-radius: 50%; background: #2563eb;"></span>
                            Terjadwal
                        </span>
                    @else
                        <span style="display: inline-flex; align-items: center; gap: 5px; background: #f1f5f9; color: #475569; font-size: 12px; font-weight: 600; padding: 3px 10px; border-radius: 5px; border: 1px solid #cbd5e1;">
                            Selesai
                        </span>
                    @endif
                </div>

                <div style="height: 30px; width: 1px; background: var(--color-border);"></div>

                <div>
                    <span style="font-size: 11px; color: var(--color-text-muted); display: block; margin-bottom: 2px;">Jendela Presensi Siswa:</span>
                    @if($attStatus === 'dibuka')
                        <span style="display: inline-flex; align-items: center; gap: 5px; background: #e0f2fe; color: #0369a1; font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 5px; border: 1px solid #7dd3fc;">
                            Sedang Dibuka ({{ substr($class->attendance_start_time, 0, 5) }} - {{ substr($class->attendance_end_time, 0, 5) }} WIB)
                        </span>
                    @elseif($attStatus === 'belum_dibuka')
                        <span style="display: inline-flex; align-items: center; gap: 5px; background: #f8fafc; color: #64748b; font-size: 12px; font-weight: 600; padding: 3px 10px; border-radius: 5px; border: 1px solid #e2e8f0;">
                            Dibuka Pukul {{ substr($class->attendance_start_time, 0, 5) }} WIB
                        </span>
                    @else
                        <span style="display: inline-flex; align-items: center; gap: 5px; background: #fef2f2; color: #991b1b; font-size: 12px; font-weight: 600; padding: 3px 10px; border-radius: 5px; border: 1px solid #fecaca;">
                            Telah Ditutup (Batas {{ substr($class->attendance_end_time, 0, 5) }} WIB)
                        </span>
                    @endif
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 16px;">
                <div style="text-align: right;">
                    <div style="font-size: 11px; color: var(--color-text-muted);">Kehadiran Terkini</div>
                    <div style="font-size: 15px; font-weight: 800; color: var(--color-navy);">
                        {{ $attendedCount }} / {{ $targetStudentsCount }} Siswa 
                        <span style="font-size: 12px; color: var(--color-text-muted); font-weight: 500;">
                            ({{ $targetStudentsCount > 0 ? round(($attendedCount / $targetStudentsCount) * 100) : 0 }}%)
                        </span>
                    </div>
                </div>
            </div>
        </div>

        @if($class->description)
        <div style="margin-top: 12px; padding-top: 10px; border-top: 1px solid var(--color-border); font-size: 13px; color: var(--color-text-main); line-height: 1.5;">
            {{ $class->description }}
        </div>
        @endif
    </div>

    <!-- Nav Tabs Interaktif -->
    <div style="display: flex; gap: 8px; border-bottom: 2px solid var(--color-border); margin-bottom: 24px; overflow-x: auto;">
        <a href="{{ route('guru.kelas.show', ['id' => $class->id, 'tab' => 'materi']) }}" 
           style="padding: 10px 18px; font-size: 13.5px; font-weight: 700; text-decoration: none; border-bottom: 3px solid {{ $activeTab === 'materi' ? 'var(--color-primary)' : 'transparent' }}; color: {{ $activeTab === 'materi' ? 'var(--color-navy)' : 'var(--color-text-muted)' }}; margin-bottom: -2px; display: inline-flex; align-items: center; gap: 8px; white-space: nowrap;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
            <span>Materi Pembelajaran ({{ $class->materials->count() }})</span>
        </a>

        <a href="{{ route('guru.kelas.show', ['id' => $class->id, 'tab' => 'tugas']) }}" 
           style="padding: 10px 18px; font-size: 13.5px; font-weight: 700; text-decoration: none; border-bottom: 3px solid {{ $activeTab === 'tugas' ? 'var(--color-primary)' : 'transparent' }}; color: {{ $activeTab === 'tugas' ? 'var(--color-navy)' : 'var(--color-text-muted)' }}; margin-bottom: -2px; display: inline-flex; align-items: center; gap: 8px; white-space: nowrap;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
            <span>Tugas &amp; Pengumpulan ({{ $class->tasks->count() }})</span>
        </a>

        <a href="{{ route('guru.kelas.show', ['id' => $class->id, 'tab' => 'absensi']) }}" 
           style="padding: 10px 18px; font-size: 13.5px; font-weight: 700; text-decoration: none; border-bottom: 3px solid {{ $activeTab === 'absensi' ? 'var(--color-primary)' : 'transparent' }}; color: {{ $activeTab === 'absensi' ? 'var(--color-navy)' : 'var(--color-text-muted)' }}; margin-bottom: -2px; display: inline-flex; align-items: center; gap: 8px; white-space: nowrap;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><polyline points="16 11 18 13 22 9"></polyline></svg>
            <span>Rekapitulasi Presensi ({{ $class->attendances->count() }}/{{ $targetStudentsCount }})</span>
        </a>
    </div>

    <!-- ======================================================== -->
    <!-- TAB 1: MATERI PEMBELAJARAN -->
    <!-- ======================================================== -->
    @if($activeTab === 'materi')
    <div style="display: grid; grid-template-columns: 1fr 340px; gap: 20px; align-items: flex-start;">
        <!-- Daftar Materi Tersedia -->
        <div>
            @if($class->materials->isEmpty())
            <div class="card" style="padding: 40px 20px; text-align: center; border: 1px dashed var(--color-border); border-radius: 10px; background: #fff;">
                <div style="font-size: 14px; font-weight: 700; color: var(--color-navy); margin-bottom: 6px;">Belum Ada Materi Pembelajaran</div>
                <p style="font-size: 12.5px; color: var(--color-text-muted); margin: 0 auto; max-width: 400px;">
                    Unggah file presentasi (PPT/PDF), video pembelajaran, atau dokumen materi melalui formulir di samping kanan.
                </p>
            </div>
            @else
            <div style="display: flex; flex-direction: column; gap: 14px;">
                @foreach($class->materials as $mat)
                <div class="card" style="border: 1px solid var(--color-border); border-radius: 10px; background: #fff; padding: 16px 20px; box-shadow: var(--shadow-sm);">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 14px;">
                        <div style="flex: 1;">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                                @if($mat->material_type === 'slide')
                                    <span style="background: #fef3c7; color: #92400e; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 4px;">Slide Presentasi</span>
                                @elseif($mat->material_type === 'video')
                                    <span style="background: #fee2e2; color: #991b1b; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 4px;">Video Pembelajaran</span>
                                @else
                                    <span style="background: #e0f2fe; color: #075985; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 4px;">Dokumen Tambahan</span>
                                @endif
                                <span style="font-size: 11.5px; color: var(--color-text-muted);">Diunggah: {{ $mat->created_at->format('d M Y, H:i') }} WIB</span>
                            </div>

                            <h4 style="font-size: 15px; font-weight: 700; color: var(--color-navy); margin: 0 0 6px 0;">
                                {{ $mat->title }}
                            </h4>

                            @if($mat->description)
                            <p style="font-size: 12.5px; color: var(--color-text-muted); margin: 0 0 10px 0; line-height: 1.4;">
                                {{ $mat->description }}
                            </p>
                            @endif

                            <!-- Preview Video atau Tombol Unduh -->
                            @if($mat->material_type === 'video' && $mat->youtube_embed_url)
                            <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 8px; margin: 10px 0 14px 0; background: #000;">
                                <iframe src="{{ $mat->youtube_embed_url }}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;" allowfullscreen></iframe>
                            </div>
                            @elseif($mat->material_type === 'video' && $mat->video_url)
                            <div style="margin: 8px 0 12px 0;">
                                <a href="{{ $mat->video_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary" style="font-size: 12px; padding: 6px 12px; display: inline-flex; align-items: center; gap: 6px;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                    <span>Buka Tautan Video Pembelajaran</span>
                                </a>
                            </div>
                            @endif

                            @if($mat->file_path)
                            <div style="display: flex; align-items: center; gap: 10px; margin-top: 6px;">
                                <a href="{{ route('guru.kelas.materi.download', ['id' => $class->id, 'materialId' => $mat->id]) }}" class="btn btn-secondary" style="font-size: 12px; padding: 6px 12px; display: inline-flex; align-items: center; gap: 6px;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                    <span>Unduh Berkas ({{ $mat->file_name }} &bull; {{ $mat->formatted_file_size }})</span>
                                </a>
                            </div>
                            @endif
                        </div>

                        <div style="display: flex; align-items: center; gap: 5px; flex-shrink: 0;">
                            <!-- Geser Urutan Materi -->
                            <form action="{{ route('guru.kelas.materi.reorder', ['id' => $class->id, 'materialId' => $mat->id]) }}" method="POST" style="margin: 0;">
                                @csrf
                                <input type="hidden" name="direction" value="up">
                                <button type="submit" class="btn btn-secondary" style="padding: 5px 8px; font-size: 11px; line-height: 1;" title="Geser Urutan Naik" {{ $loop->first ? 'disabled style=opacity:0.35;cursor:not-allowed;' : '' }}>
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"></polyline></svg>
                                </button>
                            </form>
                            <form action="{{ route('guru.kelas.materi.reorder', ['id' => $class->id, 'materialId' => $mat->id]) }}" method="POST" style="margin: 0;">
                                @csrf
                                <input type="hidden" name="direction" value="down">
                                <button type="submit" class="btn btn-secondary" style="padding: 5px 8px; font-size: 11px; line-height: 1;" title="Geser Urutan Turun" {{ $loop->last ? 'disabled style=opacity:0.35;cursor:not-allowed;' : '' }}>
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                </button>
                            </form>

                            <!-- Edit Materi -->
                            <button type="button" class="btn btn-secondary" onclick="openEditMaterialModal({{ $mat->id }}, {{ json_encode($mat->title) }}, {{ json_encode($mat->description ?? '') }}, '{{ $mat->material_type }}', {{ json_encode($mat->video_url ?? '') }})" style="padding: 5px 8px; font-size: 11px; line-height: 1;" title="Edit Informasi Materi">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>

                            <!-- Hapus Materi -->
                            <button type="button" class="btn btn-secondary" style="color: #dc2626; border-color: #fecaca; padding: 5px 8px; font-size: 11px; line-height: 1;" title="Hapus Materi" onclick="openDeleteMaterialModal('{{ $mat->id }}', '{{ addslashes($mat->title) }}')">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <!-- Formulir Tambah Materi Baru -->
        <div class="card" style="border: 1px solid var(--color-border); border-radius: 10px; background: #fff; overflow: hidden; position: sticky; top: 20px;">
            <div style="padding: 12px 16px; background: var(--color-surface-subtle, #f8fafd); border-bottom: 1px solid var(--color-border);">
                <h4 style="font-size: 13.5px; font-weight: 700; color: var(--color-navy); margin: 0;">
                    Tambah Materi Pembelajaran
                </h4>
            </div>
            <div class="card-body" style="padding: 16px;">
                <form action="{{ route('guru.kelas.materi.store', $class->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div style="margin-bottom: 12px;">
                        <label class="form-label" style="font-size: 12px; font-weight: 600; margin-bottom: 4px;">Jenis Materi</label>
                        <select name="material_type" id="selectMaterialType" class="form-select" style="font-size: 12.5px; min-height: 36px;" onchange="toggleMaterialInput(this.value)">
                            <option value="slide">Slide Presentasi (PPT / PPTX / PDF)</option>
                            <option value="video">Video Pembelajaran (Upload / Link YouTube)</option>
                            <option value="document">Dokumen / Modul Tambahan</option>
                        </select>
                    </div>

                    <div style="margin-bottom: 12px;">
                        <label class="form-label" style="font-size: 12px; font-weight: 600; margin-bottom: 4px;">Judul Materi <span style="color: #dc2626;">*</span></label>
                        <input type="text" name="title" required class="form-control" placeholder="Contoh: Slide Modul Rencana Karier" style="font-size: 12.5px; min-height: 36px;">
                    </div>

                    <div style="margin-bottom: 12px;">
                        <label class="form-label" style="font-size: 12px; font-weight: 600; margin-bottom: 4px;">Deskripsi Singkat</label>
                        <textarea name="description" rows="2" class="form-control" placeholder="Petunjuk membaca materi..." style="font-size: 12px;"></textarea>
                    </div>

                    <div id="fileUploadContainer" style="margin-bottom: 12px;">
                        <label class="form-label" style="font-size: 12px; font-weight: 600; margin-bottom: 4px;">Pilih Berkas File</label>
                        <input type="file" name="file" class="form-control" style="font-size: 12px;">
                        <small style="color: var(--color-text-muted); font-size: 11px; display: block; margin-top: 3px;">Maksimal 50 MB.</small>
                    </div>

                    <div id="videoUrlContainer" style="margin-bottom: 14px; display: none;">
                        <label class="form-label" style="font-size: 12px; font-weight: 600; margin-bottom: 4px;">Tautan Video (YouTube / Google Drive)</label>
                        <input type="url" name="video_url" class="form-control" placeholder="https://www.youtube.com/watch?v=..." style="font-size: 12.5px; min-height: 36px;">
                        <small style="color: var(--color-text-muted); font-size: 11px; display: block; margin-top: 3px;">Siswa dapat menonton video langsung di aplikasi.</small>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; font-weight: 700; font-size: 13px; padding: 9px; display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span>Simpan Materi</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- ======================================================== -->
    <!-- TAB 2: TUGAS & PENGUMPULAN -->
    <!-- ======================================================== -->
    @if($activeTab === 'tugas')
    <div style="display: grid; grid-template-columns: 1fr 340px; gap: 20px; align-items: flex-start;">
        <!-- Daftar Tugas & Rekap Pengumpulan -->
        <div>
            @if($class->tasks->isEmpty())
            <div class="card" style="padding: 40px 20px; text-align: center; border: 1px dashed var(--color-border); border-radius: 10px; background: #fff;">
                <div style="font-size: 14px; font-weight: 700; color: var(--color-navy); margin-bottom: 6px;">Belum Ada Tugas Pembelajaran</div>
                <p style="font-size: 12.5px; color: var(--color-text-muted); margin: 0 auto; max-width: 400px;">
                    Buat penugasan atau lembar kerja siswa melalui formulir di samping kanan. Batas pengumpulan tugas berjalan independen dari waktu absensi.
                </p>
            </div>
            @else
            <div style="display: flex; flex-direction: column; gap: 20px;">
                @foreach($class->tasks as $task)
                @php
                    $submissions = ($task->relationLoaded('submissions') && $task->submissions->isNotEmpty())
                        ? $task->submissions
                        : $task->submissions()->with('student')->get();
                    $subsCount = $submissions->count();
                    $gradedCount = $submissions->where('status', 'dinilai')->count();
                @endphp
                <div class="card" style="border: 1px solid var(--color-border); border-radius: 10px; background: #fff; overflow: hidden; box-shadow: var(--shadow-sm);">
                    <div style="padding: 16px 20px; border-bottom: 1px solid var(--color-border); display: flex; justify-content: space-between; align-items: flex-start; gap: 14px; flex-wrap: wrap;">
                        <div>
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px; flex-wrap: wrap;">
                                <span style="background: #e0f2fe; color: #0369a1; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 4px;">Penugasan BK</span>
                                @if($task->start_time)
                                <span style="font-size: 11.5px; color: var(--color-text-muted);">
                                    Mulai: <strong style="color: var(--color-text-main);">{{ $task->start_time->format('d M Y, H:i') }} WIB</strong>
                                </span>
                                <span style="color: #cbd5e1;">&bull;</span>
                                @endif
                                <span style="font-size: 11.5px; color: var(--color-text-muted);">
                                    Batas Akhir: <strong style="color: var(--color-text-main);">{{ $task->due_time ? $task->due_time->format('d M Y, H:i') : '-' }} WIB</strong>
                                </span>
                            </div>
                            <h4 style="font-size: 16px; font-weight: 700; color: var(--color-navy); margin: 0 0 6px 0;">
                                {{ $task->title }}
                            </h4>
                            <div style="font-size: 12.5px; color: var(--color-text-main); line-height: 1.5; margin-bottom: 10px;">
                                {!! nl2br(e($task->instructions)) !!}
                            </div>

                            @if($task->attachment_path)
                            <a href="{{ route('guru.kelas.tugas.download_attachment', ['id' => $class->id, 'taskId' => $task->id]) }}" class="btn btn-secondary" style="font-size: 11.5px; padding: 4px 10px; display: inline-flex; align-items: center; gap: 5px;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                <span>Unduh Lampiran Tugas ({{ $task->attachment_name }})</span>
                            </a>
                            @endif
                        </div>

                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="font-size: 12px; font-weight: 700; color: var(--color-navy); background: #f8fafc; padding: 6px 12px; border-radius: 6px; border: 1px solid var(--color-border);">
                                {{ $subsCount }} Siswa Mengumpulkan ({{ $gradedCount }} Dinilai)
                            </span>
                            <a href="{{ request()->fullUrlWithQuery(['tab' => 'tugas', '_t' => time()]) }}" class="btn btn-secondary" style="padding: 6px 10px; font-size: 12px; display: inline-flex; align-items: center; gap: 5px;" title="Segarkan data pengumpulan tugas">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 4v6h-6"></path><path d="M1 20v-6h6"></path><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
                                <span>Segarkan</span>
                            </a>
                            <button type="button" class="btn btn-secondary" onclick="openEditTaskModal({{ $task->id }}, {{ json_encode($task->title) }}, {{ json_encode($task->instructions) }}, '{{ $task->start_time ? $task->start_time->format('Y-m-d\TH:i') : '' }}', '{{ $task->due_time ? $task->due_time->format('Y-m-d\TH:i') : '' }}', {{ json_encode($task->allowed_file_types ?? '') }})" style="padding: 6px 10px; font-size: 12px; display: inline-flex; align-items: center; gap: 5px;" title="Edit Penugasan">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                <span>Edit</span>
                            </button>
                            <button type="button" class="btn btn-secondary" style="color: #dc2626; border-color: #fecaca; padding: 6px 10px; font-size: 12px;" title="Hapus Tugas" onclick="openDeleteTaskModal('{{ $task->id }}', '{{ addslashes($task->title) }}')">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Tabel Daftar Pengumpulan Siswa -->
                    <div style="padding: 16px 20px;">
                        <h5 style="font-size: 12.5px; font-weight: 700; color: var(--color-navy); margin: 0 0 10px 0;">
                            Daftar Pengumpulan &amp; Penilaian Siswa
                        </h5>

                        <div class="table-responsive" style="max-height: 320px; overflow-y: auto;">
                            <table class="table" style="font-size: 12px; margin: 0;">
                                <thead>
                                    <tr style="background: #f8fafc; border-bottom: 2px solid var(--color-border);">
                                        <th style="padding: 8px 10px;">Siswa Peserta</th>
                                        <th style="padding: 8px 10px;">Waktu Kirim</th>
                                        <th style="padding: 8px 10px;">Berkas / Catatan</th>
                                        <th style="padding: 8px 10px;">Status</th>
                                        <th style="padding: 8px 10px;">Nilai &amp; Umpan Balik</th>
                                        <th style="padding: 8px 10px; text-align: center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $subMap = $submissions->keyBy('student_id');
                                    @endphp
                                    @forelse($targetStudents as $st)
                                    @php
                                        $submission = $subMap->get($st->id) ?? $task->submissionFor($st);
                                        if ($submission && !$subMap->has($st->id)) {
                                            $subMap->put($st->id, $submission);
                                        }
                                        $ext = $submission && $submission->file_path ? strtolower(pathinfo($submission->file_path, PATHINFO_EXTENSION)) : '';
                                        $isImg = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                                        $isPdf = $ext === 'pdf';
                                        $viewUrl = $submission && $submission->file_path ? route('guru.kelas.tugas.view_submission', ['id' => $class->id, 'submissionId' => $submission->id]) : null;
                                        $downloadUrl = $submission && $submission->file_path ? route('guru.kelas.tugas.download_submission', ['id' => $class->id, 'submissionId' => $submission->id]) : null;
                                        $modalPayload = [
                                            'task_id' => $task->id,
                                            'submission_id' => $submission ? $submission->id : null,
                                            'student_id' => $st->id,
                                            'student_name' => $st->name,
                                            'student_nisn' => $st->nisn,
                                            'student_class' => optional($st->studentClass)->name,
                                            'submitted_at' => $submission && $submission->submitted_at ? $submission->submitted_at->format('d/m/Y H:i') . ' WIB' : 'Belum mengumpulkan secara online',
                                            'status' => $submission ? $submission->status : 'belum_kumpul',
                                            'score' => $submission ? $submission->score : '',
                                            'counselor_feedback' => $submission ? $submission->counselor_feedback : '',
                                            'submission_text' => $submission ? $submission->submission_text : '',
                                            'file_path' => $submission ? $submission->file_path : null,
                                            'file_name' => $submission ? ($submission->file_name ?: basename($submission->file_path)) : null,
                                            'file_size' => $submission ? $submission->formatted_file_size : '',
                                            'view_url' => $viewUrl,
                                            'download_url' => $downloadUrl,
                                            'is_image' => $isImg,
                                            'is_pdf' => $isPdf,
                                        ];
                                    @endphp
                                    <tr style="border-bottom: 1px solid var(--color-border);">
                                        <td style="padding: 10px;">
                                            <div style="font-weight: 700; color: var(--color-text-main);">{{ $st->name }}</div>
                                            <div style="font-size: 11px; color: var(--color-text-muted);">
                                                NISN: {{ $st->nisn }} &bull; {{ optional($st->studentClass)->name }}
                                            </div>
                                        </td>
                                        <td style="padding: 10px; white-space: nowrap;">
                                            @if($submission && $submission->submitted_at)
                                                <div style="font-size: 12px; font-weight: 600; color: var(--color-text-main);">{{ $submission->submitted_at->format('d/m/Y') }}</div>
                                                <div style="font-size: 11px; color: var(--color-text-muted);">{{ $submission->submitted_at->format('H:i') }} WIB</div>
                                            @else
                                                <span style="color: #94a3b8; font-size: 11.5px;">Belum kumpul</span>
                                            @endif
                                        </td>
                                        <td style="padding: 10px;">
                                            @if($submission)
                                                @if($submission->file_path)
                                                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                                        @if($isImg)
                                                        <button type="button" onclick='openReviewAndGradeModal(@json($modalPayload))' title="Klik untuk melihat foto tugas siswa" style="border: 0; background: transparent; padding: 0; cursor: pointer;">
                                                            <img src="{{ $viewUrl }}" alt="Foto Jawaban Siswa" style="width: 44px; height: 44px; object-fit: cover; border-radius: 6px; border: 1px solid #cbd5e1; box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: block;">
                                                        </button>
                                                        <div>
                                                            <button type="button" onclick='openReviewAndGradeModal(@json($modalPayload))' style="border: 0; background: transparent; padding: 0; font-size: 11.5px; font-weight: 700; color: var(--color-primary); text-decoration: none; cursor: pointer; text-align: left; display: flex; align-items: center; gap: 4px;">
                                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                                                <span>Lihat Foto Siswa</span>
                                                            </button>
                                                            <div style="font-size: 10.5px; color: var(--color-text-muted);">{{ Str::limit($submission->file_name ?: 'Foto Jawaban', 20) }}</div>
                                                        </div>
                                                        @elseif($isPdf)
                                                        <button type="button" onclick='openReviewAndGradeModal(@json($modalPayload))' style="border: 0; background: transparent; padding: 0; cursor: pointer; display: flex; align-items: center; gap: 6px; text-align: left;">
                                                            <span style="background: #fee2e2; color: #dc2626; padding: 4px 6px; border-radius: 4px; font-size: 10.5px; font-weight: 800;">PDF</span>
                                                            <div>
                                                                <span style="font-size: 11.5px; font-weight: 700; color: var(--color-primary); display: block;">Periksa Dokumen PDF</span>
                                                                <span style="font-size: 10.5px; color: var(--color-text-muted);">{{ Str::limit($submission->file_name ?: 'Dokumen PDF', 20) }}</span>
                                                            </div>
                                                        </button>
                                                        @else
                                                        <a href="{{ $downloadUrl }}" style="display: inline-flex; align-items: center; gap: 6px; background: #f1f5f9; color: var(--color-text-main); padding: 4px 8px; border-radius: 6px; font-size: 11.5px; font-weight: 600; text-decoration: none; border: 1px solid #e2e8f0;">
                                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                                            <span>{{ Str::limit($submission->file_name ?: 'Unduh Berkas', 20) }}</span>
                                                        </a>
                                                        @endif
                                                    </div>
                                                @endif
                                                @if($submission->submission_text)
                                                <div style="font-size: 11.5px; color: var(--color-text-muted); font-style: italic; margin-top: 2px;">
                                                    "{{ Str::limit($submission->submission_text, 50) }}"
                                                </div>
                                                @endif
                                                @if(!$submission->file_path && !$submission->submission_text)
                                                <span style="color: #94a3b8; font-size: 11px;">(Tanpa lampiran online)</span>
                                                @endif
                                            @else
                                                <span style="color: #94a3b8; font-size: 11.5px;">-</span>
                                            @endif
                                        </td>
                                        <td style="padding: 10px;">
                                            @if($submission)
                                                @if($submission->status === 'dinilai')
                                                    <span style="background: #dcfce7; color: #15803d; font-weight: 700; padding: 2px 7px; border-radius: 4px; font-size: 11px;">Dinilai</span>
                                                @elseif($submission->status === 'terlambat')
                                                    <span style="background: #fee2e2; color: #991b1b; font-weight: 600; padding: 2px 7px; border-radius: 4px; font-size: 11px;">Terlambat</span>
                                                @elseif($submission->status === 'perlu_revisi')
                                                    <span style="background: #fef3c7; color: #92400e; font-weight: 600; padding: 2px 7px; border-radius: 4px; font-size: 11px;">Perlu Revisi</span>
                                                @else
                                                    <span style="background: #e0f2fe; color: #0369a1; font-weight: 600; padding: 2px 7px; border-radius: 4px; font-size: 11px;">Diserahkan</span>
                                                @endif
                                            @else
                                                <span style="background: #f1f5f9; color: #64748b; padding: 2px 7px; border-radius: 4px; font-size: 11px;">Belum Kumpul</span>
                                            @endif
                                        </td>
                                        <td style="padding: 10px;">
                                            @if($submission && $submission->score !== null)
                                                <div style="font-weight: 800; font-size: 13.5px; color: var(--color-navy);">Nilai: {{ $submission->score }}</div>
                                                @if($submission->counselor_feedback)
                                                <div style="font-size: 11px; color: var(--color-text-muted); margin-top: 2px;">{{ Str::limit($submission->counselor_feedback, 35) }}</div>
                                                @endif
                                            @else
                                                <span style="color: #94a3b8; font-size: 11.5px;">Belum dinilai</span>
                                            @endif
                                        </td>
                                        <td style="padding: 10px; text-align: center;">
                                            @if($submission)
                                            <button type="button" class="btn btn-primary" style="font-size: 11px; padding: 4px 10px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;" onclick='openReviewAndGradeModal(@json($modalPayload))'>
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                                <span>{{ $submission->score !== null ? 'Ubah Nilai' : 'Periksa & Nilai' }}</span>
                                            </button>
                                            @else
                                            <button type="button" class="btn btn-secondary" style="font-size: 11px; padding: 4px 9px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;" onclick='openReviewAndGradeModal(@json($modalPayload))' title="Beri nilai manual langsung untuk tugas offline atau tatap muka">
                                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                <span>Beri Nilai</span>
                                            </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" style="text-align: center; color: var(--color-text-muted); padding: 18px;">
                                            Tidak ada siswa yang terdaftar sebagai sasaran kelas ini.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <!-- Form Buat Tugas Baru -->
        <div class="card" style="border: 1px solid var(--color-border); border-radius: 10px; background: #fff; overflow: hidden; position: sticky; top: 20px;">
            <div style="padding: 12px 16px; background: var(--color-surface-subtle, #f8fafd); border-bottom: 1px solid var(--color-border);">
                <h4 style="font-size: 13.5px; font-weight: 700; color: var(--color-navy); margin: 0;">
                    Buat Penugasan Baru
                </h4>
            </div>
            <div class="card-body" style="padding: 16px;">
                <form action="{{ route('guru.kelas.tugas.store', $class->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div style="margin-bottom: 12px;">
                        <label class="form-label" style="font-size: 12px; font-weight: 600; margin-bottom: 4px;">Judul Tugas <span style="color: #dc2626;">*</span></label>
                        <input type="text" name="title" required class="form-control" placeholder="Contoh: Lembar Refleksi Peminatan Karier" style="font-size: 12.5px; min-height: 36px;">
                    </div>

                    <div style="margin-bottom: 12px;">
                        <label class="form-label" style="font-size: 12px; font-weight: 600; margin-bottom: 4px;">Instruksi Pengerjaan <span style="color: #dc2626;">*</span></label>
                        <textarea name="instructions" rows="3" required class="form-control" placeholder="Tuliskan petunjuk pengerjaan dan format jawaban..." style="font-size: 12px;"></textarea>
                    </div>

                    <div style="margin-bottom: 12px;">
                        <label class="form-label" style="font-size: 12px; font-weight: 600; margin-bottom: 4px;">Waktu Mulai Pengerjaan</label>
                        @php
                            $defaultStartDt = \Carbon\Carbon::parse($class->class_date->format('Y-m-d') . ' ' . ($class->start_time ?? '07:00'), 'Asia/Jakarta');
                        @endphp
                        <input type="datetime-local" name="start_time" value="{{ $defaultStartDt->format('Y-m-d\TH:i') }}" required class="form-control" style="font-size: 12px;">
                        <small style="color: var(--color-text-muted); font-size: 10.5px; display: block; margin-top: 3px;">Tugas otomatis terbuka untuk siswa di kelas saat sesi pembelajaran berlangsung.</small>
                    </div>

                    <div style="margin-bottom: 12px;">
                        <label class="form-label" style="font-size: 12px; font-weight: 600; margin-bottom: 4px;">Batas Akhir Pengumpulan (Deadline)</label>
                        <input type="datetime-local" name="due_time" value="{{ \Carbon\Carbon::parse($class->class_date->format('Y-m-d') . ' 23:59', 'Asia/Jakarta')->format('Y-m-d\TH:i') }}" required class="form-control" style="font-size: 12px;">
                    </div>

                    <div style="margin-bottom: 14px;">
                        <label class="form-label" style="font-size: 12px; font-weight: 600; margin-bottom: 4px;">Lampiran Guru (Opsional LKPD / Template)</label>
                        <input type="file" name="attachment" class="form-control" style="font-size: 12px;">
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; font-weight: 700; font-size: 13px; padding: 9px; display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span>Terbitkan Tugas Siswa</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- ======================================================== -->
    <!-- TAB 3: REKAPITULASI PRESENSI & KEHADIRAN -->
    <!-- ======================================================== -->
    @if($activeTab === 'absensi')
    <div>
        <!-- Statistik Kehadiran Siswa -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px; margin-bottom: 20px;">
            <div class="card" style="padding: 14px; border: 1px solid #bbf7d0; background: #f0fdf4; border-radius: 8px;">
                <div style="font-size: 11px; font-weight: 600; color: #166534; text-transform: uppercase;">Hadir Tepat Waktu</div>
                <div style="font-size: 22px; font-weight: 800; color: #15803d; margin-top: 4px;">{{ $attendedCount }}</div>
                <div style="font-size: 11px; color: #166534;">Siswa</div>
            </div>

            <div class="card" style="padding: 14px; border: 1px solid #fef08a; background: #fefce8; border-radius: 8px;">
                <div style="font-size: 11px; font-weight: 600; color: #854d0e; text-transform: uppercase;">Terlambat</div>
                <div style="font-size: 22px; font-weight: 800; color: #a16207; margin-top: 4px;">{{ $lateCount }}</div>
                <div style="font-size: 11px; color: #854d0e;">Siswa</div>
            </div>

            <div class="card" style="padding: 14px; border: 1px solid #fed7aa; background: #fff7ed; border-radius: 8px;">
                <div style="font-size: 11px; font-weight: 600; color: #9a3412; text-transform: uppercase;">Izin / Sakit / Alpa</div>
                <div style="font-size: 22px; font-weight: 800; color: #c2410c; margin-top: 4px;">{{ $absentCount }}</div>
                <div style="font-size: 11px; color: #9a3412;">Siswa</div>
            </div>

            <div class="card" style="padding: 14px; border: 1px solid var(--color-border); background: #f8fafc; border-radius: 8px;">
                <div style="font-size: 11px; font-weight: 600; color: var(--color-text-muted); text-transform: uppercase;">Belum Presensi</div>
                <div style="font-size: 22px; font-weight: 800; color: #475569; margin-top: 4px;">
                    {{ max(0, $targetStudentsCount - ($attendedCount + $lateCount + $absentCount)) }}
                </div>
                <div style="font-size: 11px; color: var(--color-text-muted);">Siswa</div>
            </div>
        </div>

        <!-- Tabel Rekap Presensi -->
        <div class="card" style="border: 1px solid var(--color-border); border-radius: 10px; background: #fff; overflow: hidden; box-shadow: var(--shadow-sm);">
            <div style="padding: 14px 20px; background: var(--color-surface-subtle, #f8fafd); border-bottom: 1px solid var(--color-border); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                <h4 style="font-size: 14px; font-weight: 700; color: var(--color-navy); margin: 0;">
                    Tabel Presensi Kehadiran Siswa
                </h4>
                <div style="font-size: 12px; color: var(--color-text-muted);">
                    Rentang Waktu: <strong>{{ substr($class->attendance_start_time, 0, 5) }} - {{ substr($class->attendance_end_time, 0, 5) }} WIB</strong>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table" style="font-size: 12.5px; margin: 0;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 2px solid var(--color-border);">
                            <th style="padding: 10px 14px;">Nama Siswa</th>
                            <th style="padding: 10px 14px;">NISN / Kelas</th>
                            <th style="padding: 10px 14px;">Status Kehadiran</th>
                            <th style="padding: 10px 14px;">Waktu Presensi (WIB)</th>
                            <th style="padding: 10px 14px;">Keterangan / Audit Log</th>
                            <th style="padding: 10px 14px; text-align: center;">Koreksi Guru BK</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($targetStudents as $st)
                        @php
                            $att = $attendanceMap->get($st->id);
                        @endphp
                        <tr style="border-bottom: 1px solid var(--color-border);">
                            <td style="padding: 10px 14px; font-weight: 700; color: var(--color-text-main);">
                                {{ $st->name }}
                            </td>
                            <td style="padding: 10px 14px; color: var(--color-text-muted);">
                                {{ $st->nisn }} &bull; {{ optional($st->studentClass)->name }}
                            </td>
                            <td style="padding: 10px 14px;">
                                @if($att)
                                    @if($att->status === 'hadir')
                                        <span style="background: #dcfce7; color: #15803d; font-weight: 700; padding: 3px 8px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px;">
                                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                            Hadir
                                        </span>
                                    @elseif($att->status === 'terlambat')
                                        <span style="background: #fef3c7; color: #92400e; font-weight: 600; padding: 3px 8px; border-radius: 4px;">
                                            Terlambat
                                        </span>
                                    @elseif($att->status === 'izin')
                                        <span style="background: #e0f2fe; color: #0369a1; font-weight: 600; padding: 3px 8px; border-radius: 4px;">
                                            Izin
                                        </span>
                                    @elseif($att->status === 'sakit')
                                        <span style="background: #ede9fe; color: #6d28d9; font-weight: 600; padding: 3px 8px; border-radius: 4px;">
                                            Sakit
                                        </span>
                                    @else
                                        <span style="background: #fee2e2; color: #991b1b; font-weight: 600; padding: 3px 8px; border-radius: 4px;">
                                            Alpa
                                        </span>
                                    @endif
                                @else
                                    @if($attStatus === 'ditutup')
                                        <span style="background: #fee2e2; color: #991b1b; font-weight: 600; padding: 3px 8px; border-radius: 4px;">
                                            Tidak Hadir (Alpa)
                                        </span>
                                    @else
                                        <span style="background: #f1f5f9; color: #64748b; font-weight: 500; padding: 3px 8px; border-radius: 4px;">
                                            Belum Presensi
                                        </span>
                                    @endif
                                @endif
                            </td>
                            <td style="padding: 10px 14px; white-space: nowrap; color: var(--color-text-muted);">
                                @if($att && $att->attended_at)
                                    {{ $att->attended_at->format('d M Y, H:i:s') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td style="padding: 10px 14px; font-size: 11.5px; color: var(--color-text-muted);">
                                @if($att && $att->is_manual_correction)
                                    <div style="color: #b45309; font-weight: 600;">
                                        Dikoreksi Manual: {{ $att->correction_reason }}
                                    </div>
                                    <div>Oleh: {{ optional($att->corrector)->name }}</div>
                                @elseif($att)
                                    Presensi mandiri siswa (IP: {{ $att->ip_address ?: '-' }})
                                @else
                                    -
                                @endif
                            </td>
                            <td style="padding: 10px 14px; text-align: center;">
                                <button type="button" 
                                        class="btn btn-secondary" 
                                        style="font-size: 11.5px; padding: 4px 9px;" 
                                        onclick="openCorrectionModal({{ $st->id }}, '{{ addslashes($st->name) }}', '{{ $att ? $att->status : 'alpha' }}')">
                                    Koreksi
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--color-text-muted); padding: 24px;">
                                Tidak ada siswa sasaran pada kelas BK ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Modal Koreksi Kehadiran Guru BK -->
<div id="correctionModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.5); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #fff; border-radius: 10px; max-width: 440px; width: 100%; box-shadow: var(--shadow-lg); overflow: hidden; animation: fadeIn 0.15s ease;">
        <div style="padding: 14px 18px; background: var(--color-navy); color: #fff; display: flex; justify-content: space-between; align-items: center;">
            <h4 style="font-size: 14px; font-weight: 700; margin: 0; color: #fff;">Koreksi Kehadiran Siswa</h4>
            <button type="button" onclick="closeCorrectionModal()" style="background: none; border: 0; color: #cbd5e1; cursor: pointer; font-size: 18px;">&times;</button>
        </div>
        <form action="{{ route('guru.kelas.presensi.correct', $class->id) }}" method="POST" style="padding: 18px;">
            @csrf
            <input type="hidden" name="student_id" id="correctStudentId">

            <div style="margin-bottom: 12px;">
                <label style="font-size: 11.5px; color: var(--color-text-muted); display: block; margin-bottom: 2px;">Nama Siswa</label>
                <div id="correctStudentName" style="font-size: 13.5px; font-weight: 700; color: var(--color-navy);"></div>
            </div>

            <div style="margin-bottom: 14px;">
                <label class="form-label" style="font-size: 12px; font-weight: 600; margin-bottom: 4px;">Pilih Status Kehadiran Baru <span style="color: #dc2626;">*</span></label>
                <select name="status" id="correctStatusSelect" class="form-select" required style="font-size: 13px;">
                    <option value="hadir">Hadir</option>
                    <option value="terlambat">Terlambat</option>
                    <option value="izin">Izin</option>
                    <option value="sakit">Sakit</option>
                    <option value="alpha">Alpha / Tidak Hadir</option>
                </select>
            </div>

            <div style="margin-bottom: 16px;">
                <label class="form-label" style="font-size: 12px; font-weight: 600; margin-bottom: 4px;">Alasan Koreksi Resmi <span style="color: #dc2626;">*</span></label>
                <textarea name="correction_reason" required rows="3" class="form-control" placeholder="Tuliskan alasan koreksi (misal: Konfirmasi surat sakit dokter, kendala teknis perangkat, dsb.)..." style="font-size: 12.5px;"></textarea>
                <small style="color: var(--color-text-muted); font-size: 11px; display: block; margin-top: 3px;">
                    Alasan akan dicatat pada riwayat Audit Log dan tidak membuka presensi mandiri bagi siswa lain.
                </small>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 8px;">
                <button type="button" class="btn btn-secondary" onclick="closeCorrectionModal()" style="font-size: 12.5px; padding: 7px 14px;">Batal</button>
                <button type="submit" class="btn btn-primary" style="font-size: 12.5px; padding: 7px 16px; font-weight: 700;">Simpan Koreksi</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Pemeriksaan Berkas & Penilaian Tugas Siswa (Konsisten Desain Ruang BK) -->
<div id="modalReviewAndGrade" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.65); z-index: 9999; align-items: center; justify-content: center; padding: 20px; backdrop-filter: blur(2px);" onclick="if(event.target === this) closeReviewAndGradeModal()">
    <div style="background: #ffffff; border-radius: 14px; max-width: 820px; width: 100%; max-height: 90vh; display: flex; flex-direction: column; box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25); border: 1px solid var(--color-border); overflow: hidden;">
        <!-- Header Modal -->
        <div style="padding: 16px 22px; background: var(--color-navy); color: #ffffff; display: flex; justify-content: space-between; align-items: center; flex-shrink: 0;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(255, 255, 255, 0.12); display: flex; align-items: center; justify-content: center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                </div>
                <div>
                    <h4 id="modalReviewTitle" style="font-size: 15px; font-weight: 700; margin: 0; color: #ffffff;">Pemeriksaan Berkas &amp; Penilaian Siswa</h4>
                    <span style="font-size: 11.5px; color: #cbd5e1;">Evaluasi tugas siswa dan berikan penilaian manual beserta umpan balik</span>
                </div>
            </div>
            <button type="button" onclick="closeReviewAndGradeModal()" style="background: none; border: 0; color: #cbd5e1; cursor: pointer; font-size: 22px; line-height: 1; padding: 4px;" aria-label="Tutup Modal">&times;</button>
        </div>

        <!-- Scrollable Modal Body -->
        <div style="padding: 22px; overflow-y: auto; flex: 1;">
            <!-- Profil Siswa & Info Status -->
            <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; background: #f8fafc; border: 1px solid var(--color-border); border-radius: 10px; padding: 12px 16px; margin-bottom: 20px;">
                <div>
                    <div id="modalReviewStudentName" style="font-size: 15px; font-weight: 800; color: var(--color-navy);"></div>
                    <div style="font-size: 12px; color: var(--color-text-muted); margin-top: 2px;">
                        NISN: <span id="modalReviewStudentNisn" style="font-weight: 600; color: var(--color-text-main);">-</span> &bull; 
                        Kelas: <span id="modalReviewStudentClass" style="font-weight: 600; color: var(--color-text-main);">-</span>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 10px; font-size: 12px;">
                    <div>
                        <span style="color: var(--color-text-muted);">Waktu Kirim:</span>
                        <strong id="modalReviewSubmittedAt" style="color: var(--color-text-main); display: block;">-</strong>
                    </div>
                    <span id="modalReviewStatusBadge" style="padding: 4px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 700;">-</span>
                </div>
            </div>

            <!-- Bagian Jawaban / Berkas Siswa -->
            <div style="margin-bottom: 22px;">
                <h5 style="font-size: 13px; font-weight: 700; color: var(--color-navy); margin: 0 0 10px 0; display: flex; align-items: center; gap: 6px;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path></svg>
                    <span>Berkas Lampiran &amp; Jawaban Siswa</span>
                </h5>

                <!-- Catatan / Teks Siswa -->
                <div id="modalReviewTextContainer" style="display: none; background: #ffffff; border: 1px solid var(--color-border); border-left: 3px solid var(--color-primary); border-radius: 8px; padding: 12px 14px; margin-bottom: 14px;">
                    <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--color-text-muted); display: block; margin-bottom: 4px;">Pesan / Catatan Siswa:</span>
                    <p id="modalReviewText" style="margin: 0; font-size: 12.5px; color: var(--color-text-main); line-height: 1.5; white-space: pre-wrap;"></p>
                </div>

                <!-- Pratinjau Berkas / Foto Siswa -->
                <div id="modalReviewFileBox" style="display: none; border: 1px solid var(--color-border); border-radius: 10px; padding: 14px; background: #ffffff;">
                    <!-- Bar info berkas + tombol aksi cepat -->
                    <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 12px; padding-bottom: 10px; border-bottom: 1px solid #f1f5f9;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span id="modalReviewFileIcon" style="display: flex; align-items: center;"></span>
                            <div>
                                <span id="modalReviewFileName" style="font-size: 12.5px; font-weight: 700; color: var(--color-text-main);"></span>
                                <span id="modalReviewFileSize" style="font-size: 11px; color: var(--color-text-muted); margin-left: 6px;"></span>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <a id="modalReviewFileViewBtn" href="#" target="_blank" class="btn btn-secondary" style="font-size: 11.5px; padding: 4px 10px; display: inline-flex; align-items: center; gap: 4px; text-decoration: none;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                <span>Buka Tab Penuh</span>
                            </a>
                            <a id="modalReviewFileDownloadBtn" href="#" class="btn btn-secondary" style="font-size: 11.5px; padding: 4px 10px; display: inline-flex; align-items: center; gap: 4px; text-decoration: none;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                <span>Unduh Berkas</span>
                            </a>
                        </div>
                    </div>

                    <!-- Live Image Viewer -->
                    <div id="modalReviewImageViewer" style="display: none; background: #0f172a; border-radius: 8px; padding: 12px; text-align: center; max-height: 420px; overflow: auto;">
                        <img id="modalReviewImageTag" src="" alt="Pratinjau Foto Siswa" style="max-width: 100%; max-height: 390px; object-fit: contain; border-radius: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.4);">
                    </div>

                    <!-- PDF Viewer -->
                    <div id="modalReviewPdfViewer" style="display: none; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0;">
                        <iframe id="modalReviewPdfFrame" src="" style="width: 100%; height: 380px; border: 0; background: #f8fafc;"></iframe>
                    </div>

                    <!-- Office / Other File Card -->
                    <div id="modalReviewDocViewer" style="display: none; text-align: center; padding: 24px 16px; background: #f8fafc; border-radius: 8px; border: 1px dashed var(--color-border);">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="1.8" style="margin-bottom: 8px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                        <p style="margin: 0 0 8px 0; font-size: 12.5px; color: var(--color-text-main); font-weight: 600;">Format dokumen ini dapat diperiksa dengan mengunduh berkas.</p>
                        <a id="modalReviewDocDownloadBtn" href="#" class="btn btn-primary" style="font-size: 12px; padding: 6px 14px; text-decoration: none;">Unduh Berkas Jawaban</a>
                    </div>
                </div>

                <!-- State jika siswa belum kumpul berkas online -->
                <div id="modalReviewEmptyNotice" style="display: none; background: #f8fafc; border: 1px dashed var(--color-border); border-radius: 8px; padding: 14px; font-size: 12px; color: var(--color-text-muted); text-align: center;">
                    Siswa belum mengunggah berkas secara online. Guru BK tetap dapat memberikan nilai secara manual di bawah ini (misalnya untuk pengumpulan tugas fisik atau penilaian tatap muka langsung).
                </div>
            </div>

            <!-- Form Penilaian Manual -->
            <form id="modalReviewGradeForm" method="POST" action="" style="background: #f8fafc; border: 1px solid var(--color-border); border-radius: 10px; padding: 16px;">
                @csrf
                <input type="hidden" name="student_id" id="modalReviewStudentId" value="">

                <h5 style="font-size: 13px; font-weight: 700; color: var(--color-navy); margin: 0 0 12px 0; display: flex; align-items: center; gap: 6px;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    <span>Formulir Penilaian Guru BK</span>
                </h5>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 12px;">
                    <div>
                        <label class="form-label" style="font-size: 12px; font-weight: 700; margin-bottom: 4px; color: var(--color-text-main);">
                            Nilai Angka (0 sampai 100)
                        </label>
                        <input type="number" min="0" max="100" name="score" id="modalReviewScoreInput" class="form-control" placeholder="Contoh: 85" style="font-size: 13px; font-weight: 700;">
                        
                        <!-- Quick score presets -->
                        <div style="display: flex; align-items: center; gap: 4px; margin-top: 6px; flex-wrap: wrap;">
                            <span style="font-size: 10.5px; color: var(--color-text-muted);">Pilihan Cepat:</span>
                            <button type="button" class="btn btn-secondary" style="font-size: 10px; padding: 2px 6px;" onclick="setQuickScore(75)">75</button>
                            <button type="button" class="btn btn-secondary" style="font-size: 10px; padding: 2px 6px;" onclick="setQuickScore(80)">80</button>
                            <button type="button" class="btn btn-secondary" style="font-size: 10px; padding: 2px 6px;" onclick="setQuickScore(85)">85</button>
                            <button type="button" class="btn btn-secondary" style="font-size: 10px; padding: 2px 6px;" onclick="setQuickScore(90)">90</button>
                            <button type="button" class="btn btn-secondary" style="font-size: 10px; padding: 2px 6px;" onclick="setQuickScore(95)">95</button>
                            <button type="button" class="btn btn-secondary" style="font-size: 10px; padding: 2px 6px;" onclick="setQuickScore(100)">100</button>
                        </div>
                    </div>

                    <div>
                        <label class="form-label" style="font-size: 12px; font-weight: 700; margin-bottom: 4px; color: var(--color-text-main);">
                            Status Penilaian <span style="color: #dc2626;">*</span>
                        </label>
                        <select name="status" id="modalReviewStatusSelect" class="form-select" style="font-size: 12.5px;">
                            <option value="dinilai">Telah Dinilai</option>
                            <option value="perlu_revisi">Perlu Revisi (Siswa diminta perbaiki)</option>
                            <option value="diserahkan">Diserahkan (Belum Final)</option>
                        </select>
                        <small style="color: var(--color-text-muted); font-size: 10.5px; display: block; margin-top: 4px;">Pilih status kelayakan tugas siswa.</small>
                    </div>
                </div>

                <div style="margin-bottom: 14px;">
                    <label class="form-label" style="font-size: 12px; font-weight: 700; margin-bottom: 4px; color: var(--color-text-main);">
                        Catatan &amp; Umpan Balik Guru BK (Opsional)
                    </label>
                    <textarea name="counselor_feedback" id="modalReviewFeedbackInput" rows="3" class="form-control" placeholder="Tuliskan apresiasi, penguatan refleksi, atau saran perbaikan bagi siswa..." style="font-size: 12.5px;"></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 8px;">
                    <button type="button" class="btn btn-secondary" onclick="closeReviewAndGradeModal()" style="font-size: 12.5px; padding: 7px 14px;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="font-size: 12.5px; padding: 7px 18px; font-weight: 700;">Simpan Nilai Tugas</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleMaterialInput(val) {
    var fileContainer = document.getElementById('fileUploadContainer');
    var videoContainer = document.getElementById('videoUrlContainer');
    if (val === 'video') {
        videoContainer.style.display = 'block';
        fileContainer.style.display = 'block';
    } else {
        videoContainer.style.display = 'none';
        fileContainer.style.display = 'block';
    }
}

function openCorrectionModal(studentId, studentName, currentStatus) {
    document.getElementById('correctStudentId').value = studentId;
    document.getElementById('correctStudentName').textContent = studentName;
    document.getElementById('correctStatusSelect').value = currentStatus || 'hadir';
    document.getElementById('correctionModal').style.display = 'flex';
}

function closeCorrectionModal() {
    document.getElementById('correctionModal').style.display = 'none';
}

function openReviewAndGradeModal(data) {
    var modal = document.getElementById('modalReviewAndGrade');
    var form = document.getElementById('modalReviewGradeForm');

    // Action endpoint for manual or submission grading
    form.action = "{{ url('guru/kelas/' . $class->id . '/tugas') }}/" + data.task_id + "/nilai-siswa";
    document.getElementById('modalReviewStudentId').value = data.student_id;

    // Student identity
    document.getElementById('modalReviewStudentName').textContent = data.student_name;
    document.getElementById('modalReviewStudentNisn').textContent = data.student_nisn || '-';
    document.getElementById('modalReviewStudentClass').textContent = data.student_class || '-';
    document.getElementById('modalReviewSubmittedAt').textContent = data.submitted_at || '-';

    // Status badge
    var statusBadge = document.getElementById('modalReviewStatusBadge');
    if (data.status === 'dinilai') {
        statusBadge.textContent = 'Telah Dinilai';
        statusBadge.style.background = '#dcfce7';
        statusBadge.style.color = '#15803d';
    } else if (data.status === 'perlu_revisi') {
        statusBadge.textContent = 'Perlu Revisi';
        statusBadge.style.background = '#fef3c7';
        statusBadge.style.color = '#92400e';
    } else if (data.status === 'terlambat') {
        statusBadge.textContent = 'Terlambat';
        statusBadge.style.background = '#fee2e2';
        statusBadge.style.color = '#991b1b';
    } else if (data.status === 'diserahkan') {
        statusBadge.textContent = 'Diserahkan';
        statusBadge.style.background = '#e0f2fe';
        statusBadge.style.color = '#0369a1';
    } else {
        statusBadge.textContent = 'Belum Kumpul';
        statusBadge.style.background = '#f1f5f9';
        statusBadge.style.color = '#64748b';
    }

    // Student text
    var textContainer = document.getElementById('modalReviewTextContainer');
    var textElem = document.getElementById('modalReviewText');
    if (data.submission_text && data.submission_text.trim() !== '') {
        textElem.textContent = data.submission_text;
        textContainer.style.display = 'block';
    } else {
        textContainer.style.display = 'none';
    }

    // Student file
    var fileBox = document.getElementById('modalReviewFileBox');
    var emptyNotice = document.getElementById('modalReviewEmptyNotice');
    var imgViewer = document.getElementById('modalReviewImageViewer');
    var imgTag = document.getElementById('modalReviewImageTag');
    var pdfViewer = document.getElementById('modalReviewPdfViewer');
    var pdfFrame = document.getElementById('modalReviewPdfFrame');
    var docViewer = document.getElementById('modalReviewDocViewer');

    if (data.file_path && data.view_url) {
        fileBox.style.display = 'block';
        emptyNotice.style.display = 'none';
        document.getElementById('modalReviewFileName').textContent = data.file_name || 'Berkas Jawaban';
        document.getElementById('modalReviewFileSize').textContent = data.file_size ? '(' + data.file_size + ')' : '';
        document.getElementById('modalReviewFileViewBtn').href = data.view_url;
        document.getElementById('modalReviewFileDownloadBtn').href = data.download_url;

        if (data.is_image) {
            imgViewer.style.display = 'block';
            imgTag.src = data.view_url;
            pdfViewer.style.display = 'none';
            pdfFrame.src = '';
            docViewer.style.display = 'none';
            document.getElementById('modalReviewFileIcon').innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>';
        } else if (data.is_pdf) {
            imgViewer.style.display = 'none';
            imgTag.src = '';
            pdfViewer.style.display = 'block';
            pdfFrame.src = data.view_url;
            docViewer.style.display = 'none';
            document.getElementById('modalReviewFileIcon').innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>';
        } else {
            imgViewer.style.display = 'none';
            imgTag.src = '';
            pdfViewer.style.display = 'none';
            pdfFrame.src = '';
            docViewer.style.display = 'block';
            document.getElementById('modalReviewDocDownloadBtn').href = data.download_url;
            document.getElementById('modalReviewFileIcon').innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>';
        }
    } else {
        fileBox.style.display = 'none';
        imgViewer.style.display = 'none';
        imgTag.src = '';
        pdfViewer.style.display = 'none';
        pdfFrame.src = '';
        docViewer.style.display = 'none';
        emptyNotice.style.display = 'block';
    }

    // Grading fields
    document.getElementById('modalReviewScoreInput').value = (data.score !== null && data.score !== undefined && data.score !== '') ? data.score : '';
    document.getElementById('modalReviewStatusSelect').value = (data.status && data.status !== 'belum_kumpul') ? data.status : 'dinilai';
    document.getElementById('modalReviewFeedbackInput').value = data.counselor_feedback || '';

    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeReviewAndGradeModal() {
    var modal = document.getElementById('modalReviewAndGrade');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
        var pdfFrame = document.getElementById('modalReviewPdfFrame');
        if (pdfFrame) pdfFrame.src = '';
        var imgTag = document.getElementById('modalReviewImageTag');
        if (imgTag) imgTag.src = '';
    }
}

function setQuickScore(val) {
    var input = document.getElementById('modalReviewScoreInput');
    if (input) {
        input.value = val;
    }
}

// Fallback compatibility
function openGradeModal(submissionId, studentName, score, feedback, status) {
    openReviewAndGradeModal({
        task_id: '',
        submission_id: submissionId,
        student_id: '',
        student_name: studentName,
        score: score,
        counselor_feedback: feedback,
        status: status
    });
}

function closeGradeModal() {
    closeReviewAndGradeModal();
}

// Keyboard escape handler for accessibility (R-32)
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeReviewAndGradeModal();
        closeCorrectionModal();
        if (typeof closeEditMaterialModal === 'function') closeEditMaterialModal();
        if (typeof closeEditTaskModal === 'function') closeEditTaskModal();
        if (typeof closeDeleteClassDetailModal === 'function') closeDeleteClassDetailModal();
        if (typeof closeDeleteMaterialModal === 'function') closeDeleteMaterialModal();
        if (typeof closeDeleteTaskModal === 'function') closeDeleteTaskModal();
    }
});
</script>

<!-- Modal Edit Materi Pembelajaran -->
<div id="modalEditMaterial" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 1050; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #fff; border-radius: 12px; width: 100%; max-width: 520px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2); overflow: hidden;">
        <div style="padding: 16px 20px; background: var(--color-surface-subtle, #f8fafc); border-bottom: 1px solid var(--color-border); display: flex; justify-content: space-between; align-items: center;">
            <h4 style="margin: 0; font-size: 15px; font-weight: 700; color: var(--color-navy);">Edit Materi Pembelajaran</h4>
            <button type="button" onclick="closeEditMaterialModal()" style="border: 0; background: transparent; font-size: 20px; cursor: pointer; color: #64748b;">&times;</button>
        </div>
        <form id="formEditMaterial" method="POST" enctype="multipart/form-data" style="padding: 20px;">
            @csrf
            @method('PUT')
            <div style="margin-bottom: 14px;">
                <label class="form-label" style="font-size: 12px; font-weight: 600; margin-bottom: 4px;">Jenis Materi</label>
                <select name="material_type" id="editMaterialType" class="form-select" style="font-size: 12.5px;" onchange="toggleEditMaterialInput(this.value)">
                    <option value="slide">Slide Presentasi (PPT / PPTX / PDF)</option>
                    <option value="video">Video Pembelajaran (Upload / Link YouTube)</option>
                    <option value="document">Dokumen / Modul Tambahan</option>
                </select>
            </div>
            <div style="margin-bottom: 14px;">
                <label class="form-label" style="font-size: 12px; font-weight: 600; margin-bottom: 4px;">Judul Materi <span style="color: #dc2626;">*</span></label>
                <input type="text" name="title" id="editMaterialTitle" required class="form-control" style="font-size: 12.5px;">
            </div>
            <div style="margin-bottom: 14px;">
                <label class="form-label" style="font-size: 12px; font-weight: 600; margin-bottom: 4px;">Deskripsi Singkat</label>
                <textarea name="description" id="editMaterialDescription" rows="2" class="form-control" style="font-size: 12px;"></textarea>
            </div>
            <div id="editVideoUrlContainer" style="margin-bottom: 14px; display: none;">
                <label class="form-label" style="font-size: 12px; font-weight: 600; margin-bottom: 4px;">Tautan Video (YouTube / Google Drive)</label>
                <input type="url" name="video_url" id="editMaterialVideoUrl" class="form-control" placeholder="https://www.youtube.com/watch?v=..." style="font-size: 12px;">
            </div>
            <div style="margin-bottom: 16px;">
                <label class="form-label" style="font-size: 12px; font-weight: 600; margin-bottom: 4px;">Ganti Berkas File (Opsional)</label>
                <input type="file" name="file" class="form-control" style="font-size: 12px;">
                <small style="color: var(--color-text-muted); font-size: 11px; display: block; margin-top: 3px;">Biarkan kosong jika tidak ingin mengubah berkas yang sudah diunggah.</small>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 8px;">
                <button type="button" class="btn btn-secondary" onclick="closeEditMaterialModal()" style="font-size: 12.5px; padding: 7px 14px;">Batal</button>
                <button type="submit" class="btn btn-primary" style="font-size: 12.5px; padding: 7px 16px; font-weight: 700;">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Penugasan -->
<div id="modalEditTask" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 1050; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #fff; border-radius: 12px; width: 100%; max-width: 580px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2); overflow: hidden;">
        <div style="padding: 16px 20px; background: var(--color-surface-subtle, #f8fafc); border-bottom: 1px solid var(--color-border); display: flex; justify-content: space-between; align-items: center;">
            <h4 style="margin: 0; font-size: 15px; font-weight: 700; color: var(--color-navy);">Edit Penugasan Pembelajaran</h4>
            <button type="button" onclick="closeEditTaskModal()" style="border: 0; background: transparent; font-size: 20px; cursor: pointer; color: #64748b;">&times;</button>
        </div>
        <form id="formEditTask" method="POST" enctype="multipart/form-data" style="padding: 20px;">
            @csrf
            @method('PUT')
            <div style="margin-bottom: 14px;">
                <label class="form-label" style="font-size: 12px; font-weight: 600; margin-bottom: 4px;">Judul Tugas <span style="color: #dc2626;">*</span></label>
                <input type="text" name="title" id="editTaskTitle" required class="form-control" style="font-size: 12.5px;">
            </div>
            <div style="margin-bottom: 14px;">
                <label class="form-label" style="font-size: 12px; font-weight: 600; margin-bottom: 4px;">Instruksi Pengerjaan <span style="color: #dc2626;">*</span></label>
                <textarea name="instructions" id="editTaskInstructions" rows="3" required class="form-control" style="font-size: 12px;"></textarea>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
                <div>
                    <label class="form-label" style="font-size: 12px; font-weight: 600; margin-bottom: 4px;">Waktu Mulai <span style="color: #dc2626;">*</span></label>
                    <input type="datetime-local" name="start_time" id="editTaskStartTime" required class="form-control" style="font-size: 12px;">
                    <small style="color: var(--color-text-muted); font-size: 10.5px; display: block; margin-top: 3px;">Siswa di kelas aktif dapat langsung mengumpulkan tugas.</small>
                </div>
                <div>
                    <label class="form-label" style="font-size: 12px; font-weight: 600; margin-bottom: 4px;">Batas Akhir (Tenggat) <span style="color: #dc2626;">*</span></label>
                    <input type="datetime-local" name="due_time" id="editTaskDueTime" required class="form-control" style="font-size: 12px;">
                </div>
            </div>
            <div style="margin-bottom: 14px;">
                <label class="form-label" style="font-size: 12px; font-weight: 600; margin-bottom: 4px;">Format Berkas yang Diizinkan</label>
                <input type="text" name="allowed_file_types" id="editTaskAllowedTypes" class="form-control" placeholder="pdf,docx,doc,ppt,pptx,zip,jpg,png" style="font-size: 12px;">
            </div>
            <div style="margin-bottom: 16px;">
                <label class="form-label" style="font-size: 12px; font-weight: 600; margin-bottom: 4px;">Ganti Berkas Soal / Lampiran (Opsional)</label>
                <input type="file" name="attachment" class="form-control" style="font-size: 12px;">
                <small style="color: var(--color-text-muted); font-size: 11px; display: block; margin-top: 3px;">Biarkan kosong bila tidak ingin mengganti lampiran berkas tugas.</small>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 8px;">
                <button type="button" class="btn btn-secondary" onclick="closeEditTaskModal()" style="font-size: 12.5px; padding: 7px 14px;">Batal</button>
                <button type="submit" class="btn btn-primary" style="font-size: 12.5px; padding: 7px 16px; font-weight: 700;">Simpan Perubahan Tugas</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditMaterialModal(materialId, title, description, type, videoUrl) {
    var form = document.getElementById('formEditMaterial');
    form.action = "{{ url('guru/kelas/' . $class->id . '/materi') }}/" + materialId;
    document.getElementById('editMaterialTitle').value = title || '';
    document.getElementById('editMaterialDescription').value = description || '';
    document.getElementById('editMaterialType').value = type || 'slide';
    document.getElementById('editMaterialVideoUrl').value = videoUrl || '';
    toggleEditMaterialInput(type);
    document.getElementById('modalEditMaterial').style.display = 'flex';
}

function closeEditMaterialModal() {
    document.getElementById('modalEditMaterial').style.display = 'none';
}

function toggleEditMaterialInput(type) {
    var videoContainer = document.getElementById('editVideoUrlContainer');
    if (type === 'video') {
        videoContainer.style.display = 'block';
    } else {
        videoContainer.style.display = 'none';
    }
}

function openEditTaskModal(taskId, title, instructions, startTime, dueTime, allowedTypes) {
    var form = document.getElementById('formEditTask');
    form.action = "{{ url('guru/kelas/' . $class->id . '/tugas') }}/" + taskId;
    document.getElementById('editTaskTitle').value = title || '';
    document.getElementById('editTaskInstructions').value = instructions || '';
    document.getElementById('editTaskStartTime').value = startTime || '';
    document.getElementById('editTaskDueTime').value = dueTime || '';
    document.getElementById('editTaskAllowedTypes').value = allowedTypes || '';
    document.getElementById('modalEditTask').style.display = 'flex';
}

function closeEditTaskModal() {
    document.getElementById('modalEditTask').style.display = 'none';
}

// Modal Konfirmasi Hapus Kelas (Detail)
function openDeleteClassDetailModal(classId, classTitle) {
    var modal = document.getElementById('deleteClassDetailModal');
    var form = document.getElementById('deleteClassDetailForm');
    var titleText = document.getElementById('deleteClassDetailTitleText');
    if (form) form.action = "{{ url('guru/kelas') }}/" + classId;
    if (titleText) titleText.textContent = classTitle;
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeDeleteClassDetailModal() {
    var modal = document.getElementById('deleteClassDetailModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// Modal Konfirmasi Hapus Materi
function openDeleteMaterialModal(materialId, materialTitle) {
    var modal = document.getElementById('deleteMaterialModal');
    var form = document.getElementById('deleteMaterialForm');
    var titleText = document.getElementById('deleteMaterialTitleText');
    if (form) form.action = "{{ url('guru/kelas/' . $class->id . '/materi') }}/" + materialId;
    if (titleText) titleText.textContent = materialTitle;
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeDeleteMaterialModal() {
    var modal = document.getElementById('deleteMaterialModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// Modal Konfirmasi Hapus Tugas
function openDeleteTaskModal(taskId, taskTitle) {
    var modal = document.getElementById('deleteTaskModal');
    var form = document.getElementById('deleteTaskForm');
    var titleText = document.getElementById('deleteTaskTitleText');
    if (form) form.action = "{{ url('guru/kelas/' . $class->id . '/tugas') }}/" + taskId;
    if (titleText) titleText.textContent = taskTitle;
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeDeleteTaskModal() {
    var modal = document.getElementById('deleteTaskModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

function handleGenericModalBackdropClick(e, modalId, closeFunc) {
    if (e.target === document.getElementById(modalId)) {
        closeFunc();
    }
}

// Deteksi dan sinkronisasi otomatis saat guru kembali fokus ke tab tugas
(function() {
    var lastHiddenTime = 0;
    var activeTab = "{{ $activeTab }}";

    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            lastHiddenTime = Date.now();
        } else {
            // Jika guru kembali fokus ke tab tugas setelah berpindah tab lebih dari 4 detik
            if (activeTab === 'tugas' && lastHiddenTime > 0 && (Date.now() - lastHiddenTime) > 4000) {
                var isModalOpen = document.querySelector('.modal-backdrop.active, [id^="modal"][style*="flex"], [id^="delete"][style*="flex"]');
                var isEditingForm = document.activeElement && (document.activeElement.tagName === 'INPUT' || document.activeElement.tagName === 'TEXTAREA');

                // Jika tidak sedang membuka modal atau mengetik form, lakukan pembaruan data
                if (!isModalOpen && !isEditingForm) {
                    var currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.set('tab', 'tugas');
                    currentUrl.searchParams.set('_sync', Date.now());
                    window.location.replace(currentUrl.toString());
                }
            }
        }
    });
})();
</script>

<!-- Modal Konfirmasi Hapus Kelas BK (Konsisten Desain Ruang BK) -->
<div id="deleteClassDetailModal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="deleteClassDetailModalTitle" onclick="handleGenericModalBackdropClick(event, 'deleteClassDetailModal', closeDeleteClassDetailModal)">
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
            <h3 id="deleteClassDetailModalTitle" class="modal-title">Konfirmasi Hapus Kelas BK</h3>
            <p class="modal-desc">
                Apakah Anda yakin ingin menghapus kelas BK <strong id="deleteClassDetailTitleText" style="color: var(--color-text-main);"></strong>?
            </p>
            <div style="background-color: var(--color-danger-bg); border: 1px solid var(--color-danger-border); border-radius: var(--radius-sm); padding: 10px 14px; margin-top: 14px; font-size: 12px; color: var(--color-danger); display: flex; align-items: flex-start; gap: 8px; line-height: 1.45;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0; margin-top: 2px;">
                    <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
                <span>Perhatian: Seluruh materi slide pembelajaran, video, data penugasan, berkas jawaban siswa, serta rekaman presensi kelas ini akan dihapus secara permanen.</span>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeDeleteClassDetailModal()">
                Batal
            </button>
            <form id="deleteClassDetailForm" method="POST" action="" style="display: inline; margin: 0;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    Ya, Hapus Kelas
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus Materi Pembelajaran -->
<div id="deleteMaterialModal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="deleteMaterialModalTitle" onclick="handleGenericModalBackdropClick(event, 'deleteMaterialModal', closeDeleteMaterialModal)">
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
            <h3 id="deleteMaterialModalTitle" class="modal-title">Konfirmasi Hapus Materi</h3>
            <p class="modal-desc">
                Apakah Anda yakin ingin menghapus materi pembelajaran <strong id="deleteMaterialTitleText" style="color: var(--color-text-main);"></strong>?
            </p>
            <div style="background-color: var(--color-danger-bg); border: 1px solid var(--color-danger-border); border-radius: var(--radius-sm); padding: 10px 14px; margin-top: 14px; font-size: 12px; color: var(--color-danger); display: flex; align-items: flex-start; gap: 8px; line-height: 1.45;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0; margin-top: 2px;">
                    <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
                <span>Berkas materi atau tautan video ini akan dihapus dari kelas dan siswa tidak lagi dapat mengaksesnya.</span>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeDeleteMaterialModal()">
                Batal
            </button>
            <form id="deleteMaterialForm" method="POST" action="" style="display: inline; margin: 0;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    Ya, Hapus Materi
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus Tugas Pembelajaran -->
<div id="deleteTaskModal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="deleteTaskModalTitle" onclick="handleGenericModalBackdropClick(event, 'deleteTaskModal', closeDeleteTaskModal)">
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
            <h3 id="deleteTaskModalTitle" class="modal-title">Konfirmasi Hapus Penugasan</h3>
            <p class="modal-desc">
                Apakah Anda yakin ingin menghapus penugasan <strong id="deleteTaskTitleText" style="color: var(--color-text-main);"></strong>?
            </p>
            <div style="background-color: var(--color-danger-bg); border: 1px solid var(--color-danger-border); border-radius: var(--radius-sm); padding: 10px 14px; margin-top: 14px; font-size: 12px; color: var(--color-danger); display: flex; align-items: flex-start; gap: 8px; line-height: 1.45;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0; margin-top: 2px;">
                    <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
                <span>Perhatian: Seluruh berkas jawaban yang telah dikumpulkan siswa beserta catatan penilaian akan dihapus secara permanen.</span>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeDeleteTaskModal()">
                Batal
            </button>
            <form id="deleteTaskForm" method="POST" action="" style="display: inline; margin: 0;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    Ya, Hapus Penugasan
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
