@extends('layouts.app')

@section('title', 'Kelas BK Online - Bimbingan & Konseling')
@section('header_title', 'Kelas BK Online')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
    <div>
        <h2 style="font-size: 20px; font-weight: 800; color: var(--color-navy); margin: 0 0 4px 0;">Manajemen Kelas BK Online</h2>
        <p style="font-size: 13px; color: var(--color-text-muted); margin: 0;">
            Kelola ruang pembelajaran digital, materi, penugasan bimbingan, serta pantau kehadiran presensi siswa binaan secara real-time.
        </p>
    </div>
    <div>
        <a href="{{ route('guru.kelas.create') }}" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; padding: 10px 20px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            <span>Buat Kelas BK Baru</span>
        </a>
    </div>
</div>

<!-- Statistik Ringkas -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <div class="card" style="padding: 16px; border: 1px solid var(--color-border); border-radius: 10px; background: #fff; box-shadow: var(--shadow-sm);">
        <div style="font-size: 11.5px; font-weight: 600; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">Total Kelas BK</div>
        <div style="font-size: 24px; font-weight: 800; color: var(--color-navy);">{{ $totalClassesCount }}</div>
        <div style="font-size: 11.5px; color: var(--color-text-muted); margin-top: 4px;">Terdaftar di sistem</div>
    </div>
    <div class="card" style="padding: 16px; border: 1px solid #bbf7d0; border-radius: 10px; background: #f0fdf4; box-shadow: var(--shadow-sm);">
        <div style="font-size: 11.5px; font-weight: 600; color: #166534; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">Sedang Berlangsung</div>
        <div style="font-size: 24px; font-weight: 800; color: #15803d;">{{ $berlangsungCount }}</div>
        <div style="font-size: 11.5px; color: #166534; margin-top: 4px;">Pembelajaran aktif saat ini</div>
    </div>
    <div class="card" style="padding: 16px; border: 1px solid #bfdbfe; border-radius: 10px; background: #eff6ff; box-shadow: var(--shadow-sm);">
        <div style="font-size: 11.5px; font-weight: 600; color: #1e40af; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">Terjadwal</div>
        <div style="font-size: 24px; font-weight: 800; color: #1d4ed8;">{{ $terjadwalCount }}</div>
        <div style="font-size: 11.5px; color: #1e40af; margin-top: 4px;">Menunggu jadwal pelaksanaan</div>
    </div>
    <div class="card" style="padding: 16px; border: 1px solid var(--color-border); border-radius: 10px; background: #f8fafc; box-shadow: var(--shadow-sm);">
        <div style="font-size: 11.5px; font-weight: 600; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">Selesai</div>
        <div style="font-size: 24px; font-weight: 800; color: #475569;">{{ $selesaiCount }}</div>
        <div style="font-size: 11.5px; color: var(--color-text-muted); margin-top: 4px;">Telah melewati jam pembelajaran</div>
    </div>
</div>

<!-- Filter & Pencarian -->
<div class="card" style="margin-bottom: 24px; border: 1px solid var(--color-border); border-radius: 10px;">
    <div class="card-body" style="padding: 16px 20px;">
        <form action="{{ route('guru.kelas.index') }}" method="GET" style="display: grid; grid-template-columns: 1fr auto auto auto; gap: 12px; align-items: flex-end;">
            <div>
                <label class="form-label" style="font-size: 12px; font-weight: 600; margin-bottom: 5px;">Cari Judul / Pembelajaran</label>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Ketik topik atau judul kelas BK..." style="font-size: 13px; min-height: 38px;">
            </div>
            <div style="min-width: 170px;">
                <label class="form-label" style="font-size: 12px; font-weight: 600; margin-bottom: 5px;">Filter Tanggal</label>
                <select name="date_filter" class="form-select" style="font-size: 13px; min-height: 38px;">
                    <option value="">Semua Tanggal</option>
                    <option value="today" {{ request('date_filter') === 'today' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="upcoming" {{ request('date_filter') === 'upcoming' ? 'selected' : '' }}>Mendatang</option>
                    <option value="past" {{ request('date_filter') === 'past' ? 'selected' : '' }}>Riwayat Lalu</option>
                </select>
            </div>
            <div>
                <button type="submit" class="btn btn-secondary" style="min-height: 38px; display: inline-flex; align-items: center; gap: 6px;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <span>Filter</span>
                </button>
            </div>
            @if(request()->hasAny(['q', 'date_filter']))
            <div>
                <a href="{{ route('guru.kelas.index') }}" class="btn btn-secondary" style="min-height: 38px; color: #dc2626; border-color: #fecaca;" title="Reset Filter">
                    <span>Reset</span>
                </a>
            </div>
            @endif
        </form>
    </div>
</div>

<!-- Daftar Kelas BK -->
@if($classes->isEmpty())
<div class="card" style="padding: 48px 24px; text-align: center; border: 1px dashed var(--color-border); border-radius: 12px; background: #fff;">
    <div style="width: 56px; height: 56px; border-radius: 50%; background: #eff6ff; display: inline-flex; align-items: center; justify-content: center; color: var(--color-primary); margin-bottom: 14px;">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
    </div>
    <h3 style="font-size: 16px; font-weight: 700; color: var(--color-navy); margin: 0 0 6px 0;">Belum Ada Kelas BK Online</h3>
    <p style="font-size: 13px; color: var(--color-text-muted); max-width: 460px; margin: 0 auto 18px auto;">
        Anda belum membuat kelas bimbingan online untuk siswa binaan. Klik tombol di bawah untuk menjadwalkan kelas pembelajaran pertama.
    </p>
    <div>
        <a href="{{ route('guru.kelas.create') }}" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; padding: 9px 18px;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            <span>Buat Kelas BK Sekarang</span>
        </a>
    </div>
</div>
@else
<div style="display: flex; flex-direction: column; gap: 16px;">
    @foreach($classes as $item)
    @php
        $compStatus = $item->computed_status;
        $attStatus = $item->attendance_status;
        $targetCount = $item->getTargetStudents()->count();
        $attendedCount = $item->attendances->where('status', 'hadir')->count();
        $percentAttended = $targetCount > 0 ? round(($attendedCount / $targetCount) * 100) : 0;
    @endphp
    <div class="card" style="border: 1px solid var(--color-border); border-radius: 10px; overflow: hidden; background: #fff; box-shadow: var(--shadow-sm); transition: transform 0.15s ease, box-shadow 0.15s ease;">
        <div style="padding: 18px 22px; display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 280px;">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px; flex-wrap: wrap;">
                    <!-- Badge Status Kelas -->
                    @if($compStatus === 'berlangsung')
                        <span style="display: inline-flex; align-items: center; gap: 5px; background: #dcfce7; color: #166534; font-size: 11.5px; font-weight: 700; padding: 3px 9px; border-radius: 4px; border: 1px solid #86efac;">
                            <span style="width: 7px; height: 7px; border-radius: 50%; background: #16a34a; display: inline-block;"></span>
                            Sedang Berlangsung
                        </span>
                    @elseif($compStatus === 'terjadwal')
                        <span style="display: inline-flex; align-items: center; gap: 5px; background: #dbeafe; color: #1e40af; font-size: 11.5px; font-weight: 700; padding: 3px 9px; border-radius: 4px; border: 1px solid #93c5fd;">
                            <span style="width: 7px; height: 7px; border-radius: 50%; background: #2563eb; display: inline-block;"></span>
                            Terjadwal
                        </span>
                    @elseif($compStatus === 'selesai')
                        <span style="display: inline-flex; align-items: center; gap: 5px; background: #f1f5f9; color: #475569; font-size: 11.5px; font-weight: 600; padding: 3px 9px; border-radius: 4px; border: 1px solid #cbd5e1;">
                            Selesai
                        </span>
                    @else
                        <span style="display: inline-flex; align-items: center; gap: 5px; background: #fee2e2; color: #991b1b; font-size: 11.5px; font-weight: 600; padding: 3px 9px; border-radius: 4px;">
                            Dibatalkan
                        </span>
                    @endif

                    <!-- Badge Status Presensi -->
                    @if($attStatus === 'dibuka')
                        <span style="background: #e0f2fe; color: #0369a1; font-size: 11px; font-weight: 600; padding: 3px 8px; border-radius: 4px; border: 1px solid #bae6fd;">
                            Absensi Dibuka
                        </span>
                    @elseif($attStatus === 'belum_dibuka')
                        <span style="background: #f8fafc; color: #64748b; font-size: 11px; font-weight: 600; padding: 3px 8px; border-radius: 4px; border: 1px solid #e2e8f0;">
                            Absensi Belum Dibuka
                        </span>
                    @else
                        <span style="background: #fef2f2; color: #991b1b; font-size: 11px; font-weight: 600; padding: 3px 8px; border-radius: 4px; border: 1px solid #fecaca;">
                            Absensi Ditutup
                        </span>
                    @endif

                    <span style="font-size: 12px; color: var(--color-text-muted);">
                        Sasaran: <strong style="color: var(--color-text-main);">{{ $item->target_classes_text }}</strong>
                    </span>
                </div>

                <h3 style="font-size: 16px; font-weight: 700; color: var(--color-navy); margin: 0 0 6px 0;">
                    <a href="{{ route('guru.kelas.show', $item->id) }}" style="color: inherit; text-decoration: none;">
                        {{ $item->title }}
                    </a>
                </h3>

                @if($item->description)
                <p style="font-size: 12.5px; color: var(--color-text-muted); margin: 0 0 10px 0; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                    {{ $item->description }}
                </p>
                @endif

                <div style="display: flex; gap: 16px; font-size: 12px; color: var(--color-text-muted); flex-wrap: wrap;">
                    <div style="display: inline-flex; align-items: center; gap: 5px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        <span>{{ \Carbon\Carbon::parse($item->class_date)->translatedFormat('l, d F Y') }}</span>
                    </div>
                    <div style="display: inline-flex; align-items: center; gap: 5px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        <span>{{ substr($item->start_time, 0, 5) }} - {{ substr($item->end_time, 0, 5) }} WIB</span>
                    </div>
                    <div style="display: inline-flex; align-items: center; gap: 5px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                        <span>Presensi: {{ substr($item->attendance_start_time, 0, 5) }} - {{ substr($item->attendance_end_time, 0, 5) }} WIB</span>
                    </div>
                </div>
            </div>

            <!-- Ringkasan Statistik Kelas & Tombol Aksi -->
            <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 12px;">
                <div style="display: flex; gap: 12px; align-items: center;">
                    <div style="text-align: right;">
                        <div style="font-size: 11px; color: var(--color-text-muted);">Kehadiran Siswa</div>
                        <div style="font-size: 13px; font-weight: 700; color: var(--color-navy);">
                            {{ $attendedCount }} / {{ $targetCount }} Siswa ({{ $percentAttended }}%)
                        </div>
                    </div>
                    <div style="display: flex; gap: 8px;">
                        <span style="font-size: 11.5px; background: #f1f5f9; padding: 4px 8px; border-radius: 5px; color: var(--color-text-main); font-weight: 600;">
                            {{ $item->materials->count() }} Materi
                        </span>
                        <span style="font-size: 11.5px; background: #f1f5f9; padding: 4px 8px; border-radius: 5px; color: var(--color-text-main); font-weight: 600;">
                            {{ $item->tasks->count() }} Tugas
                        </span>
                    </div>
                </div>

                <div style="display: flex; gap: 8px; align-items: center;">
                    <a href="{{ route('guru.kelas.show', $item->id) }}" class="btn btn-primary" style="padding: 7px 14px; font-size: 12.5px; display: inline-flex; align-items: center; gap: 5px;">
                        <span>Kelola Kelas</span>
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </a>
                    <a href="{{ route('guru.kelas.edit', $item->id) }}" class="btn btn-secondary" style="padding: 7px 11px; font-size: 12.5px;" title="Edit Kelas">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    </a>
                    <form action="{{ route('guru.kelas.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kelas BK ini beserta seluruh materi, tugas, dan rekaman presensi?');" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-secondary" style="padding: 7px 11px; font-size: 12.5px; color: #dc2626; border-color: #fecaca;" title="Hapus Kelas">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <div style="margin-top: 10px;">
        {{ $classes->links() }}
    </div>
</div>
@endif
@endsection
