@extends('layouts.siswa')

@section('title', 'Jejak Alumni & Inspirasi Karier')

@section('content')
<div style="margin-bottom: 24px;">
    <h2 style="font-size: 20px; font-weight: 800; color: var(--color-text-main);">Jejak Alumni & Inspirasi Masa Depan</h2>
    <p style="font-size: 13px; color: var(--color-text-muted);">
        Kisah dan pencapaian kakak kelas yang telah lulus menempuh studi perguruan tinggi maupun berkarier di dunia industri profesional.
    </p>
</div>

<!-- Jika Siswa Sudah Berstatus Lulus: Form Pembaruan Tracer Study (Section 24) -->
@if($student->status === 'lulus')
    <div class="card" style="border-top: 4px solid var(--color-primary); margin-bottom: 28px;">
        <div class="card-header">
            <h3 class="card-title">Pembaruan Pelacakan Alumni (Tracer Study)</h3>
            <span class="badge badge-success">Status Lulusan</span>
        </div>
        <div class="card-body">
            <p style="font-size: 13px; color: var(--color-text-muted); margin-bottom: 16px;">
                Sebagai alumni, data keberlanjutan karier Anda sangat berharga bagi sekolah dan adik kelas. Perbarui status aktivitas Anda saat ini:
            </p>

            <form action="{{ route('siswa.alumni.tracer.update') }}" method="POST">
                @csrf
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Tahun Kelulusan *</label>
                        <input type="number" name="graduation_year" class="form-control" value="{{ old('graduation_year', $myTracer ? $myTracer->graduation_year : date('Y')) }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Periode Pelacakan Saat Ini *</label>
                        <select name="tracking_period" class="form-select" required>
                            <option value="3_bulan" {{ $myTracer && $myTracer->tracking_period === '3_bulan' ? 'selected' : '' }}>3 Bulan Pasca Kelulusan</option>
                            <option value="6_bulan" {{ $myTracer && $myTracer->tracking_period === '6_bulan' ? 'selected' : '' }}>6 Bulan Pasca Kelulusan</option>
                            <option value="12_bulan" {{ $myTracer && $myTracer->tracking_period === '12_bulan' ? 'selected' : '' }}>12 Bulan (1 Tahun) Pasca Kelulusan</option>
                            <option value="24_bulan" {{ $myTracer && $myTracer->tracking_period === '24_bulan' ? 'selected' : '' }}>24 Bulan (2 Tahun) Pasca Kelulusan</option>
                        </select>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Aktivitas Utama Anda Saat Ini *</label>
                        <select name="current_status" class="form-select" required>
                            <option value="bekerja" {{ $myTracer && $myTracer->current_status === 'bekerja' ? 'selected' : '' }}>Bekerja (Perusahaan / Swasta / BUMN / PNS)</option>
                            <option value="kuliah" {{ $myTracer && $myTracer->current_status === 'kuliah' ? 'selected' : '' }}>Kuliah (Studi Lanjut)</option>
                            <option value="wirausaha" {{ $myTracer && $myTracer->current_status === 'wirausaha' ? 'selected' : '' }}>Wirausaha (Membuka Usaha Mandiri)</option>
                            <option value="mencari_kerja" {{ $myTracer && $myTracer->current_status === 'mencari_kerja' ? 'selected' : '' }}>Sedang Proses Mencari Kerja</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nama Instansi / Perusahaan / Kampus</label>
                        <input type="text" name="institution_or_company" class="form-control" value="{{ old('institution_or_company', $myTracer ? $myTracer->institution_or_company : '') }}" placeholder="Contoh: PT Telkom Indonesia atau ITB">
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Posisi Jabatan / Program Studi</label>
                        <input type="text" name="major_or_position" class="form-control" value="{{ old('major_or_position', $myTracer ? $myTracer->major_or_position : '') }}" placeholder="Contoh: IT Support atau S1 Teknik Informatika">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Kisaran Pendapatan Rata-rata per Bulan</label>
                        <input type="text" name="monthly_income_range" class="form-control" value="{{ old('monthly_income_range', $myTracer ? $myTracer->monthly_income_range : '') }}" placeholder="Contoh: Rp 4.500.000 - Rp 6.000.000">
                    </div>
                </div>

                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; cursor: pointer;">
                        <input type="checkbox" name="allow_public_showcase" value="1" {{ $myTracer && $myTracer->allow_public_showcase ? 'checked' : '' }}>
                        <span>Saya mengizinkan jejak karier saya ditampilkan sebagai inspirasi untuk adik kelas di sekolah.</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary">
                    Simpan & Perbarui Data Tracer Study Saya
                </button>
            </form>
        </div>
    </div>
@endif

<!-- Galeri Inspirasi Alumni -->
<div class="grid-3">
    @forelse($showcaseAlumni as $als)
        <div class="card" style="padding: 20px; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 14px;">
                    @if($als->photo)
                        <img src="{{ asset('storage/' . $als->photo) }}" alt="{{ $als->student ? $als->student->name : 'Alumni' }}" style="width: 44px; height: 44px; border-radius: 50%; object-fit: cover; border: 2px solid var(--color-primary-border); flex-shrink: 0;">
                    @elseif($als->student && $als->student->avatar)
                        <img src="{{ asset('storage/' . $als->student->avatar) }}" alt="{{ $als->student->name }}" style="width: 44px; height: 44px; border-radius: 50%; object-fit: cover; border: 2px solid var(--color-primary-border); flex-shrink: 0;">
                    @else
                        <div class="user-avatar-initial" style="background: var(--color-primary); width: 44px; height: 44px; font-size: 15px; flex-shrink: 0;">
                            {{ strtoupper(substr($als->student ? $als->student->name : 'A', 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <div style="font-weight: 700; font-size: 14px; color: var(--color-text-main);">
                            {{ $als->student ? $als->student->name : 'Alumni' }}
                        </div>
                        <div style="font-size: 12px; color: var(--color-text-subtle);">
                            Lulusan Tahun {{ $als->graduation_year }}
                        </div>
                    </div>
                </div>

                <div style="margin-bottom: 12px;">
                    @if($als->current_status === 'bekerja')
                        <span class="badge badge-success">Bekerja Profesional</span>
                    @elseif($als->current_status === 'kuliah')
                        <span class="badge badge-primary">Menempuh Pendidikan Tinggi</span>
                    @elseif($als->current_status === 'wirausaha')
                        <span class="badge badge-warning">Wirausahawan Mandiri</span>
                    @endif
                </div>

                <div style="font-size: 13px; color: var(--color-text-main); font-weight: 700;">
                    {{ $als->major_or_position ?: 'Tenaga Profesional' }}
                </div>
                <div style="font-size: 13px; color: var(--color-text-muted); margin-top: 2px;">
                    {{ $als->institution_or_company ?: '-' }}
                </div>

                @if($als->notes)
                    <p style="font-size: 12px; color: var(--color-text-subtle); margin-top: 10px; line-height: 1.5; font-style: italic;">
                        "{{ $als->notes }}"
                    </p>
                @endif
            </div>

            <div style="font-size: 11px; color: var(--color-text-subtle); margin-top: 16px; border-top: 1px dashed var(--color-border); padding-top: 8px;">
                Jurusan Asal: {{ $als->student && $als->student->studentClass ? $als->student->studentClass->major : '-' }}
            </div>
        </div>
    @empty
        <div style="grid-column: 1 / -1;">
            <div class="empty-state">
                <p class="empty-state-title">Belum Ada Jejak Alumni yang Ditampilkan</p>
                <p class="empty-state-desc">Alumni yang memberikan izin publikasi akan ditampilkan di sini sebagai inspirasi pilihan karier.</p>
            </div>
        </div>
    @endforelse
</div>

<div style="margin-top: 24px;">
    {{ $showcaseAlumni->links() }}
</div>
@endsection
