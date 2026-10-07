@extends(auth()->user()->role === 'guru_bk' ? 'layouts.app' : 'layouts.siswa')

@section('title', 'Pusat Pemberitahuan')
@section('header_title', 'Pusat Pemberitahuan Sistem')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
        <div>
            <h2 style="font-size: 20px; font-weight: 800; color: var(--color-text-main);">Pusat Pemberitahuan</h2>
            <p style="font-size: 13px; color: var(--color-text-muted);">
                Semua notifikasi pengajuan jadwal, tindak lanjut, dan kabar kegiatan sekolah.
            </p>
        </div>
        <form action="{{ route('notifikasi.read_all') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-secondary btn-sm">
                Tandai Semua Telah Dibaca
            </button>
        </form>
    </div>

    <div class="card">
        <div class="card-body" style="padding: 0;">
            @if($notifications->count() > 0)
                <div>
                    @foreach($notifications as $n)
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; border-bottom: 1px solid var(--color-border); background-color: {{ $n->is_read ? '#ffffff' : '#f0fdfa' }};">
                            <div style="flex: 1;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <strong style="font-size: 14px; color: var(--color-text-main);">{{ $n->title }}</strong>
                                    @if(!$n->is_read)
                                        <span class="badge badge-primary">Baru</span>
                                    @endif
                                </div>
                                <p style="font-size: 13px; color: var(--color-text-muted); margin-top: 4px;">
                                    {{ $n->message }}
                                </p>
                                <small style="color: var(--color-text-subtle);">
                                    {{ $n->created_at->diffForHumans() }} ({{ $n->created_at->translatedFormat('d M Y, H:i') }})
                                </small>
                            </div>
                            <div style="margin-left: 16px;">
                                @if(!$n->is_read)
                                    <a href="{{ route('notifikasi.read', $n->id) }}" class="btn btn-secondary btn-sm">
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

                <div style="padding: 16px 20px;">
                    {{ $notifications->links() }}
                </div>
            @else
                <div class="empty-state">
                    <p class="empty-state-title">Tidak Ada Pemberitahuan</p>
                    <p class="empty-state-desc">Kotak notifikasi Anda bersih. Belum ada pemberitahuan baru.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
