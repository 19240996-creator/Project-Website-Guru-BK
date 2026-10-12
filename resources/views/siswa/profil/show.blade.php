@extends('layouts.siswa')

@section('title', 'Profil Saya - ' . $student->name)

@section('content')
<div style="margin-bottom: 24px;">
    <h2 style="font-size: 20px; font-weight: 800; color: var(--color-text-main);">Profil Pribadi Saya</h2>
    <p style="font-size: 13px; color: var(--color-text-muted);">
        Periksa kelengkapan data pribadi Anda dan pastikan kontak WhatsApp aktif untuk menerima kabar jadwal konseling.
    </p>
</div>

<div class="grid-2">
    <!-- Data Akademik & Sekolah -->
    <div class="card">
        <div class="card-header card-header-navy">
            <h3 class="card-title">Identitas Pokok Siswa</h3>
            <span class="badge badge-translucent">{{ ucfirst($student->status) }}</span>
        </div>
        <div class="card-body">
            <div style="margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid var(--color-border);">
                <div class="avatar-profile-header">
                    <div class="avatar-current-wrapper">
                        <img src="{{ $student->avatar_url }}" alt="{{ $student->name }}" id="studentAvatarImg">
                    </div>
                    <div class="avatar-profile-info">
                        @if($student->hasCustomAvatar())
                            <div class="avatar-status-badge custom">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                <span>Foto Khusus Terpasang</span>
                            </div>
                        @else
                            <div class="avatar-status-badge default">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                <span>Menggunakan Avatar Default</span>
                            </div>
                        @endif
                        <h3 style="font-size: 16px; font-weight: 700; color: var(--color-text-main); margin-bottom: 4px;">{{ $student->name }}</h3>
                        <p style="font-size: 12px; color: var(--color-text-muted); margin-bottom: 10px;">
                            Format JPG, JPEG, PNG, atau WebP. Maksimal 5 MB. Dilengkapi preview crop interaktif.
                        </p>
                        <div class="avatar-btn-group">
                            <input type="file" id="studentPhotoInput" accept=".jpg,.jpeg,.png,.webp" style="display: none;" onchange="handleStudentPhotoSelect(this)">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('studentPhotoInput').click()" title="Unggah foto profil baru dengan crop preview">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                                <span>{{ $student->hasCustomAvatar() ? 'Ganti Foto' : 'Unggah Foto Baru' }}</span>
                            </button>
                            @if($student->hasCustomAvatar())
                                <button type="button" class="btn btn-danger-subtle" onclick="handleStudentPhotoDelete()" title="Hapus foto profil dan kembali ke avatar default">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                    <span>Hapus Foto</span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <table class="table" style="font-size: 13px;">
                <tr>
                    <td style="width: 140px; font-weight: 600; color: var(--color-text-muted);">Nama Lengkap</td>
                    <td><strong>{{ $student->name }}</strong></td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--color-text-muted);">NISN</td>
                    <td>{{ $student->nisn }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--color-text-muted);">NIS</td>
                    <td>{{ $student->nis }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--color-text-muted);">Kelas Saat Ini</td>
                    <td>{{ $student->studentClass ? $student->studentClass->name : '-' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--color-text-muted);">Program Keahlian</td>
                    <td>{{ $student->studentClass ? $student->studentClass->major : '-' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--color-text-muted);">Tahun Ajaran</td>
                    <td>{{ $student->studentClass && $student->studentClass->academicYear ? $student->studentClass->academicYear->name : '-' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--color-text-muted);">Tempat, Tgl Lahir</td>
                    <td>{{ $student->birth_place ?: '-' }}, {{ $student->birth_date ? $student->birth_date->translatedFormat('d F Y') : '-' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--color-text-muted);">Nama Orang Tua</td>
                    <td>{{ $student->parent_name ?: '-' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Pembaruan Kontak Mandiri -->
    <div class="card">
        <div class="card-header card-header-navy">
            <h3 class="card-title">Perbarui Kontak Mandiri</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('siswa.profil.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label">Nomor HP / WhatsApp Aktif</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $student->phone) }}" placeholder="08..." required>
                    <div class="form-hint" style="font-size: 11.5px; color: var(--color-text-muted); margin-top: 4px;">
                        Digunakan guru BK untuk bimbingan &amp; otomatis menjadi kata sandi login Anda. Jika diganti, Anda wajib login memakai nomor baru ini.
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nomor Kontak Orang Tua / Wali</label>
                    <input type="text" name="parent_phone" class="form-control" value="{{ old('parent_phone', $student->parent_phone) }}" placeholder="08...">
                </div>

                <div class="form-group">
                    <label class="form-label">Alamat Tempat Tinggal Sekarang</label>
                    <textarea name="address" class="form-control" rows="3" placeholder="Jl. ...">{{ old('address', $student->address) }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    Simpan Perubahan Kontak
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function handleStudentPhotoSelect(input) {
    if (!input.files || !input.files[0]) return;
    window.AvatarCropper.openForFile(input.files[0], {
        uploadUrl: '{{ route('siswa.profil.photo.update') }}',
        csrf: '{{ csrf_token() }}'
    });
    input.value = '';
}

function handleStudentPhotoDelete() {
    window.AvatarCropper.openDeleteModal({
        deleteUrl: '{{ route('siswa.profil.photo.destroy') }}',
        csrf: '{{ csrf_token() }}'
    });
}
</script>
@endsection
