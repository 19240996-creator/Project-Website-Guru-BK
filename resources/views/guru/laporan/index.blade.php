@extends('layouts.app')

@section('title', 'Paket Laporan Pimpinan - Guru BK')
@section('header_title', 'Pusat Pembuatan Dokumen Laporan Pimpinan')

@section('content')
<div style="margin-bottom: 24px;">
    <h2 style="font-size: 20px; font-weight: 800; color: var(--color-text-main);">Paket Template Laporan Otomatis</h2>
    <p style="font-size: 13px; color: var(--color-text-muted);">
        Sesuai spesifikasi sistem, tidak ada akun login Wakasek atau Kepala Sekolah. Guru BK membuat dan mendistribusikan laporan siap cetak/ekspor sesuai kebutuhan masing-masing pimpinan.
    </p>
</div>

<div class="grid-3" style="margin-bottom: 24px;">
    <!-- Paket A: Kesiswaan -->
    <div class="card" style="border-top: 4px solid #2563eb; display: flex; flex-direction: column; justify-content: space-between;">
        <div>
            <div class="card-header">
                <h3 class="card-title">Paket A: Wakasek Kesiswaan</h3>
                <span class="badge badge-primary">Kesiswaan</span>
            </div>
            <div class="card-body">
                <div style="font-weight: 700; font-size: 14px; margin-bottom: 8px;">Laporan Perkembangan & Kondisi Siswa</div>
                <p style="font-size: 13px; color: var(--color-text-muted); line-height: 1.6; margin-bottom: 12px;">
                    Memuat data agregat kondisi siswa, distribusi kelas, daftar siswa yang memerlukan pembinaan/perhatian, serta rekapitulasi layanan konseling sekolah.
                </p>
                <ul style="font-size: 12px; color: var(--color-text-subtle); padding-left: 18px; margin-bottom: 16px;">
                    <li>Data agregat privasi terlindungi</li>
                    <li>Daftar prioritas penanganan siswa</li>
                    <li>Statistik penyelesaian layanan BK</li>
                </ul>
            </div>
        </div>
        <div class="card-footer">
            <a href="{{ route('guru.laporan.generate', ['package' => 'paket_a']) }}" target="_blank" class="btn btn-primary" style="width: 100%;">
                Buka & Cetak Laporan Paket A
            </a>
        </div>
    </div>

    <!-- Paket B: Kurikulum -->
    <div class="card" style="border-top: 4px solid #059669; display: flex; flex-direction: column; justify-content: space-between;">
        <div>
            <div class="card-header">
                <h3 class="card-title">Paket B: Wakasek Kurikulum</h3>
                <span class="badge badge-success">Kurikulum</span>
            </div>
            <div class="card-body">
                <div style="font-weight: 700; font-size: 14px; margin-bottom: 8px;">Laporan Jadwal & Kegiatan Kampus/Industri</div>
                <p style="font-size: 13px; color: var(--color-text-muted); line-height: 1.6; margin-bottom: 12px;">
                    Fokus pada sinkronisasi jadwal pembelajaran dengan agenda mitra: sosialisasi kampus, kunjungan industri, seminar vokasi, dan status benturan ruang/kelas.
                </p>
                <ul style="font-size: 12px; color: var(--color-text-subtle); padding-left: 18px; margin-bottom: 16px;">
                    <li>Rincian tanggal, jam, dan ruang</li>
                    <li>Kelas rombel yang terdampak kegiatan</li>
                    <li>Status verifikasi benturan jadwal</li>
                </ul>
            </div>
        </div>
        <div class="card-footer">
            <a href="{{ route('guru.laporan.generate', ['package' => 'paket_b']) }}" target="_blank" class="btn btn-accent" style="width: 100%;">
                Buka & Cetak Laporan Paket B
            </a>
        </div>
    </div>

    <!-- Paket C: Kepala Sekolah -->
    <div class="card" style="border-top: 4px solid #d97706; display: flex; flex-direction: column; justify-content: space-between;">
        <div>
            <div class="card-header">
                <h3 class="card-title">Paket C: Kepala Sekolah</h3>
                <span class="badge badge-warning">Eksekutif</span>
            </div>
            <div class="card-body">
                <div style="font-weight: 700; font-size: 14px; margin-bottom: 8px;">Laporan Eksekutif Bimbingan, Karier & Lulusan</div>
                <p style="font-size: 13px; color: var(--color-text-muted); line-height: 1.6; margin-bottom: 12px;">
                    Ringkasan tingkat pimpinan: efektivitas bimbingan, peta persentase rencana masa depan lulusan kelas XII, capaian kemitraan, dan hasil penyerapan alumni.
                </p>
                <ul style="font-size: 12px; color: var(--color-text-subtle); padding-left: 18px; margin-bottom: 16px;">
                    <li>Peta persentase kuliah vs kerja vs usaha</li>
                    <li>Rekap kemitraan PT & Industri</li>
                    <li>Hasil tracer study alumni</li>
                </ul>
            </div>
        </div>
        <div class="card-footer">
            <a href="{{ route('guru.laporan.generate', ['package' => 'paket_c']) }}" target="_blank" class="btn btn-secondary" style="width: 100%; border-color: var(--color-warning);">
                Buka & Cetak Laporan Paket C
            </a>
        </div>
    </div>
</div>
@endsection
