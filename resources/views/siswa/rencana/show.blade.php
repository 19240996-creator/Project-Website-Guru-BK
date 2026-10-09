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
        <div class="card-header card-header-navy">
            <h3 class="card-title">Pilih Arah Pilihan Utama Anda</h3>
            @if($currentPlan)
                <span style="font-size: 13px; font-weight: 600; color: #bfdbfe;">
                    Versi ke-{{ $currentPlan->version }} (Aktif)
                </span>
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
                                <option value="Kelas Malam" {{ $currentPlan && $currentPlan->primary_goal === 'kuliah_kerja' && in_array($currentPlan->entry_path, ['Kelas Malam', 'Kelas Karyawan']) ? 'selected' : '' }}>Kelas Malam</option>
                                <option value="Kuliah Daring / Hybrid" {{ $currentPlan && $currentPlan->primary_goal === 'kuliah_kerja' && $currentPlan->entry_path === 'Kuliah Daring / Hybrid' ? 'selected' : '' }}>Kuliah Daring / Hybrid Learning</option>
                                <option value="Kelas Karyawan (Jumat & Sabtu)" {{ $currentPlan && $currentPlan->primary_goal === 'kuliah_kerja' && in_array($currentPlan->entry_path, ['Kelas Karyawan (Jumat & Sabtu)', 'Kelas Akhir Pekan']) ? 'selected' : '' }}>Kelas Karyawan (Jumat & Sabtu)</option>
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
    @if($history->count() > 0)
        <div class="card">
            <div class="card-header card-header-navy">
                <h3 class="card-title">Riwayat Perubahan Pilihan Cita-cita</h3>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table" style="margin-bottom: 0;">
                        <thead class="table-thead-navy">
                            <tr>
                                <th style="width: 110px; white-space: nowrap;">Versi</th>
                                <th style="width: 190px; white-space: nowrap;">Pilihan Utama</th>
                                <th style="min-width: 280px;">Target Spesifik</th>
                                <th style="width: 200px; white-space: nowrap;">Waktu Pembaruan</th>
                                <th style="width: 80px; text-align: center; white-space: nowrap;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($history as $p)
                                <tr>
                                    <td style="white-space: nowrap; vertical-align: middle;">
                                        <strong style="color: var(--color-text-main); font-size: 13px;">Versi {{ $p->version }}</strong>
                                    </td>
                                    <td style="white-space: nowrap; vertical-align: middle;">
                                        <span style="font-weight: 700; color: var(--color-text-main); font-size: 13px;">
                                            @if($p->primary_goal === 'kuliah')
                                                Kuliah
                                            @elseif($p->primary_goal === 'bekerja')
                                                Bekerja
                                            @elseif($p->primary_goal === 'kuliah_kerja')
                                                Kuliah Sambil Bekerja
                                            @elseif($p->primary_goal === 'wirausaha')
                                                Wirausaha
                                            @else
                                                {{ ucfirst(str_replace('_', ' ', $p->primary_goal)) }}
                                            @endif
                                        </span>
                                    </td>
                                    <td style="vertical-align: middle; line-height: 1.5;">
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
                                    <td style="white-space: nowrap; vertical-align: middle; color: var(--color-text-muted); font-size: 13px;">
                                        {{ $p->created_at->translatedFormat('d M Y, H:i') }} WIB
                                    </td>
                                    <td style="text-align: center; vertical-align: middle; white-space: nowrap;">
                                        <button type="button" class="btn-icon-danger" onclick="openDeletePlanModal({{ $p->id }}, 'Versi {{ $p->version }}', '{{ $p->created_at->translatedFormat('d M Y, H:i') }} WIB', '{{ $p->primary_goal === 'kuliah' ? 'Kuliah' : ($p->primary_goal === 'bekerja' ? 'Bekerja' : ($p->primary_goal === 'kuliah_kerja' ? 'Kuliah Sambil Bekerja' : ($p->primary_goal === 'wirausaha' ? 'Wirausaha' : ucfirst(str_replace('_', ' ', $p->primary_goal))))) }}')" title="Hapus Riwayat Versi {{ $p->version }}" aria-label="Hapus Riwayat Rencana">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                                <line x1="14" y1="11" x2="14" y2="17"></line>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer" style="background-color: #ffffff;">
                {{ $history->links() }}
            </div>
        </div>
    @endif
</div>

<!-- Modal Konfirmasi Hapus Riwayat Rencana -->
<div id="deletePlanModal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="deletePlanModalTitle" onclick="handleDeletePlanBackdrop(event)">
    <div class="modal-dialog">
        <div class="modal-body">
            <div class="modal-icon-badge danger">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    <line x1="10" y1="11" x2="10" y2="17"></line>
                    <line x1="14" y1="11" x2="14" y2="17"></line>
                </svg>
            </div>
            <h3 id="deletePlanModalTitle" class="modal-title">Konfirmasi Hapus Riwayat Rencana</h3>
            <p class="modal-desc">
                Apakah Anda yakin ingin menghapus catatan riwayat pilihan rencana ini? Tindakan ini bersifat permanen.
            </p>
            <div style="background-color: var(--color-surface-hover); border: 1px solid var(--color-border); border-radius: var(--radius-sm); padding: 12px 14px; margin-top: 14px; text-align: left;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                    <strong style="color: var(--color-navy); font-size: 13.5px;" id="deletePlanVersionPreview"></strong>
                    <span style="color: var(--color-text-subtle); font-size: 11px;" id="deletePlanTimePreview"></span>
                </div>
                <div style="color: var(--color-text-muted); font-size: 12.5px;" id="deletePlanGoalPreview"></div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeDeletePlanModal()">
                Batal
            </button>
            <form id="deletePlanForm" method="POST" action="" style="display: inline; margin: 0;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    Ya, Hapus Riwayat
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function openDeletePlanModal(id, versionText, timeText, goalText) {
        var form = document.getElementById('deletePlanForm');
        if (form) {
            form.action = '{{ url("/siswa/rencana-masa-depan") }}/' + id;
        }
        var verEl = document.getElementById('deletePlanVersionPreview');
        if (verEl) verEl.textContent = versionText;
        var timeEl = document.getElementById('deletePlanTimePreview');
        if (timeEl) timeEl.textContent = timeText;
        var goalEl = document.getElementById('deletePlanGoalPreview');
        if (goalEl) goalEl.textContent = 'Pilihan: ' + goalText;
        var modal = document.getElementById('deletePlanModal');
        if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeDeletePlanModal() {
        var modal = document.getElementById('deletePlanModal');
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    function handleDeletePlanBackdrop(e) {
        if (e.target.id === 'deletePlanModal') {
            closeDeletePlanModal();
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDeletePlanModal();
        }
    });

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
