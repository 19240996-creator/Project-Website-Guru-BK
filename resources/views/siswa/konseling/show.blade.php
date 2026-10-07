@extends('layouts.siswa')

@section('title', 'Detail Permohonan Konseling - ' . $counseling->code)

@section('content')
<div style="max-width: 860px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
        <div>
            <div style="display: flex; align-items: center; gap: 12px;">
                <h2 style="font-size: 20px; font-weight: 800; color: var(--color-text-main);">{{ $counseling->code }}</h2>
                <span style="display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; color: {{ $counseling->status === 'selesai' ? 'var(--color-success)' : ($counseling->status === 'dijadwalkan' ? 'var(--color-primary)' : 'var(--color-text-muted)') }};">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background-color: {{ $counseling->status === 'selesai' ? 'var(--color-success)' : ($counseling->status === 'dijadwalkan' ? 'var(--color-primary)' : 'var(--color-text-subtle)') }}; display: inline-block;"></span>
                    {{ ucfirst(str_replace('_', ' ', $counseling->status)) }}
                </span>
            </div>
            <p style="font-size: 13px; color: var(--color-text-muted); margin-top: 4px;">
                Diajukan pada {{ $counseling->created_at->translatedFormat('l, d F Y') }} pukul {{ $counseling->created_at->format('H:i') }} WIB
            </p>
        </div>
        <a href="{{ route('siswa.konseling.index') }}" class="btn btn-secondary">
            Kembali ke Daftar
        </a>
    </div>

    <!-- Alur Tahapan Penanganan Layanan -->
    <div class="card" style="margin-bottom: 20px;">
        <div class="card-body" style="padding: 18px 22px;">
            <div style="font-size: 11px; font-weight: 700; color: var(--color-text-subtle); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 12px;">
                Alur Tahapan Penanganan Layanan
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 12px;">
                @php
                    $steps = ['diajukan' => '1. Diajukan', 'dijadwalkan' => '2. Dijadwalkan', 'dilaksanakan' => '3. Berlangsung', 'selesai' => '4. Selesai'];
                    $currentStatus = $counseling->status;
                @endphp
                @foreach($steps as $key => $label)
                    @php
                        $isActive = in_array($currentStatus, [$key, 'selesai']) || ($key === 'diajukan');
                    @endphp
                    <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; color: {{ $isActive ? 'var(--color-primary)' : 'var(--color-text-subtle)' }}; padding: 8px 12px; background: {{ $isActive ? 'var(--color-primary-light)' : 'transparent' }}; border-radius: var(--radius-sm); border: 1px solid {{ $isActive ? 'var(--color-primary-border)' : 'var(--color-border)' }};">
                        <span style="width: 7px; height: 7px; border-radius: 50%; background-color: {{ $isActive ? 'var(--color-primary)' : 'var(--color-border-strong)' }}; display: inline-block;"></span>
                        <span>{{ $label }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Jadwal Pertemuan dari Guru BK (Jika sudah ditetapkan) -->
    @if($counseling->scheduled_date)
        <div class="card" style="margin-bottom: 20px; border-left: 4px solid var(--color-primary);">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h3 class="card-title">Jadwal Sesi Konseling Ditetapkan</h3>
                <span style="font-size: 12px; font-weight: 600; color: var(--color-success); display: inline-flex; align-items: center; gap: 6px;">
                    <span style="width: 7px; height: 7px; border-radius: 50%; background-color: var(--color-success); display: inline-block;"></span>
                    Siap Hadir
                </span>
            </div>
            <div class="card-body" style="padding: 0;">
                <table class="table" style="margin: 0; width: 100%;">
                    <tbody>
                        <tr>
                            <td style="width: 220px; font-weight: 600; color: var(--color-text-muted); background: var(--color-bg); border-right: 1px solid var(--color-border);">Hari & Tanggal</td>
                            <td style="font-weight: 600; color: var(--color-text-main);">{{ $counseling->scheduled_date->translatedFormat('l, d F Y') }}</td>
                        </tr>
                        <tr>
                            <td style="width: 220px; font-weight: 600; color: var(--color-text-muted); background: var(--color-bg); border-right: 1px solid var(--color-border);">Waktu Pertemuan</td>
                            <td style="font-weight: 600; color: var(--color-text-main);">Pukul {{ substr($counseling->scheduled_time, 0, 5) }} WIB</td>
                        </tr>
                        <tr>
                            <td style="width: 220px; font-weight: 600; color: var(--color-text-muted); background: var(--color-bg); border-right: 1px solid var(--color-border);">Ruangan / Tempat</td>
                            <td style="color: var(--color-text-main);">{{ $counseling->scheduled_location }}</td>
                        </tr>
                        <tr>
                            <td style="width: 220px; font-weight: 600; color: var(--color-text-muted); background: var(--color-bg); border-right: 1px solid var(--color-border);">Guru Pembimbing</td>
                            <td style="color: var(--color-text-main);">{{ $counseling->counselor ? $counseling->counselor->name : 'Guru Bimbingan Konseling' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Rincian Permohonan (Keterangan & Isian Sejajar) -->
    <div class="card" style="margin-bottom: 20px;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 class="card-title">Rincian Data Permohonan Konseling</h3>
            <span style="font-size: 13px; font-weight: 600; color: var(--color-text-muted);">
                {{ $counseling->category ? $counseling->category->name : 'Konseling Umum' }}
            </span>
        </div>
        <div class="card-body" style="padding: 0;">
            <table class="table" style="margin: 0; width: 100%;">
                <tbody>
                    <tr>
                        <td style="width: 220px; font-weight: 600; color: var(--color-text-muted); background: var(--color-bg); border-right: 1px solid var(--color-border);">Nomor Permohonan</td>
                        <td style="font-weight: 700; color: var(--color-primary);">{{ $counseling->code }}</td>
                    </tr>
                    <tr>
                        <td style="width: 220px; font-weight: 600; color: var(--color-text-muted); background: var(--color-bg); border-right: 1px solid var(--color-border);">Waktu Pengajuan</td>
                        <td style="color: var(--color-text-main);">{{ $counseling->created_at->translatedFormat('l, d F Y') }} pukul {{ $counseling->created_at->format('H:i') }} WIB</td>
                    </tr>
                    <tr>
                        <td style="width: 220px; font-weight: 600; color: var(--color-text-muted); background: var(--color-bg); border-right: 1px solid var(--color-border);">Kategori Masalah</td>
                        <td style="color: var(--color-text-main);">{{ $counseling->category ? $counseling->category->name : 'Konseling Umum' }}</td>
                    </tr>
                    <tr>
                        <td style="width: 220px; font-weight: 600; color: var(--color-text-muted); background: var(--color-bg); border-right: 1px solid var(--color-border);">Tingkat Urgensi</td>
                        <td>
                            @if($counseling->urgency === 'mendesak' || $counseling->urgency === 'tinggi')
                                <span style="display: inline-flex; align-items: center; gap: 6px; font-weight: 600; font-size: 13px; color: var(--color-danger);">
                                    <span style="width: 7px; height: 7px; border-radius: 50%; background-color: var(--color-danger); display: inline-block;"></span>
                                    {{ ucfirst($counseling->urgency) }}
                                </span>
                            @elseif($counseling->urgency === 'sedang')
                                <span style="display: inline-flex; align-items: center; gap: 6px; font-weight: 600; font-size: 13px; color: var(--color-warning);">
                                    <span style="width: 7px; height: 7px; border-radius: 50%; background-color: var(--color-warning); display: inline-block;"></span>
                                    Sedang
                                </span>
                            @else
                                <span style="display: inline-flex; align-items: center; gap: 6px; font-weight: 600; font-size: 13px; color: var(--color-text-subtle);">
                                    <span style="width: 7px; height: 7px; border-radius: 50%; background-color: var(--color-text-subtle); display: inline-block;"></span>
                                    Rendah
                                </span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 220px; font-weight: 600; color: var(--color-text-muted); background: var(--color-bg); border-right: 1px solid var(--color-border);">Status Penanganan</td>
                        <td>
                            @if($counseling->status === 'dijadwalkan')
                                <span style="display: inline-flex; align-items: center; gap: 6px; font-weight: 600; font-size: 13px; color: var(--color-primary);">
                                    <span style="width: 7px; height: 7px; border-radius: 50%; background-color: var(--color-primary); display: inline-block;"></span>
                                    Dijadwalkan
                                </span>
                            @elseif($counseling->status === 'selesai')
                                <span style="display: inline-flex; align-items: center; gap: 6px; font-weight: 600; font-size: 13px; color: var(--color-success);">
                                    <span style="width: 7px; height: 7px; border-radius: 50%; background-color: var(--color-success); display: inline-block;"></span>
                                    Selesai
                                </span>
                            @elseif($counseling->status === 'dilaksanakan')
                                <span style="display: inline-flex; align-items: center; gap: 6px; font-weight: 600; font-size: 13px; color: var(--color-info);">
                                    <span style="width: 7px; height: 7px; border-radius: 50%; background-color: var(--color-info); display: inline-block;"></span>
                                    Sedang Berlangsung
                                </span>
                            @else
                                <span style="display: inline-flex; align-items: center; gap: 6px; font-weight: 600; font-size: 13px; color: var(--color-text-muted);">
                                    <span style="width: 7px; height: 7px; border-radius: 50%; background-color: var(--color-text-subtle); display: inline-block;"></span>
                                    Diajukan (Menunggu Konfirmasi Jadwal)
                                </span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 220px; font-weight: 600; color: var(--color-text-muted); background: var(--color-bg); border-right: 1px solid var(--color-border);">Topik Konsultasi</td>
                        <td style="font-weight: 600; color: var(--color-text-main);">{{ $counseling->topic }}</td>
                    </tr>
                    @if($counseling->preferred_schedule)
                        <tr>
                            <td style="width: 220px; font-weight: 600; color: var(--color-text-muted); background: var(--color-bg); border-right: 1px solid var(--color-border);">Waktu Harapan Siswa</td>
                            <td style="color: var(--color-text-main);">{{ $counseling->preferred_schedule }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td style="width: 220px; font-weight: 600; color: var(--color-text-muted); background: var(--color-bg); border-right: 1px solid var(--color-border); vertical-align: top; padding-top: 14px;">Uraian Cerita Siswa</td>
                        <td style="color: var(--color-text-main); line-height: 1.6; white-space: pre-line; padding-top: 14px;">{{ $counseling->story }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Kesepakatan & Rencana Aksi Siswa -->
    @if($counseling->student_action_plan)
        <div class="card" style="margin-bottom: 20px;">
            <div class="card-header">
                <h3 class="card-title">Kesepakatan & Rencana Tindak Lanjut Mandiri</h3>
            </div>
            <div class="card-body" style="padding: 0;">
                <table class="table" style="margin: 0; width: 100%;">
                    <tbody>
                        <tr>
                            <td style="width: 220px; font-weight: 600; color: var(--color-text-muted); background: var(--color-bg); border-right: 1px solid var(--color-border); vertical-align: top; padding-top: 14px;">Catatan Rencana Aksi</td>
                            <td style="color: var(--color-text-main); line-height: 1.6; white-space: pre-line; padding-top: 14px;">{{ $counseling->student_action_plan }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
