@extends('layouts.pemantau')

@section('title', 'Profil Akun - Ruang BK Digital')

@section('content')
<!-- Page Header Bar -->
<div class="page-header-bar">
    <div>
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
            <span class="badge badge-primary" style="font-size: 11px; padding: 3px 8px; font-weight: 700;">
                PENGATURAN AKUN
            </span>
            <span style="font-size: 12px; color: var(--color-text-muted);">
                Identitas Akun & Keamanan Sesi
            </span>
        </div>
        <h1 class="page-header-title">
            Profil Pengguna {{ auth()->user()->role_label }}
        </h1>
    </div>
    <div class="page-header-date">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
        <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px; margin-bottom: 28px;">
    <!-- Card Info Akun -->
    <div class="card">
        <div class="card-body" style="padding: 24px; text-align: center;">
            <div style="display: flex; flex-direction: column; align-items: center; margin-bottom: 16px;">
                <div class="avatar-current-wrapper" style="width: 104px; height: 104px; margin-bottom: 12px; border: 3px solid var(--color-surface); outline: 2px solid var(--color-primary-border);">
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" id="userAvatarImg" />
                </div>

                @if($user->hasCustomAvatar())
                    <div class="avatar-status-badge custom" style="margin-bottom: 10px;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>Foto Khusus Terpasang</span>
                    </div>
                @else
                    <div class="avatar-status-badge default" style="margin-bottom: 10px;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                        <span>Menggunakan Avatar Default</span>
                    </div>
                @endif

                <div class="avatar-btn-group" style="justify-content: center; margin-top: 2px; margin-bottom: 8px;">
                    <input type="file" id="userPhotoInput" accept=".jpg,.jpeg,.png,.webp" style="display: none;" onchange="handleUserPhotoSelect(this)">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('userPhotoInput').click()" title="Unggah foto profil baru dengan preview crop interaktif">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                        <span>{{ $user->hasCustomAvatar() ? 'Ganti Foto' : 'Unggah Foto Baru' }}</span>
                    </button>
                    @if($user->hasCustomAvatar())
                        <button type="button" class="btn btn-danger-subtle btn-sm" onclick="handleUserPhotoDelete()" title="Hapus foto profil dan kembali ke avatar default">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                            <span>Hapus Foto</span>
                        </button>
                    @endif
                </div>
                <div style="font-size: 11px; color: var(--color-text-subtle); margin-top: 4px;">
                    Format JPG, JPEG, PNG, atau WebP (Maks. 5 MB)
                </div>
            </div>

            <h2 style="font-size: 17px; font-weight: 800; color: var(--color-navy); margin-bottom: 4px;">{{ $user->name }}</h2>
            <div style="font-size: 13px; color: var(--color-text-muted); margin-bottom: 14px;">
                Username: <span style="font-family: monospace; font-weight: 700; color: var(--color-navy);">{{ $user->username }}</span>
            </div>
            <span class="badge badge-primary" style="font-size: 12px; padding: 5px 12px;">
                {{ $user->role_label }}
            </span>

            <hr style="border: none; border-top: 1px solid var(--color-border); margin: 20px 0;" />

            <div style="text-align: left; display: flex; flex-direction: column; gap: 12px; font-size: 13px;">
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--color-text-muted);">Email Akun</span>
                    <span style="font-weight: 600; color: var(--color-navy);">{{ $user->email ?? '-' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--color-text-muted);">Nomor Handphone</span>
                    <span style="font-weight: 600; color: var(--color-navy);">{{ $user->phone ?? '-' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--color-text-muted);">Status Keaktifan</span>
                    <span class="badge badge-success" style="font-size: 11px;">Aktif</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Form Edit Profil -->
    <div class="card">
        <div class="card-header card-header-navy" style="padding: 16px 20px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(255, 255, 255, 0.12); color: #60A5FA; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                </div>
                <div>
                    <h3 class="card-title" style="font-size: 15px; font-weight: 700; color: #FFFFFF; margin: 0;">Perbarui Informasi Profil</h3>
                </div>
            </div>
        </div>
        <form method="POST" action="{{ route($rolePrefix . '.profil.update') }}">
            @csrf
            @method('PUT')
            <div class="card-body" style="padding: 20px; display: flex; flex-direction: column; gap: 16px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: var(--color-navy); margin-bottom: 6px;">
                        Nama Lengkap Beserta Gelar <span style="color: var(--color-danger);">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required style="height: 40px;" />
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: var(--color-navy); margin-bottom: 6px;">
                        Nomor Handphone / WhatsApp
                    </label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control" placeholder="Contoh: 081234567890" style="height: 40px;" />
                </div>

                <hr style="border: none; border-top: 1px solid var(--color-border); margin: 6px 0;" />
                <div style="font-size: 12.5px; font-weight: 700; color: var(--color-navy);">Ganti Kata Sandi (Opsional)</div>

                <div>
                    <label style="display: block; font-size: 12px; font-weight: 600; color: var(--color-text-muted); margin-bottom: 4px;">
                        Kata Sandi Saat Ini
                    </label>
                    <input type="password" name="current_password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah kata sandi" style="height: 40px;" />
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 600; color: var(--color-text-muted); margin-bottom: 4px;">
                            Kata Sandi Baru
                        </label>
                        <input type="password" name="new_password" class="form-control" placeholder="Minimal 6 karakter" style="height: 40px;" />
                    </div>
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 600; color: var(--color-text-muted); margin-bottom: 4px;">
                            Ulangi Kata Sandi Baru
                        </label>
                        <input type="password" name="new_password_confirmation" class="form-control" placeholder="Konfirmasi kata sandi" style="height: 40px;" />
                    </div>
                </div>

                <div style="margin-top: 10px; display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn btn-primary" style="height: 42px; font-weight: 700; padding: 0 20px;">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function handleUserPhotoSelect(input) {
    if (!input.files || !input.files[0]) return;
    window.AvatarCropper.openForFile(input.files[0], {
        uploadUrl: '{{ route($rolePrefix . '.profil.photo.update') }}',
        csrf: '{{ csrf_token() }}'
    });
    input.value = '';
}

function handleUserPhotoDelete() {
    window.AvatarCropper.openDeleteModal({
        deleteUrl: '{{ route($rolePrefix . '.profil.photo.destroy') }}',
        csrf: '{{ csrf_token() }}'
    });
}
</script>
@endsection

