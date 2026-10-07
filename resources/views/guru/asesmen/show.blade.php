@extends('layouts.app')

@section('title', 'Detail Asesmen - ' . $assessment->title)
@section('header_title', 'Detail Asesmen & Butir Soal')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
    <div>
        <h2 style="font-size: 20px; font-weight: 800; color: var(--color-text-main);">{{ $assessment->title }}</h2>
        <p style="font-size: 13px; color: var(--color-text-muted);">
            Kategori: <strong>{{ $assessment->category }}</strong> | Status: <span class="badge badge-success">Aktif</span>
        </p>
    </div>
    <a href="{{ route('guru.asesmen.index') }}" class="btn btn-secondary">
        Kembali ke Daftar Asesmen
    </a>
</div>

<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h3 class="card-title">Instruksi & Butir Pertanyaan ({{ $assessment->questions->count() }} Soal)</h3>
    </div>
    <div class="card-body">
        <p style="font-size: 13px; color: var(--color-text-muted); margin-bottom: 16px; padding: 12px; background: #f8fafc; border-radius: var(--radius-sm); border: 1px solid var(--color-border);">
            <strong>Petunjuk:</strong> {{ $assessment->instructions }}
        </p>

        <div style="display: flex; flex-direction: column; gap: 16px;">
            @foreach($assessment->questions as $idx => $q)
                <div style="padding: 14px 16px; border: 1px solid var(--color-border); border-radius: var(--radius-sm); background: #ffffff;">
                    <div style="font-weight: 700; font-size: 13px; color: var(--color-text-main); margin-bottom: 8px;">
                        {{ $idx + 1 }}. {{ $q->question_text }}
                    </div>
                    <ul style="padding-left: 20px; font-size: 13px; color: var(--color-text-muted);">
                        @foreach($q->options as $opt)
                            <li style="margin-bottom: 4px;">
                                {{ $opt->option_text }}
                                <span style="font-size: 11px; color: var(--color-text-subtle);">
                                    [Dimensi: {{ $opt->dimension_code }} | Skor: {{ $opt->score_value }}]
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Hasil Pengerjaan Siswa ({{ $assessment->studentResults->count() }})</h3>
    </div>
    <div class="card-body" style="padding: 0;">
        @if($assessment->studentResults->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Kategori Hasil</th>
                            <th>Ringkasan Interpretasi</th>
                            <th>Status Publikasi</th>
                            <th style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assessment->studentResults as $res)
                            <tr>
                                <td><strong>{{ $res->student ? $res->student->name : '-' }}</strong></td>
                                <td>{{ $res->student && $res->student->studentClass ? $res->student->studentClass->name : '-' }}</td>
                                <td><span class="badge badge-primary">{{ $res->result_category }}</span></td>
                                <td style="max-width: 300px; font-size: 12px; color: var(--color-text-muted);">{{ $res->summary }}</td>
                                <td>
                                    <span class="badge badge-{{ $res->is_published ? 'success' : 'secondary' }}">
                                        {{ $res->is_published ? 'Dipublikasikan' : 'Draf Internal' }}
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <form action="{{ route('guru.asesmen.result.update', $res->id) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        <input type="hidden" name="is_published" value="{{ $res->is_published ? 0 : 1 }}">
                                        <button type="submit" class="btn btn-secondary btn-sm">
                                            {{ $res->is_published ? 'Tutup Akses Siswa' : 'Buka Untuk Siswa' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <p class="empty-state-title">Belum Ada Siswa yang Mengisi</p>
                <p class="empty-state-desc">Belum ada siswa yang mengirimkan jawaban untuk asesmen ini.</p>
            </div>
        @endif
    </div>
</div>
@endsection
