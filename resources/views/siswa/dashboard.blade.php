@extends('layouts.siswa')

@section('title', 'Ruang BK - ' . $student->name)

@section('content')
<!-- Hero Welcome Section -->
<div style="background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%); color: #ffffff; padding: 32px 28px; border-radius: var(--radius-lg); margin-bottom: 24px; box-shadow: var(--shadow-md);">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <span style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; background: rgba(255,255,255,0.15); padding: 4px 10px; border-radius: 20px;">
                Ruang Mandiri Siswa
            </span>
            <h1 style="font-size: 22px; font-weight: 800; margin-top: 10px;">Halo, {{ $student->name }}!</h1>
            <p style="font-size: 13px; color: #cbd5e1; margin-top: 4px; max-width: 620px; line-height: 1.5;">
                Selamat datang di platform pendampingan BK. Kenali minat dan bakatmu, konsultasikan cita-citamu, dan temukan rute terbaik menuju perguruan tinggi atau dunia kerja idamanmu.
            </p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('siswa.konseling.create') }}" class="btn btn-secondary" style="background: #ffffff; color: #1e3a8a; border: none; font-weight: 700;">
                + Ajukan Konseling
            </a>
            <a href="{{ route('siswa.rencana.show') }}" class="btn" style="background: rgba(255,255,255,0.15); color: #ffffff; border: 1px solid rgba(255,255,255,0.3); font-weight: 600;">
                Rute Masa Depan
            </a>
        </div>
    </div>
</div>

<div class="grid-2" style="margin-bottom: 24px;">
    <!-- Sesi Konseling Saya Terkini -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Layanan Bimbingan Konseling Saya</h3>
            <a href="{{ route('siswa.konseling.index') }}" class="btn btn-secondary btn-sm">Lihat Semua</a>
        </div>
        <div class="card-body">
            @if($upcomingCounseling)
                <div style="padding: 16px; background: #f8fafc; border: 1px solid var(--color-border); border-radius: var(--radius-md);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <span class="badge badge-primary">{{ ucfirst(str_replace('_', ' ', $upcomingCounseling->status)) }}</span>
                        <small style="color: var(--color-text-subtle);">{{ $upcomingCounseling->code }}</small>
                    </div>
                    <div style="font-weight: 700; font-size: 14px; color: var(--color-text-main); margin-bottom: 4px;">
                        {{ $upcomingCounseling->topic }}
                    </div>
                    <p style="font-size: 12px; color: var(--color-text-muted); margin-bottom: 12px;">
                        Kategori: <strong>{{ $upcomingCounseling->category ? $upcomingCounseling->category->name : 'Umum' }}</strong>
                    </p>

                    @if($upcomingCounseling->status === 'dijadwalkan')
                        <div style="padding: 10px 12px; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: var(--radius-sm); font-size: 12px; color: #1e3a8a; margin-bottom: 12px;">
                            <strong>Jadwal Pertemuan Ditetapkan:</strong><br>
                            {{ $upcomingCounseling->scheduled_date ? $upcomingCounseling->scheduled_date->translatedFormat('l, d F Y') : '-' }} pukul {{ substr($upcomingCounseling->scheduled_time, 0, 5) }} WIB di {{ $upcomingCounseling->scheduled_location }}
                        </div>
                    @endif

                    <a href="{{ route('siswa.konseling.show', $upcomingCounseling->id) }}" class="btn btn-primary btn-sm" style="width: 100%;">
                        Buka Rincian Sesi Konseling
                    </a>
                </div>
            @else
                <div class="empty-state" style="padding: 24px 16px;">
                    <p class="empty-state-title">Tidak Ada Sesi Konseling Aktif</p>
                    <p class="empty-state-desc">Punya hal yang ingin diceritakan mengenai belajar, karier, atau pertemanan? Guru BK selalu siap mendengarkan.</p>
                    <a href="{{ route('siswa.konseling.create') }}" class="btn btn-secondary btn-sm">Ajukan Konseling Baru</a>
                </div>
            @endif
        </div>
    </div>

    <!-- Rute Masa Depan Saya (Section 38) -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Rencana Masa Depan Saya</h3>
            <a href="{{ route('siswa.rencana.show') }}" class="btn btn-secondary btn-sm">Perbarui Rute</a>
        </div>
        <div class="card-body">
            @if($student->futurePlan && $student->futurePlan->primary_goal !== 'belum_menentukan')
                <div style="padding: 16px; background: #f0fdfa; border: 1px solid var(--color-accent-border); border-radius: var(--radius-md);">
                    <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--color-accent); letter-spacing: 0.05em;">
                        Pilihan Arah Karier:
                    </div>
                    <div style="font-size: 16px; font-weight: 800; color: var(--color-text-main); margin-top: 4px; margin-bottom: 8px;">
                        @if($student->futurePlan->primary_goal === 'kuliah')
                            Melanjutkan Kuliah di Perguruan Tinggi
                        @elseif($student->futurePlan->primary_goal === 'bekerja')
                            Bekerja di Dunia Industri
                        @elseif($student->futurePlan->primary_goal === 'wirausaha')
                            Membangun Bisnis & Wirausaha
                        @else
                            Kuliah Sambil Bekerja
                        @endif
                    </div>

                    @if($student->futurePlan->primary_goal === 'kuliah')
                        <div style="font-size: 13px; color: var(--color-text-main);">
                            Target: <strong>{{ $student->futurePlan->college_target ?: '-' }}</strong><br>
                            Program Studi: {{ $student->futurePlan->study_program ?: '-' }} (Jalur {{ $student->futurePlan->entry_path ?: 'SNBP/SNBT' }})
                        </div>
                    @elseif($student->futurePlan->primary_goal === 'bekerja')
                        <div style="font-size: 13px; color: var(--color-text-main);">
                            Target Perusahaan: <strong>{{ $student->futurePlan->work_target_company ?: '-' }}</strong><br>
                            Bidang: {{ $student->futurePlan->work_target_field ?: '-' }}
                        </div>
                    @elseif($student->futurePlan->primary_goal === 'kuliah_kerja')
                        <div style="font-size: 13px; color: var(--color-text-main);">
                            Target Kampus: <strong>{{ $student->futurePlan->college_target ?: '-' }}</strong> (Prodi: {{ $student->futurePlan->study_program ?: '-' }})<br>
                            Target Karier: {{ $student->futurePlan->work_target_field ?: ($student->futurePlan->work_target_company ?: '-') }} ({{ $student->futurePlan->entry_path ?: 'Fleksibel' }})
                        </div>
                    @elseif($student->futurePlan->primary_goal === 'wirausaha')
                        <div style="font-size: 13px; color: var(--color-text-main);">
                            Bidang Usaha: <strong>{{ $student->futurePlan->business_field ?: '-' }}</strong><br>
                            Ide: {{ $student->futurePlan->business_idea ?: '-' }}
                        </div>
                    @endif

                    <div style="margin-top: 14px;">
                        <a href="{{ route('siswa.rencana.show') }}" class="btn btn-accent btn-sm" style="width: 100%;">
                            Lihat Rekomendasi Kampus & Peluang Terkait
                        </a>
                    </div>
                </div>
            @else
                <div class="empty-state" style="padding: 24px 16px;">
                    <p class="empty-state-title">Belum Menentukan Arah Masa Depan</p>
                    <p class="empty-state-desc">Tentukan rute masa depan Anda (Kuliah, Bekerja, Kuliah Sambil Bekerja, atau Wirausaha) untuk mendapatkan rekomendasi beasiswa, magang, dan karier yang relevan.</p>
                    <a href="{{ route('siswa.rencana.show') }}" class="btn btn-primary btn-sm">Tentukan Rute Karier</a>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="grid-2">
    <!-- Asesmen Diri Tersedia -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Asesmen Minat & Kepribadian</h3>
            <a href="{{ route('siswa.asesmen.index') }}" class="btn btn-secondary btn-sm">Semua Asesmen</a>
        </div>
        <div class="card-body">
            @foreach($availableAssessments as $asm)
                @php
                    $result = $asm->studentResults->first();
                @endphp
                <div style="padding: 12px; background: #f8fafc; border: 1px solid var(--color-border); border-radius: var(--radius-sm); margin-bottom: 10px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <strong style="font-size: 13px;">{{ $asm->title }}</strong>
                        @if($result)
                            <span class="badge badge-success">Selesai: {{ $result->result_category }}</span>
                        @else
                            <span class="badge badge-warning">Belum Dikerjakan</span>
                        @endif
                    </div>
                    <p style="font-size: 12px; color: var(--color-text-muted); margin-top: 4px;">{{ $asm->description }}</p>
                    <div style="margin-top: 10px;">
                        @if($result)
                            <a href="{{ route('siswa.asesmen.result', $result->id) }}" class="btn btn-secondary btn-sm">Lihat Analisis Hasil</a>
                        @else
                            <a href="{{ route('siswa.asesmen.take', $asm->id) }}" class="btn btn-primary btn-sm">Kerjakan Asesmen Sekarang</a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Peluang & Beasiswa Pilihan -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Peluang Beasiswa & Magang Terbaru</h3>
            <a href="{{ route('siswa.peluang.index') }}" class="btn btn-secondary btn-sm">Buka Semua Peluang</a>
        </div>
        <div class="card-body">
            @if($opportunities->count() > 0)
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    @foreach($opportunities as $op)
                        <div style="padding: 12px; border: 1px solid var(--color-border); border-radius: var(--radius-sm); background: #ffffff;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <strong style="font-size: 13px;">{{ $op->title }}</strong>
                                <span class="badge badge-secondary">{{ ucfirst(str_replace('_', ' ', $op->type)) }}</span>
                            </div>
                            <div style="font-size: 12px; color: var(--color-text-muted); margin-top: 2px;">
                                Mitra: {{ $op->partner ? $op->partner->name : 'Sekolah' }} | Batas: {{ $op->deadline ? $op->deadline->translatedFormat('d M Y') : 'Terbuka' }}
                            </div>
                            <div style="margin-top: 8px;">
                                <a href="{{ route('siswa.peluang.show', $op->id) }}" class="btn btn-secondary btn-sm">Lihat Persyaratan & Daftar</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <p class="empty-state-title">Belum Ada Peluang Aktif</p>
                    <p class="empty-state-desc">Peluang terbaru dari mitra perguruan tinggi dan industri akan tampil di sini.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
