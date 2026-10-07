@extends('layouts.siswa')

@section('title', 'Eksplorasi Peluang & Beasiswa')

@section('content')
<div style="margin-bottom: 24px;">
    <h2 style="font-size: 20px; font-weight: 800; color: var(--color-text-main);">Pusat Peluang, Beasiswa & Magang</h2>
    <p style="font-size: 13px; color: var(--color-text-muted);">
        Temukan beasiswa perguruan tinggi mitra, program magang vokasi, dan sertifikasi keahlian yang relevan dengan masa depan Anda.
    </p>
</div>

<!-- Tab Filter Tipe Peluang -->
<div class="tab-nav">
    <a href="{{ route('siswa.peluang.index') }}" class="tab-btn {{ !request('type') ? 'active' : '' }}">Semua Peluang</a>
    <a href="{{ route('siswa.peluang.index', ['type' => 'beasiswa']) }}" class="tab-btn {{ request('type') === 'beasiswa' ? 'active' : '' }}">Beasiswa Kuliah</a>
    <a href="{{ route('siswa.peluang.index', ['type' => 'magang']) }}" class="tab-btn {{ request('type') === 'magang' ? 'active' : '' }}">Magang Industri</a>
    <a href="{{ route('siswa.peluang.index', ['type' => 'lowongan_kerja']) }}" class="tab-btn {{ request('type') === 'lowongan_kerja' ? 'active' : '' }}">Lowongan Kerja</a>
    <a href="{{ route('siswa.peluang.index', ['type' => 'sertifikasi']) }}" class="tab-btn {{ request('type') === 'sertifikasi' ? 'active' : '' }}">Pelatihan & Sertifikasi</a>
</div>

<div class="grid-3">
    @forelse($opportunities as $op)
        @php
            $isRegistered = array_key_exists($op->id, $myRegistrations);
            $regStatus = $isRegistered ? $myRegistrations[$op->id] : null;

            // Logika Rekomendasi Pintar (Section 20 Blueprint)
            // Cek apakah judul / sasaran cocok dengan jurusan atau rencana siswa
            $major = $student->studentClass ? strtolower($student->studentClass->major) : '';
            $goal = $student->futurePlan ? $student->futurePlan->primary_goal : '';
            $isRecommended = false;

            if ($goal === 'kuliah' && $op->type === 'beasiswa') {
                $isRecommended = true;
            } elseif ($goal === 'bekerja' && in_array($op->type, ['magang', 'lowongan_kerja'])) {
                $isRecommended = true;
            } elseif ($goal === 'kuliah_kerja' && in_array($op->type, ['beasiswa', 'magang', 'lowongan_kerja'])) {
                $isRecommended = true;
            }
        @endphp
        <div class="card" style="display: flex; flex-direction: column; justify-content: space-between; position: relative;">
            <div>
                <div class="card-header" style="background: #ffffff; border-bottom: 1px solid var(--color-border);">
                    <span class="badge badge-primary">{{ ucfirst(str_replace('_', ' ', $op->type)) }}</span>
                    @if($isRecommended)
                        <span class="badge badge-success">Cocok dengan Rute Anda</span>
                    @endif
                </div>
                <div class="card-body">
                    <h3 style="font-size: 15px; font-weight: 700; color: var(--color-text-main); margin-bottom: 6px; line-height: 1.4;">
                        {{ $op->title }}
                    </h3>
                    <div style="font-size: 12px; color: var(--color-text-muted); margin-bottom: 12px;">
                        Mitra: <strong>{{ $op->partner ? $op->partner->name : 'Sekolah' }}</strong>
                    </div>

                    <p style="font-size: 13px; color: var(--color-text-muted); line-height: 1.5; margin-bottom: 14px;">
                        {{ \Illuminate\Support\Str::limit($op->description, 110) }}
                    </p>

                    <div style="font-size: 12px; color: var(--color-text-subtle); display: flex; flex-direction: column; gap: 4px;">
                        <div>Sasaran: <strong>{{ $op->target_audience ?: 'Semua Siswa' }}</strong></div>
                        <div>Batas Akhir: <strong>{{ $op->deadline ? $op->deadline->translatedFormat('d M Y') : 'Terbuka' }}</strong></div>
                    </div>
                </div>
            </div>

            <div class="card-footer" style="display: flex; justify-content: space-between; align-items: center;">
                @if($isRegistered)
                    <span class="badge badge-success">Terdaftar ({{ ucfirst($regStatus) }})</span>
                @else
                    <span style="font-size: 12px; color: var(--color-text-subtle);">
                        Kuota: {{ $op->quota ? $op->quota . ' Siswa' : 'Fleksibel' }}
                    </span>
                @endif
                <a href="{{ route('siswa.peluang.show', $op->id) }}" class="btn btn-secondary btn-sm">
                    Lihat & Daftar
                </a>
            </div>
        </div>
    @empty
        <div style="grid-column: 1 / -1;">
            <div class="empty-state">
                <p class="empty-state-title">Belum Ada Peluang Tersedia</p>
                <p class="empty-state-desc">Peluang beasiswa dan magang untuk kategori ini belum dipublikasikan oleh Guru BK.</p>
            </div>
        </div>
    @endforelse
</div>

<div style="margin-top: 24px;">
    {{ $opportunities->links() }}
</div>
@endsection
