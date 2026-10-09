@extends(auth()->user()->role === 'guru_bk' ? 'layouts.app' : 'layouts.siswa')

@section('title', 'Pusat Pemberitahuan')
@section('header_title', 'Pusat Pemberitahuan Sistem')

@section('content')
<div style="max-width: 860px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 14px;">
        <div>
            <h2 style="font-size: 20px; font-weight: 800; color: var(--color-navy); letter-spacing: -0.01em;">Pusat Pemberitahuan</h2>
            <p style="font-size: 13px; color: var(--color-text-muted); margin-top: 2px;">
                Semua notifikasi pengajuan jadwal, tindak lanjut, dan kabar kegiatan sekolah.
            </p>
        </div>
        <form action="{{ route('notifikasi.read_all') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-secondary btn-sm" title="Tandai Semua Sudah Dibaca">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span>Tandai Semua Telah Dibaca</span>
            </button>
        </form>
    </div>

    <div class="card">
        <div class="card-body" style="padding: 0;">
            @if($notifications->count() > 0)
                <div>
                    @foreach($notifications as $n)
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 18px 22px; border-bottom: 1px solid var(--color-border); background-color: {{ $n->is_read ? 'var(--color-surface)' : 'var(--color-primary-light)' }}; transition: background-color var(--transition-fast);">
                            <div style="flex: 1;">
                                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                    <strong style="font-size: 14px; color: var(--color-navy);">{{ $n->title }}</strong>
                                    @if(!$n->is_read)
                                        <span class="badge badge-primary" style="padding: 2px 7px;">Baru</span>
                                    @endif
                                </div>
                                <p style="font-size: 13px; color: var(--color-text-muted); margin-top: 4px; line-height: 1.5;">
                                    {{ $n->message }}
                                </p>
                                <small style="color: var(--color-text-subtle); display: inline-block; margin-top: 6px;">
                                    {{ $n->created_at->diffForHumans() }} &bull; {{ $n->created_at->translatedFormat('d M Y, H:i') }} WIB
                                </small>
                            </div>
                            <div style="margin-left: 16px; flex-shrink: 0;">
                                @if(!$n->is_read)
                                    <a href="{{ route('notifikasi.read', $n->id) }}" class="btn btn-primary btn-sm">
                                        Buka & Baca
                                    </a>
                                @elseif($n->url)
                                    <a href="{{ $n->url }}" class="btn btn-secondary btn-sm">
                                        Kunjungi
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div style="padding: 16px 22px;">
                    {{ $notifications->links() }}
                </div>
            @else
                <div class="empty-state">
                    <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--color-surface-hover); color: var(--color-text-subtle); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                    </div>
                    <p class="empty-state-title">Tidak Ada Pemberitahuan</p>
                    <p class="empty-state-desc">Pemberitahuan aktivitas bimbingan, jadwal konseling, dan agenda sekolah akan muncul di sini.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
