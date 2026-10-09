@extends('layouts.app')

@section('title', 'Audit Log Aktivitas - Guru BK')
@section('header_title', 'Audit Log & Rekam Jejak Sistem')

@section('content')
<div style="margin-bottom: 24px;">
    <h1 style="font-size: 20px; font-weight: 800; color: var(--color-navy); margin-bottom: 4px;">
        Audit Log Aktivitas & Perubahan Data
    </h1>
    <p style="font-size: 13px; color: var(--color-text-muted); margin: 0;">
        Rekam jejak setiap tindakan pada data konseling, penjadwalan, dan profil siswa untuk menjamin akuntabilitas serta keamanan data.
    </p>
</div>

<!-- Filter Bar (Horizontal) -->
<div class="card" style="margin-bottom: 20px;">
    <div class="card-body" style="padding: 16px 20px;">
        <form action="{{ route('guru.audit.index') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)) 100px; gap: 12px; align-items: flex-end;">
            <div>
                <label class="form-label" style="font-size: 12px; margin-bottom: 6px; display: block;">Cari Deskripsi / IP / Pelaksana</label>
                <input type="text" name="q" class="form-control" style="min-height: 38px; font-size: 13px;" value="{{ request('q') }}" placeholder="Ketik kata kunci...">
            </div>

            <div>
                <label class="form-label" style="font-size: 12px; margin-bottom: 6px; display: block;">Kategori Aksi</label>
                <select name="action" class="form-select" style="min-height: 38px; font-size: 13px;">
                    <option value="">Semua Aksi</option>
                    @foreach($actions as $act)
                        <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>{{ str_replace('_', ' ', $act) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label" style="font-size: 12px; margin-bottom: 6px; display: block;">Entitas Data</label>
                <select name="entity_type" class="form-select" style="min-height: 38px; font-size: 13px;">
                    <option value="">Semua Entitas</option>
                    @foreach($entityTypes as $ent)
                        <option value="{{ $ent }}" {{ request('entity_type') === $ent ? 'selected' : '' }}>{{ $ent }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label" style="font-size: 12px; margin-bottom: 6px; display: block;">Tanggal Aktivitas</label>
                <input type="date" name="date" class="form-control" style="min-height: 38px; font-size: 13px;" value="{{ request('date') }}">
            </div>

            <div>
                <button type="submit" class="btn btn-secondary" style="width: 100%; min-height: 38px;">
                    Filter
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header card-header-navy" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; padding: 16px 20px;">
        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
            <h2 class="card-title" style="margin: 0; font-size: 15px;">Catatan Riwayat Aktivitas Sistem</h2>
            @if(request()->hasAny(['q', 'action', 'entity_type', 'date']))
                <a href="{{ route('guru.audit.index') }}" class="btn btn-secondary btn-sm" style="font-size: 11px; padding: 4px 10px; color: #ffffff; border-color: rgba(255,255,255,0.3); background: rgba(255,255,255,0.1);">
                    Reset Filter
                </a>
            @endif
        </div>
        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            <span class="badge badge-translucent" style="font-size: 12px;">Total: {{ number_format($logs->total()) }} Log</span>
            
            <!-- Bulk Action: Hapus Terpilih -->
            <button type="button" id="bulkDeleteBtn" class="btn-danger-subtle" style="display: none;" onclick="openBulkDeleteModal()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                <span>Hapus Terpilih (<span id="bulkSelectedCount">0</span>)</span>
            </button>

            <!-- Action: Bersihkan Log -->
            <button type="button" class="btn btn-secondary btn-sm" style="background: rgba(255, 255, 255, 0.12); color: #FFFFFF; border-color: rgba(255, 255, 255, 0.25);" onclick="openClearLogsModal()" title="Bersihkan Catatan Log">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                <span>Bersihkan Riwayat</span>
            </button>
        </div>
    </div>
    <div class="card-body" style="padding: 0;">
        @if($logs->count() > 0)
            <div class="table-responsive">
                <table class="table" style="margin-bottom: 0;">
                    <thead class="table-thead-navy">
                        <tr>
                            <th style="width: 44px; text-align: center;">
                                <input type="checkbox" id="selectAllLogs" onchange="toggleSelectAllLogs(this)" title="Pilih Semua Log pada Halaman Ini" style="cursor: pointer; width: 16px; height: 16px;" />
                            </th>
                            <th style="width: 140px; min-width: 130px;">Waktu & Tanggal</th>
                            <th style="width: 170px; min-width: 150px;">Pengguna Pelaksana</th>
                            <th style="width: 150px; min-width: 130px; text-align: center;">Aksi</th>
                            <th style="width: 150px; min-width: 130px;">Entitas Data</th>
                            <th>Deskripsi Perubahan</th>
                            <th style="width: 110px; min-width: 90px;">Alamat IP</th>
                            <th style="width: 80px; text-align: center;">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            @php
                                $actionUpper = strtoupper($log->action);
                                if (str_contains($actionUpper, 'DELETE') || str_contains($actionUpper, 'HAPUS')) {
                                    $actionBadgeClass = 'badge-danger';
                                } elseif (str_contains($actionUpper, 'PENGAJUAN') || str_contains($actionUpper, 'CREATE') || str_contains($actionUpper, 'TAMBAH')) {
                                    $actionBadgeClass = 'badge-success';
                                } elseif (str_contains($actionUpper, 'PERBARUI') || str_contains($actionUpper, 'UPDATE') || str_contains($actionUpper, 'EDIT')) {
                                    $actionBadgeClass = 'badge-primary';
                                } else {
                                    $actionBadgeClass = 'badge-secondary';
                                }
                            @endphp
                            <tr id="log-row-{{ $log->id }}">
                                <td style="text-align: center; vertical-align: middle;">
                                    <input type="checkbox" class="log-checkbox" value="{{ $log->id }}" onchange="updateSelectedLogCount()" style="cursor: pointer; width: 16px; height: 16px;" />
                                </td>
                                <td>
                                    <strong style="color: var(--color-navy);">{{ $log->created_at->translatedFormat('d M Y') }}</strong><br>
                                    <small class="text-muted">{{ $log->created_at->format('H:i:s') }} WIB</small>
                                </td>
                                <td>
                                    <strong style="color: var(--color-navy);">{{ $log->user ? $log->user->name : 'Sistem' }}</strong><br>
                                    <small style="color: var(--color-text-subtle);">{{ $log->user ? ucfirst(str_replace('_', ' ', $log->user->role)) : 'Automatis' }}</small>
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <span class="badge {{ $actionBadgeClass }}" style="display: inline-flex; align-items: center; justify-content: center; font-size: 11px; padding: 3px 8px;">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td>
                                    <code style="font-size: 12px;">{{ $log->entity_type }} #{{ $log->entity_id ?: '-' }}</code>
                                </td>
                                <td style="font-size: 13px; color: var(--color-text-main); line-height: 1.45;">
                                    {{ $log->description }}
                                </td>
                                <td>
                                    <small style="color: var(--color-text-subtle); font-family: monospace;">{{ $log->ip_address ?: '127.0.0.1' }}</small>
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <button type="button" class="btn-icon-danger" onclick="openDeleteLogModal({{ $log->id }}, {{ json_encode($log->description) }}, '{{ $log->created_at->format('d M Y, H:i:s') }} WIB')" title="Hapus Catatan Log Ini" aria-label="Hapus Catatan Log">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="padding: 16px 20px; border-top: 1px solid var(--color-border);">
                {{ $logs->links() }}
            </div>
        @else
            <div class="empty-state">
                <p class="empty-state-title">
                    @if(request()->hasAny(['q', 'action', 'entity_type', 'date']))
                        Tidak Ada Log Aktivitas yang Sesuai Filter
                    @else
                        Belum Ada Aktivitas
                    @endif
                </p>
                <p class="empty-state-desc">
                    @if(request()->hasAny(['q', 'action', 'entity_type', 'date']))
                        Coba sesuaikan kata kunci pencarian atau ubah kriteria filter yang Anda pilih.
                    @else
                        Aktivitas penting sistem akan terekam secara otomatis di sini.
                    @endif
                </p>
                @if(request()->hasAny(['q', 'action', 'entity_type', 'date']))
                    <div style="margin-top: 14px;">
                        <a href="{{ route('guru.audit.index') }}" class="btn btn-secondary btn-sm">Reset Filter</a>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Modal 1: Konfirmasi Hapus Single Log -->
<div id="deleteSingleLogModal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="deleteSingleLogModalTitle" onclick="handleDeleteLogBackdrop(event)">
    <div class="modal-dialog">
        <div class="modal-body">
            <div class="modal-icon-badge danger">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    <line x1="10" y1="11" x2="10" y2="17"></line>
                    <line x1="14" y1="11" x2="14" y2="17"></line>
                </svg>
            </div>
            <h3 id="deleteSingleLogModalTitle" class="modal-title">Hapus Catatan Audit Log</h3>
            <p class="modal-desc">
                Apakah Anda yakin ingin menghapus catatan riwayat aktivitas berikut?
            </p>
            <div style="background-color: var(--color-surface-subtle); border: 1px solid var(--color-border); border-radius: var(--radius-sm); padding: 12px 14px; margin-top: 12px; font-size: 12.5px;">
                <div style="color: var(--color-text-subtle); font-size: 11px; margin-bottom: 4px;" id="deleteLogTimePreview"></div>
                <div style="color: var(--color-navy); font-weight: 600; line-height: 1.45;" id="deleteLogDescPreview"></div>
            </div>
            <div style="background-color: var(--color-danger-bg); border: 1px solid var(--color-danger-border); border-radius: var(--radius-sm); padding: 10px 12px; margin-top: 12px; font-size: 12px; color: var(--color-danger); display: flex; align-items: flex-start; gap: 8px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0; margin-top: 1px;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                <span>Peringatan: Penghapusan log bersifat permanen dari basis data sistem.</span>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeDeleteSingleLogModal()">
                Batal
            </button>
            <form id="deleteSingleLogForm" method="POST" action="" style="display: inline; margin: 0;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    Ya, Hapus Log
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Modal 2: Konfirmasi Hapus Bulk Logs -->
<div id="bulkDeleteModal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="bulkDeleteModalTitle" onclick="handleBulkBackdrop(event)">
    <div class="modal-dialog">
        <div class="modal-body">
            <div class="modal-icon-badge danger">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    <line x1="10" y1="11" x2="10" y2="17"></line>
                    <line x1="14" y1="11" x2="14" y2="17"></line>
                </svg>
            </div>
            <h3 id="bulkDeleteModalTitle" class="modal-title">Hapus Log Terpilih</h3>
            <p class="modal-desc">
                Anda akan menghapus sebanyak <strong id="bulkModalCount" style="color: var(--color-danger);">0</strong> catatan riwayat log yang dicentang.
            </p>
            <div style="background-color: var(--color-danger-bg); border: 1px solid var(--color-danger-border); border-radius: var(--radius-sm); padding: 10px 12px; margin-top: 14px; font-size: 12px; color: var(--color-danger); display: flex; align-items: flex-start; gap: 8px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0; margin-top: 1px;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                <span>Tindakan ini tidak dapat dibatalkan setelah diproses.</span>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeBulkDeleteModal()">
                Batal
            </button>
            <form id="bulkDeleteForm" method="POST" action="{{ route('guru.audit.bulk-destroy') }}" style="display: inline; margin: 0;">
                @csrf
                @method('DELETE')
                <div id="bulkDeleteInputsContainer"></div>
                <button type="submit" class="btn btn-danger">
                    Hapus Log Terpilih
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Modal 3: Bersihkan Riwayat Audit Log -->
<div id="clearLogsModal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="clearLogsModalTitle" onclick="handleClearBackdrop(event)">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('guru.audit.clear') }}" style="margin: 0;">
            @csrf
            <div class="modal-body">
                <div class="modal-icon-badge danger">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                </div>
                <h3 id="clearLogsModalTitle" class="modal-title">Bersihkan Riwayat Audit Log</h3>
                <p class="modal-desc">
                    Pilih rentang data audit log yang ingin dibersihkan dari basis data:
                </p>

                <div style="margin-top: 16px;">
                    <label class="form-label" style="font-size: 12.5px; font-weight: 700; margin-bottom: 6px; display: block;">Rentang Pembersihan</label>
                    <select name="range" class="form-select" style="min-height: 42px; font-size: 13.5px;" required>
                        <option value="older_30">Hanya Log Lebih Lama dari 30 Hari</option>
                        <option value="older_90">Hanya Log Lebih Lama dari 90 Hari</option>
                        <option value="all">Bersihkan Seluruh Riwayat Log (Semua)</option>
                    </select>
                </div>

                <div style="background-color: var(--color-warning-bg); border: 1px solid var(--color-warning-border); border-radius: var(--radius-sm); padding: 10px 12px; margin-top: 14px; font-size: 12px; color: var(--color-warning); display: flex; align-items: flex-start; gap: 8px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0; margin-top: 1px;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <span>Pastikan Anda telah melakukan pengarsipan atau pencatatan yang diperlukan sebelum melanjutkan pembersihan.</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeClearLogsModal()">
                    Batal
                </button>
                <button type="submit" class="btn btn-danger">
                    Ya, Bersihkan Log
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('styles')
<style>
/* Checkbox Custom Touch */
.log-checkbox:focus-visible,
#selectAllLogs:focus-visible {
    outline: 2px solid var(--color-primary);
    outline-offset: 1px;
}
</style>
@endsection

@section('scripts')
<script>
    // 1. Single Log Modal
    function openDeleteLogModal(logId, description, timeText) {
        const form = document.getElementById('deleteSingleLogForm');
        form.action = '{{ url("/guru/audit") }}/' + logId;
        
        document.getElementById('deleteLogTimePreview').textContent = timeText;
        document.getElementById('deleteLogDescPreview').textContent = description;
        
        document.getElementById('deleteSingleLogModal').classList.add('active');
        document.body.classList.add('modal-open');
    }

    function closeDeleteSingleLogModal() {
        document.getElementById('deleteSingleLogModal').classList.remove('active');
        document.body.classList.remove('modal-open');
    }

    function handleDeleteLogBackdrop(event) {
        if (event.target === document.getElementById('deleteSingleLogModal')) {
            closeDeleteSingleLogModal();
        }
    }

    // 2. Select All & Bulk Delete
    function toggleSelectAllLogs(master) {
        const checkboxes = document.querySelectorAll('.log-checkbox');
        checkboxes.forEach(cb => cb.checked = master.checked);
        updateSelectedLogCount();
    }

    function updateSelectedLogCount() {
        const checked = document.querySelectorAll('.log-checkbox:checked');
        const count = checked.length;
        const bulkBtn = document.getElementById('bulkDeleteBtn');
        const countSpan = document.getElementById('bulkSelectedCount');

        countSpan.textContent = count;
        if (count > 0) {
            bulkBtn.style.display = 'inline-flex';
        } else {
            bulkBtn.style.display = 'none';
        }

        const allCheckboxes = document.querySelectorAll('.log-checkbox');
        const selectAll = document.getElementById('selectAllLogs');
        if (selectAll && allCheckboxes.length > 0) {
            selectAll.checked = (checked.length === allCheckboxes.length);
        }
    }

    function openBulkDeleteModal() {
        const checked = document.querySelectorAll('.log-checkbox:checked');
        if (checked.length === 0) return;

        const container = document.getElementById('bulkDeleteInputsContainer');
        container.innerHTML = '';

        checked.forEach(cb => {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'ids[]';
            hidden.value = cb.value;
            container.appendChild(hidden);
        });

        document.getElementById('bulkModalCount').textContent = checked.length;
        document.getElementById('bulkDeleteModal').classList.add('active');
        document.body.classList.add('modal-open');
    }

    function closeBulkDeleteModal() {
        document.getElementById('bulkDeleteModal').classList.remove('active');
        document.body.classList.remove('modal-open');
    }

    function handleBulkBackdrop(event) {
        if (event.target === document.getElementById('bulkDeleteModal')) {
            closeBulkDeleteModal();
        }
    }

    // 3. Clear Logs Modal
    function openClearLogsModal() {
        document.getElementById('clearLogsModal').classList.add('active');
        document.body.classList.add('modal-open');
    }

    function closeClearLogsModal() {
        document.getElementById('clearLogsModal').classList.remove('active');
        document.body.classList.remove('modal-open');
    }

    function handleClearBackdrop(event) {
        if (event.target === document.getElementById('clearLogsModal')) {
            closeClearLogsModal();
        }
    }

    // Keyboard navigation (Escape key to close modals)
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDeleteSingleLogModal();
            closeBulkDeleteModal();
            closeClearLogsModal();
        }
    });
</script>
@endsection
