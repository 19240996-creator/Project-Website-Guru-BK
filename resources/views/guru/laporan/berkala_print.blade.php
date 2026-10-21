<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $reportTitle }} - {{ $periodLabel }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background: #F8FAFC;
            color: #0F172A;
            padding: 30px 20px;
            font-size: 12.5px;
            line-height: 1.5;
        }
        .container {
            max-width: 960px;
            margin: 0 auto;
            background: #FFFFFF;
            padding: 40px 45px;
            border-radius: 8px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
        }
        .no-print {
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            background: #101A35;
            padding: 14px 20px;
            border-radius: 8px;
            color: #FFFFFF;
        }
        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            border: none;
            transition: all 0.2s ease;
        }
        .btn-back { background: #334155; color: #FFFFFF; }
        .btn-back:hover { background: #475569; }
        .btn-print { background: #2563EB; color: #FFFFFF; }
        .btn-print:hover { background: #1D4ED8; }

        /* Kop Surat Resmi */
        .kop-surat {
            text-align: center;
            border-bottom: 3px double #000000;
            padding-bottom: 14px;
            margin-bottom: 24px;
        }
        .kop-instansi {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #1E293B;
        }
        .kop-sekolah {
            font-size: 18px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #101A35;
            margin: 2px 0;
        }
        .kop-unit {
            font-size: 14px;
            font-weight: 700;
            color: #2563EB;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .kop-alamat {
            font-size: 11px;
            color: #475569;
            margin-top: 4px;
        }

        /* Dokumen Title & Metadata */
        .doc-title-box {
            text-align: center;
            margin-bottom: 24px;
        }
        .doc-title {
            font-size: 16px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #101A35;
        }
        .doc-subtitle {
            font-size: 12.5px;
            font-weight: 600;
            color: #334155;
            margin-top: 3px;
        }

        .meta-table {
            width: 100%;
            margin-bottom: 24px;
            border-collapse: collapse;
            font-size: 12px;
        }
        .meta-table td {
            padding: 4px 8px;
            vertical-align: top;
        }
        .meta-label {
            width: 170px;
            font-weight: 600;
            color: #475569;
        }
        .meta-colon { width: 12px; text-align: center; color: #475569; }
        .meta-value {
            font-weight: 600;
            color: #0F172A;
        }

        /* Section Headings */
        .section-heading {
            font-size: 13px;
            font-weight: 800;
            color: #101A35;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            margin: 20px 0 8px 0;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Tables */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
            font-size: 11.5px;
        }
        .report-table th, .report-table td {
            border: 1px solid #CBD5E1;
            padding: 7px 10px;
            vertical-align: middle;
        }
        .report-table th {
            background: #F1F5F9;
            color: #1E293B;
            font-weight: 700;
            text-align: center;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.02em;
        }

        /* Status & Urgency Badges */
        .badge-pill {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }
        .badge-done { background: #DCFCE7; color: #166534; }
        .badge-process { background: #DBEAFE; color: #1E40AF; }
        .badge-follow { background: #FEF3C7; color: #92400E; }
        .badge-danger { background: #FEE2E2; color: #991B1B; }

        /* KPI Grid in Print */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }
        .kpi-box {
            border: 1px solid #CBD5E1;
            border-radius: 6px;
            padding: 10px 12px;
            text-align: center;
            background: #F8FAFC;
        }
        .kpi-num {
            font-size: 20px;
            font-weight: 800;
            color: #101A35;
            line-height: 1.2;
        }
        .kpi-lbl {
            font-size: 10px;
            font-weight: 700;
            color: #64748B;
            text-transform: uppercase;
            margin-top: 4px;
        }

        /* Signature block */
        .signature-section {
            margin-top: 36px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            text-align: center;
            font-size: 12px;
            page-break-inside: avoid;
        }
        .sig-place-date {
            margin-bottom: 6px;
            color: #475569;
        }
        .sig-role {
            font-weight: 700;
            color: #101A35;
            margin-bottom: 65px;
        }
        .sig-name {
            font-weight: 800;
            color: #0F172A;
            text-decoration: underline;
        }
        .sig-nip {
            font-size: 11px;
            color: #475569;
            margin-top: 2px;
        }

        @media print {
            body { background: #FFFFFF; padding: 0; }
            .container { box-shadow: none; padding: 0; max-width: 100%; }
            .no-print { display: none !important; }
            .report-table th { background: #F1F5F9 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .badge-pill { border: 1px solid #CBD5E1; }
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Action Bar (Hidden on print) -->
    <div class="no-print">
        <div>
            <strong style="font-size: 14px;">Pratinjau Dokumen Cetak / PDF</strong>
            <p style="font-size: 11.5px; color: #94A3B8; margin-top: 2px;">
                Dokumen resmi siap cetak terintegrasi langsung dengan database Ruang BK.
            </p>
        </div>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('guru.laporan.index', ['tab' => 'berkala']) }}" class="btn-action btn-back">
                ← Kembali ke Hub Laporan
            </a>
            <button onclick="window.print()" class="btn-action btn-print">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                <span>Cetak / Simpan PDF</span>
            </button>
        </div>
    </div>

    <!-- Kop Surat Resmi -->
    <div class="kop-surat">
        <div class="kop-instansi">Pemerintah Daerah Provinsi Jawa Barat — Dinas Pendidikan</div>
        <div class="kop-instansi">Cabang Dinas Pendidikan Wilayah VIII</div>
        <div class="kop-sekolah">SMK NEGERI 1 SUMEDANG</div>
        <div class="kop-unit">Unit Layanan Bimbingan, Konseling & Pengembangan Karier Siswa</div>
        <div class="kop-alamat">
            Jalan Mayor Abdurrachman No. 209, Kotakaler, Kec. Sumedang Utara, Kabupaten Sumedang, Jawa Barat 45322 • Website: dico-ruangbk.my.id
        </div>
    </div>

    <!-- Judul Laporan -->
    <div class="doc-title-box">
        <div class="doc-title">{{ strtoupper($reportTitle) }}</div>
        <div class="doc-subtitle">{{ $periodLabel }}</div>
    </div>

    <!-- Identitas Dokumen & Pelaksana -->
    <table class="meta-table">
        <tr>
            <td class="meta-label">Nomor Dokumen</td>
            <td class="meta-colon">:</td>
            <td class="meta-value">421.5/BK-{{ strtoupper($periodType) }}/{{ $selectedYear }}/{{ str_pad($startDate->month, 2, '0', STR_PAD_LEFT) }}/{{ $user->id }}</td>
            <td class="meta-label">Tanggal Pelaporan</td>
            <td class="meta-colon">:</td>
            <td class="meta-value">{{ $today->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td class="meta-label">Penyusun Laporan</td>
            <td class="meta-colon">:</td>
            <td class="meta-value">
                @if($targetCounselor)
                    {{ $targetCounselor->name }} ({{ $targetCounselor->role_label }})
                @else
                    {{ $user->name }} ({{ $user->role_label }})
                @endif
            </td>
            <td class="meta-label">Lingkup Sasaran</td>
            <td class="meta-colon">:</td>
            <td class="meta-value">
                @if($targetCounselor)
                    Siswa Binaan Guru BK ({{ $targetCounselor->name }})
                @else
                    Seluruh Siswa Sekolah (Laporan Terpadu)
                @endif
            </td>
        </tr>
        <tr>
            <td class="meta-label">NIP / Identitas</td>
            <td class="meta-colon">:</td>
            <td class="meta-value">{{ $targetCounselor ? ($targetCounselor->nip ?: '-') : ($user->nip ?: '-') }}</td>
            <td class="meta-label">Status Verifikasi Data</td>
            <td class="meta-colon">:</td>
            <td class="meta-value" style="color: #166534;">Tersinkronisasi Otomatis Database Sistem</td>
        </tr>
    </table>

    <!-- 1. Ringkasan Eksekutif & KPI Beban Kasus -->
    <div class="section-heading">1. RINGKASAN EKSEKUTIF BEBAN LAYANAN BIMBINGAN</div>
    <div class="kpi-grid">
        <div class="kpi-box">
            <div class="kpi-num">{{ number_format($totalCases) }}</div>
            <div class="kpi-lbl">Total Kasus Layanan</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-num" style="color: #166534;">{{ number_format($completedCases) }}</div>
            <div class="kpi-lbl">Kasus Selesai (Tuntas)</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-num" style="color: #2563EB;">{{ $completionRate }}%</div>
            <div class="kpi-lbl">Tingkat Ketuntasan</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-num" style="color: #D97706;">{{ number_format($attentionStudents->count()) }}</div>
            <div class="kpi-lbl">Siswa Atensi Khusus</div>
        </div>
    </div>

    <!-- 2. Distribusi Kategori Masalah Bimbingan -->
    <div class="section-heading">2. DISTRIBUSI KATEGORI MASALAH KASUS KONSULTASI</div>
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th style="text-align: left;">Kategori Bidang Bimbingan</th>
                <th style="width: 120px;">Jumlah Kasus</th>
                <th style="width: 130px;">Proporsi (%)</th>
                <th style="text-align: left;">Deskripsi & Karakteristik Kasus</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categoryBreakdown as $idx => $cat)
                <tr>
                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                    <td style="font-weight: 700; color: #101A35;">{{ $cat['name'] }}</td>
                    <td style="text-align: center; font-weight: 700;">{{ $cat['count'] }} Kasus</td>
                    <td style="text-align: center;">{{ $cat['percentage'] }}%</td>
                    <td style="color: #475569;">
                        @if(str_contains(strtolower($cat['name']), 'belajar'))
                            Permasalahan motivasi, adaptasi pembelajaran, absensi, dan target nilai akademik.
                        @elseif(str_contains(strtolower($cat['name']), 'pribadi'))
                            Konseling emosional, manajemen stres, konsep diri, dan kepercayaan diri siswa.
                        @elseif(str_contains(strtolower($cat['name']), 'sosial'))
                            Dinamika pertemanan sebaya, komunikasi kelompok, dan resolusi konflik siswa.
                        @elseif(str_contains(strtolower($cat['name']), 'keluarga'))
                            Dukungan keluarga, keharmonisan lingkungan rumah, dan kendala ekonomi orang tua.
                        @else
                            Perencanaan studi lanjut PTN/PTS, magang kerja industri, dan karier vokasi.
                        @endif
                    </td>
                </tr>
            @endforeach
            <tr style="background: #F8FAFC; font-weight: 800;">
                <td colspan="2" style="text-align: right; padding-right: 14px;">TOTAL KASUS TERCATAT:</td>
                <td style="text-align: center;">{{ $totalCases }} Kasus</td>
                <td style="text-align: center;">100%</td>
                <td>Data agregat periode {{ $periodLabel }}</td>
            </tr>
        </tbody>
    </table>

    <!-- 3. Rekapitulasi Kasus & Penanganan Siswa Terperinci -->
    <div class="section-heading">3. REKAPITULASI DETAIL PENANGANAN SISWA DALAM PERIODE</div>
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 80px;">Tanggal</th>
                <th style="width: 90px;">Kode</th>
                <th style="text-align: left;">Nama Siswa & Kelas</th>
                <th style="text-align: left;">Topik Permasalahan</th>
                <th style="width: 90px;">Kategori</th>
                <th style="width: 75px;">Status</th>
                <th style="text-align: left;">Rencana Tindak Lanjut / Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($counselings as $idx => $csl)
                <tr>
                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                    <td style="text-align: center; white-space: nowrap;">
                        {{ $csl->created_at->format('d/m/Y') }}
                    </td>
                    <td style="text-align: center; font-family: monospace; font-weight: 600;">
                        {{ $csl->code ?: 'KSL-' . $csl->id }}
                    </td>
                    <td>
                        <strong style="color: #101A35;">{{ $csl->student->name ?? 'Siswa' }}</strong>
                        <div style="font-size: 10px; color: #64748B;">
                            NISN: {{ $csl->student->nisn ?? '-' }} • {{ $csl->student->studentClass->name ?? '-' }}
                        </div>
                    </td>
                    <td>
                        {{ $csl->topic ?: 'Konsultasi Layanan Bimbingan' }}
                    </td>
                    <td>{{ $csl->category->name ?? '-' }}</td>
                    <td style="text-align: center;">
                        @if($csl->status === 'selesai')
                            <span class="badge-pill badge-done">Selesai</span>
                        @elseif(in_array($csl->status, ['dilaksanakan', 'dijadwalkan']))
                            <span class="badge-pill badge-process">Proses</span>
                        @elseif($csl->status === 'tindak_lanjut')
                            <span class="badge-pill badge-follow">Tindak Lanjut</span>
                        @else
                            <span class="badge-pill" style="background: #F1F5F9; color: #475569;">{{ ucfirst($csl->status) }}</span>
                        @endif
                    </td>
                    <td style="font-size: 11px; color: #334155;">
                        {{ \Illuminate\Support\Str::limit($csl->student_action_plan ?: ($csl->counselor_notes ?: 'Penanganan berlangsung sesuai kesepakatan konseling.'), 80) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; color: #64748B; padding: 18px;">
                        Tidak ada aktivitas konseling yang tercatat pada rentang waktu {{ $periodLabel }}.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- 4. Pemantauan Siswa Perlu Perhatian Khusus -->
    @if($attentionStudents->count() > 0)
        <div class="section-heading">4. DAFTAR SISWA MEMERLUKAN PERHATIAN & TINDAK LANJUT CEPAT</div>
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th style="text-align: left;">Nama Siswa</th>
                    <th style="width: 100px;">NISN</th>
                    <th style="width: 100px;">Kelas</th>
                    <th style="width: 130px;">Tingkat Perhatian</th>
                    <th style="text-align: left;">Fokus Pembinaan & Kondisi Terakhir</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attentionStudents as $idx => $att)
                    <tr>
                        <td style="text-align: center;">{{ $idx + 1 }}</td>
                        <td style="font-weight: 700; color: #101A35;">{{ $att->name }}</td>
                        <td style="text-align: center; font-family: monospace;">{{ $att->nisn }}</td>
                        <td style="text-align: center;">{{ $att->studentClass->name ?? '-' }}</td>
                        <td style="text-align: center;">
                            @if($att->attention_level === 'segera_ditindaklanjuti')
                                <span class="badge-pill badge-danger">Segera Ditindaklanjuti</span>
                            @elseif($att->attention_level === 'prioritas')
                                <span class="badge-pill" style="background: #FFEDD5; color: #C2410C;">Prioritas</span>
                            @else
                                <span class="badge-pill badge-follow">Perlu Perhatian</span>
                            @endif
                        </td>
                        <td style="color: #334155;">
                            {{ $att->special_notes ?: 'Memerlukan pemantauan berkala dan koordinasi dengan wali kelas serta orang tua siswa.' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- 5. Khusus Koordinator: Beban Penanganan Guru BK -->
    @if($isKoordinator && count($counselorWorkload) > 0)
        <div class="section-heading">5. REKAPITULASI KINERJA & BEBAN PENANGANAN GURU BK</div>
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 35px;">No</th>
                    <th style="text-align: left;">Nama Guru BK / Konselor</th>
                    <th style="width: 130px;">NIP / Identitas</th>
                    <th style="width: 120px;">Siswa Binaan</th>
                    <th style="width: 100px;">Kasus Masuk</th>
                    <th style="width: 100px;">Kasus Selesai</th>
                    <th style="width: 100px;">Ketuntasan (%)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($counselorWorkload as $idx => $cw)
                    <tr>
                        <td style="text-align: center;">{{ $idx + 1 }}</td>
                        <td>
                            <strong style="color: #101A35;">{{ $cw['counselor']->name }}</strong>
                            <div style="font-size: 10px; color: #64748B;">{{ $cw['counselor']->role_label }}</div>
                        </td>
                        <td style="text-align: center; font-family: monospace;">{{ $cw['counselor']->nip ?: '-' }}</td>
                        <td style="text-align: center; font-weight: 700;">{{ $cw['assigned_students'] }} Siswa</td>
                        <td style="text-align: center; font-weight: 700;">{{ $cw['total_cases'] }} Kasus</td>
                        <td style="text-align: center; font-weight: 700; color: #166534;">{{ $cw['completed_cases'] }}</td>
                        <td style="text-align: center; font-weight: 700; color: #2563EB;">{{ $cw['completion_rate'] }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- 6. Evaluasi & Catatan Program BK -->
    <div class="section-heading">6. EVALUASI DAN REKOMENDASI TINDAK LANJUT</div>
    <div style="border: 1px solid #CBD5E1; border-radius: 6px; padding: 12px 14px; background: #F8FAFC; margin-bottom: 24px; font-size: 12px; color: #334155; line-height: 1.6;">
        <p style="margin-bottom: 6px;">
            <strong>A. Capaian Periode Ini:</strong> Penanganan layanan konseling pada rentang waktu {{ $periodLabel }} telah mencapai tingkat ketuntasan sebesar <strong>{{ $completionRate }}%</strong> dari total <strong>{{ $totalCases }} kasus</strong> yang ditangani secara langsung oleh tim bimbingan konseling.
        </p>
        <p style="margin-bottom: 6px;">
            <strong>B. Fokus Intervensi:</strong> Kasus didominasi oleh bidang <em>{{ $categoryBreakdown[0]['name'] ?? 'Bimbingan Konseling' }}</em>. Koordinasi lintas wali kelas dan guru mata pelajaran terus diperkuat untuk mencegah eskalasi masalah siswa ke tingkat prioritas tinggi.
        </p>
        <p>
            <strong>C. Rencana Tindak Lanjut:</strong> Melanjutkan pendampingan intensif terhadap {{ $attentionStudents->count() }} siswa dengan tingkat atensi khusus serta memfasilitasi konsultasi lanjutan bagi siswa yang masih dalam tahap proses bimbingan.
        </p>
    </div>

    <!-- Kolom Tanda Tangan Resmi -->
    <div class="signature-section">
        <!-- Kolom Kiri -->
        <div>
            <div class="sig-place-date">Mengetahui,</div>
            @if($isKoordinator && (!$targetCounselor || $targetCounselor->id === $user->id))
                <div class="sig-role">Kepala Sekolah SMKN 1 Sumedang</div>
                <div class="sig-name">Drs. H. Maman Sulaeman, M.M.</div>
                <div class="sig-nip">NIP. 19680512 199403 1 004</div>
            @else
                <div class="sig-role">Guru Koordinator Bimbingan Konseling</div>
                <div class="sig-name">{{ $koordinator->name ?? 'Dra. Hj. Siti Rahmawati, M.Pd.' }}</div>
                <div class="sig-nip">NIP. {{ $koordinator->nip ?? '19740822 199903 2 001' }}</div>
            @endif
        </div>

        <!-- Kolom Kanan -->
        <div>
            <div class="sig-place-date">Sumedang, {{ $today->translatedFormat('d F Y') }}</div>
            <div class="sig-role">
                @if($targetCounselor)
                    Guru BK Pembimbing / Konselor
                @elseif($isKoordinator)
                    Guru Koordinator Bimbingan Konseling
                @else
                    Guru BK Pembimbing / Konselor
                @endif
            </div>
            <div class="sig-name">
                {{ $targetCounselor ? $targetCounselor->name : $user->name }}
            </div>
            <div class="sig-nip">
                NIP. {{ $targetCounselor ? ($targetCounselor->nip ?: '-') : ($user->nip ?: '-') }}
            </div>
        </div>
    </div>
</div>
</body>
</html>
