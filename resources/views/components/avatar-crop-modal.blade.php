<!-- Modal Crop & Preview Foto Profil Ruang BK -->
<div id="avatarCropModal" class="avatar-modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="cropModalTitle" style="display: none;">
    <div class="avatar-modal-container">
        <div class="avatar-modal-header">
            <h3 id="cropModalTitle">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M6.13 1L6 16a2 2 0 0 0 2 2h15"></path>
                    <path d="M1 6.13L16 6a2 2 0 0 1 2 2v15"></path>
                </svg>
                Atur &amp; Potong Foto Profil
            </h3>
            <button type="button" class="avatar-modal-close-btn" id="cropModalCloseBtn" aria-label="Tutup Jendela" onclick="window.AvatarCropper.closeModal()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <div class="avatar-modal-body">
            <div id="cropErrorMessage" style="display: none; padding: 10px 14px; background: #FEF2F2; color: #DC2626; border: 1px solid #FECACA; border-radius: 8px; font-size: 12.5px; font-weight: 600;"></div>

            <!-- Area Interaktif Canvas -->
            <div class="avatar-crop-viewport" id="avatarCropViewport" title="Geser foto untuk menyesuaikan posisi">
                <canvas id="avatarCropCanvas" width="340" height="340"></canvas>
                <div class="avatar-crop-mask-overlay"></div>
                <div class="avatar-crop-crosshair-h"></div>
                <div class="avatar-crop-crosshair-v"></div>
            </div>

            <!-- Toolbar Pengatur Posisi & Skala Zoom -->
            <div class="avatar-crop-toolbar">
                <button type="button" class="avatar-crop-tool-btn" id="zoomOutBtn" title="Perkecil Foto (Zoom Out)" aria-label="Perkecil Foto" onclick="window.AvatarCropper.zoomStep(-0.15)">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                </button>
                <input type="range" class="avatar-zoom-slider" id="avatarZoomSlider" min="1" max="3" step="0.01" value="1" aria-label="Pengatur Skala Foto" oninput="window.AvatarCropper.setZoom(this.value)">
                <button type="button" class="avatar-crop-tool-btn" id="zoomInBtn" title="Perbesar Foto (Zoom In)" aria-label="Perbesar Foto" onclick="window.AvatarCropper.zoomStep(0.15)">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                </button>
                <button type="button" class="avatar-crop-tool-btn" id="resetPositionBtn" title="Pusatkan Posisi Awal" aria-label="Reset Posisi Foto" onclick="window.AvatarCropper.resetTransform()">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path><path d="M3 3v5h5"></path></svg>
                </button>
            </div>

            <!-- Pratinjau Tampilan Nyata -->
            <div class="avatar-previews-row">
                <div class="avatar-preview-item">
                    <div class="avatar-preview-circle-large">
                        <canvas id="avatarPreviewLarge" width="54" height="54"></canvas>
                    </div>
                    <div>
                        <div class="avatar-preview-label">Halaman Profil</div>
                        <div style="font-size: 10.5px; color: var(--color-text-subtle);">Format 54px</div>
                    </div>
                </div>

                <div class="avatar-preview-item">
                    <div class="avatar-preview-circle-small">
                        <canvas id="avatarPreviewSmall" width="36" height="36"></canvas>
                    </div>
                    <div>
                        <div class="avatar-preview-label">Topbar &amp; Menu</div>
                        <div style="font-size: 10.5px; color: var(--color-text-subtle);">Format 36px</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="avatar-modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" onclick="window.AvatarCropper.closeModal()">
                Batal
            </button>
            <button type="button" class="btn btn-primary btn-sm" id="applyCropBtn" onclick="window.AvatarCropper.applyCrop()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span>Terapkan &amp; Simpan Foto</span>
            </button>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus Foto Profil -->
<div id="avatarDeleteModal" class="avatar-modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="deleteModalTitle" style="display: none;">
    <div class="avatar-modal-container" style="max-width: 440px;">
        <div class="avatar-modal-header" style="background-color: var(--color-navy-dark);">
            <h3 id="deleteModalTitle" style="color: #F87171;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                Hapus Foto Profil
            </h3>
            <button type="button" class="avatar-modal-close-btn" onclick="window.AvatarCropper.closeDeleteModal()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        <div class="avatar-modal-body">
            <p style="font-size: 13.5px; color: var(--color-text-main); line-height: 1.5; margin: 0;">
                Apakah Anda yakin ingin menghapus foto profil ini? Foto khusus Anda akan dihapus permanen dan sistem otomatis mengembalikan tampilan akun ke avatar default.
            </p>
        </div>
        <div class="avatar-modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" onclick="window.AvatarCropper.closeDeleteModal()">
                Batal
            </button>
            <button type="button" class="btn btn-danger btn-sm" id="confirmDeleteAvatarBtn" onclick="window.AvatarCropper.executeDelete()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                <span>Ya, Hapus Foto</span>
            </button>
        </div>
    </div>
</div>

<script>
window.AvatarCropper = (function () {
    const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
    const MAX_SIZE = 5 * 1024 * 1024; // 5 MB

    let modal = null;
    let deleteModal = null;
    let canvas = null;
    let ctx = null;
    let previewLarge = null;
    let previewSmall = null;
    let zoomSlider = null;
    let errBox = null;

    let img = null;
    let baseScale = 1;
    let zoom = 1;
    let posX = 0;
    let posY = 0;
    let isDragging = false;
    let startX = 0;
    let startY = 0;

    let currentUploadUrl = null;
    let currentDeleteUrl = null;
    let currentCsrf = null;
    let onSaveCallback = null;
    let onDeleteCallback = null;

    function init() {
        modal = document.getElementById('avatarCropModal');
        deleteModal = document.getElementById('avatarDeleteModal');
        canvas = document.getElementById('avatarCropCanvas');
        if (canvas) {
            ctx = canvas.getContext('2d');
        }
        previewLarge = document.getElementById('avatarPreviewLarge');
        previewSmall = document.getElementById('avatarPreviewSmall');
        zoomSlider = document.getElementById('avatarZoomSlider');
        errBox = document.getElementById('cropErrorMessage');

        const viewport = document.getElementById('avatarCropViewport');
        if (viewport) {
            viewport.addEventListener('mousedown', onMouseDown);
            window.addEventListener('mousemove', onMouseMove);
            window.addEventListener('mouseup', onMouseUp);

            viewport.addEventListener('touchstart', onTouchStart, { passive: false });
            window.addEventListener('touchmove', onTouchMove, { passive: false });
            window.addEventListener('touchend', onTouchEnd);

            viewport.addEventListener('wheel', onWheel, { passive: false });
        }

        window.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeModal();
                closeDeleteModal();
            }
        });
    }

    function showError(msg) {
        if (!errBox) return;
        errBox.textContent = msg;
        errBox.style.display = 'block';
    }

    function clearError() {
        if (!errBox) return;
        errBox.textContent = '';
        errBox.style.display = 'none';
    }

    function openForFile(file, options = {}) {
        if (!canvas) init();
        clearError();

        if (!file) return;

        if (!ALLOWED_TYPES.includes(file.type)) {
            alert('Format file tidak didukung. Harap pilih gambar dengan format JPG, JPEG, PNG, atau WebP.');
            return;
        }

        if (file.size > MAX_SIZE) {
            alert('Ukuran file terlalu besar (' + (file.size / (1024 * 1024)).toFixed(2) + ' MB). Ukuran maksimal yang diizinkan adalah 5 MB.');
            return;
        }

        currentUploadUrl = options.uploadUrl || null;
        currentDeleteUrl = options.deleteUrl || null;
        currentCsrf = options.csrf || (document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : null);
        onSaveCallback = options.onSave || null;
        onDeleteCallback = options.onDelete || null;

        const reader = new FileReader();
        reader.onload = function (e) {
            img = new Image();
            img.onload = function () {
                resetTransform();
                showModal();
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    function resetTransform() {
        if (!img || !canvas) return;

        const size = canvas.width;
        // Hitung skala dasar agar foto menutupi area crop 1:1
        baseScale = Math.max(size / img.width, size / img.height);
        zoom = 1;
        if (zoomSlider) zoomSlider.value = 1;

        // Posisikan foto tepat di tengah kanvas
        posX = (size - img.width * baseScale) / 2;
        posY = (size - img.height * baseScale) / 2;

        draw();
    }

    function setZoom(val) {
        val = parseFloat(val);
        if (isNaN(val) || val < 1 || val > 3) return;
        const oldZoom = zoom;
        zoom = val;

        // Pertahankan titik tengah saat zoom berubah
        const size = canvas.width;
        const centerX = size / 2;
        const centerY = size / 2;

        posX = centerX - (centerX - posX) * (zoom / oldZoom);
        posY = centerY - (centerY - posY) * (zoom / oldZoom);

        clampPosition();
        draw();
    }

    function zoomStep(delta) {
        if (!zoomSlider) return;
        let next = parseFloat(zoomSlider.value) + delta;
        if (next < 1) next = 1;
        if (next > 3) next = 3;
        zoomSlider.value = next;
        setZoom(next);
    }

    function clampPosition() {
        if (!img || !canvas) return;
        const size = canvas.width;
        const curW = img.width * baseScale * zoom;
        const curH = img.height * baseScale * zoom;

        // Batasi geser agar area lingkaran tetap terisi foto
        const pad = 15; // margin masker
        const minX = size - pad - curW;
        const maxX = pad;
        const minY = size - pad - curH;
        const maxY = pad;

        if (curW >= size - pad * 2) {
            posX = Math.min(maxX, Math.max(minX, posX));
        } else {
            posX = (size - curW) / 2;
        }

        if (curH >= size - pad * 2) {
            posY = Math.min(maxY, Math.max(minY, posY));
        } else {
            posY = (size - curH) / 2;
        }
    }

    function draw() {
        if (!ctx || !img) return;

        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = '#0F172A';
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        const curW = img.width * baseScale * zoom;
        const curH = img.height * baseScale * zoom;

        ctx.drawImage(img, posX, posY, curW, curH);

        updatePreviews();
    }

    function updatePreviews() {
        if (!canvas) return;

        // Update pratinjau bulat 54px & 36px
        [previewLarge, previewSmall].forEach(function (p) {
            if (!p) return;
            const pCtx = p.getContext('2d');
            pCtx.clearRect(0, 0, p.width, p.height);

            pCtx.save();
            pCtx.beginPath();
            pCtx.arc(p.width / 2, p.height / 2, p.width / 2, 0, Math.PI * 2);
            pCtx.closePath();
            pCtx.clip();

            // Ambil area di dalam batas lingkaran canvas (margin 15px)
            const margin = 15;
            const cropDiameter = canvas.width - margin * 2;
            pCtx.drawImage(
                canvas,
                margin, margin, cropDiameter, cropDiameter,
                0, 0, p.width, p.height
            );
            pCtx.restore();
        });
    }

    function onMouseDown(e) {
        isDragging = true;
        startX = e.clientX - posX;
        startY = e.clientY - posY;
    }

    function onMouseMove(e) {
        if (!isDragging) return;
        posX = e.clientX - startX;
        posY = e.clientY - startY;
        clampPosition();
        draw();
    }

    function onMouseUp() {
        isDragging = false;
    }

    function onTouchStart(e) {
        if (e.touches.length === 1) {
            isDragging = true;
            startX = e.touches[0].clientX - posX;
            startY = e.touches[0].clientY - posY;
            e.preventDefault();
        }
    }

    function onTouchMove(e) {
        if (isDragging && e.touches.length === 1) {
            posX = e.touches[0].clientX - startX;
            posY = e.touches[0].clientY - startY;
            clampPosition();
            draw();
            e.preventDefault();
        }
    }

    function onTouchEnd() {
        isDragging = false;
    }

    function onWheel(e) {
        e.preventDefault();
        const delta = e.deltaY < 0 ? 0.08 : -0.08;
        zoomStep(delta);
    }

    function showModal() {
        if (!modal) init();
        modal.style.display = 'flex';
        setTimeout(() => modal.classList.add('show'), 10);
    }

    function closeModal() {
        if (!modal) return;
        modal.classList.remove('show');
        setTimeout(() => {
            modal.style.display = 'none';
        }, 150);
    }

    function openDeleteModal(options = {}) {
        if (!deleteModal) init();
        currentDeleteUrl = options.deleteUrl || null;
        currentCsrf = options.csrf || (document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : null);
        onDeleteCallback = options.onDelete || null;

        deleteModal.style.display = 'flex';
        setTimeout(() => deleteModal.classList.add('show'), 10);
    }

    function closeDeleteModal() {
        if (!deleteModal) return;
        deleteModal.classList.remove('show');
        setTimeout(() => {
            deleteModal.style.display = 'none';
        }, 150);
    }

    function getCroppedBase64(targetSize = 512) {
        if (!img || !canvas) return null;

        const offCanvas = document.createElement('canvas');
        offCanvas.width = targetSize;
        offCanvas.height = targetSize;
        const offCtx = offCanvas.getContext('2d');

        const margin = 15;
        const cropDiameter = canvas.width - margin * 2;

        offCtx.drawImage(
            canvas,
            margin, margin, cropDiameter, cropDiameter,
            0, 0, targetSize, targetSize
        );

        return offCanvas.toDataURL('image/jpeg', 0.92);
    }

    function applyCrop() {
        const base64Data = getCroppedBase64(512);
        if (!base64Data) {
            showError('Gagal memproses gambar foto.');
            return;
        }

        const applyBtn = document.getElementById('applyCropBtn');
        const originalHtml = applyBtn.innerHTML;

        if (onSaveCallback) {
            onSaveCallback(base64Data);
            closeModal();
            return;
        }

        if (!currentUploadUrl) {
            closeModal();
            return;
        }

        applyBtn.disabled = true;
        applyBtn.innerHTML = '<span>Menyimpan...</span>';

        fetch(currentUploadUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': currentCsrf,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ cropped_image: base64Data })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw new Error(err.message || 'Gagal menyimpan foto.'); });
            }
            return response.json();
        })
        .then(data => {
            applyBtn.disabled = false;
            applyBtn.innerHTML = originalHtml;
            closeModal();
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Terjadi kesalahan.');
            }
        })
        .catch(err => {
            applyBtn.disabled = false;
            applyBtn.innerHTML = originalHtml;
            showError(err.message);
        });
    }

    function executeDelete() {
        const delBtn = document.getElementById('confirmDeleteAvatarBtn');
        const originalHtml = delBtn.innerHTML;

        if (onDeleteCallback) {
            onDeleteCallback();
            closeDeleteModal();
            return;
        }

        if (!currentDeleteUrl) {
            closeDeleteModal();
            return;
        }

        delBtn.disabled = true;
        delBtn.innerHTML = '<span>Menghapus...</span>';

        fetch(currentDeleteUrl, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': currentCsrf,
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw new Error(err.message || 'Gagal menghapus foto.'); });
            }
            return response.json();
        })
        .then(data => {
            delBtn.disabled = false;
            delBtn.innerHTML = originalHtml;
            closeDeleteModal();
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Terjadi kesalahan.');
            }
        })
        .catch(err => {
            delBtn.disabled = false;
            delBtn.innerHTML = originalHtml;
            alert(err.message);
        });
    }

    document.addEventListener('DOMContentLoaded', init);

    return {
        openForFile,
        setZoom,
        zoomStep,
        resetTransform,
        closeModal,
        openDeleteModal,
        closeDeleteModal,
        applyCrop,
        executeDelete,
        getCroppedBase64
    };
})();
</script>
