<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Jadwal & Kegiatan Perguruan Tinggi/Perusahaan - Wakasek Kurikulum</title>
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
            <div class="report-title">LAPORAN JADWAL & KEGIATAN KEMITRAAN (PAKET B)</div>
            <div class="report-sub">Peruntukan: Wakil Kepala Sekolah Bidang Kurikulum | Tujuan: Sinkronisasi Jadwal Pembelajaran</div>
            <div style="font-size: 11px; color: #555; margin-top: 4px;">Tanggal Cetak Dokumen: {{ $today->translatedFormat('d F Y') }}</div>
        </div>

        <p style="font-size: 13px; line-height: 1.6; margin-bottom: 16px;">
            Laporan ini disusun oleh Guru Bimbingan Konseling agar Wakasek Kurikulum dapat mengatur jadwal pembelajaran dan penggunaan ruang sekolah tanpa perlu melakukan login ke sistem secara terpisah.
        </p>

        <!-- 1. Rekap Jadwal Kegiatan Mitra -->
        <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 8px;">1. JADWAL SOSIALISASI, SEMINAR & KUNJUNGAN INDUSTRI</h4>
        <table class="table" style="margin-bottom: 24px; border: 1px solid #000;">
            <thead>
                <tr style="background: #f1f5f9;">
                    <th style="border: 1px solid #000;">Tanggal & Waktu</th>
                    <th style="border: 1px solid #000;">Mitra Pelaksana</th>
                    <th style="border: 1px solid #000;">Nama Kegiatan</th>
                    <th style="border: 1px solid #000;">Kelas Terdampak</th>
                    <th style="border: 1px solid #000;">Ruang / Lokasi</th>
                    <th style="border: 1px solid #000;">Penanggung Jawab</th>
                    <th style="border: 1px solid #000;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($activities as $act)
                    <tr>
                        <td style="border: 1px solid #000;">
                            <strong>{{ $act->date->translatedFormat('d/m/Y') }}</strong><br>
                            <small>{{ substr($act->start_time, 0, 5) }} - {{ substr($act->end_time, 0, 5) }}</small>
                        </td>
                        <td style="border: 1px solid #000;">{{ $act->partner ? $act->partner->name : '-' }}</td>
                        <td style="border: 1px solid #000; font-weight: 600;">{{ $act->title }}</td>
                        <td style="border: 1px solid #000;">{{ $act->targetClass ? $act->targetClass->name : 'Seluruh Siswa' }}</td>
                        <td style="border: 1px solid #000;">{{ $act->room_location }}</td>
                        <td style="border: 1px solid #000;">{{ $act->pic_name ?: '-' }}</td>
                        <td style="border: 1px solid #000;">{{ ucfirst($act->status) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="border: 1px solid #000; text-align: center;">Tidak ada agenda kemitraan terjadwal.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- 2. Mitra Kerja Sama Aktif -->
        <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 8px;">2. REKAPITULASI DOKUMEN KERJA SAMA (MoU / PKS)</h4>
        <table class="table" style="margin-bottom: 24px; border: 1px solid #000;">
            <thead>
                <tr style="background: #f1f5f9;">
                    <th style="border: 1px solid #000;">Nama Mitra</th>
                    <th style="border: 1px solid #000;">Tipe Instansi</th>
                    <th style="border: 1px solid #000;">Nomor Dokumen MoU</th>
                    <th style="border: 1px solid #000;">Jumlah Agenda</th>
                </tr>
            </thead>
            <tbody>
                @foreach($activePartners as $ap)
                    <tr>
                        <td style="border: 1px solid #000; font-weight: 700;">{{ $ap->name }}</td>
                        <td style="border: 1px solid #000;">{{ ucfirst(str_replace('_', ' ', $ap->type)) }}</td>
                        <td style="border: 1px solid #000;">{{ $ap->partnership_doc_number ?: 'Dalam Proses' }}</td>
                        <td style="border: 1px solid #000;">{{ $ap->activities_count }} Kegiatan</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="signature-grid">
            <div>
                Mengetahui,<br>
                <strong>Wakil Kepala Sekolah Bidang Kurikulum</strong>
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
