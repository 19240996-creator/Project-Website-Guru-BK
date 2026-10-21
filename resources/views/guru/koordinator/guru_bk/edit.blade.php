@extends('layouts.app')

@section('title', 'Ubah Akun Guru BK - Ruang BK')
@section('header_title', 'Ubah Akun Guru Bimbingan Konseling')

@section('content')
<!-- Page Header Bar -->
<div class="page-header-bar" style="margin-bottom: 24px;">
    <div>
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
            <a href="{{ route('guru.koordinator.guru_bk.index') }}" style="display: inline-flex; align-items: center; gap: 6px; color: var(--color-primary); text-decoration: none; font-size: 12.5px; font-weight: 700;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                <span>Kembali ke Daftar Guru BK</span>
            </a>
            <span style="color: var(--color-border-strong);">/</span>
            <span style="font-size: 12px; color: var(--color-text-muted);">Ubah Akun Pendidik</span>
        </div>
        <h1 class="page-header-title">Ubah Informasi Akun Guru BK</h1>
    </div>
    <div style="display: flex; gap: 10px; align-items: center;">
        <a href="{{ route('guru.koordinator.guru_bk.show', $counselor->id) }}" class="btn btn-secondary btn-sm" title="Lihat detail profil dan siswa binaan">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
            <span>Lihat Profil & Siswa Binaan</span>
        </a>
    </div>
</div>

<div class="card" style="max-width: 860px; margin: 0 auto 36px; box-shadow: var(--shadow-sm); border: 1px solid var(--color-border); border-radius: var(--radius-lg); overflow: hidden;">
    <div class="card-header card-header-navy" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; padding: 18px 24px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 38px; height: 38px; border-radius: 8px; background: rgba(255, 255, 255, 0.12); display: flex; align-items: center; justify-content: center; color: #FFFFFF;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            </div>
            <div>
                <h3 class="card-title" style="margin: 0; font-size: 16px; font-weight: 800; color: #FFFFFF;">Pembaruan Data & Kredensial Guru BK</h3>
                <p style="margin: 2px 0 0 0; font-size: 12px; color: rgba(255, 255, 255, 0.75);">Perbarui informasi profil, identitas kepegawaian, kontak dinas, serta hak status akun</p>
            </div>
        </div>
        <span class="badge badge-translucent" style="font-size: 11.5px; font-weight: 700;">
            ID Akun: #{{ $counselor->id }}
        </span>
    </div>

    <div class="card-body" style="padding: 28px;">
        <form method="POST" action="{{ route('guru.koordinator.guru_bk.update', $counselor->id) }}" enctype="multipart/form-data" id="editGuruBkForm">
            @csrf
            @method('PUT')

            <!-- Section 1: Foto Profil Guru BK -->
            <div style="margin-bottom: 28px; padding: 20px; background-color: var(--color-surface-subtle); border: 1px solid var(--color-border); border-radius: var(--radius-md);">
                <div class="avatar-profile-header">
                    <div class="avatar-current-wrapper" style="width: 84px; height: 84px;">
                        <img src="{{ $counselor->avatar_url }}" alt="{{ $counselor->name }}" id="counselorAvatarPreview" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div class="avatar-profile-info">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px; flex-wrap: wrap;">
                            @if($counselor->avatar)
                                <div class="avatar-status-badge custom" style="margin-bottom: 0;">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    <span>Foto Khusus Terpasang</span>
                                </div>
                            @else
                                <div class="avatar-status-badge default" style="margin-bottom: 0;">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                    <span>Menggunakan Avatar Default</span>
                                </div>
                            @endif
                            <span style="font-size: 11.5px; color: var(--color-text-muted);">Maksimal 3 MB (JPG, PNG, WebP)</span>
                        </div>
                        <h4 style="font-size: 14px; font-weight: 700; color: var(--color-text-main); margin-bottom: 4px;">Foto Profil Pendidik</h4>
                        <p style="font-size: 12px; color: var(--color-text-muted); margin-bottom: 12px; line-height: 1.45;">
                            Foto profil resmi ini akan tampil pada antarmuka penugasan siswa, lembar hasil asesmen, dan kartu bimbingan konseling.
                        </p>
                        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                            <input type="file" name="photo" id="counselorPhotoInput" accept="image/jpeg,image/png,image/jpg,image/webp" style="display: none;" onchange="previewCounselorAvatar(this)">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('counselorPhotoInput').click()" style="font-weight: 600;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                                <span>Pilih Foto Baru</span>
                            </button>
                            <span id="counselorPhotoFilename" style="font-size: 12px; color: var(--color-primary); font-weight: 600; display: none;"></span>
                        </div>
                    </div>
                </div>
                @error('photo')
                    <div class="form-error" style="margin-top: 8px;">{{ $message }}</div>
                @enderror
            </div>

            <!-- Section 2: Data Pokok Pendidik -->
            <div style="margin-bottom: 26px;">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 14px; padding-bottom: 8px; border-bottom: 1px solid var(--color-border);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="color: var(--color-primary);"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    <h4 style="font-size: 13.5px; font-weight: 800; color: var(--color-navy); margin: 0; text-transform: uppercase; letter-spacing: 0.03em;">Data Pokok & Kontak Kedinasan</h4>
                </div>

                <div class="form-group" style="margin-bottom: 18px;">
                    <label for="name" class="form-label">
                        Nama Lengkap Beserta Gelar <span style="color: var(--color-danger);">*</span>
                    </label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $counselor->name) }}" placeholder="Contoh: Dra. Hj. Siti Rahmawati, M.Pd." required />
                    <div class="form-hint">Cantumkan nama lengkap dan gelar akademis resmi untuk keperluan administrasi dan pelaporan bimbingan.</div>
                    @error('name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 18px;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="nip" class="form-label">
                            NIP / NUPTK (Opsional)
                        </label>
                        <input type="text" name="nip" id="nip" class="form-control" value="{{ old('nip', $counselor->nip) }}" placeholder="Contoh: 19800512 200501 2 004" />
                        <div class="form-hint">Nomor identitas kepegawaian resmi Guru BK.</div>
                        @error('nip')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="phone" class="form-label">
                            Nomor WhatsApp / HP Aktif
                        </label>
                        <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $counselor->phone) }}" placeholder="Contoh: 081234567890" />
                        <div class="form-hint">Kontak dinas yang dapat dihubungi terkait koordinasi konseling siswa.</div>
                        @error('phone')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section 3: Kredensial Akses & Akun Login -->
            <div style="margin-bottom: 26px;">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 14px; padding-bottom: 8px; border-bottom: 1px solid var(--color-border);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="color: var(--color-primary);"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    <h4 style="font-size: 13.5px; font-weight: 800; color: var(--color-navy); margin: 0; text-transform: uppercase; letter-spacing: 0.03em;">Kredensial Akses Aplikasi</h4>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 18px;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="username" class="form-label">
                            Username Login <span style="color: var(--color-danger);">*</span>
                        </label>
                        <input type="text" name="username" id="username" class="form-control" value="{{ old('username', $counselor->username) }}" required />
                        <div class="form-hint">Digunakan saat masuk ke halaman login Ruang BK.</div>
                        @error('username')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="email" class="form-label">
                            Alamat Email Resmi <span style="color: var(--color-danger);">*</span>
                        </label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $counselor->email) }}" required />
                        <div class="form-hint">Email aktif untuk notifikasi sistem dan pemulihan kredensial.</div>
                        @error('email')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section 4: Atur Ulang Kata Sandi -->
            <div style="margin-bottom: 26px; padding: 18px 20px; background-color: #F8FAFD; border: 1px solid var(--color-border); border-radius: var(--radius-md);">
                <div style="display: flex; align-items: flex-start; gap: 10px; margin-bottom: 14px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--color-primary); margin-top: 2px; flex-shrink: 0;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                    <div>
                        <h4 style="font-size: 13.5px; font-weight: 700; color: var(--color-navy); margin: 0 0 2px 0;">Atur Ulang Kata Sandi (Opsional)</h4>
                        <p style="font-size: 12px; color: var(--color-text-muted); margin: 0; line-height: 1.45;">
                            Kosongkan kedua kolom di bawah jika Anda tidak ingin mengubah kata sandi Guru BK saat ini. Isi hanya jika Guru BK lupa kata sandi atau memerlukan reset kredensial.
                        </p>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 18px;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="password" class="form-label">
                            Kata Sandi Baru
                        </label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Minimal 6 karakter jika ingin diubah" autocomplete="new-password" />
                        @error('password')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="password_confirmation" class="form-label">
                            Konfirmasi Kata Sandi Baru
                        </label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Ulangi kata sandi baru" autocomplete="new-password" />
                    </div>
                </div>
            </div>

            <!-- Section 5: Status Akun Guru BK -->
            <div style="margin-bottom: 28px;">
                <label class="form-label" style="margin-bottom: 10px;">Status Akun & Hak Penanganan Siswa <span style="color: var(--color-danger);">*</span></label>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 14px;">
                    <!-- Option 1: Aktif -->
                    <label class="status-option-card {{ old('is_active', $counselor->is_active) ? 'selected-active' : '' }}" id="labelStatusActive" style="cursor: pointer; display: flex; align-items: flex-start; gap: 12px; padding: 14px 16px; border: 2px solid {{ old('is_active', $counselor->is_active) ? 'var(--color-primary)' : 'var(--color-border)' }}; border-radius: var(--radius-md); background: {{ old('is_active', $counselor->is_active) ? 'var(--color-primary-light)' : '#FFFFFF' }}; transition: all var(--transition-fast);">
                        <input type="radio" name="is_active" value="1" {{ old('is_active', $counselor->is_active) ? 'checked' : '' }} onchange="updateStatusCardVisual(1)" style="margin-top: 3px; accent-color: var(--color-primary);" />
                        <div>
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background-color: var(--color-success); display: inline-block;"></span>
                                <strong style="font-size: 13.5px; color: var(--color-navy);">Akun Aktif</strong>
                            </div>
                            <p style="font-size: 12px; color: var(--color-text-muted); margin: 4px 0 0 0; line-height: 1.4;">
                                Guru BK dapat login dan memiliki hak akses untuk membimbing siswa yang ditugaskan.
                            </p>
                        </div>
                    </label>

                    <!-- Option 2: Nonaktif -->
                    <label class="status-option-card {{ !old('is_active', $counselor->is_active) ? 'selected-inactive' : '' }}" id="labelStatusInactive" style="cursor: pointer; display: flex; align-items: flex-start; gap: 12px; padding: 14px 16px; border: 2px solid {{ !old('is_active', $counselor->is_active) ? 'var(--color-danger)' : 'var(--color-border)' }}; border-radius: var(--radius-md); background: {{ !old('is_active', $counselor->is_active) ? 'var(--color-danger-bg)' : '#FFFFFF' }}; transition: all var(--transition-fast);">
                        <input type="radio" name="is_active" value="0" {{ !old('is_active', $counselor->is_active) ? 'checked' : '' }} onchange="updateStatusCardVisual(0)" style="margin-top: 3px; accent-color: var(--color-danger);" />
                        <div>
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background-color: var(--color-danger); display: inline-block;"></span>
                                <strong style="font-size: 13.5px; color: var(--color-danger);">Nonaktif (Ditangguhkan)</strong>
                            </div>
                            <p style="font-size: 12px; color: var(--color-text-muted); margin: 4px 0 0 0; line-height: 1.4;">
                                Akses masuk ke sistem ditangguhkan sementara tanpa menghapus riwayat bimbingan lampau.
                            </p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Footer Form Action -->
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 14px; margin-top: 32px; border-top: 1px solid var(--color-border); padding-top: 20px; flex-wrap: wrap;">
                <div style="font-size: 12px; color: var(--color-text-muted);">
                    <span style="color: var(--color-danger);">*</span> Kolom bertanda bintang wajib diisi
                </div>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <a href="{{ route('guru.koordinator.guru_bk.index') }}" class="btn btn-secondary" style="font-weight: 600;">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary" style="font-weight: 700;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                        <span>Simpan Perubahan Data</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function previewCounselorAvatar(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('counselorAvatarPreview').src = e.target.result;
        };
        reader.readAsDataURL(file);

        const filenameLabel = document.getElementById('counselorPhotoFilename');
        if (filenameLabel) {
            filenameLabel.textContent = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
            filenameLabel.style.display = 'inline';
        }
    }
}

function updateStatusCardVisual(val) {
    const activeCard = document.getElementById('labelStatusActive');
    const inactiveCard = document.getElementById('labelStatusInactive');
    if (val === 1) {
        activeCard.style.borderColor = 'var(--color-primary)';
        activeCard.style.backgroundColor = 'var(--color-primary-light)';
        inactiveCard.style.borderColor = 'var(--color-border)';
        inactiveCard.style.backgroundColor = '#FFFFFF';
    } else {
        activeCard.style.borderColor = 'var(--color-border)';
        activeCard.style.backgroundColor = '#FFFFFF';
        inactiveCard.style.borderColor = 'var(--color-danger)';
        inactiveCard.style.backgroundColor = 'var(--color-danger-bg)';
    }
}
</script>
@endsection
