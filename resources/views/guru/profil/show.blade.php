@extends('layouts.app')

@section('title', 'Profil Guru BK - ' . $counselor->name)
@section('header_title', 'Profil Guru Bimbingan Konseling')

@section('content')
<div style="margin-bottom: 24px;">
    <h2 style="font-size: 20px; font-weight: 800; color: var(--color-text-main);">Profil Guru Bimbingan Konseling</h2>
    <p style="font-size: 13px; color: var(--color-text-muted);">
        Informasi identitas resmi penugasan konselor sekolah dan pembaruan kontak kedinasan.
    </p>
</div>

<div class="grid-2">
    <!-- Identitas Pokok Guru BK -->
    <div class="card">
        <div class="card-header card-header-navy">
            <h3 class="card-title">Identitas Resmi Konselor</h3>
            <span class="badge badge-translucent">Aktif Bertugas</span>
        </div>
        <div class="card-body">
            <div style="margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid var(--color-border);">
                <div class="avatar-profile-header">
                    <div class="avatar-current-wrapper">
                        <img src="{{ $counselor->avatar_url }}" alt="{{ $counselor->name }}" id="counselorAvatarImg">
                    </div>
                    <div class="avatar-profile-info">
                        @if($counselor->hasCustomAvatar())
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
                        <h3 style="font-size: 16px; font-weight: 700; color: var(--color-text-main); margin-bottom: 4px;">{{ $counselor->name }}</h3>
                        <p style="font-size: 12px; color: var(--color-text-muted); margin-bottom: 10px;">
                            Format JPG, JPEG, PNG, atau WebP. Maksimal 5 MB. Dilengkapi preview crop interaktif.
                        </p>
                        <div class="avatar-btn-group">
                            <input type="file" id="counselorPhotoInput" accept=".jpg,.jpeg,.png,.webp" style="display: none;" onchange="handleCounselorPhotoSelect(this)">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('counselorPhotoInput').click()" title="Unggah foto profil baru dengan crop preview">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                                <span>{{ $counselor->hasCustomAvatar() ? 'Ganti Foto' : 'Unggah Foto Baru' }}</span>
                            </button>
                            @if($counselor->hasCustomAvatar())
                                <button type="button" class="btn btn-danger-subtle" onclick="handleCounselorPhotoDelete()" title="Hapus foto profil dan kembali ke avatar default">
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
                    <td style="width: 170px; font-weight: 600; color: var(--color-text-muted);">Nama Lengkap</td>
                    <td><strong>{{ $counselor->name }}</strong></td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--color-text-muted);">ID Akun / Username</td>
                    <td><code>{{ $counselor->username }}</code></td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--color-text-muted);">Email Kedinasan</td>
                    <td>{{ $counselor->email }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--color-text-muted);">Nomor WhatsApp / HP</td>
                    <td>{{ $counselor->phone ?: 'Belum diatur' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--color-text-muted);">Tahun Ajaran Berjalan</td>
                    <td>{{ $activeAcademicYear ? $activeAcademicYear->name . ' (' . $activeAcademicYear->semester . ')' : 'Tahun Ajaran Aktif' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--color-text-muted);">Total Konseling Ditangani</td>
                    <td><strong>{{ $handledCounselingsCount }} Sesi</strong></td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--color-text-muted);">Konseling Terjadwal</td>
                    <td><span class="badge badge-info">{{ $scheduledCounselingsCount }} Sesi Menunggu</span></td>
                </tr>
            </table>

            <div style="margin-top: 20px; display: flex; gap: 10px;">
                <a href="{{ route('guru.konseling.index') }}" class="btn btn-secondary btn-sm">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                    <span>Buka Riwayat Konseling</span>
                </a>
                <a href="{{ route('guru.dashboard') }}" class="btn btn-secondary btn-sm">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                    <span>Kembali ke Beranda</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Pembaruan Informasi Akun -->
    <div class="card">
        <div class="card-header card-header-navy">
            <h3 class="card-title">Perbarui Informasi Akun</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('guru.profil.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label" style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 13px;">Nama Lengkap & Gelar</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $counselor->name) }}" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-family: inherit; font-size: 13px;">
                </div>

                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label" style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 13px;">Alamat Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $counselor->email) }}" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-family: inherit; font-size: 13px;">
                    <small style="color: var(--color-text-subtle); font-size: 11px; margin-top: 4px; display: block;">Digunakan untuk menerima notifikasi sistem dan konfirmasi akun.</small>
                </div>

                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label" style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 13px;">Nomor HP / WhatsApp Aktif</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $counselor->phone) }}" placeholder="Contoh: 081234567890" style="width: 100%; padding: 8px 12px; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-family: inherit; font-size: 13px;">
                    <small style="color: var(--color-text-subtle); font-size: 11px; margin-top: 4px; display: block;">Kontak resmi yang dapat dihubungi terkait agenda konseling siswa.</small>
                </div>

                <div style="margin-top: 20px; padding-top: 16px; border-top: 1px solid var(--color-border);">
                    <h4 style="font-size: 13px; font-weight: 700; color: var(--color-text-main); margin-bottom: 12px;">Ubah Kata Sandi (Opsional)</h4>

                    <div class="form-group" style="margin-bottom: 14px;">
                        <label class="form-label" style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 12px;">Kata Sandi Baru</label>
                        <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah" style="width: 100%; padding: 8px 12px; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-family: inherit; font-size: 13px;">
                    </div>

                    <div class="form-group" style="margin-bottom: 18px;">
                        <label class="form-label" style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 12px;">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi kata sandi baru" style="width: 100%; padding: 8px 12px; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-family: inherit; font-size: 13px;">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                    <span>Simpan Perubahan Profil</span>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function handleCounselorPhotoSelect(input) {
    if (!input.files || !input.files[0]) return;
    window.AvatarCropper.openForFile(input.files[0], {
        uploadUrl: '{{ route('guru.profil.photo.update') }}',
        csrf: '{{ csrf_token() }}'
    });
    input.value = '';
}

function handleCounselorPhotoDelete() {
    window.AvatarCropper.openDeleteModal({
        deleteUrl: '{{ route('guru.profil.photo.destroy') }}',
        csrf: '{{ csrf_token() }}'
    });
}
</script>
@endsection
