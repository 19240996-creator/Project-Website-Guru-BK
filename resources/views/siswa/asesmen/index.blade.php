@extends('layouts.siswa')

@section('title', 'Asesmen Diri & Minat Karier')

@section('content')
<div style="margin-bottom: 24px;">
    <h2 style="font-size: 20px; font-weight: 800; color: var(--color-text-main);">Asesmen Pengenalan Potensi Diri</h2>
    <p style="font-size: 13px; color: var(--color-text-muted);">
        Instrumen ilmiah untuk membantu Anda memahami minat karier, gaya belajar, dan kekuatan kepribadian tanpa rasa takut salah.
    </p>
</div>

<div class="grid-2">
    @foreach($assessments as $asm)
        @php
            $result = $asm->studentResults->first();
        @endphp
        <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div class="card-header card-header-navy">
                    <h3 class="card-title">{{ $asm->title }}</h3>
                    <span class="badge badge-translucent">{{ $asm->category }}</span>
                </div>
                <div class="card-body">
                    <p style="font-size: 13px; color: var(--color-text-muted); line-height: 1.6; margin-bottom: 16px;">
                        {{ $asm->description }}
                    </p>

                    @if($result)
                        <div style="padding: 12px; background: #ecfdf5; border: 1px solid var(--color-success-border); border-radius: var(--radius-sm); margin-bottom: 16px;">
                            <div style="font-size: 11px; font-weight: 700; color: var(--color-success); text-transform: uppercase;">Hasil Asesmen Anda:</div>
                            <div style="font-size: 14px; font-weight: 800; color: var(--color-success); margin-top: 2px;">
                                {{ $result->result_category }}
                            </div>
                            <div style="font-size: 12px; color: var(--color-text-muted); margin-top: 4px;">
                                Diselesaikan pada {{ $result->created_at->translatedFormat('d M Y') }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card-footer">
                @if($result)
                    <a href="{{ route('siswa.asesmen.result', $result->id) }}" class="btn btn-secondary" style="width: 100%;">
                        Lihat Ulasan & Rekomendasi
                    </a>
                @else
                    <a href="{{ route('siswa.asesmen.take', $asm->id) }}" class="btn btn-primary" style="width: 100%;">
                        Mulai Kerjakan Asesmen
                    </a>
                @endif
            </div>
        </div>
    @endforeach
</div>
@endsection
