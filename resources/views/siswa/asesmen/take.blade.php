@extends('layouts.siswa')

@section('title', 'Mengerjakan Asesmen - ' . $assessment->title)

@section('content')
<div style="max-width: 760px; margin: 0 auto;">
    <div style="margin-bottom: 24px;">
        <h2 style="font-size: 20px; font-weight: 800; color: var(--color-text-main);">{{ $assessment->title }}</h2>
        <p style="font-size: 13px; color: var(--color-text-muted); margin-top: 4px;">
            {{ $assessment->instructions ?: 'Jawablah setiap pertanyaan di bawah ini sesuai dengan kepribadian dan preferensi Anda sehari-hari.' }}
        </p>
    </div>

    @if($existingResult)
        <div class="alert alert-info" style="margin-bottom: 20px;">
            Anda sebelumnya telah mengerjakan asesmen ini pada {{ $existingResult->created_at->translatedFormat('d F Y') }}. Anda dapat mengisi ulang kuesioner ini untuk memperbarui pemetaan diri Anda.
        </div>
    @endif

    <form action="{{ route('siswa.asesmen.submit', $assessment->id) }}" method="POST">
        @csrf

        @foreach($assessment->questions as $idx => $q)
            <div class="card" style="margin-bottom: 20px;">
                <div class="card-header" style="background: #f8fafc;">
                    <div style="font-weight: 700; font-size: 14px; color: var(--color-text-main);">
                        Pertanyaan {{ $idx + 1 }} dari {{ $assessment->questions->count() }}
                    </div>
                </div>
                <div class="card-body">
                    <p style="font-size: 14px; font-weight: 600; color: var(--color-text-main); margin-bottom: 16px; line-height: 1.5;">
                        {{ $q->question_text }}
                    </p>

                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        @foreach($q->options as $opt)
                            <label style="display: flex; align-items: flex-start; gap: 10px; padding: 12px 14px; border: 1px solid var(--color-border); border-radius: var(--radius-sm); cursor: pointer; transition: background 0.15s ease;">
                                <input type="radio" name="answers[{{ $q->id }}]" value="{{ $opt->id }}" style="margin-top: 3px;" required>
                                <span style="font-size: 13px; color: var(--color-text-main); line-height: 1.4;">
                                    {{ $opt->option_text }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach

        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 24px;">
            <a href="{{ route('siswa.asesmen.index') }}" class="btn btn-secondary">Batal & Kembali</a>
            <button type="submit" class="btn btn-primary" onclick="return confirm('Kirimkan seluruh jawaban asesmen ini?')">
                Selesaikan & Analisis Hasil Asesmen
            </button>
        </div>
    </form>
</div>
@endsection
