@extends('layouts.siswa')

@section('title', $class->title . ' - Kelas BK Siswa')
@section('header_title', $class->title)

@section('content')
<div style="max-width: 960px; margin: 0 auto 30px auto;">
    <!-- Breadcrumb & Navigasi Kembali -->
    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px;">
        <a href="{{ route('siswa.kelas.index') }}" style="font-size: 12.5px; color: var(--color-primary); text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
            <span>Daftar Kelas BK</span>
        </a>
        <span style="color: var(--color-text-muted); font-size: 12px;">/</span>
        <span style="font-size: 12.5px; color: var(--color-text-muted);">Ruang Kelas Online</span>
    </div>

    <!-- Hero Card Kelas -->
    <div class="card" style="border: 1px solid var(--color-border); border-radius: 12px; background: #fff; overflow: hidden; margin-bottom: 20px; box-shadow: var(--shadow-sm);">
        <div style="padding: 22px 24px; border-bottom: 1px solid var(--color-border);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; flex-wrap: wrap;">
                <div>
                    @php
                        $compStatus = $class->computed_status;
                    @endphp
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                        @if($compStatus === 'berlangsung')
                            <span style="display: inline-flex; align-items: center; gap: 5px; background: #dcfce7; color: #166534; font-size: 11.5px; font-weight: 700; padding: 3px 9px; border-radius: 4px; border: 1px solid #86efac;">
                                <span style="width: 7px; height: 7px; border-radius: 50%; background: #16a34a;"></span>
                                Sedang Berlangsung
                            </span>
                        @elseif($compStatus === 'terjadwal')
                            <span style="display: inline-flex; align-items: center; gap: 5px; background: #dbeafe; color: #1e40af; font-size: 11.5px; font-weight: 700; padding: 3px 9px; border-radius: 4px; border: 1px solid #93c5fd;">
                                Terjadwal
                            </span>
                        @else
                            <span style="background: #f1f5f9; color: #475569; font-size: 11.5px; font-weight: 600; padding: 3px 9px; border-radius: 4px; border: 1px solid #cbd5e1;">
                                Selesai
                            </span>
                        @endif

                        <span style="font-size: 12px; color: var(--color-text-muted);">
                            Kelas BK Online
                        </span>
                    </div>

                    <h2 style="font-size: 22px; font-weight: 800; color: var(--color-navy); margin: 0 0 8px 0; line-height: 1.35;">
                        {{ $class->title }}
                    </h2>

                    <div style="font-size: 13px; color: var(--color-text-muted); margin-bottom: 12px;">
                        Guru BK Pengampu: <strong style="color: var(--color-text-main);">{{ $class->counselor->name }}</strong>
                    </div>

                    @if($class->description)
                    <div style="font-size: 13px; color: var(--color-text-main); line-height: 1.5; max-width: 780px;">
                        {{ $class->description }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div style="padding: 12px 24px; background: #f8fafc; display: flex; gap: 20px; font-size: 12.5px; color: var(--color-text-muted); flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 6px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                <span>{{ \Carbon\Carbon::parse($class->class_date)->translatedFormat('l, d F Y') }}</span>
            </div>
            <div style="display: flex; align-items: center; gap: 6px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                <span>Waktu Kelas: {{ substr($class->start_time, 0, 5) }} - {{ substr($class->end_time, 0, 5) }} WIB</span>
            </div>
            <div style="display: flex; align-items: center; gap: 6px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                <span>Rentang Presensi: {{ substr($class->attendance_start_time, 0, 5) }} - {{ substr($class->attendance_end_time, 0, 5) }} WIB</span>
            </div>
        </div>
    </div>

    <!-- KARTU PRESENSI KEHADIRAN SISWA -->
    @php
        $attStatus = $class->attendance_status;
    @endphp
    <div class="card" style="border: 1px solid var(--color-border); border-radius: 12px; background: #fff; overflow: hidden; margin-bottom: 24px; box-shadow: var(--shadow-sm);">
        <div style="padding: 16px 22px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
            <div style="display: flex; align-items: center; gap: 14px;">
                @if($attendance)
                    <div style="width: 44px; height: 44px; border-radius: 50%; background: #dcfce7; color: #15803d; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <div>
                        <div style="font-size: 14px; font-weight: 800; color: #15803d;">
                            Status Presensi: {{ $attendance->status_label }}
                        </div>
                        <div style="font-size: 12px; color: var(--color-text-muted);">
                            Dicatat pada {{ $attendance->attended_at ? $attendance->attended_at->format('d F Y, H:i:s') : '-' }} WIB
                            @if($attendance->is_manual_correction)
                                &bull; <em style="color: #b45309;">(Koreksi Guru BK: {{ $attendance->correction_reason }})</em>
                            @endif
                        </div>
                    </div>
                @elseif($isAttendanceOpen)
                    <div style="width: 44px; height: 44px; border-radius: 50%; background: #eff6ff; color: var(--color-primary); display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    </div>
                    <div>
                        <div style="font-size: 14px; font-weight: 800; color: var(--color-navy);">
                            Waktu Absensi Sedang Dibuka
                        </div>
                        <div style="font-size: 12px; color: var(--color-text-muted);">
                            Batas akhir absensi: <strong>{{ substr($class->attendance_end_time, 0, 5) }} WIB</strong>. Silakan klik tombol untuk mencatat kehadiran.
                        </div>
                    </div>
                @elseif($attStatus === 'ditutup')
                    <div style="width: 44px; height: 44px; border-radius: 50%; background: #fef2f2; color: #dc2626; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                    </div>
                    <div>
                        <div style="font-size: 14px; font-weight: 800; color: #991b1b;">
                            Waktu Absensi Telah Berakhir
                        </div>
                        <div style="font-size: 12px; color: #b91c1c;">
                            Waktu absensi telah berakhir. Anda tidak dapat melakukan absensi untuk kelas ini.
                        </div>
                    </div>
                @else
                    <div style="width: 44px; height: 44px; border-radius: 50%; background: #f8fafc; color: #64748b; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    </div>
                    <div>
                        <div style="font-size: 14px; font-weight: 800; color: var(--color-navy);">
                            Waktu Absensi Belum Dibuka
                        </div>
                        <div style="font-size: 12px; color: var(--color-text-muted);">
                            Absensi akan dibuka pada pukul <strong>{{ substr($class->attendance_start_time, 0, 5) }} WIB</strong>.
                        </div>
                    </div>
                @endif
            </div>

            <div>
                @if(!$attendance && $isAttendanceOpen)
                    <form action="{{ route('siswa.kelas.attendance.store', $class->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary" style="font-weight: 800; font-size: 13.5px; padding: 10px 22px; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <span>Absen Sekarang</span>
                        </button>
                    </form>
                @elseif(!$attendance && $attStatus === 'ditutup')
                    <button type="button" class="btn btn-secondary" disabled style="opacity: 0.6; cursor: not-allowed; font-size: 12.5px; padding: 8px 16px;">
                        <span>Absensi Ditutup</span>
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- BAGIAN 1: MATERI & VIDEO PEMBELAJARAN -->
    <div style="margin-bottom: 30px;">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 14px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--color-navy);"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
            <h3 style="font-size: 16px; font-weight: 800; color: var(--color-navy); margin: 0;">
                Materi &amp; Video Pembelajaran
            </h3>
        </div>

        @if($class->materials->isEmpty())
        <div class="card" style="padding: 24px; text-align: center; border: 1px dashed var(--color-border); border-radius: 10px; background: #fff;">
            <p style="font-size: 12.5px; color: var(--color-text-muted); margin: 0;">
                Guru BK belum mengunggah materi atau video pembelajaran untuk sesi kelas ini.
            </p>
        </div>
        @else
        <div style="display: flex; flex-direction: column; gap: 14px;">
            @foreach($class->materials as $mat)
            <div class="card" style="border: 1px solid var(--color-border); border-radius: 10px; background: #fff; padding: 18px 22px; box-shadow: var(--shadow-sm);">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                    @if($mat->material_type === 'slide')
                        <span style="background: #fef3c7; color: #92400e; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 4px;">Slide Pembelajaran</span>
                    @elseif($mat->material_type === 'video')
                        <span style="background: #fee2e2; color: #991b1b; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 4px;">Video Pembelajaran</span>
                    @else
                        <span style="background: #e0f2fe; color: #075985; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 4px;">Dokumen Pendukung</span>
                    @endif
                </div>

                <h4 style="font-size: 15px; font-weight: 700; color: var(--color-navy); margin: 0 0 6px 0;">
                    {{ $mat->title }}
                </h4>

                @if($mat->description)
                <p style="font-size: 12.5px; color: var(--color-text-muted); margin: 0 0 10px 0; line-height: 1.45;">
                    {{ $mat->description }}
                </p>
                @endif

                <!-- Embedded Video Player jika materi tipe video -->
                @if($mat->material_type === 'video' && $mat->youtube_embed_url)
                <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 8px; margin: 12px 0 14px 0; background: #000;">
                    <iframe src="{{ $mat->youtube_embed_url }}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;" allowfullscreen></iframe>
                </div>
                @elseif($mat->material_type === 'video' && $mat->video_url)
                <div style="margin: 8px 0 12px 0;">
                    <a href="{{ $mat->video_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary" style="font-size: 12px; padding: 7px 14px; display: inline-flex; align-items: center; gap: 6px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                        <span>Tonton Video Pembelajaran (Tautan Eksternal)</span>
                    </a>
                </div>
                @endif

                <!-- Unduh Berkas Slide / Dokumen -->
                @if($mat->file_path)
                <div style="margin-top: 8px;">
                    <a href="{{ route('siswa.kelas.materi.download', ['id' => $class->id, 'materialId' => $mat->id]) }}" class="btn btn-secondary" style="font-size: 12px; padding: 7px 14px; display: inline-flex; align-items: center; gap: 6px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        <span>Unduh Berkas Pembelajaran ({{ $mat->file_name }} &bull; {{ $mat->formatted_file_size }})</span>
                    </a>
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <!-- BAGIAN 2: TUGAS & PENGUMPULAN SISWA -->
    <div>
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 14px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--color-navy);"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
            <h3 style="font-size: 16px; font-weight: 800; color: var(--color-navy); margin: 0;">
                Tugas Pembelajaran BK
            </h3>
        </div>

        @if($class->tasks->isEmpty())
        <div class="card" style="padding: 24px; text-align: center; border: 1px dashed var(--color-border); border-radius: 10px; background: #fff;">
            <p style="font-size: 12.5px; color: var(--color-text-muted); margin: 0;">
                Tidak ada tugas yang harus dikumpulkan untuk sesi kelas ini.
            </p>
        </div>
        @else
        <div style="display: flex; flex-direction: column; gap: 18px;">
            @foreach($class->tasks as $task)
            @php
                $submission = $task->submissionFor($student);
                $isTaskExpired = $task->isExpired();
            @endphp
            <div class="card" style="border: 1px solid var(--color-border); border-radius: 12px; background: #fff; overflow: hidden; box-shadow: var(--shadow-sm);">
                <div style="padding: 18px 22px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: flex-start; gap: 14px; flex-wrap: wrap;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px; flex-wrap: wrap;">
                            <span style="background: #e0f2fe; color: #0369a1; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 4px;">Tugas BK</span>
                            @if($task->start_time)
                            <span style="font-size: 11.5px; color: var(--color-text-muted);">
                                Waktu Mulai: <strong style="color: var(--color-text-main);">{{ $task->start_time->format('d M Y, H:i') }} WIB</strong>
                            </span>
                            <span style="color: #cbd5e1;">&bull;</span>
                            @endif
                            <span style="font-size: 11.5px; color: var(--color-text-muted);">
                                Batas Pengumpulan: <strong style="color: var(--color-text-main);">{{ $task->due_time ? $task->due_time->format('d M Y, H:i') : '-' }} WIB</strong>
                            </span>
                        </div>
                        <h4 style="font-size: 16px; font-weight: 700; color: var(--color-navy); margin: 0 0 6px 0;">
                            {{ $task->title }}
                        </h4>
                        <div style="font-size: 13px; color: var(--color-text-main); line-height: 1.5; margin-bottom: 12px;">
                            {!! nl2br(e($task->instructions)) !!}
                        </div>

                        @if($task->attachment_path)
                        <a href="{{ route('siswa.kelas.tugas.download_attachment', ['id' => $class->id, 'taskId' => $task->id]) }}" class="btn btn-secondary" style="font-size: 11.5px; padding: 6px 12px; display: inline-flex; align-items: center; gap: 5px;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                            <span>Unduh Lembar Kerja / Lampiran Guru ({{ $task->attachment_name }})</span>
                        </a>
                        @endif
                    </div>

                    <div>
                        @if($submission)
                            @if($submission->status === 'dinilai')
                                <span style="background: #dcfce7; color: #15803d; font-size: 12px; font-weight: 700; padding: 4px 10px; border-radius: 5px; border: 1px solid #86efac;">
                                    Telah Dinilai: {{ $submission->score }}/100
                                </span>
                            @elseif($submission->status === 'terlambat')
                                <span style="background: #fee2e2; color: #991b1b; font-size: 12px; font-weight: 700; padding: 4px 10px; border-radius: 5px;">
                                    Terkumpul (Terlambat)
                                </span>
                            @elseif($submission->status === 'perlu_revisi')
                                <span style="background: #fef3c7; color: #92400e; font-size: 12px; font-weight: 700; padding: 4px 10px; border-radius: 5px;">
                                    Perlu Revisi
                                </span>
                            @else
                                <span style="background: #e0f2fe; color: #0369a1; font-size: 12px; font-weight: 700; padding: 4px 10px; border-radius: 5px;">
                                    Telah Dikumpulkan
                                </span>
                            @endif
                        @else
                            <span style="background: #f1f5f9; color: #64748b; font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 5px;">
                                Belum Mengumpulkan
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Bagian Form Pengumpulan / Detail Jawaban Siswa -->
                <div style="padding: 18px 22px; background: #fafbfc;">
                    @if($submission)
                    <div style="margin-bottom: 14px; padding: 14px; background: #fff; border: 1px solid var(--color-border); border-radius: 8px;">
                        <div style="font-size: 12.5px; font-weight: 700; color: var(--color-navy); margin-bottom: 6px;">
                            Jawaban Tugas yang Anda Kumpulkan:
                        </div>
                        <div style="font-size: 12px; color: var(--color-text-muted); margin-bottom: 8px;">
                            Dikirim pada: {{ $submission->submitted_at ? $submission->submitted_at->format('d F Y, H:i:s') : '-' }} WIB
                        </div>

                        @if($submission->submission_text)
                        <div style="font-size: 12.5px; color: var(--color-text-main); line-height: 1.5; margin-bottom: 10px; padding: 8px; background: #f8fafc; border-radius: 6px;">
                            {!! nl2br(e($submission->submission_text)) !!}
                        </div>
                        @endif

                        @if($submission->file_path)
                        <div>
                            <a href="{{ route('siswa.kelas.tugas.download_my_submission', ['id' => $class->id, 'taskId' => $task->id]) }}" class="btn btn-secondary" style="font-size: 12px; padding: 6px 12px; display: inline-flex; align-items: center; gap: 6px;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                <span>Unduh Berkas yang Anda Kirim ({{ $submission->file_name }} &bull; {{ $submission->formatted_file_size }})</span>
                            </a>
                        </div>
                        @endif

                        @if($submission->counselor_feedback)
                        <div style="margin-top: 12px; padding-top: 10px; border-top: 1px solid #f1f5f9;">
                            <div style="font-size: 12px; font-weight: 700; color: #166534; margin-bottom: 2px;">
                                Catatan / Umpan Balik Guru BK:
                            </div>
                            <div style="font-size: 12.5px; color: var(--color-text-main); font-style: italic;">
                                "{{ $submission->counselor_feedback }}"
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    @if(!$task->isOpen())
                    <div style="padding: 16px; background: #fffbeb; border: 1px solid #fef3c7; border-radius: 8px; text-align: center;">
                        <div style="font-size: 13px; font-weight: 700; color: #92400e; margin-bottom: 4px;">
                            Waktu Pengerjaan Tugas Belum Dibuka
                        </div>
                        <div style="font-size: 12px; color: #b45309;">
                            Tugas ini dijadwalkan dibuka untuk pengumpulan pada tanggal <strong>{{ $task->start_time ? $task->start_time->format('d F Y, H:i') : '-' }} WIB</strong>.
                        </div>
                    </div>
                    @else
                    <!-- Formulir Pengumpulan atau Revisi Tugas -->
                    <div>
                        <div style="font-size: 13px; font-weight: 700; color: var(--color-navy); margin-bottom: 8px;">
                            {{ $submission ? 'Perbarui / Kirim Ulang Jawaban Tugas' : 'Kirim Jawaban Tugas' }}
                        </div>

                        <form action="{{ route('siswa.kelas.tugas.submit', ['id' => $class->id, 'taskId' => $task->id]) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div style="margin-bottom: 12px;">
                                <label class="form-label" style="font-size: 12px; font-weight: 600; margin-bottom: 4px;">
                                    Catatan / Jawaban Teks
                                </label>
                                <textarea name="submission_text" rows="3" class="form-control" placeholder="Tuliskan jawaban atau rangkuman tugas Anda di sini..." style="font-size: 12.5px;">{{ $submission ? $submission->submission_text : '' }}</textarea>
                            </div>

                            <div style="margin-bottom: 14px;">
                                <label class="form-label" style="font-size: 12px; font-weight: 600; margin-bottom: 4px;">
                                    Unggah Berkas Tugas (PDF, Dokumen, Presentasi, Gambar)
                                </label>
                                <input type="file" name="file" class="form-control" style="font-size: 12.5px;">
                                <small style="color: var(--color-text-muted); font-size: 11px; display: block; margin-top: 3px;">
                                    Format diizinkan: {{ $task->allowed_file_types }}. Maksimal 20 MB.
                                </small>
                            </div>

                            <div style="display: flex; justify-content: flex-end;">
                                <button type="submit" class="btn btn-primary" style="font-size: 12.5px; font-weight: 700; padding: 8px 18px; display: inline-flex; align-items: center; gap: 6px;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    <span>{{ $submission ? 'Kirim Ulang Jawaban' : 'Kirim Jawaban Tugas' }}</span>
                                </button>
                            </div>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
