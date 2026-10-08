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
    <div class="card-header card-header-navy" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <h3 class="card-title">Instruksi & Butir Pertanyaan ({{ $assessment->questions->count() }} Soal)</h3>
        <button type="button" class="btn btn-secondary btn-sm" onclick="openCreateQuestionModal()" style="font-weight: 700; background: rgba(255, 255, 255, 0.15); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.35);">
            + Tambah Butir Pertanyaan
        </button>
    </div>
    <div class="card-body">
        <p style="font-size: 13px; color: var(--color-text-muted); margin-bottom: 16px; padding: 12px; background: #f8fafc; border-radius: var(--radius-sm); border: 1px solid var(--color-border); line-height: 1.5;">
            <strong>Petunjuk Asesmen:</strong> {{ $assessment->instructions ?: 'Jawablah setiap pertanyaan di bawah ini sesuai dengan minat dan kepribadian Anda.' }}
            <br>
            <span style="font-size: 12px; color: var(--color-primary); font-weight: 600; display: inline-block; margin-top: 4px;">
                * Sistem asesmen ini menggunakan pembobotan poin (tidak ada jawaban benar atau salah). Setiap opsi jawaban memiliki poin yang dapat disesuaikan oleh Guru BK.
            </span>
        </p>

        <div style="display: flex; flex-direction: column; gap: 16px;">
            @forelse($assessment->questions as $idx => $q)
                <div style="padding: 16px; border: 1px solid var(--color-border); border-radius: var(--radius-sm); background: #ffffff;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; margin-bottom: 12px; flex-wrap: wrap;">
                        <div style="font-weight: 700; font-size: 14px; color: var(--color-text-main); flex: 1; line-height: 1.5;">
                            {{ $idx + 1 }}. {{ $q->question_text }}
                        </div>
                        <div style="display: inline-flex; gap: 6px; align-items: center; flex-shrink: 0;">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="openEditQuestionModal({{ json_encode($q) }})" style="font-size: 12px; padding: 4px 10px;">
                                Ubah
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="openDeleteQuestionModal({{ $q->id }}, '{{ addslashes($q->question_text) }}')" style="font-size: 12px; padding: 4px 10px;">
                                Hapus
                            </button>
                        </div>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        @foreach($q->options as $optIdx => $opt)
                            <div style="display: flex; justify-content: space-between; align-items: center; background: #f8fafc; border: 1px solid var(--color-border); border-radius: var(--radius-sm); padding: 8px 12px; font-size: 13px;">
                                <div style="color: var(--color-text-main); line-height: 1.4; display: flex; align-items: center; gap: 8px;">
                                    <span style="font-weight: 700; color: var(--color-primary); min-width: 20px;">
                                        {{ chr(65 + $optIdx) }}.
                                    </span>
                                    <span>{{ $opt->option_text }}</span>
                                </div>
                                <div style="display: inline-flex; gap: 6px; align-items: center; flex-shrink: 0;">
                                    <span class="badge badge-primary" style="font-size: 11px;">
                                        +{{ $opt->score_value }} Poin
                                    </span>
                                    @if($opt->dimension_code)
                                        <span class="badge badge-secondary" style="font-size: 11px;">
                                            Dimensi: {{ $opt->dimension_code }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="empty-state" style="padding: 24px; text-align: center;">
                    <p class="empty-state-title">Belum Ada Butir Pertanyaan</p>
                    <p class="empty-state-desc">Klik tombol "+ Tambah Butir Pertanyaan" di atas untuk menambahkan butir soal asesmen berpoin.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header card-header-navy">
        <h3 class="card-title">Daftar Hasil Pengerjaan Siswa ({{ $assessment->studentResults->count() }})</h3>
    </div>
    <div class="card-body" style="padding: 0;">
        @if($assessment->studentResults->count() > 0)
            <div class="table-responsive">
                <table class="table" style="min-width: 900px; width: 100%;">
                    <thead class="table-thead-navy">
                        <tr>
                            <th style="width: 20%; text-align: left;">Nama Siswa</th>
                            <th style="width: 14%; text-align: left;">Kelas</th>
                            <th style="width: 16%; text-align: center;">Kategori Hasil</th>
                            <th style="width: 24%; text-align: left;">Ringkasan Interpretasi</th>
                            <th style="width: 13%; text-align: center;">Status Publikasi</th>
                            <th style="width: 13%; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assessment->studentResults as $res)
                            <tr>
                                <td style="text-align: left;"><strong>{{ $res->student ? $res->student->name : '-' }}</strong></td>
                                <td style="text-align: left;">{{ $res->student && $res->student->studentClass ? $res->student->studentClass->name : '-' }}</td>
                                <td style="text-align: center;"><span class="badge badge-primary" style="justify-content: center;">{{ $res->result_category }}</span></td>
                                <td style="text-align: left; max-width: 300px; font-size: 12px; color: var(--color-text-muted);">{{ $res->summary }}</td>
                                <td style="text-align: center;">
                                    <span class="badge badge-{{ $res->is_published ? 'success' : 'secondary' }}" style="min-width: 90px; justify-content: center;">
                                        {{ $res->is_published ? 'Dipublikasikan' : 'Draf Internal' }}
                                    </span>
                                </td>
                                <td style="text-align: center; white-space: nowrap;">
                                    <form action="{{ route('guru.asesmen.result.update', $res->id) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        <input type="hidden" name="is_published" value="{{ $res->is_published ? 0 : 1 }}">
                                        <button type="submit" class="btn btn-secondary btn-sm">
                                            {{ $res->is_published ? 'Tutup Akses' : 'Buka Akses' }}
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

<!-- Modal Tambah Butir Pertanyaan -->
<div id="createQuestionModal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="createQuestionModalTitle" onclick="handleQuestionBackdropClick(event, 'createQuestionModal')">
    <div class="modal-dialog" style="max-width: 640px; max-height: 90vh; overflow-y: auto;">
        <div class="modal-body" style="padding: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px;">
                <div>
                    <h3 id="createQuestionModalTitle" style="font-size: 16px; font-weight: 800; color: var(--color-text-main); margin: 0;">
                        Tambah Butir Pertanyaan Asesmen
                    </h3>
                    <p style="font-size: 12px; color: var(--color-text-subtle); margin-top: 4px; margin-bottom: 0;">
                        Asesmen ini menggunakan sistem poin (tidak ada jawaban benar atau salah). Berikan nilai bobot poin untuk setiap opsi.
                    </p>
                </div>
                <button type="button" onclick="closeQuestionModal('createQuestionModal')" style="background: none; border: none; font-size: 22px; cursor: pointer; color: var(--color-text-subtle); line-height: 1;">&times;</button>
            </div>

            <form action="{{ route('guru.asesmen.pertanyaan.store', $assessment->id) }}" method="POST">
                @csrf

                <div class="form-group" style="margin-bottom: 18px;">
                    <label class="form-label" style="font-weight: 700;">Teks Butir Pertanyaan *</label>
                    <textarea name="question_text" class="form-control" rows="3" placeholder="Contoh: Saat dihadapkan pada situasi tugas baru, hal pertama yang Anda lakukan adalah..." required></textarea>
                </div>

                <div style="margin-bottom: 18px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <label class="form-label" style="font-weight: 700; margin: 0;">Pilihan Jawaban & Bobot Poin *</label>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="addOptionRow('createOptionsContainer')" style="padding: 3px 10px; font-size: 11px;">
                            + Tambah Opsi Jawaban
                        </button>
                    </div>

                    <div id="createOptionsContainer" style="display: flex; flex-direction: column; gap: 10px;">
                        <!-- Default 4 Opsi Jawaban -->
                        <div class="option-row" style="background: #f8fafc; border: 1px solid var(--color-border); border-radius: var(--radius-sm); padding: 10px 12px; display: grid; grid-template-columns: 24px 1fr 100px 120px 32px; gap: 8px; align-items: center;">
                            <span class="option-letter" style="font-weight: 700; color: var(--color-primary); text-align: center;">A</span>
                            <input type="text" name="options[0][option_text]" class="form-control" placeholder="Teks opsi jawaban..." required style="font-size: 13px;">
                            <input type="number" name="options[0][score_value]" class="form-control" placeholder="Poin" value="4" min="0" required title="Bobot Poin" style="font-size: 13px;">
                            <select name="options[0][dimension_code]" class="form-select" style="font-size: 12px;">
                                <option value="">Dimensi Netral</option>
                                <option value="R">R (Realistic)</option>
                                <option value="I">I (Investigative)</option>
                                <option value="A">A (Artistic)</option>
                                <option value="S">S (Social)</option>
                                <option value="E">E (Enterprising)</option>
                                <option value="C">C (Conventional)</option>
                            </select>
                            <button type="button" onclick="removeOptionRow(this, 'createOptionsContainer')" class="btn btn-secondary btn-sm" style="padding: 4px; color: var(--color-danger); border-color: var(--color-border); justify-content: center;" title="Hapus opsi">&times;</button>
                        </div>

                        <div class="option-row" style="background: #f8fafc; border: 1px solid var(--color-border); border-radius: var(--radius-sm); padding: 10px 12px; display: grid; grid-template-columns: 24px 1fr 100px 120px 32px; gap: 8px; align-items: center;">
                            <span class="option-letter" style="font-weight: 700; color: var(--color-primary); text-align: center;">B</span>
                            <input type="text" name="options[1][option_text]" class="form-control" placeholder="Teks opsi jawaban..." required style="font-size: 13px;">
                            <input type="number" name="options[1][score_value]" class="form-control" placeholder="Poin" value="3" min="0" required title="Bobot Poin" style="font-size: 13px;">
                            <select name="options[1][dimension_code]" class="form-select" style="font-size: 12px;">
                                <option value="">Dimensi Netral</option>
                                <option value="R">R (Realistic)</option>
                                <option value="I">I (Investigative)</option>
                                <option value="A">A (Artistic)</option>
                                <option value="S">S (Social)</option>
                                <option value="E">E (Enterprising)</option>
                                <option value="C">C (Conventional)</option>
                            </select>
                            <button type="button" onclick="removeOptionRow(this, 'createOptionsContainer')" class="btn btn-secondary btn-sm" style="padding: 4px; color: var(--color-danger); border-color: var(--color-border); justify-content: center;" title="Hapus opsi">&times;</button>
                        </div>

                        <div class="option-row" style="background: #f8fafc; border: 1px solid var(--color-border); border-radius: var(--radius-sm); padding: 10px 12px; display: grid; grid-template-columns: 24px 1fr 100px 120px 32px; gap: 8px; align-items: center;">
                            <span class="option-letter" style="font-weight: 700; color: var(--color-primary); text-align: center;">C</span>
                            <input type="text" name="options[2][option_text]" class="form-control" placeholder="Teks opsi jawaban..." required style="font-size: 13px;">
                            <input type="number" name="options[2][score_value]" class="form-control" placeholder="Poin" value="2" min="0" required title="Bobot Poin" style="font-size: 13px;">
                            <select name="options[2][dimension_code]" class="form-select" style="font-size: 12px;">
                                <option value="">Dimensi Netral</option>
                                <option value="R">R (Realistic)</option>
                                <option value="I">I (Investigative)</option>
                                <option value="A">A (Artistic)</option>
                                <option value="S">S (Social)</option>
                                <option value="E">E (Enterprising)</option>
                                <option value="C">C (Conventional)</option>
                            </select>
                            <button type="button" onclick="removeOptionRow(this, 'createOptionsContainer')" class="btn btn-secondary btn-sm" style="padding: 4px; color: var(--color-danger); border-color: var(--color-border); justify-content: center;" title="Hapus opsi">&times;</button>
                        </div>

                        <div class="option-row" style="background: #f8fafc; border: 1px solid var(--color-border); border-radius: var(--radius-sm); padding: 10px 12px; display: grid; grid-template-columns: 24px 1fr 100px 120px 32px; gap: 8px; align-items: center;">
                            <span class="option-letter" style="font-weight: 700; color: var(--color-primary); text-align: center;">D</span>
                            <input type="text" name="options[3][option_text]" class="form-control" placeholder="Teks opsi jawaban..." required style="font-size: 13px;">
                            <input type="number" name="options[3][score_value]" class="form-control" placeholder="Poin" value="1" min="0" required title="Bobot Poin" style="font-size: 13px;">
                            <select name="options[3][dimension_code]" class="form-select" style="font-size: 12px;">
                                <option value="">Dimensi Netral</option>
                                <option value="R">R (Realistic)</option>
                                <option value="I">I (Investigative)</option>
                                <option value="A">A (Artistic)</option>
                                <option value="S">S (Social)</option>
                                <option value="E">E (Enterprising)</option>
                                <option value="C">C (Conventional)</option>
                            </select>
                            <button type="button" onclick="removeOptionRow(this, 'createOptionsContainer')" class="btn btn-secondary btn-sm" style="padding: 4px; color: var(--color-danger); border-color: var(--color-border); justify-content: center;" title="Hapus opsi">&times;</button>
                        </div>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                    <button type="button" class="btn btn-secondary" onclick="closeQuestionModal('createQuestionModal')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Pertanyaan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ubah Butir Pertanyaan -->
<div id="editQuestionModal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="editQuestionModalTitle" onclick="handleQuestionBackdropClick(event, 'editQuestionModal')">
    <div class="modal-dialog" style="max-width: 640px; max-height: 90vh; overflow-y: auto;">
        <div class="modal-body" style="padding: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px;">
                <div>
                    <h3 id="editQuestionModalTitle" style="font-size: 16px; font-weight: 800; color: var(--color-text-main); margin: 0;">
                        Ubah Butir Pertanyaan & Bobot Poin
                    </h3>
                    <p style="font-size: 12px; color: var(--color-text-subtle); margin-top: 4px; margin-bottom: 0;">
                        Sesuaikan teks pertanyaan, teks opsi, dan bobot poin yang diberikan kepada siswa.
                    </p>
                </div>
                <button type="button" onclick="closeQuestionModal('editQuestionModal')" style="background: none; border: none; font-size: 22px; cursor: pointer; color: var(--color-text-subtle); line-height: 1;">&times;</button>
            </div>

            <form id="editQuestionForm" method="POST" action="">
                @csrf
                @method('PUT')

                <div class="form-group" style="margin-bottom: 18px;">
                    <label class="form-label" style="font-weight: 700;">Teks Butir Pertanyaan *</label>
                    <textarea name="question_text" id="editQuestionText" class="form-control" rows="3" required></textarea>
                </div>

                <div style="margin-bottom: 18px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <label class="form-label" style="font-weight: 700; margin: 0;">Pilihan Jawaban & Bobot Poin *</label>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="addOptionRow('editOptionsContainer')" style="padding: 3px 10px; font-size: 11px;">
                            + Tambah Opsi Jawaban
                        </button>
                    </div>

                    <div id="editOptionsContainer" style="display: flex; flex-direction: column; gap: 10px;">
                        <!-- Diisi via JavaScript saat tombol Ubah diklik -->
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                    <button type="button" class="btn btn-secondary" onclick="closeQuestionModal('editQuestionModal')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Hapus Pertanyaan -->
<div id="deleteQuestionModal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="deleteQuestionModalTitle" onclick="handleQuestionBackdropClick(event, 'deleteQuestionModal')">
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
            <h3 id="deleteQuestionModalTitle" class="modal-title">Hapus Butir Pertanyaan</h3>
            <p class="modal-desc" style="margin-bottom: 8px;">
                Apakah Anda yakin ingin menghapus butir pertanyaan ini beserta seluruh opsi jawabannya?
            </p>
            <div id="deleteQuestionPreviewText" style="padding: 10px 12px; background: #f8fafc; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-size: 13px; color: var(--color-text-main); font-weight: 600;"></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeQuestionModal('deleteQuestionModal')">
                Batal
            </button>
            <form id="deleteQuestionForm" method="POST" action="" style="display: inline; margin: 0;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    Ya, Hapus Butir Pertanyaan
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function openCreateQuestionModal() {
    var modal = document.getElementById('createQuestionModal');
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function openEditQuestionModal(questionData) {
    var modal = document.getElementById('editQuestionModal');
    var form = document.getElementById('editQuestionForm');
    var textEl = document.getElementById('editQuestionText');
    var container = document.getElementById('editOptionsContainer');

    if (modal && form && textEl && container) {
        form.action = '/guru/asesmen/{{ $assessment->id }}/pertanyaan/' + questionData.id;
        textEl.value = questionData.question_text;
        container.innerHTML = '';

        var options = questionData.options || [];
        if (options.length === 0) {
            options = [
                { option_text: '', score_value: 4, dimension_code: '' },
                { option_text: '', score_value: 3, dimension_code: '' }
            ];
        }

        options.forEach(function(opt, idx) {
            var letter = String.fromCharCode(65 + idx);
            var row = createOptionRowElement(idx, letter, opt.option_text || '', opt.score_value || 1, opt.dimension_code || '');
            container.appendChild(row);
        });

        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function openDeleteQuestionModal(questionId, questionText) {
    var modal = document.getElementById('deleteQuestionModal');
    var form = document.getElementById('deleteQuestionForm');
    var preview = document.getElementById('deleteQuestionPreviewText');

    if (modal && form) {
        form.action = '/guru/asesmen/{{ $assessment->id }}/pertanyaan/' + questionId;
        if (preview) {
            preview.textContent = questionText;
        }
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeQuestionModal(modalId) {
    var modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

function handleQuestionBackdropClick(event, modalId) {
    if (event.target === document.getElementById(modalId)) {
        closeQuestionModal(modalId);
    }
}

function createOptionRowElement(index, letter, text, score, dimCode) {
    var div = document.createElement('div');
    div.className = 'option-row';
    div.style.cssText = 'background: #f8fafc; border: 1px solid var(--color-border); border-radius: var(--radius-sm); padding: 10px 12px; display: grid; grid-template-columns: 24px 1fr 100px 120px 32px; gap: 8px; align-items: center;';

    div.innerHTML = `
        <span class="option-letter" style="font-weight: 700; color: var(--color-primary); text-align: center;">${letter}</span>
        <input type="text" name="options[${index}][option_text]" class="form-control" value="${escapeHtml(text)}" placeholder="Teks opsi jawaban..." required style="font-size: 13px;">
        <input type="number" name="options[${index}][score_value]" class="form-control" value="${score}" placeholder="Poin" min="0" required title="Bobot Poin" style="font-size: 13px;">
        <select name="options[${index}][dimension_code]" class="form-select" style="font-size: 12px;">
            <option value="" ${dimCode === '' ? 'selected' : ''}>Dimensi Netral</option>
            <option value="R" ${dimCode === 'R' ? 'selected' : ''}>R (Realistic)</option>
            <option value="I" ${dimCode === 'I' ? 'selected' : ''}>I (Investigative)</option>
            <option value="A" ${dimCode === 'A' ? 'selected' : ''}>A (Artistic)</option>
            <option value="S" ${dimCode === 'S' ? 'selected' : ''}>S (Social)</option>
            <option value="E" ${dimCode === 'E' ? 'selected' : ''}>E (Enterprising)</option>
            <option value="C" ${dimCode === 'C' ? 'selected' : ''}>C (Conventional)</option>
        </select>
        <button type="button" onclick="removeOptionRow(this, '${div.parentElement ? div.parentElement.id : 'editOptionsContainer'}')" class="btn btn-secondary btn-sm" style="padding: 4px; color: var(--color-danger); border-color: var(--color-border); justify-content: center;" title="Hapus opsi">&times;</button>
    `;

    return div;
}

function addOptionRow(containerId) {
    var container = document.getElementById(containerId);
    if (!container) return;

    var rows = container.querySelectorAll('.option-row');
    var index = rows.length;
    var letter = String.fromCharCode(65 + index);

    var defaultScore = Math.max(1, 4 - index);
    var row = createOptionRowElement(index, letter, '', defaultScore, '');
    container.appendChild(row);
    reindexOptionLetters(containerId);
}

function removeOptionRow(btn, containerId) {
    var container = document.getElementById(containerId);
    if (!container) return;

    var rows = container.querySelectorAll('.option-row');
    if (rows.length <= 2) {
        alert('Minimal harus terdapat 2 opsi pilihan jawaban.');
        return;
    }

    var row = btn.closest('.option-row');
    if (row) {
        row.remove();
        reindexOptionLetters(containerId);
    }
}

function reindexOptionLetters(containerId) {
    var container = document.getElementById(containerId);
    if (!container) return;

    var rows = container.querySelectorAll('.option-row');
    rows.forEach(function(row, idx) {
        var letter = String.fromCharCode(65 + idx);
        var letterSpan = row.querySelector('.option-letter');
        if (letterSpan) letterSpan.textContent = letter;

        var textInput = row.querySelector('input[type="text"]');
        if (textInput) textInput.name = `options[${idx}][option_text]`;

        var scoreInput = row.querySelector('input[type="number"]');
        if (scoreInput) scoreInput.name = `options[${idx}][score_value]`;

        var dimSelect = row.querySelector('select');
        if (dimSelect) dimSelect.name = `options[${idx}][dimension_code]`;

        var removeBtn = row.querySelector('button');
        if (removeBtn) {
            removeBtn.setAttribute('onclick', `removeOptionRow(this, '${containerId}')`);
        }
    });
}

function escapeHtml(text) {
    if (!text) return '';
    return text.replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#039;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}
</script>
@endsection
