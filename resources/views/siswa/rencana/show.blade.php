@extends('layouts.siswa')

@section('title', 'Rute Masa Depan Saya')

@section('content')
<div style="max-width: 840px; margin: 0 auto;">
    <div style="margin-bottom: 24px;">
        <h2 style="font-size: 20px; font-weight: 800; color: var(--color-text-main);">Rute Masa Depan Saya</h2>
        <p style="font-size: 13px; color: var(--color-text-muted);">
            Tentukan ke mana langkah Anda setelah lulus sekolah. Rencana ini dapat Anda perbarui kapan saja seiring bertambahnya wawasan dan minat Anda.
        </p>
    </div>

    <!-- Formulir Pembaruan Rencana Masa Depan -->
    <div class="card" style="margin-bottom: 28px;">
        <div class="card-header">
            <h3 class="card-title">Pilih Arah Pilihan Utama Anda</h3>
            @if($currentPlan)
                <span class="badge badge-primary">Versi ke-{{ $currentPlan->version }} (Aktif)</span>
            @endif
        </div>
        <div class="card-body">
            <form action="{{ route('siswa.rencana.update') }}" method="POST" id="planForm">
                @csrf

                <!-- Pilihan Jalur Utama (Radio Buttons dengan 4 Kartu Pilihan) -->
                <div class="form-group">
                    <label class="form-label" style="margin-bottom: 12px;">Pilihan Prioritas Setelah Lulus Sekolah:</label>
                    <div class="grid-4" style="gap: 12px;">
                        @php
                            $goal = $currentPlan ? $currentPlan->primary_goal : 'kuliah';
                            if ($goal === 'belum_menentukan' || empty($goal)) {
                                $goal = 'kuliah';
                            }
                        @endphp
                        <label id="card-kuliah" style="border: 2px solid {{ $goal === 'kuliah' ? 'var(--color-primary)' : 'var(--color-border)' }}; padding: 16px; border-radius: var(--radius-md); cursor: pointer; background: {{ $goal === 'kuliah' ? 'var(--color-primary-light)' : '#ffffff' }}; display: flex; flex-direction: column; justify-content: flex-start; transition: all 0.2s ease;">
                            <input type="radio" name="primary_goal" value="kuliah" {{ $goal === 'kuliah' ? 'checked' : '' }} onchange="toggleSections('kuliah')" style="accent-color: var(--color-primary);">
                            <div style="font-weight: 800; font-size: 14px; margin-top: 8px; color: var(--color-primary);">1. Ingin Kuliah</div>
                            <div style="font-size: 12px; color: var(--color-text-muted); margin-top: 4px; line-height: 1.4;">Melanjutkan pendidikan diploma atau sarjana di PTN, PTS, atau Kedinasan.</div>
                        </label>

                        <label id="card-bekerja" style="border: 2px solid {{ $goal === 'bekerja' ? 'var(--color-accent)' : 'var(--color-border)' }}; padding: 16px; border-radius: var(--radius-md); cursor: pointer; background: {{ $goal === 'bekerja' ? 'var(--color-accent-light)' : '#ffffff' }}; display: flex; flex-direction: column; justify-content: flex-start; transition: all 0.2s ease;">
                            <input type="radio" name="primary_goal" value="bekerja" {{ $goal === 'bekerja' ? 'checked' : '' }} onchange="toggleSections('bekerja')" style="accent-color: var(--color-accent);">
                            <div style="font-weight: 800; font-size: 14px; margin-top: 8px; color: var(--color-accent);">2. Ingin Bekerja</div>
                            <div style="font-size: 12px; color: var(--color-text-muted); margin-top: 4px; line-height: 1.4;">Langsung berkarier di industri manufaktur, IT, perbankan, atau swasta/BUMN.</div>
                        </label>

                        <label id="card-kuliah_kerja" style="border: 2px solid {{ $goal === 'kuliah_kerja' ? 'var(--color-info)' : 'var(--color-border)' }}; padding: 16px; border-radius: var(--radius-md); cursor: pointer; background: {{ $goal === 'kuliah_kerja' ? 'var(--color-info-bg)' : '#ffffff' }}; display: flex; flex-direction: column; justify-content: flex-start; transition: all 0.2s ease;">
                            <input type="radio" name="primary_goal" value="kuliah_kerja" {{ $goal === 'kuliah_kerja' ? 'checked' : '' }} onchange="toggleSections('kuliah_kerja')" style="accent-color: var(--color-info);">
                            <div style="font-weight: 800; font-size: 14px; margin-top: 8px; color: var(--color-info);">3. Ingin Kuliah Sambil Bekerja</div>
                            <div style="font-size: 12px; color: var(--color-text-muted); margin-top: 4px; line-height: 1.4;">Menempuh perkuliahan sembari mandiri berpenghasilan dan berkarier.</div>
                        </label>

                        <label id="card-wirausaha" style="border: 2px solid {{ $goal === 'wirausaha' ? 'var(--color-warning)' : 'var(--color-border)' }}; padding: 16px; border-radius: var(--radius-md); cursor: pointer; background: {{ $goal === 'wirausaha' ? 'var(--color-warning-bg)' : '#ffffff' }}; display: flex; flex-direction: column; justify-content: flex-start; transition: all 0.2s ease;">
                            <input type="radio" name="primary_goal" value="wirausaha" {{ $goal === 'wirausaha' ? 'checked' : '' }} onchange="toggleSections('wirausaha')" style="accent-color: var(--color-warning);">
                            <div style="font-weight: 800; font-size: 14px; margin-top: 8px; color: var(--color-warning);">4. Ingin Wirausaha</div>
                            <div style="font-size: 12px; color: var(--color-text-muted); margin-top: 4px; line-height: 1.4;">Membuka usaha mandiri, UMKM, rintisan bisnis jasa atau produk.</div>
                        </label>
                    </div>
                </div>

                <!-- Bagian Form Kuliah -->
                <div id="sectionKuliah" style="display: {{ $goal === 'kuliah' ? 'block' : 'none' }}; padding: 20px; background: #f8fafc; border: 1px solid var(--color-border); border-radius: var(--radius-sm); margin-bottom: 20px;">
                    <h4 style="font-size: 14px; font-weight: 700; color: var(--color-primary); margin-bottom: 14px;">Rincian Rencana Perguruan Tinggi</h4>
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Target Perguruan Tinggi</label>
                            <input type="text" name="college_target" class="form-control" value="{{ old('college_target', $currentPlan && $currentPlan->primary_goal === 'kuliah' ? $currentPlan->college_target : '') }}" placeholder="Contoh: ITB, UI, Telkom University, UNPAD...">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Program Studi Tujuan</label>
                            <input type="text" name="study_program" class="form-control" value="{{ old('study_program', $currentPlan && $currentPlan->primary_goal === 'kuliah' ? $currentPlan->study_program : '') }}" placeholder="Contoh: Teknik Informatika, Manajemen Bisnis...">
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Target Jalur Seleksi Masuk</label>
                        <select name="entry_path" class="form-select">
                            <option value="SNBP" {{ $currentPlan && $currentPlan->entry_path === 'SNBP' ? 'selected' : '' }}>SNBP (Prestasi Rapor)</option>
                            <option value="SNBT" {{ $currentPlan && $currentPlan->entry_path === 'SNBT' ? 'selected' : '' }}>SNBT (Tes UTBK)</option>
                            <option value="Mandiri" {{ $currentPlan && $currentPlan->entry_path === 'Mandiri' ? 'selected' : '' }}>Seleksi Mandiri PTN</option>
                            <option value="PTS_Beasiswa" {{ $currentPlan && $currentPlan->entry_path === 'PTS_Beasiswa' ? 'selected' : '' }}>Beasiswa Perguruan Tinggi Swasta</option>
                            <option value="Kedinasan" {{ $currentPlan && $currentPlan->entry_path === 'Kedinasan' ? 'selected' : '' }}>Sekolah Kedinasan</option>
                        </select>
                    </div>
                </div>

                <!-- Bagian Form Bekerja -->
                <div id="sectionBekerja" style="display: {{ $goal === 'bekerja' ? 'block' : 'none' }}; padding: 20px; background: #f8fafc; border: 1px solid var(--color-border); border-radius: var(--radius-sm); margin-bottom: 20px;">
                    <h4 style="font-size: 14px; font-weight: 700; color: var(--color-accent); margin-bottom: 14px;">Rincian Target Dunia Kerja</h4>
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Bidang Profesi / Pekerjaan yang Diminati</label>
                            <input type="text" name="work_target_field" class="form-control" value="{{ old('work_target_field', $currentPlan && $currentPlan->primary_goal === 'bekerja' ? $currentPlan->work_target_field : '') }}" placeholder="Contoh: Network Engineer, Web Developer, Teknisi Otomotif...">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Perusahaan Target Idaman</label>
                            <input type="text" name="work_target_company" class="form-control" value="{{ old('work_target_company', $currentPlan && $currentPlan->primary_goal === 'bekerja' ? $currentPlan->work_target_company : '') }}" placeholder="Contoh: PT Telkom Indonesia, PT Astra Honda Motor...">
                        </div>
                    </div>
                </div>

                <!-- Bagian Form Kuliah Sambil Bekerja -->
                <div id="sectionKuliahKerja" style="display: {{ $goal === 'kuliah_kerja' ? 'block' : 'none' }}; padding: 20px; background: #f8fafc; border: 1px solid var(--color-border); border-radius: var(--radius-sm); margin-bottom: 20px;">
                    <h4 style="font-size: 14px; font-weight: 700; color: var(--color-info); margin-bottom: 6px;">Rincian Rencana Kuliah Sambil Bekerja</h4>
                    <p style="font-size: 12px; color: var(--color-text-muted); margin-bottom: 16px;">
                        Tentukan target program studi yang fleksibel bersamaan dengan target dunia kerja yang ingin Anda tuju.
                    </p>

                    <div style="background: #ffffff; padding: 16px; border: 1px solid var(--color-border); border-radius: var(--radius-sm); margin-bottom: 14px;">
                        <div style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: var(--color-info); letter-spacing: 0.04em; margin-bottom: 12px;">
                            A. Target Perkuliahan
                        </div>
                        <div class="grid-2">
                            <div class="form-group">
                                <label class="form-label">Target Perguruan Tinggi</label>
                                <input type="text" name="college_target_kk" class="form-control" value="{{ old('college_target_kk', $currentPlan && $currentPlan->primary_goal === 'kuliah_kerja' ? $currentPlan->college_target : '') }}" placeholder="Contoh: Universitas Terbuka, BINUS Online, UNPAM...">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Program Studi Tujuan</label>
                                <input type="text" name="study_program_kk" class="form-control" value="{{ old('study_program_kk', $currentPlan && $currentPlan->primary_goal === 'kuliah_kerja' ? $currentPlan->study_program : '') }}" placeholder="Contoh: Sistem Informasi, Manajemen Bisnis...">
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Model / Waktu Perkuliahan</label>
                            <select name="entry_path_kk" class="form-select">
                                <option value="Kelas Karyawan" {{ $currentPlan && $currentPlan->primary_goal === 'kuliah_kerja' && $currentPlan->entry_path === 'Kelas Karyawan' ? 'selected' : '' }}>Kelas Karyawan (Sore / Malam)</option>
                                <option value="Kuliah Daring / Hybrid" {{ $currentPlan && $currentPlan->primary_goal === 'kuliah_kerja' && $currentPlan->entry_path === 'Kuliah Daring / Hybrid' ? 'selected' : '' }}>Kuliah Daring / Hybrid Learning</option>
                                <option value="Kelas Akhir Pekan" {{ $currentPlan && $currentPlan->primary_goal === 'kuliah_kerja' && $currentPlan->entry_path === 'Kelas Akhir Pekan' ? 'selected' : '' }}>Kelas Akhir Pekan (Sabtu & Minggu)</option>
                                <option value="Program Magang Bersertifikat" {{ $currentPlan && $currentPlan->primary_goal === 'kuliah_kerja' && $currentPlan->entry_path === 'Program Magang Bersertifikat' ? 'selected' : '' }}>Program Magang Bersertifikat / Ikatan Kerja</option>
                            </select>
                        </div>
                    </div>

                    <div style="background: #ffffff; padding: 16px; border: 1px solid var(--color-border); border-radius: var(--radius-sm);">
                        <div style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: var(--color-accent); letter-spacing: 0.04em; margin-bottom: 12px;">
                            B. Target Dunia Kerja
                        </div>
                        <div class="grid-2">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label">Bidang Profesi / Pekerjaan yang Diincar</label>
                                <input type="text" name="work_target_field_kk" class="form-control" value="{{ old('work_target_field_kk', $currentPlan && $currentPlan->primary_goal === 'kuliah_kerja' ? $currentPlan->work_target_field : '') }}" placeholder="Contoh: Staff IT Support, Junior Programmer, Administrasi...">
                            </div>

                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label">Target Perusahaan / Industri</label>
                                <input type="text" name="work_target_company_kk" class="form-control" value="{{ old('work_target_company_kk', $currentPlan && $currentPlan->primary_goal === 'kuliah_kerja' ? $currentPlan->work_target_company : '') }}" placeholder="Contoh: Industri Digital, Retail, Perkantoran Swasta...">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bagian Form Wirausaha -->
                <div id="sectionWirausaha" style="display: {{ $goal === 'wirausaha' ? 'block' : 'none' }}; padding: 20px; background: #f8fafc; border: 1px solid var(--color-border); border-radius: var(--radius-sm); margin-bottom: 20px;">
                    <h4 style="font-size: 14px; font-weight: 700; color: var(--color-warning); margin-bottom: 14px;">Rincian Rencana Berwirausaha</h4>
                    <div class="form-group">
                        <label class="form-label">Sektor / Bidang Usaha yang Ingin Dibangun</label>
                        <input type="text" name="business_field" class="form-control" value="{{ old('business_field', $currentPlan && $currentPlan->primary_goal === 'wirausaha' ? $currentPlan->business_field : '') }}" placeholder="Contoh: Bengkel Fabrikasi Logam, Jasa Instalasi Jaringan, Studio Kreatif...">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Gagasan / Ide Rencana Usaha Singkat</label>
                        <textarea name="business_idea" class="form-control" rows="3" placeholder="Ceritakan ide bisnis Anda dan apa yang dibutuhkan untuk memulainya...">{{ old('business_idea', $currentPlan && $currentPlan->primary_goal === 'wirausaha' ? $currentPlan->business_idea : '') }}</textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Catatan Tambahan Mengenai Cita-cita Anda</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Harapan, kendala, atau hal yang perlu dipersiapkan...">{{ old('notes', $currentPlan ? $currentPlan->notes : '') }}</textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px;">
                    <button type="submit" class="btn btn-primary">
                        Simpan Pembaruan Rencana Masa Depan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Riwayat Versi Rencana Siswa -->
    @if($student->futurePlans->count() > 0)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Riwayat Perubahan Pilihan Cita-cita</h3>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Versi</th>
                                <th>Pilihan Utama</th>
                                <th>Target Spesifik</th>
                                <th>Waktu Pembaruan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($student->futurePlans as $p)
                                <tr>
                                    <td><strong>Versi {{ $p->version }}</strong></td>
                                    <td>
                                        @if($p->primary_goal === 'kuliah')
                                            <span class="badge badge-primary">Ingin Kuliah</span>
                                        @elseif($p->primary_goal === 'bekerja')
                                            <span class="badge badge-success">Ingin Bekerja</span>
                                        @elseif($p->primary_goal === 'kuliah_kerja')
                                            <span class="badge badge-info">Kuliah Sambil Bekerja</span>
                                        @elseif($p->primary_goal === 'wirausaha')
                                            <span class="badge badge-warning">Ingin Wirausaha</span>
                                        @else
                                            <span class="badge badge-secondary">{{ ucfirst(str_replace('_', ' ', $p->primary_goal)) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($p->primary_goal === 'kuliah_kerja')
                                            <strong>{{ $p->college_target ?: '-' }}</strong> (Prodi: {{ $p->study_program ?: '-' }}) &bull; Target Kerja: {{ $p->work_target_field ?: ($p->work_target_company ?: '-') }}
                                        @elseif($p->primary_goal === 'kuliah')
                                            <strong>{{ $p->college_target ?: '-' }}</strong> (Prodi: {{ $p->study_program ?: '-' }})
                                        @elseif($p->primary_goal === 'bekerja')
                                            <strong>{{ $p->work_target_company ?: '-' }}</strong> (Bidang: {{ $p->work_target_field ?: '-' }})
                                        @elseif($p->primary_goal === 'wirausaha')
                                            <strong>{{ $p->business_field ?: '-' }}</strong>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $p->created_at->translatedFormat('d M Y, H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    function toggleSections(val) {
        var secKuliah = document.getElementById('sectionKuliah');
        var secBekerja = document.getElementById('sectionBekerja');
        var secKuliahKerja = document.getElementById('sectionKuliahKerja');
        var secWirausaha = document.getElementById('sectionWirausaha');

        if (secKuliah) secKuliah.style.display = (val === 'kuliah') ? 'block' : 'none';
        if (secBekerja) secBekerja.style.display = (val === 'bekerja') ? 'block' : 'none';
        if (secKuliahKerja) secKuliahKerja.style.display = (val === 'kuliah_kerja') ? 'block' : 'none';
        if (secWirausaha) secWirausaha.style.display = (val === 'wirausaha') ? 'block' : 'none';

        var cardStyles = {
            'kuliah': { border: 'var(--color-primary)', bg: 'var(--color-primary-light)' },
            'bekerja': { border: 'var(--color-accent)', bg: 'var(--color-accent-light)' },
            'kuliah_kerja': { border: 'var(--color-info)', bg: 'var(--color-info-bg)' },
            'wirausaha': { border: 'var(--color-warning)', bg: 'var(--color-warning-bg)' }
        };

        ['kuliah', 'bekerja', 'kuliah_kerja', 'wirausaha'].forEach(function(key) {
            var card = document.getElementById('card-' + key);
            if (card) {
                if (key === val) {
                    card.style.borderColor = cardStyles[key].border;
                    card.style.backgroundColor = cardStyles[key].bg;
                } else {
                    card.style.borderColor = 'var(--color-border)';
                    card.style.backgroundColor = '#ffffff';
                }
            }
        });
    }
</script>
@endsection
