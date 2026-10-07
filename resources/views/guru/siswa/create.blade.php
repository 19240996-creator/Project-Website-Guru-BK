@extends('layouts.app')

@section('title', 'Tambah Siswa Baru - Guru BK')
@section('header_title', 'Tambah Siswa Baru')

@section('content')
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title">Formulir Pendaftaran Siswa Baru</h3>
        <a href="{{ route('guru.siswa.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
    </div>
    <div class="card-body">
        <form action="{{ route('guru.siswa.store') }}" method="POST">
            @csrf
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Nomor Induk Siswa (NIS) *</label>
                    <input type="text" name="nis" class="form-control" value="{{ old('nis') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">NISN (10 Digit) *</label>
                    <input type="text" name="nisn" class="form-control" value="{{ old('nisn') }}" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Nama Lengkap Siswa *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Jenis Kelamin *</label>
                    <select name="gender" class="form-select" required>
                        <option value="L" {{ old('gender') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('gender') === 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Kelas & Jurusan *</label>
                    <select name="student_class_id" class="form-select" required>
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ old('student_class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->major }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Tempat Lahir</label>
                    <input type="text" name="birth_place" class="form-control" value="{{ old('birth_place') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Tanggal Lahir</label>
                    <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date') }}">
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">No. Telepon / WhatsApp Siswa</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Tingkat Perhatian BK *</label>
                    <select name="attention_level" class="form-select" required>
                        <option value="normal" {{ old('attention_level') === 'normal' ? 'selected' : '' }}>Normal (Kondisi Stabil)</option>
                        <option value="perlu_perhatian" {{ old('attention_level') === 'perlu_perhatian' ? 'selected' : '' }}>Perlu Perhatian (Pantau)</option>
                        <option value="prioritas" {{ old('attention_level') === 'prioritas' ? 'selected' : '' }}>Prioritas (Jadwalkan Konseling)</option>
                        <option value="segera_ditindaklanjuti" {{ old('attention_level') === 'segera_ditindaklanjuti' ? 'selected' : '' }}>Segera Ditindaklanjuti</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Alamat Lengkap Siswa</label>
                <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Nama Orang Tua / Wali</label>
                    <input type="text" name="parent_name" class="form-control" value="{{ old('parent_name') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">No. Telepon Orang Tua</label>
                    <input type="text" name="parent_phone" class="form-control" value="{{ old('parent_phone') }}">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Pekerjaan Orang Tua</label>
                <input type="text" name="parent_job" class="form-control" value="{{ old('parent_job') }}">
            </div>

            <div class="form-group">
                <label class="form-label">Catatan Khusus Bimbingan (Internal BK)</label>
                <textarea name="special_notes" class="form-control" rows="3" placeholder="Informasi latar belakang atau kebutuhan pendampingan siswa...">{{ old('special_notes') }}</textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px;">
                <a href="{{ route('guru.siswa.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Data Siswa</button>
            </div>
        </form>
    </div>
</div>
@endsection
