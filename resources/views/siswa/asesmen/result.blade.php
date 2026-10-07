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

            <!-- Ringkasan Interpretasi Minat -->
            <div style="padding: 16px; background: #f8fafc; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-size: 14px; line-height: 1.6; color: var(--color-text-main); margin-bottom: 20px;">
                {{ $result->summary }}
            </div>

            <!-- Distribusi Dimensi RIASEC -->
            @if(!empty($result->dimension_scores) && is_array($result->dimension_scores))
                <div style="margin-bottom: 24px; padding: 18px; background: #ffffff; border: 1px solid var(--color-border); border-radius: var(--radius-sm);">
                    <div style="font-size: 13px; font-weight: 700; color: var(--color-text-main); margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center;">
                        <span>Distribusi Skor Dimensi Minat (RIASEC):</span>
                        <span style="font-size: 11px; color: var(--color-text-muted); font-weight: 500;">Berdasarkan jawaban kuesioner</span>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        @foreach($result->dimension_scores as $dim)
                            <div>
                                <div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 4px;">
                                    <span style="font-weight: 600; color: var(--color-text-main);">
                                        <strong>[{{ $dim['code'] }}]</strong> {{ $dim['name'] }} <span style="color: var(--color-text-muted); font-weight: normal;">({{ $dim['label'] }})</span>
                                    </span>
                                    <span style="font-weight: 700; color: var(--color-primary);">
                                        {{ $dim['percentage'] }}% ({{ $dim['count'] }} butir)
                                    </span>
                                </div>
                                <div style="width: 100%; height: 8px; background: var(--color-border); border-radius: 4px; overflow: hidden;">
                                    <div style="width: {{ max(4, $dim['percentage']) }}%; height: 100%; background: var(--color-primary); border-radius: 4px; transition: width 0.3s ease;"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Rekomendasi Bimbingan Karier & Program Studi -->
            <div style="font-size: 14px; font-weight: 700; color: var(--color-text-main); margin-bottom: 8px;">
                Rekomendasi Bimbingan Karier & Rumpun Studi:
            </div>
            <div style="padding: 16px; background: #f8fafc; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-size: 13px; line-height: 1.7; color: var(--color-text-main); white-space: pre-line;">
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
            Lanjutkan ke Rute Masa Depan
        </a>
    </div>
</div>
@endsection
