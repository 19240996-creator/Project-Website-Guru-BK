@extends('layouts.app')

@section('title', 'Asesmen Siswa - Guru BK')
@section('header_title', 'Asesmen Minat, Bakat & Kepribadian')

@section('content')
<div style="margin-bottom: 24px;">
    <h2 style="font-size: 20px; font-weight: 800; color: var(--color-text-main);">Asesmen Bimbingan & Pemetaan Diri</h2>
    <p style="font-size: 13px; color: var(--color-text-muted);">
        Instrumen asesmen untuk mengenali gaya belajar, tipologi minat karier, dan potensi masa depan siswa.
    </p>
</div>

<div class="grid-2" style="margin-bottom: 28px;">
    @foreach($assessments as $asm)
        <div class="card">
            <div class="card-header card-header-navy">
                <div>
                    <h3 class="card-title">{{ $asm->title }}</h3>
                    <small style="color: #bfdbfe;">Kategori: {{ $asm->category }}</small>
                </div>
                <span class="badge badge-translucent">Aktif</span>
            </div>
            <div class="card-body">
                <p style="font-size: 13px; color: var(--color-text-muted); line-height: 1.6; margin-bottom: 16px;">
                    {{ $asm->description }}
                </p>

                <div style="display: flex; gap: 20px; font-size: 13px; color: var(--color-text-main); margin-bottom: 20px;">
                    <div>
                        <span style="color: var(--color-text-subtle);">Total Soal:</span>
                        <strong>{{ $asm->questions_count }} Butir</strong>
                    </div>
                    <div>
                        <span style="color: var(--color-text-subtle);">Siswa Mengerjakan:</span>
                        <strong>{{ $asm->student_results_count }} Siswa</strong>
                    </div>
                </div>

                <a href="{{ route('guru.asesmen.show', $asm->id) }}" class="btn btn-secondary" style="width: 100%;">
                    Buka Butir Pertanyaan & Hasil Siswa
                </a>
            </div>
        </div>
    @endforeach
</div>

<div class="card">
    <div class="card-header card-header-navy">
        <h3 class="card-title">Hasil Pengerjaan Asesmen Siswa Terbaru</h3>
    </div>
    <div class="card-body" style="padding: 0;">
        @if($recentResults->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead class="table-thead-navy">
                        <tr>
                            <th>Siswa</th>
                            <th>Kelas</th>
                            <th>Asesmen</th>
                            <th>Kategori Hasil</th>
                            <th>Status Publikasi</th>
                            <th>Tanggal Pengerjaan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentResults as $r)
                            <tr>
                                <td><strong>{{ $r->student ? $r->student->name : '-' }}</strong></td>
                                <td>{{ $r->student && $r->student->studentClass ? $r->student->studentClass->name : '-' }}</td>
                                <td>{{ $r->assessment ? $r->assessment->title : '-' }}</td>
                                <td>
                                    <span class="badge badge-primary">{{ $r->result_category }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $r->is_published ? 'success' : 'secondary' }}">
                                        {{ $r->is_published ? 'Terbuka Untuk Siswa' : 'Draf BK' }}
                                    </span>
                                </td>
                                <td>{{ $r->created_at->translatedFormat('d M Y, H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <p class="empty-state-title">Belum Ada Siswa yang Menyelesaikan Asesmen</p>
                <p class="empty-state-desc">Data pengerjaan akan tampil di sini saat siswa mengisi kuesioner.</p>
            </div>
        @endif
    </div>
</div>
@endsection
