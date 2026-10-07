<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Perkembangan & Kondisi Siswa - Wakasek Kesiswaan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body { background: #fff; padding: 24px; }
        .report-header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 12px;
            margin-bottom: 24px;
        }
        .report-title {
            font-size: 18px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .report-sub {
            font-size: 13px;
            color: #333;
            margin-top: 4px;
        }
        .signature-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            margin-top: 48px;
            text-align: center;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div style="max-width: 900px; margin: 0 auto;">
        <div class="no-print" style="margin-bottom: 20px; display: flex; justify-content: space-between;">
            <a href="{{ route('guru.laporan.index') }}" class="btn btn-secondary">← Kembali ke Hub Laporan</a>
            <button onclick="window.print()" class="btn btn-primary">Cetak / Simpan PDF</button>
        </div>

        <div class="report-header">
            <div style="font-size: 14px; font-weight: 700; text-transform: uppercase;">Pemerintah Daerah Provinsi Jawa Barat - Dinas Pendidikan</div>
            <div class="report-title">LAPORAN PERKEMBANGAN & KONDISI SISWA (PAKET A)</div>
            <div class="report-sub">Peruntukan: Wakil Kepala Sekolah Bidang Kesiswaan | Periode: Tahun Ajaran Berjalan</div>
            <div style="font-size: 11px; color: #555; margin-top: 4px;">Tanggal Cetak Dokumen: {{ $today->translatedFormat('d F Y') }}</div>
        </div>

        <!-- 1. Kondisi Agregat Siswa -->
        <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 8px;">1. DATA AGREGAT DISTRIBUSI SISWA</h4>
        <table class="table" style="margin-bottom: 24px; border: 1px solid #000;">
            <thead>
                <tr style="background: #f1f5f9;">
                    <th style="border: 1px solid #000;">Tingkat Jenjang</th>
                    <th style="border: 1px solid #000;">Jumlah Siswa Aktif</th>
                    <th style="border: 1px solid #000;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="border: 1px solid #000;">Kelas X</td>
                    <td style="border: 1px solid #000; font-weight: 700;">{{ $studentsByGrade['X'] }} Siswa</td>
                    <td style="border: 1px solid #000;">Fase Fondasi & Adaptasi Vokasi</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000;">Kelas XI</td>
                    <td style="border: 1px solid #000; font-weight: 700;">{{ $studentsByGrade['XI'] }} Siswa</td>
                    <td style="border: 1px solid #000;">Fase Pemantapan & Persiapan Magang</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000;">Kelas XII</td>
                    <td style="border: 1px solid #000; font-weight: 700;">{{ $studentsByGrade['XII'] }} Siswa</td>
                    <td style="border: 1px solid #000;">Fase Kelulusan & Transisi Karier</td>
                </tr>
                <tr style="background: #f8fafc; font-weight: 800;">
                    <td style="border: 1px solid #000;">TOTAL SELURUH SISWA AKTIF</td>
                    <td style="border: 1px solid #000;">{{ $totalActive }} Siswa</td>
                    <td style="border: 1px solid #000;">Satu Sumber Kebenaran Data BK</td>
                </tr>
            </tbody>
        </table>

        <!-- 2. Siswa yang Memerlukan Perhatian & Pembinaan -->
        <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 8px;">2. DAFTAR SISWA MEMERLUKAN PERHATIAN & PEMBINAAN KHUSUS</h4>
        <table class="table" style="margin-bottom: 24px; border: 1px solid #000;">
            <thead>
                <tr style="background: #f1f5f9;">
                    <th style="border: 1px solid #000; width: 40px;">No</th>
                    <th style="border: 1px solid #000;">Nama Siswa</th>
                    <th style="border: 1px solid #000;">Kelas</th>
                    <th style="border: 1px solid #000;">Tingkat Perhatian</th>
                    <th style="border: 1px solid #000;">Fokus Pembinaan / Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attentionList as $idx => $att)
                    <tr>
                        <td style="border: 1px solid #000;">{{ $idx + 1 }}</td>
                        <td style="border: 1px solid #000; font-weight: 700;">{{ $att->name }}</td>
                        <td style="border: 1px solid #000;">{{ $att->studentClass ? $att->studentClass->name : '-' }}</td>
                        <td style="border: 1px solid #000;">{{ ucfirst(str_replace('_', ' ', $att->attention_level)) }}</td>
                        <td style="border: 1px solid #000;">{{ $att->special_notes ?: 'Perlu pemantauan berkala' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="border: 1px solid #000; text-align: center;">Tidak ada siswa dengan status perlu perhatian khusus saat ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- 3. Rekapitulasi Layanan Bimbingan & Konseling -->
        <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 8px;">3. REKAPITULASI LAYANAN BIMBINGAN & KONSELING</h4>
        <div style="display: flex; gap: 20px; margin-bottom: 16px;">
            <div style="flex: 1; border: 1px solid #000; padding: 12px; text-align: center;">
                <div style="font-size: 11px; text-transform: uppercase;">Total Layanan Konseling</div>
                <div style="font-size: 20px; font-weight: 800;">{{ $counselingStats['total'] }} Kasus</div>
            </div>
            <div style="flex: 1; border: 1px solid #000; padding: 12px; text-align: center;">
                <div style="font-size: 11px; text-transform: uppercase;">Layanan Terselesaikan</div>
                <div style="font-size: 20px; font-weight: 800; color: #047857;">{{ $counselingStats['selesai'] }} Kasus</div>
            </div>
            <div style="flex: 1; border: 1px solid #000; padding: 12px; text-align: center;">
                <div style="font-size: 11px; text-transform: uppercase;">Sedang Ditindaklanjuti</div>
                <div style="font-size: 20px; font-weight: 800; color: #b45309;">{{ $counselingStats['proses'] }} Kasus</div>
            </div>
        </div>

        <div class="signature-grid">
            <div>
                Mengetahui,<br>
                <strong>Wakil Kepala Sekolah Bidang Kesiswaan</strong>
                <br><br><br><br>
                ( .................................................... )<br>
                NIP. ...............................................
            </div>
            <div>
                Dibuat oleh,<br>
                <strong>Koordinator Guru Bimbingan Konseling</strong>
                <br><br><br><br>
                <strong>{{ auth()->user()->name }}</strong><br>
                NIP/NUPTK. ........................................
            </div>
        </div>
    </div>
</body>
</html>
