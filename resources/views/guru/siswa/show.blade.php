@extends('layouts.app')

@section('title', 'Profil 360° - ' . $student->name)
@section('header_title', 'Profil Siswa 360 Derajat')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
    <div style="display: flex; align-items: center; gap: 16px;">
        <div class="user-avatar-initial" style="width: 54px; height: 54px; font-size: 20px;">
            {{ strtoupper(substr($student->name, 0, 1)) }}
        </div>
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
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('guru.siswa.edit', $student->id) }}" class="btn btn-secondary">
            Edit Data Profil
        </a>
        @if($student->status === 'aktif')
            <button type="button" class="btn btn-accent" onclick="document.getElementById('graduateModal').style.display = 'block';">
                Luluskan Menjadi Alumni
            </button>
        @endif
    </div>
</div>

<div class="grid-2" style="margin-bottom: 24px;">
    <!-- Bagian 1: Identitas & Keluarga -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">1. Identitas Pribadi & Keluarga</h3>
            <span class="badge badge-primary">Tingkat Perhatian: {{ ucfirst(str_replace('_', ' ', $student->attention_level)) }}</span>
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
        <div class="card-header">
            <h3 class="card-title">2. Peminatan & Rencana Masa Depan</h3>
            <span class="badge badge-secondary">Tersimpan</span>
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
        <div class="card-header">
            <h3 class="card-title">3. Riwayat Layanan Konseling ({{ $student->counselings->count() }})</h3>
            <a href="{{ route('guru.konseling.index', ['q' => $student->nisn]) }}" class="btn btn-secondary btn-sm">Lihat Semua</a>
        </div>
        <div class="card-body" style="padding: 0;">
            @if($student->counselings->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Kode & Tanggal</th>
                                <th>Topik & Kategori</th>
                                <th>Status</th>
                                <th style="text-align: right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($student->counselings as $c)
                                <tr>
                                    <td>
                                        <strong>{{ $c->code }}</strong><br>
                                        <small class="text-muted">{{ $c->created_at->translatedFormat('d M Y') }}</small>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600;">{{ $c->topic }}</div>
                                        <small style="color: var(--color-text-subtle);">{{ $c->category ? $c->category->name : '-' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary">{{ ucfirst(str_replace('_', ' ', $c->status)) }}</span>
                                    </td>
                                    <td style="text-align: right;">
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
        <div class="card-header">
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
<div id="graduateModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 50; overflow-y: auto;">
    <div style="max-width: 500px; margin: 60px auto; background: #fff; padding: 24px; border-radius: var(--radius-md);">
        <h3 style="font-size: 16px; font-weight: 800; margin-bottom: 12px;">Luluskan Siswa Menjadi Alumni</h3>
        <p style="font-size: 13px; color: var(--color-text-muted); margin-bottom: 16px;">
            Sistem akan mengubah status siswa menjadi <strong>Lulus</strong> dan membuka rekam jejak pelacakan alumni (Tracer Study).
        </p>

        <form action="{{ route('guru.alumni.graduate', $student->id) }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Tahun Kelulusan</label>
                <input type="number" name="graduation_year" class="form-control" value="{{ date('Y') }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Status Terkini Setelah Lulus</label>
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
                <input type="text" name="institution_or_company" class="form-control" placeholder="Contoh: ITB atau PT Telkom">
            </div>

            <div class="form-group">
                <label class="form-label">Program Studi / Posisi Pekerjaan</label>
                <input type="text" name="major_or_position" class="form-control" placeholder="Contoh: Teknik Komputer atau Junior Developer">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('graduateModal').style.display = 'none';">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Kelulusan</button>
            </div>
        </form>
    </div>
</div>
@endsection
