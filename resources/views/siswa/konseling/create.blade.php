@extends('layouts.siswa')

@section('title', 'Ajukan Konseling Baru')

@section('content')
<div class="card" style="max-width: 820px; margin: 0 auto;">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3 class="card-title">Formulir Pengajuan Layanan Konseling</h3>
        <a href="{{ route('siswa.konseling.index') }}" class="btn btn-secondary btn-sm">Batal</a>
    </div>
    <div class="card-body" style="padding: 24px 28px;">
        <div class="alert alert-info" style="font-size: 13px; margin-bottom: 24px;">
            <strong>Kerahasiaan Terjamin:</strong> Informasi yang Anda sampaikan bersifat rahasia dan hanya dapat dibaca oleh Guru Bimbingan Konseling sekolah.
        </div>

        <form action="{{ route('siswa.konseling.store') }}" method="POST">
            @csrf

            <div style="display: grid; grid-template-columns: 200px 1fr; gap: 16px; margin-bottom: 18px; align-items: start;">
                <label class="form-label" style="padding-top: 8px; margin-bottom: 0;">Kategori Masalah <span style="color: var(--color-danger);">*</span></label>
                <div>
                    <select name="category_id" class="form-select" required>
                        <option value="">-- Pilih Kategori Masalah --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 200px 1fr; gap: 16px; margin-bottom: 18px; align-items: start;">
                <label class="form-label" style="padding-top: 8px; margin-bottom: 0;">Judul / Pokok Topik <span style="color: var(--color-danger);">*</span></label>
                <div>
                    <input type="text" name="topic" class="form-control" value="{{ old('topic') }}" placeholder="Contoh: Kesulitan mengatur waktu belajar dan tugas kejuruan..." required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 200px 1fr; gap: 16px; margin-bottom: 18px; align-items: start;">
                <label class="form-label" style="padding-top: 8px; margin-bottom: 0;">Tingkat Urgensi <span style="color: var(--color-danger);">*</span></label>
                <div>
                    <select name="urgency" class="form-select" required>
                        <option value="sedang" {{ old('urgency') === 'sedang' ? 'selected' : '' }}>Sedang (Dapat dijadwalkan dalam pekan ini)</option>
                        <option value="rendah" {{ old('urgency') === 'rendah' ? 'selected' : '' }}>Rendah (Konsultasi santai berkala)</option>
                        <option value="tinggi" {{ old('urgency') === 'tinggi' ? 'selected' : '' }}>Tinggi (Perlu penanganan segera)</option>
                        <option value="mendesak" {{ old('urgency') === 'mendesak' ? 'selected' : '' }}>Mendesak (Membutuhkan perhatian hari ini)</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 200px 1fr; gap: 16px; margin-bottom: 18px; align-items: start;">
                <label class="form-label" style="padding-top: 8px; margin-bottom: 0;">Waktu yang Diharapkan</label>
                <div>
                    <input type="text" name="preferred_schedule" class="form-control" value="{{ old('preferred_schedule') }}" placeholder="Contoh: Hari Rabu setelah istirahat kedua atau jam pulang sekolah">
                    <div class="form-hint" style="margin-top: 6px; font-size: 12px; color: var(--color-text-subtle);">Guru BK akan menyesuaikan jadwal agar tidak berbenturan dengan kegiatan belajar utama Anda.</div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 200px 1fr; gap: 16px; margin-bottom: 24px; align-items: start;">
                <label class="form-label" style="padding-top: 8px; margin-bottom: 0;">Uraian / Cerita Singkat <span style="color: var(--color-danger);">*</span></label>
                <div>
                    <textarea name="story" class="form-control" rows="5" placeholder="Ceritakan secara terbuka apa yang sedang Anda alami atau butuhkan..." required>{{ old('story') }}</textarea>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; padding-top: 18px; border-top: 1px solid var(--color-border);">
                <a href="{{ route('siswa.konseling.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Kirim Permohonan Konseling</button>
            </div>
        </form>
    </div>
</div>
@endsection
