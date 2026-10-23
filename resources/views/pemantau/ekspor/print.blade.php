<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Data Siswa - Ruang BK Digital</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Plus Jakarta Sans', Arial, sans-serif; font-size: 11pt; color: #111111; line-height: 1.4; padding: 24px; background: #FAFAFA; }
        .sheet { max-width: 960px; margin: 0 auto; background: #FFFFFF; padding: 32px; border: 1px solid #DDDDDD; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .kop-surat { display: flex; align-items: center; border-bottom: 2.5px solid #101A35; padding-bottom: 12px; margin-bottom: 20px; gap: 16px; }
        .kop-logo { width: 64px; height: 64px; object-fit: contain; }
        .kop-text { flex: 1; text-align: center; }
        .kop-school { font-size: 15pt; font-weight: 800; color: #101A35; text-transform: uppercase; letter-spacing: 0.04em; }
        .kop-inst { font-size: 10pt; color: #444444; margin-top: 2px; }
        .kop-addr { font-size: 9pt; color: #666666; margin-top: 2px; }
        .doc-title { text-align: center; font-size: 13pt; font-weight: 800; color: #101A35; text-transform: uppercase; margin-bottom: 6px; }
        .doc-meta { text-align: center; font-size: 9.5pt; color: #555555; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        th { background: #101A35; color: #FFFFFF; font-size: 9.5pt; font-weight: 700; padding: 6px 8px; border: 1px solid #101A35; text-align: left; }
        td { font-size: 9pt; padding: 5px 8px; border: 1px solid #CCCCCC; }
        .text-center { text-align: center; }
        .actions-bar { max-width: 960px; margin: 0 auto 16px auto; display: flex; justify-content: space-between; align-items: center; }
        .btn { padding: 8px 16px; border-radius: 6px; font-weight: 600; font-size: 13px; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; border: none; }
        .btn-primary { background: #2447A8; color: #FFFFFF; }
        .btn-secondary { background: #E5E9F2; color: #101A35; }

        @media print {
            body { padding: 0; background: #FFFFFF; font-size: 10pt; }
            .sheet { border: none; box-shadow: none; padding: 0; max-width: 100%; }
            .actions-bar { display: none; }
            th { background: #EEEEEE !important; color: #000000 !important; border: 1px solid #666666 !important; }
            td { border: 1px solid #888888 !important; }
        }
    </style>
</head>
<body>
    <div class="actions-bar">
        <a href="javascript:history.back()" class="btn btn-secondary">&larr; Kembali ke Menu Ekspor</a>
        <button onclick="window.print()" class="btn btn-primary">
            Cetak Dokumen Sekarang (PDF)
        </button>
    </div>

    <div class="sheet">
        <!-- Kop Surat -->
        <div class="kop-surat">
            @if(!empty($schoolSetting->logo_url))
                <img src="{{ $schoolSetting->logo_url }}" alt="Logo Sekolah" class="kop-logo" />
            @endif
            <div class="kop-text">
                <div class="kop-school">{{ $schoolSetting->school_name ?? 'SMK NEGERI 1 INDONESIA' }}</div>
                <div class="kop-inst">LAYANAN RUANG BIMBINGAN KONSELING & PENGEMBANGAN SISWA DIGITAL</div>
                <div class="kop-addr">{{ $schoolSetting->address ?? 'Jalan Pendidikan No. 1, Wilayah Sekolah' }} | Telp: {{ $schoolSetting->phone ?? '-' }}</div>
            </div>
        </div>

        <div class="doc-title">REKAPITULASI RESMI DATA SISWA SEKOLAH</div>
        <div class="doc-meta">
            Cakupan Filter: <strong>{{ $filterDescription }}</strong> | Total: <strong>{{ number_format($students->count()) }} Siswa</strong> | Tanggal Unduh: {{ date('d F Y') }}
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 30px;" class="text-center">No</th>
                    <th style="width: 75px;">NIS</th>
                    <th style="width: 90px;">NISN</th>
                    <th>Nama Lengkap Siswa</th>
                    <th style="width: 35px;" class="text-center">L/P</th>
                    <th style="width: 50px;" class="text-center">Tingkat</th>
                    <th>Konsentrasi Keahlian / Jurusan</th>
                    <th>Rombel</th>
                    <th style="width: 60px;" class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $idx => $st)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td>{{ $st->nis ?? '-' }}</td>
                        <td>{{ $st->nisn ?? '-' }}</td>
                        <td style="font-weight: 600;">{{ $st->name }}</td>
                        <td class="text-center">{{ $st->gender ?? '-' }}</td>
                        <td class="text-center">{{ $st->studentClass->grade ?? '-' }}</td>
                        <td>{{ $st->studentClass->major ?? '-' }}</td>
                        <td>{{ $st->studentClass->name ?? '-' }}</td>
                        <td class="text-center">{{ ucfirst((string) ($st->status ?? 'aktif')) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center" style="padding: 24px; color: #888888;">
                            Tidak ada data siswa yang sesuai dengan kriteria filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Signature Section -->
        <div style="display: flex; justify-content: flex-end; margin-top: 32px; page-break-inside: avoid;">
            <div style="text-align: center; min-width: 220px;">
                <div style="font-size: 10pt; color: #444444;">{{ $schoolSetting->city ?? 'Jakarta' }}, {{ date('d F Y') }}</div>
                <div style="font-size: 10pt; font-weight: 700; color: #101A35; margin-top: 4px;">{{ auth()->user()->role_label }}</div>
                <div style="height: 60px;"></div>
                <div style="font-size: 10.5pt; font-weight: 800; color: #101A35; text-decoration: underline;">
                    {{ auth()->user()->name }}
                </div>
                <div style="font-size: 9pt; color: #666666;">
                    NIP. {{ auth()->user()->nip ?? '---------------------' }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
