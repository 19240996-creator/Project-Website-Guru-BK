@extends('layouts.siswa')

@section('title', 'Hasil Asesmen - ' . $result->result_category)

@section('content')
<div style="max-width: 760px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
        <div>
            <h2 style="font-size: 20px; font-weight: 800; color: var(--color-text-main);">Hasil Pemetaan Asesmen Diri</h2>
            <p style="font-size: 13px; color: var(--color-text-muted);">
                Instrumen: <strong>{{ $result->assessment ? $result->assessment->title : '-' }}</strong> | Tanggal: {{ $result->created_at->translatedFormat('d F Y') }}
            </p>
        </div>
        <a href="{{ route('siswa.asesmen.index') }}" class="btn btn-secondary">
            Kembali ke Daftar Asesmen
        </a>
    </div>

    <!-- Highlight Box -->
    <div class="card" style="border-top: 4px solid var(--color-primary); margin-bottom: 24px;">
        <div class="card-body" style="padding: 28px;">
            <div style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: var(--color-primary); letter-spacing: 0.05em;">
                Tipologi / Kategori Minat Utama Anda:
            </div>
            <div style="font-size: 24px; font-weight: 800; color: var(--color-text-main); margin-top: 6px; margin-bottom: 12px;">
                {{ $result->result_category }}
            </div>

            <div style="padding: 16px; background: #f8fafc; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-size: 14px; line-height: 1.6; color: var(--color-text-main); margin-bottom: 20px;">
                {{ $result->summary }}
            </div>

            <div style="font-size: 13px; font-weight: 700; color: var(--color-text-main); margin-bottom: 6px;">
                Rekomendasi Bimbingan Karier:
            </div>
            <div style="font-size: 13px; line-height: 1.6; color: var(--color-text-muted);">
                {{ $result->recommendations }}
            </div>

            <div class="alert alert-info" style="font-size: 12px; margin-top: 24px; margin-bottom: 0;">
                <strong>Catatan Penting:</strong> Sesuai prinsip bimbingan konseling, hasil asesmen ini <strong>bukan diagnosis mutlak</strong> dan bukan keputusan sepihak atas masa depan Anda, melainkan bahan pertimbangan berharga untuk menyusun rencana masa depan bersama Guru BK.
            </div>
        </div>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center;">
        <a href="{{ route('siswa.asesmen.take', $result->assessment_id) }}" class="btn btn-secondary">
            Isi Ulang Asesmen
        </a>
        <a href="{{ route('siswa.rencana.show') }}" class="btn btn-primary">
            Lanjutkan ke Rute Masa Depan →
        </a>
    </div>
</div>
@endsection
