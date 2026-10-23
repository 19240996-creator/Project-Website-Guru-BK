@extends('layouts.pemantau')

@section('title', 'Ekspor Data Siswa - Ruang BK Digital')

@section('content')
<!-- Page Header Bar -->
<div class="page-header-bar">
    <div>
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
            <span class="badge badge-primary" style="font-size: 11px; padding: 3px 8px; font-weight: 700;">
                FITUR EKSPOR RESMI
            </span>
            <span style="font-size: 12px; color: var(--color-text-muted);">
                Pengolahan Data Siswa Tingkat X, XI, dan XII
            </span>
        </div>
        <h1 class="page-header-title">
            Ekspor Data Siswa Ruang BK Digital
        </h1>
    </div>
    <div class="page-header-date">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
        <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
    </div>
</div>

<!-- Grid Layout: Filter Form on Left, Live Preview Card on Right -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 24px; margin-bottom: 28px;">
    <!-- Card Form Filter Ekspor -->
    <div class="card">
        <div class="card-header" style="padding: 18px 22px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 32px; height: 32px; border-radius: 8px; background: var(--color-primary-light); color: var(--color-primary); display: flex; align-items: center; justify-content: center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                </div>
                <div>
                    <h3 style="font-size: 15px; font-weight: 700; color: var(--color-navy); margin: 0;">Parameter Filter Ekspor</h3>
                    <div style="font-size: 12px; color: var(--color-text-muted);">Pilih cakupan data siswa yang ingin diekspor</div>
                </div>
            </div>
        </div>

        <form id="exportForm" action="{{ route($rolePrefix . '.ekspor.download') }}" method="GET" style="margin: 0;">
            <div class="card-body" style="padding: 22px; display: flex; flex-direction: column; gap: 18px;">
                <!-- 1. Pilihan Tingkat Kelas -->
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: var(--color-navy); margin-bottom: 6px;">
                        Tingkat Kelas Siswa <span style="color: var(--color-danger);">*</span>
                    </label>
                    <select name="grade" id="filterGrade" class="form-control" style="height: 42px; font-size: 13.5px;" onchange="updatePreviewCount()">
                        <option value="semua">Semua Tingkat Kelas (X, XI, XII)</option>
                        <option value="X">Kelas X Saja</option>
                        <option value="XI">Kelas XI Saja</option>
                        <option value="XII">Kelas XII Saja</option>
                    </select>
                    <div style="font-size: 11.5px; color: var(--color-text-muted); margin-top: 4px;">
                        Pilih tingkat kelas yang diinginkan atau seluruh tingkat sekaligus.
                    </div>
                </div>

                <!-- 2. Pilihan Jurusan -->
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: var(--color-navy); margin-bottom: 6px;">
                        Konsentrasi Keahlian / Jurusan
                    </label>
                    <select name="major" id="filterMajor" class="form-control" style="height: 42px; font-size: 13.5px;" onchange="updatePreviewCount()">
                        <option value="semua">Semua Jurusan</option>
                        @foreach($majors as $m)
                            <option value="{{ $m }}">{{ $m }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 3. Pilihan Rombel -->
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: var(--color-navy); margin-bottom: 6px;">
                        Rombongan Belajar (Rombel)
                    </label>
                    <select name="student_class_id" id="filterRombel" class="form-control" style="height: 42px; font-size: 13.5px;" onchange="updatePreviewCount()">
                        <option value="semua">Semua Rombel</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" data-grade="{{ $c->grade }}" data-major="{{ $c->major }}">
                                {{ $c->name }} (Kelas {{ $c->grade }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 4. Pilihan Format File -->
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: var(--color-navy); margin-bottom: 8px;">
                        Pilih Format Dokumen Ekspor <span style="color: var(--color-danger);">*</span>
                    </label>
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;">
                        <label style="display: block; cursor: pointer;">
                            <input type="radio" name="format" value="xlsx" checked style="display: none;" onchange="selectFormatCard(this)" />
                            <div class="format-card selected" id="cardXlsx" style="padding: 12px; border: 2px solid var(--color-primary); background: var(--color-primary-light); border-radius: var(--radius-sm); text-align: center; transition: all 0.2s;">
                                <div style="font-weight: 700; color: var(--color-primary); font-size: 13.5px;">Excel (.xlsx)</div>
                                <div style="font-size: 11px; color: var(--color-text-muted); margin-top: 2px;">Tabel Data Siap Olah</div>
                            </div>
                        </label>

                        <label style="display: block; cursor: pointer;">
                            <input type="radio" name="format" value="csv" style="display: none;" onchange="selectFormatCard(this)" />
                            <div class="format-card" id="cardCsv" style="padding: 12px; border: 1px solid var(--color-border); background: var(--color-surface); border-radius: var(--radius-sm); text-align: center; transition: all 0.2s;">
                                <div style="font-weight: 700; color: var(--color-navy); font-size: 13.5px;">CSV (.csv)</div>
                                <div style="font-size: 11px; color: var(--color-text-muted); margin-top: 2px;">Format Universal</div>
                            </div>
                        </label>

                        <label style="display: block; cursor: pointer;">
                            <input type="radio" name="format" value="print" style="display: none;" onchange="selectFormatCard(this)" />
                            <div class="format-card" id="cardPrint" style="padding: 12px; border: 1px solid var(--color-border); background: var(--color-surface); border-radius: var(--radius-sm); text-align: center; transition: all 0.2s;">
                                <div style="font-weight: 700; color: var(--color-navy); font-size: 13.5px;">Cetak / PDF</div>
                                <div style="font-size: 11px; color: var(--color-text-muted); margin-top: 2px;">Laporan Siap Cetak</div>
                            </div>
                        </label>
                    </div>
                </div>

                <div style="margin-top: 8px;">
                    <button type="submit" id="submitBtn" class="btn btn-primary" style="width: 100%; height: 46px; font-size: 14.5px; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 8px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        <span>Ekspor Data Sekarang</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Live Preview Box & Compliance Information -->
    <div style="display: flex; flex-direction: column; gap: 20px;">
        <!-- Live Count Box -->
        <div class="card" style="background: linear-gradient(135deg, var(--color-navy) 0%, var(--color-navy-light) 100%); color: #FFFFFF;">
            <div class="card-body" style="padding: 24px;">
                <div style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-gold); margin-bottom: 8px;">
                    Pratinjau Hasil Ekspor
                </div>
                <div style="display: flex; align-items: baseline; gap: 10px; margin-bottom: 6px;">
                    <div id="previewCountDisplay" style="font-size: 44px; font-weight: 800; line-height: 1; color: #FFFFFF;">
                        {{ number_format($initialCount) }}
                    </div>
                    <span style="font-size: 16px; font-weight: 600; color: rgba(255, 255, 255, 0.8);">Siswa</span>
                </div>
                <div id="previewLabelDisplay" style="font-size: 13.5px; color: rgba(255, 255, 255, 0.75);">
                    Semua Tingkat Kelas (Data Aktual Database)
                </div>

                <div id="loadingIndicator" style="display: none; align-items: center; gap: 8px; margin-top: 10px; font-size: 12px; color: var(--color-gold);">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="spin"><line x1="12" y1="2" x2="12" y2="6"></line><line x1="12" y1="18" x2="12" y2="22"></line><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line><line x1="2" y1="12" x2="6" y2="12"></line><line x1="18" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line></svg>
                    <span>Menghitung jumlah data siswa...</span>
                </div>
            </div>
        </div>

        <!-- Ketentuan Isi & Keamanan Data (Sesuai PRD Bagian 6) -->
        <div class="card">
            <div class="card-header" style="padding: 16px 20px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--color-success);"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    <h4 style="font-size: 14px; font-weight: 700; color: var(--color-navy); margin: 0;">Ketentuan Isi & Proteksi Data</h4>
                </div>
            </div>
            <div class="card-body" style="padding: 16px 20px;">
                <ul style="margin: 0; padding-left: 18px; font-size: 13px; color: var(--color-text-main); line-height: 1.6; display: flex; flex-direction: column; gap: 8px;">
                    <li><strong>Kolom Aktual yang Disertakan:</strong> Nomor Urut, NIS, NISN, Nama Lengkap Siswa, Jenis Kelamin, Tingkat Kelas, Jurusan, Rombel, Nomor HP, dan Status Siswa.</li>
                    <li><strong>Perlindungan Privasi:</strong> Tidak menyertakan catatan konseling, riwayat permasalahan pribadi, atau data sensitif siswa.</li>
                    <li><strong>Pemisahan Jejak Aktivitas:</strong> Setiap pengunduhan file dicatat pada audit log akun Anda sendiri dan tidak dapat diakses oleh role lain.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .spin {
        animation: spin 1s linear infinite;
    }
</style>
<script>
    function selectFormatCard(radio) {
        var cards = document.querySelectorAll('.format-card');
        cards.forEach(function (c) {
            c.style.border = '1px solid var(--color-border)';
            c.style.background = 'var(--color-surface)';
            var title = c.querySelector('div:first-child');
            if (title) title.style.color = 'var(--color-navy)';
        });

        var parent = radio.closest('label');
        if (parent) {
            var activeCard = parent.querySelector('.format-card');
            if (activeCard) {
                activeCard.style.border = '2px solid var(--color-primary)';
                activeCard.style.background = 'var(--color-primary-light)';
                var activeTitle = activeCard.querySelector('div:first-child');
                if (activeTitle) activeTitle.style.color = 'var(--color-primary)';
            }
        }
    }

    var previewTimeout = null;

    function updatePreviewCount() {
        var grade = document.getElementById('filterGrade').value;
        var major = document.getElementById('filterMajor').value;
        var rombel = document.getElementById('filterRombel').value;
        var loading = document.getElementById('loadingIndicator');
        var countEl = document.getElementById('previewCountDisplay');
        var labelEl = document.getElementById('previewLabelDisplay');
        var submitBtn = document.getElementById('submitBtn');

        loading.style.display = 'flex';

        if (previewTimeout) clearTimeout(previewTimeout);

        previewTimeout = setTimeout(function () {
            var url = "{{ route($rolePrefix . '.ekspor.count') }}?grade=" + encodeURIComponent(grade) + "&major=" + encodeURIComponent(major) + "&student_class_id=" + encodeURIComponent(rombel);

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                loading.style.display = 'none';
                countEl.textContent = data.count.toLocaleString('id-ID');
                labelEl.textContent = data.label + ' (' + (major !== 'semua' ? major : 'Semua Jurusan') + ')';

                if (data.count === 0) {
                    submitBtn.classList.remove('btn-primary');
                    submitBtn.classList.add('btn-secondary');
                } else {
                    submitBtn.classList.remove('btn-secondary');
                    submitBtn.classList.add('btn-primary');
                }
            })
            .catch(function () {
                loading.style.display = 'none';
            });
        }, 150);
    }
</script>
@endsection
