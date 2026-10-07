@extends('layouts.siswa')

@section('title', 'Detail Permohonan Konseling - ' . $counseling->code)

@section('content')
<div style="max-width: 760px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
        <div>
            <div style="display: flex; align-items: center; gap: 10px;">
                <h2 style="font-size: 20px; font-weight: 800; color: var(--color-text-main);">{{ $counseling->code }}</h2>
                <span class="badge badge-primary">{{ ucfirst(str_replace('_', ' ', $counseling->status)) }}</span>
            </div>
            <p style="font-size: 13px; color: var(--color-text-muted); margin-top: 4px;">
                Diajukan pada {{ $counseling->created_at->translatedFormat('l, d F Y - H:i') }} WIB
            </p>
        </div>
        <a href="{{ route('siswa.konseling.index') }}" class="btn btn-secondary">
            Kembali ke Daftar
        </a>
    </div>

    <!-- Status Progression Box (Section 8 Blueprint) -->
    <div class="card" style="margin-bottom: 20px;">
        <div class="card-body" style="padding: 20px;">
            <div style="font-size: 12px; font-weight: 700; color: var(--color-text-subtle); text-transform: uppercase; margin-bottom: 12px;">
                Alur Tahapan Penanganan Layanan:
            </div>
            <div style="display: flex; justify-content: space-between; position: relative; gap: 8px; flex-wrap: wrap;">
                @php
                    $steps = ['diajukan' => '1. Diajukan', 'dijadwalkan' => '2. Dijadwalkan', 'dilaksanakan' => '3. Berlangsung', 'selesai' => '4. Selesai'];
                    $currentStatus = $counseling->status;
                @endphp
                @foreach($steps as $key => $label)
                    <div style="display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; color: {{ in_array($currentStatus, [$key, 'selesai']) || ($key === 'diajukan') ? 'var(--color-primary)' : 'var(--color-text-subtle)' }};">
                        <span>●</span>
                        <span>{{ $label }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Jadwal Pertemuan dari Guru BK -->
    @if($counseling->scheduled_date)
        <div class="card" style="border-left: 4px solid var(--color-primary); margin-bottom: 20px;">
            <div class="card-header">
                <h3 class="card-title">Jadwal Sesi Pertemuan Ditetapkan</h3>
                <span class="badge badge-success">Siap Hadir</span>
            </div>
            <div class="card-body">
                <div style="font-size: 14px; font-weight: 700; color: var(--color-text-main); margin-bottom: 8px;">
                    {{ $counseling->scheduled_date->translatedFormat('l, d F Y') }} pukul {{ substr($counseling->scheduled_time, 0, 5) }} WIB
                </div>
                <div style="font-size: 13px; color: var(--color-text-muted); margin-bottom: 6px;">
                    Tempat / Ruangan: <strong>{{ $counseling->scheduled_location }}</strong>
                </div>
                <div style="font-size: 13px; color: var(--color-text-muted);">
                    Guru Pembimbing: <strong>{{ $counseling->counselor ? $counseling->counselor->name : 'Guru BK' }}</strong>
                </div>
            </div>
        </div>
    @endif

    <!-- Detail Masalah yang Diajukan -->
    <div class="card" style="margin-bottom: 20px;">
        <div class="card-header">
            <h3 class="card-title">Rincian Pengajuan Anda</h3>
            <span class="badge badge-secondary">{{ $counseling->category ? $counseling->category->name : 'Umum' }}</span>
        </div>
        <div class="card-body">
            <div style="margin-bottom: 14px;">
                <div style="font-size: 12px; font-weight: 600; color: var(--color-text-subtle);">Topik:</div>
                <div style="font-size: 15px; font-weight: 700; color: var(--color-text-main); margin-top: 2px;">
                    {{ $counseling->topic }}
                </div>
            </div>

            <div style="margin-bottom: 14px;">
                <div style="font-size: 12px; font-weight: 600; color: var(--color-text-subtle);">Cerita yang Anda Sampaikan:</div>
                <div style="margin-top: 6px; padding: 12px; background: #f8fafc; border-radius: var(--radius-sm); border: 1px solid var(--color-border); font-size: 13px; line-height: 1.6;">
                    {{ $counseling->story }}
                </div>
            </div>

            @if($counseling->preferred_schedule)
                <div>
                    <div style="font-size: 12px; font-weight: 600; color: var(--color-text-subtle);">Pilihan Waktu Harapan:</div>
                    <div style="font-size: 13px; color: var(--color-text-muted); margin-top: 2px;">
                        {{ $counseling->preferred_schedule }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Kesepakatan & Rencana Aksi Siswa -->
    @if($counseling->student_action_plan)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Kesepakatan & Rencana Tindak Lanjut Mandiri</h3>
            </div>
            <div class="card-body">
                <p style="font-size: 13px; color: var(--color-text-main); line-height: 1.6;">
                    {{ $counseling->student_action_plan }}
                </p>
            </div>
        </div>
    @endif
</div>
@endsection
