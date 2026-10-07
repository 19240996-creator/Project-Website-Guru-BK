@extends('layouts.siswa')

@section('title', 'Ajukan Konseling Baru')

@section('content')
<div class="card" style="max-width: 720px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title">Formulir Pengajuan Layanan Konseling</h3>
        <a href="{{ route('siswa.konseling.index') }}" class="btn btn-secondary btn-sm">Batal</a>
    </div>
    <div class="card-body">
        <div class="alert alert-info" style="font-size: 13px; margin-bottom: 20px;">
            <strong>Kerahasiaan Terjamin:</strong> Informasi yang Anda tuliskan bersifat rahasia dan hanya dapat dibaca oleh Guru Bimbingan Konseling sekolah.
        </div>

        <form action="{{ route('siswa.konseling.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Kategori Masalah / Topik Konsultasi *</label>
                <select name="category_id" class="form-select" required>
                    <option value="">-- Pilih Kategori Masalah --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Judul / Pokok Topik *</label>
                <input type="text" name="topic" class="form-control" value="{{ old('topic') }}" placeholder="Contoh: Kesulitan mengatur waktu belajar dan tugas kejuruan..." required>
            </div>

            <div class="form-group">
                <label class="form-label">Tingkat Urgensi yang Anda Rasakan *</label>
                <select name="urgency" class="form-select" required>
                    <option value="sedang" {{ old('urgency') === 'sedang' ? 'selected' : '' }}>Sedang (Dapat dijadwalkan dalam pekan ini)</option>
                    <option value="rendah" {{ old('urgency') === 'rendah' ? 'selected' : '' }}>Rendah (Konsultasi santai berkala)</option>
                    <option value="tinggi" {{ old('urgency') === 'tinggi' ? 'selected' : '' }}>Tinggi (Perlu penanganan segera)</option>
                    <option value="mendesak" {{ old('urgency') === 'mendesak' ? 'selected' : '' }}>Mendesak (Membutuhkan perhatian hari ini)</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Pilihan Waktu / Jam yang Nyaman untuk Anda</label>
                <input type="text" name="preferred_schedule" class="form-control" value="{{ old('preferred_schedule') }}" placeholder="Contoh: Hari Rabu setelah istirahat kedua atau jam pulang sekolah">
                <div class="form-hint">Guru BK akan menyesuaikan waktu agar tidak mengganggu jam pelajaran penting Anda.</div>
            </div>

            <div class="form-group">
                <label class="form-label">Cerita Singkat Hal yang Ingin Anda Konsultasikan *</label>
                <textarea name="story" class="form-control" rows="5" placeholder="Ceritakan secara terbuka apa yang sedang Anda alami atau rasakan..." required>{{ old('story') }}</textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px;">
                <a href="{{ route('siswa.konseling.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Kirim Permohonan Konseling</button>
            </div>
        </form>
    </div>
</div>
@endsection
