<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Eksekutif Kepala Sekolah - Bimbingan, Karier & Lulusan</title>
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
            <div class="report-title">LAPORAN EKSEKUTIF KEPALA SEKOLAH</div>
            <div class="report-sub">Peruntukan: Kepala Sekolah | Pengambilan Keputusan Strategis Berbasis Data</div>
            <div style="font-size: 11px; color: #555; margin-top: 4px;">Tanggal Cetak Dokumen: {{ $today->translatedFormat('d F Y') }}</div>
        </div>

        <!-- 1. Ringkasan Eksekutif -->
        <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 8px;">1. RINGKASAN EKSEKUTIF LAYANAN BIMBINGAN & KEMITRAAN</h4>
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 24px;">
            <div style="border: 1px solid #000; padding: 12px; text-align: center;">
                <div style="font-size: 11px; text-transform: uppercase;">Total Siswa Aktif</div>
                <div style="font-size: 20px; font-weight: 800;">{{ $totalStudents }}</div>
            </div>
            <div style="border: 1px solid #000; padding: 12px; text-align: center;">
                <div style="font-size: 11px; text-transform: uppercase;">Total Kasus Konseling</div>
                <div style="font-size: 20px; font-weight: 800;">{{ $totalCounselings }}</div>
            </div>
            <div style="border: 1px solid #000; padding: 12px; text-align: center;">
                <div style="font-size: 11px; text-transform: uppercase;">Mitra Kampus (PT)</div>
                <div style="font-size: 20px; font-weight: 800;">{{ $partnerStats['ptn_pts'] }}</div>
            </div>
            <div style="border: 1px solid #000; padding: 12px; text-align: center;">
                <div style="font-size: 11px; text-transform: uppercase;">Mitra Industri (DUDI)</div>
                <div style="font-size: 20px; font-weight: 800;">{{ $partnerStats['industri'] }}</div>
            </div>
        </div>

        <!-- 2. Peta Rencana Masa Depan Siswa Kelas XII (Lulusan) -->
        <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 8px;">2. PETA RENCANA MASA DEPAN KELAS XII (TOTAL: {{ $totalXII }} SISWA)</h4>
        <table class="table" style="margin-bottom: 24px; border: 1px solid #000;">
            <thead>
                <tr style="background: #f1f5f9;">
                    <th style="border: 1px solid #000;">Arah Pilihan Siswa</th>
                    <th style="border: 1px solid #000;">Jumlah Siswa</th>
                    <th style="border: 1px solid #000;">Persentase</th>
                    <th style="border: 1px solid #000;">Analisis Strategis BK</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="border: 1px solid #000; font-weight: 700;">Melanjutkan Kuliah (PTN / PTS)</td>
                    <td style="border: 1px solid #000;">{{ $kuliah }} Siswa</td>
                    <td style="border: 1px solid #000; font-weight: 700;">{{ $planPercentages['kuliah'] }}%</td>
                    <td style="border: 1px solid #000;">Pendampingan intensif SNBP, SNBT, dan beasiswa KIP-Kuliah</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; font-weight: 700;">Bekerja (Dunia Usaha / Industri)</td>
                    <td style="border: 1px solid #000;">{{ $kerja }} Siswa</td>
                    <td style="border: 1px solid #000; font-weight: 700;">{{ $planPercentages['kerja'] }}%</td>
                    <td style="border: 1px solid #000;">Penyaluran ke bursa kerja khusus dan rekrutmen mitra DUDI</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; font-weight: 700;">Wirausaha Mandiri</td>
                    <td style="border: 1px solid #000;">{{ $wirausaha }} Siswa</td>
                    <td style="border: 1px solid #000; font-weight: 700;">{{ $planPercentages['wirausaha'] }}%</td>
                    <td style="border: 1px solid #000;">Penguatan kompetensi kewirausahaan dan inkubasi bisnis mini</td>
                </tr>
                <tr style="background: #fffdf5;">
                    <td style="border: 1px solid #000; font-weight: 700; color: #b91c1c;">Belum Menentukan Pilihan</td>
                    <td style="border: 1px solid #000; color: #b91c1c;">{{ $undecided }} Siswa</td>
                    <td style="border: 1px solid #000; font-weight: 700; color: #b91c1c;">{{ $planPercentages['undecided'] }}%</td>
                    <td style="border: 1px solid #000; color: #b91c1c;">Prioritas pendampingan konseling individual dan asesmen minat</td>
                </tr>
            </tbody>
        </table>

        <!-- 3. Hasil Pelacakan Lulusan (Tracer Study) -->
        <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 8px;">3. HASIL PELACAKAN ALUMNI (TRACER STUDY)</h4>
        <table class="table" style="margin-bottom: 24px; border: 1px solid #000;">
            <thead>
                <tr style="background: #f1f5f9;">
                    <th style="border: 1px solid #000;">Kategori Penyerapan</th>
                    <th style="border: 1px solid #000;">Jumlah Terlacak</th>
                    <th style="border: 1px solid #000;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="border: 1px solid #000;">Alumni Bekerja di Industri</td>
                    <td style="border: 1px solid #000; font-weight: 700;">{{ $alumniStats['bekerja'] }} Orang</td>
                    <td style="border: 1px solid #000;">Terserap dalam 6 sampai 12 bulan pertama</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000;">Alumni Menempuh Pendidikan Tinggi</td>
                    <td style="border: 1px solid #000; font-weight: 700;">{{ $alumniStats['kuliah'] }} Orang</td>
                    <td style="border: 1px solid #000;">Studi lanjut diploma dan sarjana</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000;">Alumni Berwirausaha</td>
                    <td style="border: 1px solid #000; font-weight: 700;">{{ $alumniStats['wirausaha'] }} Orang</td>
                    <td style="border: 1px solid #000;">Membuka lapangan kerja mandiri</td>
                </tr>
            </tbody>
        </table>

        <div class="signature-grid">
            <div>
                Diterima & Disetujui oleh,<br>
                <strong>Kepala Sekolah</strong>
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
