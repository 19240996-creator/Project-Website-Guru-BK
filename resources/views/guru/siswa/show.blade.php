@extends('layouts.app')

@section('title', 'Profil Siswa - ' . $student->name)
@section('header_title', 'Profil Siswa')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
    <div style="display: flex; align-items: center; gap: 16px;">
        @if($student->avatar)
            <img src="{{ asset('storage/' . $student->avatar) }}" alt="{{ $student->name }}" style="width: 54px; height: 54px; border-radius: 50%; object-fit: cover; border: 2px solid var(--color-primary-border);">
        @else
            <div class="user-avatar-initial" style="width: 54px; height: 54px; font-size: 20px;">
                {{ strtoupper(substr($student->name, 0, 1)) }}
            </div>
        @endif
        <div>
            <h2 style="font-size: 20px; font-weight: 800; color: var(--color-text-main);">{{ $student->name }}</h2>
            <div style="font-size: 13px; color: var(--color-text-muted); display: flex; gap: 12px; align-items: center; margin-top: 2px;">
                <span>NISN: <strong>{{ $student->nisn }}</strong></span>
                <span>•</span>
                <span>Kelas: <strong>{{ $student->studentClass ? $student->studentClass->name : '-' }}</strong></span>
                <span>•</span>
                <span>Status: <span class="badge badge-{{ $student->status === 'aktif' ? 'success' : 'secondary' }}">{{ ucfirst($student->status) }}</span></span>
            </div>
        </div>
    </div>
    <div style="display: flex; gap: 10px; align-items: center;">
        <a href="{{ route('guru.siswa.edit', $student->id) }}" class="btn btn-secondary">
            Edit Data Profil
        </a>
        @if($student->status === 'aktif')
            <button type="button" class="btn btn-accent" onclick="document.getElementById('graduateModal').style.display = 'block';">
                Luluskan Menjadi Alumni
            </button>
        @endif
        <button type="button" class="btn btn-danger" onclick="openDeleteStudentModal('{{ $student->id }}', '{{ addslashes($student->name) }}', '{{ $student->nisn }}')" title="Hapus Data Siswa">
            Hapus Siswa
        </button>
    </div>
</div>

<div class="grid-2" style="margin-bottom: 24px;">
    <!-- Bagian 1: Identitas & Keluarga -->
    <div class="card">
        <div class="card-header card-header-navy" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <h3 class="card-title">1. Identitas Pribadi & Keluarga</h3>
            <span class="badge badge-translucent">Tingkat Perhatian: {{ ucfirst(str_replace('_', ' ', $student->attention_level)) }}</span>
        </div>
        <div class="card-body">
            <table class="table" style="font-size: 13px;">
                <tr>
                    <td style="width: 140px; font-weight: 600; color: var(--color-text-muted);">NIS / NISN</td>
                    <td>{{ $student->nis }} / {{ $student->nisn }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--color-text-muted);">Jenis Kelamin</td>
                    <td>{{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--color-text-muted);">Tempat, Tanggal Lahir</td>
                    <td>{{ $student->birth_place ?: '-' }}, {{ $student->birth_date ? $student->birth_date->translatedFormat('d F Y') : '-' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--color-text-muted);">Nomor Telepon/HP</td>
                    <td>{{ $student->phone ?: '-' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--color-text-muted);">Alamat Rumah</td>
                    <td>{{ $student->address ?: '-' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--color-text-muted);">Nama Orang Tua/Wali</td>
                    <td>{{ $student->parent_name ?: '-' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--color-text-muted);">Kontak Orang Tua</td>
                    <td>{{ $student->parent_phone ?: '-' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--color-text-muted);">Pekerjaan Orang Tua</td>
                    <td>{{ $student->parent_job ?: '-' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--color-text-muted);">Catatan Latar Belakang</td>
                    <td>{{ $student->special_notes ?: 'Tidak ada catatan khusus.' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Bagian 2: Rencana Masa Depan & Peminatan Karier -->
    <div class="card">
        <div class="card-header card-header-navy" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <h3 class="card-title">2. Peminatan & Rencana Masa Depan</h3>
            <span class="badge badge-translucent">Tersimpan</span>
        </div>
        <div class="card-body">
            @if($student->futurePlan)
                <div style="margin-bottom: 16px;">
                    <div style="font-size: 12px; font-weight: 700; color: var(--color-text-subtle); text-transform: uppercase;">Arah Pilihan Utama:</div>
                    <div style="font-size: 16px; font-weight: 800; color: var(--color-primary); margin-top: 2px;">
                        @if($student->futurePlan->primary_goal === 'kuliah')
                            Melanjutkan Kuliah (Perguruan Tinggi)
                        @elseif($student->futurePlan->primary_goal === 'bekerja')
                            Langsung Bekerja (Dunia Industri)
                        @elseif($student->futurePlan->primary_goal === 'wirausaha')
                            Membangun Wirausaha Mandiri
                        @elseif($student->futurePlan->primary_goal === 'kuliah_kerja')
                            Kuliah Sambil Bekerja
                        @else
                            Belum Menentukan Pilihan
                        @endif
                    </div>
                </div>

                <table class="table" style="font-size: 13px;">
                    @if($student->futurePlan->primary_goal === 'kuliah')
                        <tr>
                            <td style="width: 140px; font-weight: 600; color: var(--color-text-muted);">Target Kampus</td>
                            <td><strong>{{ $student->futurePlan->college_target ?: '-' }}</strong></td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600; color: var(--color-text-muted);">Program Studi</td>
                            <td>{{ $student->futurePlan->study_program ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600; color: var(--color-text-muted);">Jalur Seleksi</td>
                            <td>{{ $student->futurePlan->entry_path ?: '-' }}</td>
                        </tr>
                    @elseif($student->futurePlan->primary_goal === 'bekerja')
                        <tr>
                            <td style="width: 140px; font-weight: 600; color: var(--color-text-muted);">Bidang Kerja</td>
                            <td><strong>{{ $student->futurePlan->work_target_field ?: '-' }}</strong></td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600; color: var(--color-text-muted);">Perusahaan Target</td>
                            <td>{{ $student->futurePlan->work_target_company ?: '-' }}</td>
                        </tr>
                    @elseif($student->futurePlan->primary_goal === 'kuliah_kerja')
                        <tr>
                            <td style="width: 140px; font-weight: 600; color: var(--color-text-muted);">Target Kampus</td>
                            <td><strong>{{ $student->futurePlan->college_target ?: '-' }}</strong> (Prodi: {{ $student->futurePlan->study_program ?: '-' }})</td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600; color: var(--color-text-muted);">Format Kuliah</td>
                            <td>{{ $student->futurePlan->entry_path ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600; color: var(--color-text-muted);">Target Dunia Kerja</td>
                            <td><strong>{{ $student->futurePlan->work_target_field ?: '-' }}</strong> (Perusahaan: {{ $student->futurePlan->work_target_company ?: '-' }})</td>
                        </tr>
                    @elseif($student->futurePlan->primary_goal === 'wirausaha')
                        <tr>
                            <td style="width: 140px; font-weight: 600; color: var(--color-text-muted);">Bidang Usaha</td>
                            <td><strong>{{ $student->futurePlan->business_field ?: '-' }}</strong></td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600; color: var(--color-text-muted);">Ide Rencana Usaha</td>
                            <td>{{ $student->futurePlan->business_idea ?: '-' }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td style="font-weight: 600; color: var(--color-text-muted);">Catatan Siswa</td>
                        <td>{{ $student->futurePlan->notes ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: 600; color: var(--color-text-subtle);">Terakhir Diperbarui</td>
                        <td style="font-size: 12px; color: var(--color-text-subtle);">
                            {{ $student->futurePlan->updated_at->translatedFormat('d M Y, H:i') }} (Versi ke-{{ $student->futurePlan->version }})
                        </td>
                    </tr>
                </table>
            @else
                <div class="empty-state">
                    <p class="empty-state-title">Belum Ada Rencana Masa Depan</p>
                    <p class="empty-state-desc">Siswa belum mengisi formulir perencanaan arah masa depan.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="grid-2" style="margin-bottom: 24px;">
    <!-- Bagian 3: Riwayat Bimbingan & Konseling Siswa -->
    <div class="card">
        <div class="card-header card-header-navy" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <h3 class="card-title">3. Riwayat Layanan Konseling ({{ $student->counselings->count() }})</h3>
            <a href="{{ route('guru.konseling.index', ['q' => $student->nisn]) }}" class="btn btn-secondary btn-sm">Lihat Semua</a>
        </div>
        <div class="card-body" style="padding: 0;">
            @if($student->counselings->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead class="table-thead-navy">
                            <tr>
                                <th style="width: 28%; text-align: left;">Kode & Tanggal</th>
                                <th style="width: 38%; text-align: left;">Topik & Kategori</th>
                                <th style="width: 20%; text-align: center;">Status</th>
                                <th style="width: 14%; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($student->counselings as $c)
                                <tr>
                                    <td style="text-align: left;">
                                        <strong>{{ $c->code }}</strong><br>
                                        <small class="text-muted">{{ $c->created_at->translatedFormat('d M Y') }}</small>
                                    </td>
                                    <td style="text-align: left;">
                                        <div style="font-weight: 600;">{{ $c->topic }}</div>
                                        <small style="color: var(--color-text-subtle);">{{ $c->category ? $c->category->name : '-' }}</small>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="badge badge-primary" style="justify-content: center;">{{ ucfirst(str_replace('_', ' ', $c->status)) }}</span>
                                    </td>
                                    <td style="text-align: center;">
                                        <a href="{{ route('guru.konseling.show', $c->id) }}" class="btn btn-secondary btn-sm">Buka</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <p class="empty-state-title">Belum Ada Riwayat Konseling</p>
                    <p class="empty-state-desc">Siswa belum pernah mengajukan atau terjadwal dalam sesi bimbingan.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Bagian 4: Hasil Asesmen & Prestasi -->
    <div class="card">
        <div class="card-header card-header-navy">
            <h3 class="card-title">4. Hasil Asesmen & Prestasi Siswa</h3>
        </div>
        <div class="card-body">
            <div style="font-size: 13px; font-weight: 700; color: var(--color-text-main); margin-bottom: 8px;">
                Hasil Asesmen Minat / Kepribadian:
            </div>
            @if($student->assessmentResults->count() > 0)
                @foreach($student->assessmentResults as $res)
                    <div style="padding: 12px; background: #f8fafc; border: 1px solid var(--color-border); border-radius: var(--radius-sm); margin-bottom: 10px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <strong>{{ $res->assessment ? $res->assessment->title : 'Asesmen' }}</strong>
                            <span class="badge badge-success">{{ $res->result_category }}</span>
                        </div>
                        <p style="font-size: 12px; color: var(--color-text-muted); margin-top: 4px;">{{ $res->summary }}</p>
                    </div>
                @endforeach
            @else
                <p style="font-size: 12px; color: var(--color-text-subtle); margin-bottom: 16px;">Belum ada hasil asesmen yang dikerjakan.</p>
            @endif

            <div style="font-size: 13px; font-weight: 700; color: var(--color-text-main); margin-top: 16px; margin-bottom: 8px;">
                Prestasi yang Diraih:
            </div>
            @if($student->achievements->count() > 0)
                <ul style="padding-left: 20px; font-size: 13px; color: var(--color-text-main);">
                    @foreach($student->achievements as $ach)
                        <li style="margin-bottom: 6px;">
                            <strong>{{ $ach->title }}</strong> (Tingkat {{ $ach->level }}, {{ $ach->year }})<br>
                            <small style="color: var(--color-text-subtle);">{{ $ach->description }}</small>
                        </li>
                    @endforeach
                </ul>
            @else
                <p style="font-size: 12px; color: var(--color-text-subtle);">Belum ada catatan prestasi yang terdata.</p>
            @endif
        </div>
    </div>
</div>

<!-- Modal Luluskan Siswa (Section 24) -->
<div id="graduateModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 100; overflow-y: auto; backdrop-filter: blur(2px);">
    <div style="max-width: 520px; margin: 40px auto; background: var(--color-surface); padding: 24px; border-radius: var(--radius-md); box-shadow: var(--shadow-lg); border: 1px solid var(--color-border);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
            <h3 style="font-size: 16px; font-weight: 800; color: var(--color-text-main);">Luluskan Siswa Menjadi Alumni</h3>
            <button type="button" onclick="closeGraduateModal()" style="background: none; border: none; font-size: 20px; cursor: pointer; color: var(--color-text-subtle); line-height: 1;">&times;</button>
        </div>
        <p style="font-size: 13px; color: var(--color-text-muted); margin-bottom: 16px; line-height: 1.5;">
            Sistem akan mengubah status siswa menjadi <strong>Lulus</strong> dan membuka rekam jejak pelacakan alumni (Tracer Study).
        </p>

        <form action="{{ route('guru.alumni.graduate', $student->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- Upload Foto Alumni -->
            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label" style="font-weight: 700; color: var(--color-text-main);">Foto Formal / Pasfoto Alumni</label>
                <div style="border: 2px dashed var(--color-primary-border); background-color: var(--color-bg); padding: 16px; border-radius: var(--radius-md); text-align: center;">
                    <div id="photoPreviewArea" style="display: none; margin-bottom: 10px;">
                        <img id="photoPreviewImg" src="#" alt="Pratinjau Foto" style="width: 76px; height: 76px; border-radius: 50%; object-fit: cover; border: 2px solid var(--color-primary); margin: 0 auto 8px auto; display: block;">
                        <div id="photoPreviewName" style="font-size: 12px; font-weight: 600; color: var(--color-text-main);"></div>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="clearPhotoSelection()" style="margin-top: 6px; padding: 3px 8px; font-size: 11px;">
                            Ganti / Hapus Foto
                        </button>
                    </div>

                    <div id="photoPromptArea">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--color-primary); margin: 0 auto 6px auto; display: block;">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                            <polyline points="21 15 16 10 5 21"></polyline>
                        </svg>
                        <div style="font-size: 13px; font-weight: 600; color: var(--color-text-main);">
                            Unggah Foto Profil / Kelulusan
                        </div>
                        <div style="font-size: 11px; color: var(--color-text-subtle); margin-top: 2px;">
                            Format JPG, PNG, atau WebP (Maks. 3 MB)
                        </div>
                        <label for="alumniPhotoInput" class="btn btn-secondary btn-sm" style="margin-top: 10px; cursor: pointer; display: inline-block;">
                            Pilih Berkas Foto
                        </label>
                    </div>
                    <input type="file" id="alumniPhotoInput" name="photo" accept="image/jpeg,image/png,image/jpg,image/webp" style="display: none;" onchange="previewAlumniPhoto(event)">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Tahun Kelulusan *</label>
                <input type="number" name="graduation_year" class="form-control" value="{{ date('Y') }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Status Terkini Setelah Lulus *</label>
                <select name="current_status" class="form-select" required>
                    <option value="bekerja">Bekerja</option>
                    <option value="kuliah">Kuliah</option>
                    <option value="wirausaha">Wirausaha</option>
                    <option value="mencari_kerja">Sedang Mencari Pekerjaan</option>
                    <option value="belum_terlacak" selected>Belum Terlacak</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Instansi / Kampus / Perusahaan</label>
                <input type="text" name="institution_or_company" class="form-control" placeholder="Contoh: ITB atau PT Telkom Indonesia">
            </div>

            <div class="form-group">
                <label class="form-label">Program Studi / Posisi Pekerjaan</label>
                <input type="text" name="major_or_position" class="form-control" placeholder="Contoh: Teknik Informatika atau Software Engineer">
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text-main); cursor: pointer;">
                    <input type="checkbox" name="allow_public_showcase" value="1" checked>
                    <span>Tampilkan di Galeri Inspirasi Alumni untuk adik kelas</span>
                </label>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 22px;">
                <button type="button" class="btn btn-secondary" onclick="closeGraduateModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan & Luluskan Siswa</button>
            </div>
        </form>
    </div>
</div>

<script>
function closeGraduateModal() {
    document.getElementById('graduateModal').style.display = 'none';
}

function previewAlumniPhoto(event) {
    const file = event.target.files[0];
    if (file) {
        if (file.size > 3 * 1024 * 1024) {
            alert('Ukuran file foto maksimal adalah 3 MB.');
            event.target.value = '';
            return;
        }
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('photoPreviewImg').src = e.target.result;
            document.getElementById('photoPreviewName').textContent = file.name;
            document.getElementById('photoPreviewArea').style.display = 'block';
            document.getElementById('photoPromptArea').style.display = 'none';
        };
        reader.readAsDataURL(file);
    }
}

function clearPhotoSelection() {
    const input = document.getElementById('alumniPhotoInput');
    input.value = '';
    document.getElementById('photoPreviewImg').src = '#';
    document.getElementById('photoPreviewName').textContent = '';
    document.getElementById('photoPreviewArea').style.display = 'none';
    document.getElementById('photoPromptArea').style.display = 'block';
}

function openDeleteStudentModal(studentId, studentName, studentNisn) {
    var modal = document.getElementById('deleteStudentModal');
    var form = document.getElementById('deleteStudentForm');
    var nameSpan = document.getElementById('deleteStudentNameText');
    var nisnSpan = document.getElementById('deleteStudentNisnText');

    if (modal && form) {
        form.action = '/guru/siswa/' + studentId;
        if (nameSpan) nameSpan.textContent = studentName;
        if (nisnSpan) nisnSpan.textContent = 'NISN: ' + studentNisn;
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeDeleteStudentModal() {
    var modal = document.getElementById('deleteStudentModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

function handleDeleteBackdropClick(event) {
    if (event.target === document.getElementById('deleteStudentModal')) {
        closeDeleteStudentModal();
    }
}
</script>

<!-- Modal Konfirmasi Hapus Data Siswa -->
<div id="deleteStudentModal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="deleteStudentModalTitle" onclick="handleDeleteBackdropClick(event)">
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
            <h3 id="deleteStudentModalTitle" class="modal-title">Konfirmasi Hapus Data Siswa</h3>
            <p class="modal-desc">
                Apakah Anda yakin ingin menghapus data siswa <strong id="deleteStudentNameText" style="color: var(--color-text-main);"></strong> (<span id="deleteStudentNisnText" style="color: var(--color-text-subtle);"></span>)?
            </p>
            <div style="background-color: var(--color-danger-bg); border: 1px solid var(--color-danger-border); border-radius: var(--radius-sm); padding: 10px 14px; margin-top: 14px; font-size: 12px; color: var(--color-danger); display: flex; align-items: flex-start; gap: 8px; line-height: 1.45;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0; margin-top: 2px;">
                    <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
                <span>Perhatian: Seluruh riwayat bimbingan konseling, hasil asesmen, dan akun login siswa ini akan dihapus secara permanen dan tidak dapat dipulihkan.</span>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeDeleteStudentModal()">
                Batal
            </button>
            <form id="deleteStudentForm" method="POST" action="" style="display: inline; margin: 0;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    Ya, Hapus Data
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
