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
            <form action="{{ route('guru.kelas.destroy', $class->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kelas ini? Seluruh data materi, tugas, dan presensi akan dihapus permanen.');" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-secondary" style="font-size: 12.5px; padding: 8px 12px; color: #dc2626; border-color: #fecaca;" title="Hapus Kelas">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                </button>
            </form>
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

                        <div>
                            <form action="{{ route('guru.kelas.materi.destroy', ['id' => $class->id, 'materialId' => $mat->id]) }}" method="POST" onsubmit="return confirm('Hapus materi pembelajaran ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-secondary" style="color: #dc2626; border-color: #fecaca; padding: 6px 10px; font-size: 12px;" title="Hapus Materi">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                </button>
                            </form>
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
                    $subsCount = $task->submissions->count();
                    $gradedCount = $task->submissions->where('status', 'dinilai')->count();
                @endphp
                <div class="card" style="border: 1px solid var(--color-border); border-radius: 10px; background: #fff; overflow: hidden; box-shadow: var(--shadow-sm);">
                    <div style="padding: 16px 20px; border-bottom: 1px solid var(--color-border); display: flex; justify-content: space-between; align-items: flex-start; gap: 14px; flex-wrap: wrap;">
                        <div>
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                <span style="background: #e0f2fe; color: #0369a1; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 4px;">Penugasan BK</span>
                                <span style="font-size: 11.5px; color: var(--color-text-muted);">
                                    Batas Akhir: <strong>{{ $task->due_time ? $task->due_time->format('d M Y, H:i') : '-' }} WIB</strong>
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

                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span style="font-size: 12px; font-weight: 700; color: var(--color-navy); background: #f8fafc; padding: 6px 12px; border-radius: 6px; border: 1px solid var(--color-border);">
                                {{ $subsCount }} Siswa Mengumpulkan ({{ $gradedCount }} Dinilai)
                            </span>
                            <form action="{{ route('guru.kelas.tugas.destroy', ['id' => $class->id, 'taskId' => $task->id]) }}" method="POST" onsubmit="return confirm('Hapus penugasan ini beserta seluruh berkas jawaban siswa?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-secondary" style="color: #dc2626; border-color: #fecaca; padding: 6px 10px; font-size: 12px;" title="Hapus Tugas">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                </button>
                            </form>
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
                                        $subMap = $task->submissions->keyBy('student_id');
                                    @endphp
                                    @forelse($targetStudents as $st)
                                    @php
                                        $submission = $subMap->get($st->id);
                                    @endphp
                                    <tr style="border-bottom: 1px solid var(--color-border);">
                                        <td style="padding: 10px;">
                                            <div style="font-weight: 700; color: var(--color-text-main);">{{ $st->name }}</div>
                                            <div style="font-size: 11px; color: var(--color-text-muted);">
                                                NISN: {{ $st->nisn }} &bull; {{ optional($st->studentClass)->name }}
                                            </div>
                                        </td>
                                        <td style="padding: 10px; white-space: nowrap;">
                                            @if($submission)
                                                {{ $submission->submitted_at ? $submission->submitted_at->format('d/m/Y H:i') : '-' }}
                                            @else
                                                <span style="color: #94a3b8;">Belum mengumpulkan</span>
                                            @endif
                                        </td>
                                        <td style="padding: 10px;">
                                            @if($submission)
                                                @if($submission->file_path)
                                                <a href="{{ route('guru.kelas.tugas.download_submission', ['id' => $class->id, 'submissionId' => $submission->id]) }}" style="color: var(--color-primary); font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                                    <span>{{ Str::limit($submission->file_name ?: 'Berkas Tugas', 25) }}</span>
                                                </a>
                                                @endif
                                                @if($submission->submission_text)
                                                <div style="font-size: 11.5px; color: var(--color-text-muted); margin-top: 3px;">
                                                    "{{ Str::limit($submission->submission_text, 60) }}"
                                                </div>
                                                @endif
                                            @else
                                                <span style="color: #94a3b8;">-</span>
                                            @endif
                                        </td>
                                        <td style="padding: 10px;">
                                            @if($submission)
                                                @if($submission->status === 'dinilai')
                                                    <span style="background: #dcfce7; color: #15803d; font-weight: 700; padding: 2px 7px; border-radius: 4px;">Dinilai</span>
                                                @elseif($submission->status === 'terlambat')
                                                    <span style="background: #fee2e2; color: #991b1b; font-weight: 600; padding: 2px 7px; border-radius: 4px;">Terlambat</span>
                                                @elseif($submission->status === 'perlu_revisi')
                                                    <span style="background: #fef3c7; color: #92400e; font-weight: 600; padding: 2px 7px; border-radius: 4px;">Perlu Revisi</span>
                                                @else
                                                    <span style="background: #e0f2fe; color: #0369a1; font-weight: 600; padding: 2px 7px; border-radius: 4px;">Diserahkan</span>
                                                @endif
                                            @else
                                                <span style="background: #f1f5f9; color: #64748b; padding: 2px 7px; border-radius: 4px;">Belum Kumpul</span>
                                            @endif
                                        </td>
                                        <td style="padding: 10px;">
                                            @if($submission && $submission->score !== null)
                                                <span style="font-weight: 800; font-size: 13px; color: var(--color-navy);">Nilai: {{ $submission->score }}</span>
                                                @if($submission->counselor_feedback)
                                                <div style="font-size: 11px; color: var(--color-text-muted);">{{ Str::limit($submission->counselor_feedback, 40) }}</div>
                                                @endif
                                            @else
                                                <span style="color: #94a3b8;">Belum dinilai</span>
                                            @endif
                                        </td>
                                        <td style="padding: 10px; text-align: center;">
                                            @if($submission)
                                            <button type="button" class="btn btn-secondary" style="font-size: 11px; padding: 4px 8px;" onclick="openGradeModal({{ $submission->id }}, '{{ addslashes($st->name) }}', '{{ $submission->score }}', '{{ addslashes($submission->counselor_feedback) }}', '{{ $submission->status }}')">
                                                Nilai
                                            </button>
                                            @else
                                            <span style="color: #cbd5e1;">-</span>
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
                        <input type="datetime-local" name="start_time" value="{{ \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d\TH:i') }}" required class="form-control" style="font-size: 12px;">
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

<!-- Modal Penilaian Tugas Siswa -->
<div id="gradingModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.5); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #fff; border-radius: 10px; max-width: 440px; width: 100%; box-shadow: var(--shadow-lg); overflow: hidden;">
        <div style="padding: 14px 18px; background: var(--color-navy); color: #fff; display: flex; justify-content: space-between; align-items: center;">
            <h4 style="font-size: 14px; font-weight: 700; margin: 0; color: #fff;">Penilaian Tugas Siswa</h4>
            <button type="button" onclick="closeGradeModal()" style="background: none; border: 0; color: #cbd5e1; cursor: pointer; font-size: 18px;">&times;</button>
        </div>
        <form id="gradeForm" action="" method="POST" style="padding: 18px;">
            @csrf
            <div style="margin-bottom: 12px;">
                <label style="font-size: 11.5px; color: var(--color-text-muted); display: block; margin-bottom: 2px;">Nama Siswa</label>
                <div id="gradeStudentName" style="font-size: 13.5px; font-weight: 700; color: var(--color-navy);"></div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
                <div>
                    <label class="form-label" style="font-size: 12px; font-weight: 600; margin-bottom: 4px;">Nilai Angka (0-100)</label>
                    <input type="number" min="0" max="100" name="score" id="gradeScoreInput" class="form-control" placeholder="0 - 100" style="font-size: 13px;">
                </div>
                <div>
                    <label class="form-label" style="font-size: 12px; font-weight: 600; margin-bottom: 4px;">Status Penilaian</label>
                    <select name="status" id="gradeStatusSelect" class="form-select" style="font-size: 12.5px;">
                        <option value="dinilai">Telah Dinilai</option>
                        <option value="perlu_revisi">Perlu Revisi</option>
                        <option value="diserahkan">Diserahkan</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 16px;">
                <label class="form-label" style="font-size: 12px; font-weight: 600; margin-bottom: 4px;">Catatan / Umpan Balik Guru BK</label>
                <textarea name="counselor_feedback" id="gradeFeedbackInput" rows="3" class="form-control" placeholder="Apresiasi atau catatan bimbingan untuk siswa..." style="font-size: 12.5px;"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 8px;">
                <button type="button" class="btn btn-secondary" onclick="closeGradeModal()" style="font-size: 12.5px; padding: 7px 14px;">Batal</button>
                <button type="submit" class="btn btn-primary" style="font-size: 12.5px; padding: 7px 16px; font-weight: 700;">Simpan Nilai</button>
            </div>
        </form>
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

function openGradeModal(submissionId, studentName, score, feedback, status) {
    var form = document.getElementById('gradeForm');
    form.action = "{{ url('guru/kelas/' . $class->id . '/tugas/submission') }}/" + submissionId + "/nilai";
    document.getElementById('gradeStudentName').textContent = studentName;
    document.getElementById('gradeScoreInput').value = score || '';
    document.getElementById('gradeFeedbackInput').value = feedback || '';
    document.getElementById('gradeStatusSelect').value = status || 'dinilai';
    document.getElementById('gradingModal').style.display = 'flex';
}

function closeGradeModal() {
    document.getElementById('gradingModal').style.display = 'none';
}
</script>
@endsection
