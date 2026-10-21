@extends('layouts.siswa')

@section('title', 'Kelas BK Online - Bimbingan & Konseling')
@section('header_title', 'Kelas BK Online')

@section('content')
<div style="margin-bottom: 24px;">
    <div style="margin-bottom: 20px;">
        <h2 style="font-size: 20px; font-weight: 800; color: var(--color-navy); margin: 0 0 4px 0;">
            Kelas Bimbingan &amp; Konseling Online
        </h2>
        <p style="font-size: 13px; color: var(--color-text-muted); margin: 0;">
            Ikuti pembelajaran bimbingan klasikal, pelajari materi &amp; video edukasi, lakukan absensi, dan kerjakan tugas yang diberikan oleh Guru BK pengampu.
        </p>
    </div>

    <!-- Tab Filter Status -->
    <div style="display: flex; gap: 8px; border-bottom: 2px solid var(--color-border); margin-bottom: 20px; overflow-x: auto;">
        <a href="{{ route('siswa.kelas.index', ['tab' => 'semua']) }}" 
           style="padding: 9px 16px; font-size: 13px; font-weight: 700; text-decoration: none; border-bottom: 3px solid {{ $tab === 'semua' ? 'var(--color-primary)' : 'transparent' }}; color: {{ $tab === 'semua' ? 'var(--color-navy)' : 'var(--color-text-muted)' }}; margin-bottom: -2px; white-space: nowrap;">
            Semua Kelas
        </a>
        <a href="{{ route('siswa.kelas.index', ['tab' => 'berlangsung']) }}" 
           style="padding: 9px 16px; font-size: 13px; font-weight: 700; text-decoration: none; border-bottom: 3px solid {{ $tab === 'berlangsung' ? 'var(--color-primary)' : 'transparent' }}; color: {{ $tab === 'berlangsung' ? 'var(--color-navy)' : 'var(--color-text-muted)' }}; margin-bottom: -2px; white-space: nowrap;">
            Sedang Berlangsung
        </a>
        <a href="{{ route('siswa.kelas.index', ['tab' => 'terjadwal']) }}" 
           style="padding: 9px 16px; font-size: 13px; font-weight: 700; text-decoration: none; border-bottom: 3px solid {{ $tab === 'terjadwal' ? 'var(--color-primary)' : 'transparent' }}; color: {{ $tab === 'terjadwal' ? 'var(--color-navy)' : 'var(--color-text-muted)' }}; margin-bottom: -2px; white-space: nowrap;">
            Terjadwal
        </a>
        <a href="{{ route('siswa.kelas.index', ['tab' => 'selesai']) }}" 
           style="padding: 9px 16px; font-size: 13px; font-weight: 700; text-decoration: none; border-bottom: 3px solid {{ $tab === 'selesai' ? 'var(--color-primary)' : 'transparent' }}; color: {{ $tab === 'selesai' ? 'var(--color-navy)' : 'var(--color-text-muted)' }}; margin-bottom: -2px; white-space: nowrap;">
            Riwayat Selesai
        </a>
    </div>

    <!-- List Kelas BK -->
    @if($classes->isEmpty())
    <div class="card" style="padding: 48px 20px; text-align: center; border: 1px dashed var(--color-border); border-radius: 12px; background: #fff;">
        <div style="width: 56px; height: 56px; border-radius: 50%; background: #eff6ff; display: inline-flex; align-items: center; justify-content: center; color: var(--color-primary); margin-bottom: 14px;">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
        </div>
        <h3 style="font-size: 16px; font-weight: 700; color: var(--color-navy); margin: 0 0 6px 0;">Tidak Ada Kelas BK</h3>
        <p style="font-size: 13px; color: var(--color-text-muted); max-width: 420px; margin: 0 auto;">
            Saat ini belum ada jadwal kelas pembelajaran BK online yang ditujukan untuk kelas atau rombel Anda.
        </p>
    </div>
    @else
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 18px;">
        @foreach($classes as $item)
        @php
            $compStatus = $item->computed_status;
            $att = $attendances->get($item->id);
            $attStatus = $item->attendance_status;
        @endphp
        <div class="card" style="border: 1px solid var(--color-border); border-radius: 12px; background: #fff; overflow: hidden; display: flex; flex-direction: column; justify-content: space-between; box-shadow: var(--shadow-sm); transition: transform 0.15s ease, box-shadow 0.15s ease;">
            <!-- Header Card -->
            <div style="padding: 18px 20px; border-bottom: 1px solid #f1f5f9;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; gap: 8px;">
                    <!-- Badge Status Kelas -->
                    @if($compStatus === 'berlangsung')
                        <span style="display: inline-flex; align-items: center; gap: 5px; background: #dcfce7; color: #166534; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 4px; border: 1px solid #86efac;">
                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #16a34a;"></span>
                            Sedang Berlangsung
                        </span>
                    @elseif($compStatus === 'terjadwal')
                        <span style="display: inline-flex; align-items: center; gap: 5px; background: #dbeafe; color: #1e40af; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 4px; border: 1px solid #93c5fd;">
                            Terjadwal
                        </span>
                    @else
                        <span style="background: #f1f5f9; color: #475569; font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 4px; border: 1px solid #cbd5e1;">
                            Selesai
                        </span>
                    @endif

                    <!-- Status Presensi Siswa -->
                    @if($att)
                        <span style="display: inline-flex; align-items: center; gap: 4px; background: #dcfce7; color: #15803d; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 4px;">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            {{ $att->status_label }}
                        </span>
                    @elseif($attStatus === 'dibuka')
                        <span style="background: #e0f2fe; color: #0369a1; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 4px; border: 1px solid #bae6fd;">
                            Absensi Dibuka
                        </span>
                    @elseif($attStatus === 'ditutup')
                        <span style="background: #fee2e2; color: #991b1b; font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 4px;">
                            Absensi Ditutup
                        </span>
                    @else
                        <span style="background: #f8fafc; color: #64748b; font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 4px;">
                            Belum Dibuka
                        </span>
                    @endif
                </div>

                <h3 style="font-size: 16px; font-weight: 800; color: var(--color-navy); margin: 0 0 8px 0; line-height: 1.35;">
                    <a href="{{ route('siswa.kelas.show', $item->id) }}" style="color: inherit; text-decoration: none;">
                        {{ $item->title }}
                    </a>
                </h3>

                <div style="font-size: 12px; color: var(--color-text-muted); margin-bottom: 12px;">
                    Guru BK: <strong style="color: var(--color-text-main);">{{ $item->counselor->name }}</strong>
                </div>

                @if($item->description)
                <p style="font-size: 12px; color: var(--color-text-muted); margin: 0; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                    {{ $item->description }}
                </p>
                @endif
            </div>

            <!-- Detail Jadwal & Footer Card -->
            <div style="padding: 14px 20px; background: #fafbfc;">
                <div style="display: flex; flex-direction: column; gap: 6px; font-size: 11.5px; color: var(--color-text-muted); margin-bottom: 14px;">
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        <span>{{ \Carbon\Carbon::parse($item->class_date)->translatedFormat('l, d F Y') }}</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        <span>{{ substr($item->start_time, 0, 5) }} - {{ substr($item->end_time, 0, 5) }} WIB</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path></svg>
                        <span>{{ $item->materials->count() }} Materi &bull; {{ $item->tasks->count() }} Tugas</span>
                    </div>
                </div>

                <div>
                    <a href="{{ route('siswa.kelas.show', $item->id) }}" class="btn btn-primary" style="width: 100%; justify-content: center; font-weight: 700; font-size: 13px; padding: 9px; display: inline-flex; align-items: center; gap: 6px;">
                        <span>Buka Ruang Kelas</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
