@extends('layouts.app')

@section('title', 'Analisis Hasil Asesmen Siswa')
@section('header_title', 'Analisis Hasil Asesmen Siswa')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
    <div>
        <h2 style="font-size: 20px; font-weight: 800; color: var(--color-text-main);">Hasil Asesmen: {{ $result->student->name }}</h2>
        <p style="font-size: 13px; color: var(--color-text-muted);">
            Instrumen: <strong>{{ $result->assessment ? $result->assessment->title : '-' }}</strong> | Tanggal Mengerjakan: {{ $result->created_at->translatedFormat('d F Y') }}
        </p>
    </div>
    <a href="{{ route('guru.asesmen.index') }}" class="btn btn-secondary">
        Kembali ke Daftar Asesmen
    </a>
</div>

<div class="grid-2">
    <!-- Kolom Kiri: Hasil Mentah & Dimensi -->
    <div>
        <div class="card" style="border-top: 4px solid var(--color-primary); margin-bottom: 24px;">
            <div class="card-header card-header-navy">
                <h3 class="card-title">Kategori Minat Utama: {{ $result->result_category }}</h3>
            </div>
            <div class="card-body">
                <!-- Distribusi Dimensi RIASEC -->
                @if(!empty($result->dimension_scores) && is_array($result->dimension_scores))
                    <div style="margin-bottom: 16px;">
                        <div style="font-size: 13px; font-weight: 700; color: var(--color-text-main); margin-bottom: 12px;">
                            Distribusi Skor Dimensi Minat (RIASEC):
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            @foreach($result->dimension_scores as $dim)
                                <div>
                                    <div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 4px;">
                                        <span style="font-weight: 600; color: var(--color-text-main);">
                                            <strong>[{{ $dim['code'] }}]</strong> {{ $dim['name'] }}
                                        </span>
                                        <span style="font-weight: 700; color: var(--color-primary);">
                                            {{ $dim['percentage'] }}% ({{ $dim['count'] }} butir)
                                        </span>
                                    </div>
                                    <div style="width: 100%; height: 8px; background: var(--color-border); border-radius: 4px; overflow: hidden;">
                                        <div style="width: {{ max(4, $dim['percentage']) }}%; height: 100%; background: var(--color-primary); border-radius: 4px;"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <p style="font-size: 13px; color: var(--color-text-muted);">Detail distribusi dimensi tidak tersedia.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Form Analisis & Catatan Guru -->
    <div>
        <div class="card">
            <div class="card-header card-header-navy">
                <h3 class="card-title">Analisis Konselor & Tindak Lanjut</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('guru.asesmen.result.update', $result->id) }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label class="form-label">Ringkasan Interpretasi Minat</label>
                        <textarea name="summary" class="form-control" rows="4" placeholder="Tuliskan ringkasan hasil asesmen untuk siswa...">{{ old('summary', $result->summary) }}</textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Rekomendasi Bimbingan Karier & Rumpun Studi</label>
                        <textarea name="recommendations" class="form-control" rows="5" placeholder="Berikan rekomendasi yang mengarahkan pengembangan karier siswa...">{{ old('recommendations', $result->recommendations) }}</textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Status Publikasi</label>
                        <select name="is_published" class="form-control" required>
                            <option value="1" {{ old('is_published', $result->is_published) == '1' ? 'selected' : '' }}>Terbuka Untuk Siswa (Dapat dilihat oleh siswa)</option>
                            <option value="0" {{ old('is_published', $result->is_published) == '0' ? 'selected' : '' }}>Disembunyikan (Hanya untuk analisis Guru BK)</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        Simpan Analisis & Publikasi
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
