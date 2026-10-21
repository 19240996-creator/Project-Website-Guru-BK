@extends('layouts.app')

@section('title', 'Pengaturan Sekolah & Kop Surat - Koordinator BK')
@section('header_title', 'Pengaturan Identitas Sekolah')

@section('content')
<!-- Page Header Bar -->
<div class="page-header-bar" style="margin-bottom: 24px;">
    <div>
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
            <span class="badge badge-primary" style="font-size: 11px; padding: 3px 8px; font-weight: 700;">
                KOORDINATOR BK
            </span>
            <span style="font-size: 12px; color: var(--color-text-muted);">
                Otoritas Konfigurasi Lembaga
            </span>
        </div>
        <h1 class="page-header-title">Pengaturan Identitas Sekolah &amp; Kop Surat Resmi</h1>
        <p style="font-size: 13px; color: var(--color-text-muted); margin: 4px 0 0 0;">
            Kelola nama sekolah, cabang dinas pendidikan, alamat jalan, serta profil pimpinan yang dicetak pada seluruh kop surat dokumen laporan BK.
        </p>
    </div>
    <div class="page-header-date">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
        <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success" style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px; padding: 12px 18px; border-radius: 8px; background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.3); color: #065F46;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        <span style="font-size: 13px; font-weight: 600;">{{ session('success') }}</span>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger" style="margin-bottom: 20px; padding: 12px 18px; border-radius: 8px; background: rgba(239, 68, 68, 0.12); border: 1px solid rgba(239, 68, 68, 0.3); color: #991B1B;">
        <div style="font-size: 13px; font-weight: 700; margin-bottom: 6px;">Terdapat kesalahan pengisian data:</div>
        <ul style="margin: 0; padding-left: 20px; font-size: 12px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid-2" style="grid-template-columns: 1.15fr 0.85fr; gap: 24px; align-items: flex-start;">
    <!-- Form Pengaturan Sekolah -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header card-header-navy" style="display: flex; align-items: center; justify-content: space-between; padding: 14px 20px;">
            <div>
                <h3 class="card-title" style="margin: 0; font-size: 15px; color: #FFFFFF;">
                    Formulir Identitas Sekolah &amp; Cabang Pendidikan
                </h3>
                <p style="font-size: 12px; color: #CCD8FD; margin: 2px 0 0 0;">
                    Perubahan langsung berlaku ke seluruh dokumen laporan siap cetak.
                </p>
            </div>
            <span class="badge" style="background: rgba(255,255,255,0.2); color: #FFFFFF; font-size: 11px;">
                Tersinkronisasi
            </span>
        </div>
        <div class="card-body" style="padding: 22px;">
            <form action="{{ route('guru.koordinator.pengaturan.update') }}" method="POST">
                @csrf
                @method('PUT')

                <!-- 1. Dinas & Cabang Pendidikan -->
                <div style="margin-bottom: 18px;">
                    <label class="form-label" style="font-size: 12.5px; font-weight: 700; color: var(--color-navy); margin-bottom: 5px;">
                        Pemerintah Daerah / Dinas Pendidikan Provinsi <span style="color: #EF4444;">*</span>
                    </label>
                    <input type="text" 
                           name="provincial_agency" 
                           id="inputProvincialAgency"
                           value="{{ old('provincial_agency', $setting->provincial_agency) }}" 
                           class="form-control" 
                           required 
                           placeholder="Contoh: PEMERINTAH DAERAH PROVINSI JAWA BARAT — DINAS PENDIDIKAN"
                           style="min-height: 40px; font-size: 13px;"
                           oninput="updateLiveKop()">
                    <small style="color: var(--color-text-muted); font-size: 11px; margin-top: 3px; display: block;">
                        Baris pertama pada kop surat dinas.
                    </small>
                </div>

                <div style="margin-bottom: 18px;">
                    <label class="form-label" style="font-size: 12.5px; font-weight: 700; color: var(--color-navy); margin-bottom: 5px;">
                        Cabang Dinas Pendidikan <span style="color: #EF4444;">*</span>
                    </label>
                    <input type="text" 
                           name="education_branch" 
                           id="inputEducationBranch"
                           value="{{ old('education_branch', $setting->education_branch) }}" 
                           class="form-control" 
                           required 
                           placeholder="Contoh: CABANG DINAS PENDIDIKAN WILAYAH VIII"
                           style="min-height: 40px; font-size: 13px;"
                           oninput="updateLiveKop()">
                    <small style="color: var(--color-text-muted); font-size: 11px; margin-top: 3px; display: block;">
                        Unit wilayah kerja cabang dinas pendidikan terkait.
                    </small>
                </div>

                <!-- 2. Nama Sekolah -->
                <div style="margin-bottom: 18px;">
                    <label class="form-label" style="font-size: 12.5px; font-weight: 700; color: var(--color-navy); margin-bottom: 5px;">
                        Nama Lengkap Sekolah <span style="color: #EF4444;">*</span>
                    </label>
                    <input type="text" 
                           name="school_name" 
                           id="inputSchoolName"
                           value="{{ old('school_name', $setting->school_name) }}" 
                           class="form-control" 
                           required 
                           placeholder="Contoh: SMK NEGERI 1 SUMEDANG"
                           style="min-height: 40px; font-size: 13.5px; font-weight: 700; letter-spacing: 0.02em;"
                           oninput="updateLiveKop()">
                    <small style="color: var(--color-text-muted); font-size: 11px; margin-top: 3px; display: block;">
                        Ditampilkan berukuran besar pada judul kop surat dan legalitas dokumen.
                    </small>
                </div>

                <!-- 3. Nama Unit BK -->
                <div style="margin-bottom: 18px;">
                    <label class="form-label" style="font-size: 12.5px; font-weight: 700; color: var(--color-navy); margin-bottom: 5px;">
                        Nama Unit / Organisasi BK <span style="color: #EF4444;">*</span>
                    </label>
                    <input type="text" 
                           name="unit_name" 
                           id="inputUnitName"
                           value="{{ old('unit_name', $setting->unit_name) }}" 
                           class="form-control" 
                           required 
                           placeholder="Contoh: UNIT LAYANAN BIMBINGAN, KONSELING & PENGEMBANGAN KARIER SISWA"
                           style="min-height: 40px; font-size: 13px;"
                           oninput="updateLiveKop()">
                    <small style="color: var(--color-text-muted); font-size: 11px; margin-top: 3px; display: block;">
                        Sub-unit pelaksana bimbingan konseling.
                    </small>
                </div>

                <!-- 4. Alamat Lengkap & Jalan -->
                <div style="margin-bottom: 18px;">
                    <label class="form-label" style="font-size: 12.5px; font-weight: 700; color: var(--color-navy); margin-bottom: 5px;">
                        Alamat Jalan &amp; Domisili Sekolah <span style="color: #EF4444;">*</span>
                    </label>
                    <textarea name="address" 
                              id="inputAddress"
                              rows="3" 
                              class="form-control" 
                              required 
                              placeholder="Contoh: Jalan Mayor Abdurrachman No. 209, Kotakaler, Kec. Sumedang Utara, Kabupaten Sumedang, Jawa Barat 45322"
                              style="font-size: 13px; line-height: 1.5;"
                              oninput="updateLiveKop()">{{ old('address', $setting->address) }}</textarea>
                    <small style="color: var(--color-text-muted); font-size: 11px; margin-top: 3px; display: block;">
                        Alamat jalan lengkap yang tertera di baris bawah kop surat resmi.
                    </small>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 18px;">
                    <div>
                        <label class="form-label" style="font-size: 12px; font-weight: 600; margin-bottom: 5px;">
                            Kota / Kabupaten
                        </label>
                        <input type="text" 
                               name="city" 
                               id="inputCity"
                               value="{{ old('city', $setting->city) }}" 
                               class="form-control" 
                               placeholder="Contoh: Sumedang"
                               style="min-height: 38px; font-size: 13px;"
                               oninput="updateLiveKop()">
                        <small style="color: var(--color-text-muted); font-size: 11px; margin-top: 3px; display: block;">
                            Untuk penulisan titimangsa tanda tangan (misal: <em>Sumedang, 21 Oktober 2026</em>).
                        </small>
                    </div>

                    <div>
                        <label class="form-label" style="font-size: 12px; font-weight: 600; margin-bottom: 5px;">
                            Kode Pos
                        </label>
                        <input type="text" 
                               name="postal_code" 
                               id="inputPostalCode"
                               value="{{ old('postal_code', $setting->postal_code) }}" 
                               class="form-control" 
                               placeholder="Contoh: 45322"
                               style="min-height: 38px; font-size: 13px;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 22px;">
                    <div>
                        <label class="form-label" style="font-size: 12px; font-weight: 600; margin-bottom: 5px;">
                            Website / Portal Sekolah
                        </label>
                        <input type="text" 
                               name="website" 
                               id="inputWebsite"
                               value="{{ old('website', $setting->website) }}" 
                               class="form-control" 
                               placeholder="Contoh: dico-ruangbk.my.id"
                               style="min-height: 38px; font-size: 13px;"
                               oninput="updateLiveKop()">
                    </div>

                    <div>
                        <label class="form-label" style="font-size: 12px; font-weight: 600; margin-bottom: 5px;">
                            Telepon / Kontak
                        </label>
                        <input type="text" 
                               name="phone" 
                               id="inputPhone"
                               value="{{ old('phone', $setting->phone) }}" 
                               class="form-control" 
                               placeholder="Contoh: (0261) 201531"
                               style="min-height: 38px; font-size: 13px;">
                    </div>
                </div>

                <!-- 5. Profil Pimpinan Sekolah -->
                <div style="padding: 14px; background: var(--color-surface-subtle, #F8FAFD); border-radius: 8px; border: 1px solid var(--color-border); margin-bottom: 16px;">
                    <div style="font-size: 13px; font-weight: 700; color: var(--color-navy); margin-bottom: 4px;">
                        Pejabat Kepala Sekolah (Penanda Tangan Laporan)
                    </div>
                    <p style="font-size: 11.5px; color: var(--color-text-muted); margin: 0 0 12px 0;">
                        Nama dan NIP kepala sekolah yang dicantumkan pada bagian lembar pengesahan / tanda tangan mengetahui.
                    </p>

                    <div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 12px;">
                        <div>
                            <label class="form-label" style="font-size: 11.5px; font-weight: 600; margin-bottom: 4px;">Nama Lengkap &amp; Gelar</label>
                            <input type="text" 
                                   name="headmaster_name" 
                                   id="inputHeadmasterName"
                                   value="{{ old('headmaster_name', $setting->headmaster_name) }}" 
                                   class="form-control" 
                                   placeholder="Contoh: Dra. Hj. Elis Herawati, M.Pd."
                                   style="min-height: 36px; font-size: 12.5px;"
                                   oninput="updateLiveKop()">
                        </div>
                        <div>
                            <label class="form-label" style="font-size: 11.5px; font-weight: 600; margin-bottom: 4px;">NIP Kepala Sekolah</label>
                            <input type="text" 
                                   name="headmaster_nip" 
                                   id="inputHeadmasterNip"
                                   value="{{ old('headmaster_nip', $setting->headmaster_nip) }}" 
                                   class="form-control" 
                                   placeholder="Contoh: 196804151994032008"
                                   style="min-height: 36px; font-size: 12.5px;"
                                   oninput="updateLiveKop()">
                        </div>
                    </div>
                </div>

                <!-- 6. Pejabat Wakasek Kesiswaan -->
                <div style="padding: 14px; background: var(--color-surface-subtle, #F8FAFD); border-radius: 8px; border: 1px solid var(--color-border); margin-bottom: 16px;">
                    <div style="font-size: 13px; font-weight: 700; color: var(--color-navy); margin-bottom: 4px;">
                        Pejabat Wakasek Kesiswaan (Penanda Tangan Paket Laporan Kesiswaan)
                    </div>
                    <p style="font-size: 11.5px; color: var(--color-text-muted); margin: 0 0 12px 0;">
                        Nama dan NIP Wakil Kepala Sekolah Bidang Kesiswaan pada lembar pengesahan Paket Laporan Kesiswaan (Paket A).
                    </p>

                    <div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 12px;">
                        <div>
                            <label class="form-label" style="font-size: 11.5px; font-weight: 600; margin-bottom: 4px;">Nama Lengkap &amp; Gelar</label>
                            <input type="text" 
                                   name="vice_headmaster_kesiswaan_name" 
                                   id="inputViceKesiswaanName"
                                   value="{{ old('vice_headmaster_kesiswaan_name', $setting->vice_headmaster_kesiswaan_name) }}" 
                                   class="form-control" 
                                   placeholder="Contoh: Drs. H. Ahmad Sudrajat, M.M.Pd."
                                   style="min-height: 36px; font-size: 12.5px;">
                        </div>
                        <div>
                            <label class="form-label" style="font-size: 11.5px; font-weight: 600; margin-bottom: 4px;">NIP Wakasek Kesiswaan</label>
                            <input type="text" 
                                   name="vice_headmaster_kesiswaan_nip" 
                                   id="inputViceKesiswaanNip"
                                   value="{{ old('vice_headmaster_kesiswaan_nip', $setting->vice_headmaster_kesiswaan_nip) }}" 
                                   class="form-control" 
                                   placeholder="Contoh: 197203101998021003"
                                   style="min-height: 36px; font-size: 12.5px;">
                        </div>
                    </div>
                </div>

                <!-- 7. Pejabat Wakasek Kurikulum / Hubin -->
                <div style="padding: 14px; background: var(--color-surface-subtle, #F8FAFD); border-radius: 8px; border: 1px solid var(--color-border); margin-bottom: 24px;">
                    <div style="font-size: 13px; font-weight: 700; color: var(--color-navy); margin-bottom: 4px;">
                        Pejabat Wakasek Kurikulum / Hubin (Penanda Tangan Paket Laporan Kurikulum &amp; Mitra)
                    </div>
                    <p style="font-size: 11.5px; color: var(--color-text-muted); margin: 0 0 12px 0;">
                        Nama dan NIP Wakil Kepala Sekolah Bidang Kurikulum / Hubin pada lembar pengesahan Paket Laporan Agenda &amp; Mitra (Paket B).
                    </p>

                    <div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 12px;">
                        <div>
                            <label class="form-label" style="font-size: 11.5px; font-weight: 600; margin-bottom: 4px;">Nama Lengkap &amp; Gelar</label>
                            <input type="text" 
                                   name="vice_headmaster_kurikulum_name" 
                                   id="inputViceKurikulumName"
                                   value="{{ old('vice_headmaster_kurikulum_name', $setting->vice_headmaster_kurikulum_name) }}" 
                                   class="form-control" 
                                   placeholder="Contoh: Cecep Suhendar, S.T., M.Kom."
                                   style="min-height: 36px; font-size: 12.5px;">
                        </div>
                        <div>
                            <label class="form-label" style="font-size: 11.5px; font-weight: 600; margin-bottom: 4px;">NIP Wakasek Kurikulum</label>
                            <input type="text" 
                                   name="vice_headmaster_kurikulum_nip" 
                                   id="inputViceKurikulumNip"
                                   value="{{ old('vice_headmaster_kurikulum_nip', $setting->vice_headmaster_kurikulum_nip) }}" 
                                   class="form-control" 
                                   placeholder="Contoh: 197805202006041011"
                                   style="min-height: 36px; font-size: 12.5px;">
                        </div>
                    </div>
                </div>

                <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; border-top: 1px solid var(--color-border); padding-top: 18px;">
                    <span style="font-size: 12px; color: var(--color-text-muted);">
                        Pastikan data sekolah terisi akurat untuk standardisasi dokumen resmi.
                    </span>
                    <button type="submit" class="btn btn-primary" style="font-weight: 700; padding: 10px 24px; display: inline-flex; align-items: center; gap: 8px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                        <span>Simpan Pengaturan Sekolah</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Live Pratinjau Kop Surat Resmi -->
    <div style="position: sticky; top: 20px;">
        <div class="card" style="margin-bottom: 0; box-shadow: 0 4px 16px rgba(0,0,0,0.06);">
            <div class="card-header card-header-navy" style="display: flex; align-items: center; justify-content: space-between; padding: 14px 20px;">
                <div>
                    <h3 class="card-title" style="margin: 0; font-size: 14px; color: #FFFFFF;">
                        Pratinjau Nyata Kop Surat Resmi
                    </h3>
                    <p style="font-size: 11.5px; color: #CCD8FD; margin: 2px 0 0 0;">
                        Visualisasi cetak langsung dokumen laporan resmi
                    </p>
                </div>
                <span class="badge" style="background: rgba(255,255,255,0.2); color: #FFFFFF; font-size: 10.5px;">
                    Live Preview
                </span>
            </div>
            <div class="card-body" style="padding: 24px 20px; background: #FFFFFF;">
                <div style="border: 1px dashed #CBD5E1; border-radius: 8px; padding: 20px; background: #FAFBFD;">
                    <!-- Simulasi Kop Surat -->
                    <div style="text-align: center; border-bottom: 3px double #000000; padding-bottom: 12px; margin-bottom: 16px;">
                        <div id="previewProvincialAgency" style="font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: #1E293B;">
                            {{ $setting->provincial_agency }}
                        </div>
                        <div id="previewEducationBranch" style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: #1E293B; margin-top: 1px;">
                            {{ $setting->education_branch }}
                        </div>
                        <div id="previewSchoolName" style="font-size: 16px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: #101A35; margin: 3px 0;">
                            {{ $setting->school_name }}
                        </div>
                        <div id="previewUnitName" style="font-size: 11.5px; font-weight: 700; color: #2563EB; text-transform: uppercase; letter-spacing: 0.03em;">
                            {{ $setting->unit_name }}
                        </div>
                        <div id="previewAddress" style="font-size: 10.5px; color: #475569; margin-top: 4px; line-height: 1.4;">
                            {{ $setting->address }} • Website: {{ $setting->website ?: 'dico-ruangbk.my.id' }}
                        </div>
                    </div>

                    <!-- Dummy Judul Laporan -->
                    <div style="text-align: center; margin-bottom: 18px;">
                        <div style="font-size: 13px; font-weight: 800; text-transform: uppercase; color: #101A35;">
                            LAPORAN PERIODIK LAYANAN BIMBINGAN &amp; KONSELING
                        </div>
                        <div style="font-size: 11px; font-weight: 600; color: #64748B;">
                            Periode Aktif Kalender Akademik
                        </div>
                    </div>

                    <!-- Dummy Isi Singkat -->
                    <div style="padding: 10px; background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 6px; font-size: 11px; color: #64748B; text-align: center; margin-bottom: 18px;">
                        [Tabel Penanganan Kasus Konseling, Analisis Kategori, &amp; Agenda Siswa]
                    </div>

                    <!-- Simulasi Tanda Tangan -->
                    <div style="display: flex; justify-content: flex-end;">
                        <div style="text-align: center; font-size: 11px; min-width: 170px;">
                            <div id="previewPlaceDate" style="color: #475569; margin-bottom: 4px;">
                                {{ $setting->city ?: 'Sumedang' }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                            </div>
                            <div id="previewRoleHeadmaster" style="font-weight: 600; color: #101A35; margin-bottom: 40px;">
                                Kepala Sekolah {{ $setting->school_name }}
                            </div>
                            <div id="previewHeadmasterName" style="font-weight: 700; text-decoration: underline; color: #101A35;">
                                {{ $setting->headmaster_name }}
                            </div>
                            <div id="previewHeadmasterNip" style="font-family: monospace; font-size: 10px; color: #64748B;">
                                NIP. {{ $setting->headmaster_nip }}
                            </div>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 14px; display: flex; align-items: center; justify-content: space-between;">
                    <span style="font-size: 11.5px; color: var(--color-text-muted);">Format Dokumen:</span>
                    <a href="{{ route('guru.laporan.generate_periodic', ['period_type' => 'bulanan']) }}" target="_blank" class="btn btn-secondary btn-sm" style="font-size: 11.5px; padding: 4px 10px;">
                        ↗ Buka Contoh Dokumen Cetak
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function updateLiveKop() {
        var agency = document.getElementById('inputProvincialAgency')?.value || 'PEMERINTAH DAERAH PROVINSI JAWA BARAT — DINAS PENDIDIKAN';
        var branch = document.getElementById('inputEducationBranch')?.value || 'CABANG DINAS PENDIDIKAN WILAYAH VIII';
        var school = document.getElementById('inputSchoolName')?.value || 'SMK NEGERI 1 SUMEDANG';
        var unit = document.getElementById('inputUnitName')?.value || 'UNIT LAYANAN BIMBINGAN, KONSELING & PENGEMBANGAN KARIER SISWA';
        var address = document.getElementById('inputAddress')?.value || '';
        var website = document.getElementById('inputWebsite')?.value || 'dico-ruangbk.my.id';
        var city = document.getElementById('inputCity')?.value || 'Sumedang';
        var headmasterName = document.getElementById('inputHeadmasterName')?.value || 'Dra. Hj. Elis Herawati, M.Pd.';
        var headmasterNip = document.getElementById('inputHeadmasterNip')?.value || '196804151994032008';

        var pAgency = document.getElementById('previewProvincialAgency');
        var pBranch = document.getElementById('previewEducationBranch');
        var pSchool = document.getElementById('previewSchoolName');
        var pUnit = document.getElementById('previewUnitName');
        var pAddress = document.getElementById('previewAddress');
        var pPlace = document.getElementById('previewPlaceDate');
        var pRole = document.getElementById('previewRoleHeadmaster');
        var pHeadName = document.getElementById('previewHeadmasterName');
        var pHeadNip = document.getElementById('previewHeadmasterNip');

        if (pAgency) pAgency.innerText = agency;
        if (pBranch) pBranch.innerText = branch;
        if (pSchool) pSchool.innerText = school;
        if (pUnit) pUnit.innerText = unit;
        if (pAddress) pAddress.innerText = address + (website ? ' • Website: ' + website : '');
        if (pPlace) pPlace.innerText = city + ', {{ \Carbon\Carbon::now()->translatedFormat("d F Y") }}';
        if (pRole) pRole.innerText = 'Kepala Sekolah ' + school;
        if (pHeadName) pHeadName.innerText = headmasterName;
        if (pHeadNip) pHeadNip.innerText = 'NIP. ' + headmasterNip;
    }
</script>
@endsection
