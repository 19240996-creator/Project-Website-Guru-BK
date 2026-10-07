@extends('layouts.app')

@section('title', 'Ubah Data Siswa - ' . $student->name)
@section('header_title', 'Ubah Data Siswa')

@section('content')
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title">Perbarui Profil: {{ $student->name }}</h3>
        <a href="{{ route('guru.siswa.show', $student->id) }}" class="btn btn-secondary btn-sm">Kembali</a>
    </div>
    <div class="card-body">
        <form action="{{ route('guru.siswa.update', $student->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">NIS *</label>
                    <input type="text" name="nis" class="form-control" value="{{ old('nis', $student->nis) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">NISN *</label>
                    <input type="text" name="nisn" class="form-control" value="{{ old('nisn', $student->nisn) }}" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Nama Lengkap Siswa *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $student->name) }}" required>
            </div>

            <div class="grid-3">
                <div class="form-group">
                    <label class="form-label">Jenis Kelamin *</label>
                    <select name="gender" class="form-select" required>
                        <option value="L" {{ old('gender', $student->gender) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('gender', $student->gender) === 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Kelas & Jurusan *</label>
                    <select name="student_class_id" class="form-select" required>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ old('student_class_id', $student->student_class_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Status Siswa *</label>
                    <select name="status" class="form-select" required>
                        <option value="aktif" {{ old('status', $student->status) === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="lulus" {{ old('status', $student->status) === 'lulus' ? 'selected' : '' }}>Lulus</option>
                        <option value="pindah" {{ old('status', $student->status) === 'pindah' ? 'selected' : '' }}>Pindah</option>
                        <option value="tidak_aktif" {{ old('status', $student->status) === 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Tempat Lahir</label>
                    <input type="text" name="birth_place" class="form-control" value="{{ old('birth_place', $student->birth_place) }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Tanggal Lahir</label>
                    <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date', $student->birth_date ? $student->birth_date->format('Y-m-d') : '') }}">
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">No. Telepon / HP Siswa</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $student->phone) }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Tingkat Perhatian *</label>
                    <select name="attention_level" class="form-select" required>
                        <option value="normal" {{ old('attention_level', $student->attention_level) === 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="perlu_perhatian" {{ old('attention_level', $student->attention_level) === 'perlu_perhatian' ? 'selected' : '' }}>Perlu Perhatian</option>
                        <option value="prioritas" {{ old('attention_level', $student->attention_level) === 'prioritas' ? 'selected' : '' }}>Prioritas</option>
                        <option value="segera_ditindaklanjuti" {{ old('attention_level', $student->attention_level) === 'segera_ditindaklanjuti' ? 'selected' : '' }}>Segera Ditindaklanjuti</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Alamat Rumah</label>
                <textarea name="address" class="form-control" rows="2">{{ old('address', $student->address) }}</textarea>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Nama Orang Tua / Wali</label>
                    <input type="text" name="parent_name" class="form-control" value="{{ old('parent_name', $student->parent_name) }}">
                </div>

                <div class="form-group">
                    <label class="form-label">No. Telepon Orang Tua</label>
                    <input type="text" name="parent_phone" class="form-control" value="{{ old('parent_phone', $student->parent_phone) }}">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Pekerjaan Orang Tua</label>
                <input type="text" name="parent_job" class="form-control" value="{{ old('parent_job', $student->parent_job) }}">
            </div>

            <div class="form-group">
                <label class="form-label">Catatan Khusus Bimbingan</label>
                <textarea name="special_notes" class="form-control" rows="3">{{ old('special_notes', $student->special_notes) }}</textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px;">
                <a href="{{ route('guru.siswa.show', $student->id) }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
