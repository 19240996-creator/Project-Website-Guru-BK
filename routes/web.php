<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Guru\DashboardController as GuruDashboardController;
use App\Http\Controllers\Guru\StudentController as GuruStudentController;
use App\Http\Controllers\Guru\CounselingController as GuruCounselingController;
use App\Http\Controllers\Guru\AssessmentController as GuruAssessmentController;
use App\Http\Controllers\Guru\FuturePlanController as GuruFuturePlanController;
use App\Http\Controllers\Guru\PartnerController as GuruPartnerController;
use App\Http\Controllers\Guru\OpportunityController as GuruOpportunityController;
use App\Http\Controllers\Guru\AlumniController as GuruAlumniController;
use App\Http\Controllers\Guru\ReportController as GuruReportController;
use App\Http\Controllers\Guru\AuditLogController as GuruAuditLogController;
use App\Http\Controllers\Guru\ProfileController as GuruProfileController;
use App\Http\Controllers\Guru\BkClassController as GuruBkClassController;
use App\Http\Controllers\Guru\Koordinator\AssignmentController as KoordinatorAssignmentController;
use App\Http\Controllers\Guru\Koordinator\GuruBkController as KoordinatorGuruBkController;
use App\Http\Controllers\Guru\Koordinator\SettingController as KoordinatorSettingController;

use App\Http\Controllers\Siswa\DashboardController as SiswaDashboardController;
use App\Http\Controllers\Siswa\ProfileController as SiswaProfileController;
use App\Http\Controllers\Siswa\CounselingController as SiswaCounselingController;
use App\Http\Controllers\Siswa\AssessmentController as SiswaAssessmentController;
use App\Http\Controllers\Siswa\FuturePlanController as SiswaFuturePlanController;
use App\Http\Controllers\Siswa\OpportunityController as SiswaOpportunityController;
use App\Http\Controllers\Siswa\AlumniController as SiswaAlumniController;
use App\Http\Controllers\Siswa\BkClassController as SiswaBkClassController;
use App\Http\Controllers\Pemantau\PemantauController;
use App\Services\Security\SecurePath;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Informasi Bimbingan Konseling & Karier Siswa
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->isGuruOrKoordinator()) {
            return redirect()->route('guru.dashboard');
        }
        if ($user->role === 'kepala_kampus') {
            return redirect()->route('kepala_kampus.dashboard');
        }
        if ($user->role === 'admin_dico') {
            return redirect()->route('admin_dico.dashboard');
        }
        return redirect()->route('siswa.dashboard');
    }
    return redirect()->route('login');
});

// Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// Shared Notifications (Authenticated users)
Route::middleware(['auth'])->group(function () {
    Route::get('/notifikasi', [NotificationController::class, 'index'])->name('notifikasi.index');
    Route::get('/notifikasi/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifikasi.read');
    Route::post('/notifikasi/read-all', [NotificationController::class, 'markAllRead'])->name('notifikasi.read_all');
    Route::delete('/notifikasi/{id}', [NotificationController::class, 'destroy'])->name('notifikasi.destroy');
});

// ==========================================
// ROLE 1: GURU BK & GURU KOORDINATOR BK
// ==========================================
Route::middleware(['auth', 'role:guru_bk,koordinator_bk'])->prefix('guru')->name('guru.')->group(function () {
    // 4. Beranda & "Yang Perlu Saya Kerjakan"
    Route::get('/dashboard', [GuruDashboardController::class, 'index'])->name('dashboard');

    // Profil Guru BK
    Route::get('/profil', [GuruProfileController::class, 'show'])->name('profil.show');
    Route::put('/profil', [GuruProfileController::class, 'update'])->name('profil.update');
    Route::post('/profil/foto', [GuruProfileController::class, 'updatePhoto'])->name('profil.photo.update');
    Route::delete('/profil/foto', [GuruProfileController::class, 'destroyPhoto'])->name('profil.photo.destroy');

    // 5. Data Siswa & Profil 360 Derajat
    Route::get('/siswa', [GuruStudentController::class, 'index'])->name('siswa.index');
    Route::get('/siswa/tambah', [GuruStudentController::class, 'create'])->name('siswa.create');
    Route::post('/siswa', [GuruStudentController::class, 'store'])->name('siswa.store');
    Route::post('/siswa/tambah-jurusan', [GuruStudentController::class, 'storeMajor'])->name('siswa.store_major');
    Route::post('/siswa/hapus-jurusan', [GuruStudentController::class, 'destroyMajor'])->name('siswa.destroy_major');
    Route::post('/siswa/import', [GuruStudentController::class, 'import'])->name('siswa.import');
    Route::get('/siswa/template-impor', [GuruStudentController::class, 'downloadTemplate'])->name('siswa.template');
    Route::get('/siswa/kenaikan-kelas-massal', [GuruStudentController::class, 'massPromoteView'])->name('siswa.mass_promote_view');
    Route::post('/siswa/kenaikan-kelas-massal', [GuruStudentController::class, 'massPromote'])->name('siswa.mass_promote');
    Route::delete('/siswa/hapus-per-kelas', [GuruStudentController::class, 'destroyByClass'])->name('siswa.destroy_by_class');
    Route::get('/siswa/{id}', [GuruStudentController::class, 'show'])->name('siswa.show');
    Route::get('/siswa/{id}/edit', [GuruStudentController::class, 'edit'])->name('siswa.edit');
    Route::put('/siswa/{id}', [GuruStudentController::class, 'update'])->name('siswa.update');
    Route::delete('/siswa/{id}', [GuruStudentController::class, 'destroy'])->name('siswa.destroy');
    Route::post('/siswa/{id}/foto', [GuruStudentController::class, 'updatePhoto'])->name('siswa.photo.update');
    Route::delete('/siswa/{id}/foto', [GuruStudentController::class, 'destroyPhoto'])->name('siswa.photo.destroy');

    // 7 - 12. Bimbingan & Konseling
    Route::get('/konseling', [GuruCounselingController::class, 'index'])->name('konseling.index');
    Route::get('/konseling/{id}', [GuruCounselingController::class, 'show'])->name('konseling.show');
    Route::post('/konseling/{id}/schedule', [GuruCounselingController::class, 'schedule'])->name('konseling.schedule');
    Route::post('/konseling/{id}/notes', [GuruCounselingController::class, 'updateNotes'])->name('konseling.notes');
    Route::post('/konseling/{id}/follow-up', [GuruCounselingController::class, 'storeFollowUp'])->name('konseling.follow_up.store');
    Route::post('/konseling/follow-up/{followUpId}/status', [GuruCounselingController::class, 'updateFollowUp'])->name('konseling.follow_up.update');

    // 6. Asesmen Siswa
    Route::get('/asesmen', [GuruAssessmentController::class, 'index'])->name('asesmen.index');
    Route::post('/asesmen', [GuruAssessmentController::class, 'store'])->name('asesmen.store');
    Route::delete('/asesmen/{id}', [GuruAssessmentController::class, 'destroy'])->name('asesmen.destroy');
    Route::get('/asesmen/{id}', [GuruAssessmentController::class, 'show'])->name('asesmen.show');
    Route::get('/asesmen/hasil/{id}', [GuruAssessmentController::class, 'showResult'])->name('asesmen.result');
    Route::post('/asesmen/hasil/{id}/update', [GuruAssessmentController::class, 'updateResult'])->name('asesmen.result.update');
    Route::post('/asesmen/{id}/pertanyaan', [GuruAssessmentController::class, 'storeQuestion'])->name('asesmen.pertanyaan.store');
    Route::put('/asesmen/{id}/pertanyaan/{questionId}', [GuruAssessmentController::class, 'updateQuestion'])->name('asesmen.pertanyaan.update');
    Route::delete('/asesmen/{id}/pertanyaan/{questionId}', [GuruAssessmentController::class, 'destroyQuestion'])->name('asesmen.pertanyaan.destroy');

    // Kelas BK Online (Pembelajaran, Materi, Tugas, Presensi)
    Route::get('/kelas', [GuruBkClassController::class, 'index'])->name('kelas.index');
    Route::get('/kelas/buat', [GuruBkClassController::class, 'create'])->name('kelas.create');
    Route::post('/kelas', [GuruBkClassController::class, 'store'])->name('kelas.store');
    Route::get('/kelas/{id}', [GuruBkClassController::class, 'show'])->name('kelas.show');
    Route::get('/kelas/{id}/edit', [GuruBkClassController::class, 'edit'])->name('kelas.edit');
    Route::put('/kelas/{id}', [GuruBkClassController::class, 'update'])->name('kelas.update');
    Route::delete('/kelas/{id}', [GuruBkClassController::class, 'destroy'])->name('kelas.destroy');

    // Materi Pembelajaran
    Route::post('/kelas/{id}/materi', [GuruBkClassController::class, 'storeMaterial'])->name('kelas.materi.store');
    Route::put('/kelas/{id}/materi/{materialId}', [GuruBkClassController::class, 'updateMaterial'])->name('kelas.materi.update');
    Route::post('/kelas/{id}/materi/{materialId}/urutan', [GuruBkClassController::class, 'reorderMaterial'])->name('kelas.materi.reorder');
    Route::delete('/kelas/{id}/materi/{materialId}', [GuruBkClassController::class, 'destroyMaterial'])->name('kelas.materi.destroy');
    Route::get('/kelas/{id}/materi/{materialId}/unduh', [GuruBkClassController::class, 'downloadMaterial'])->name('kelas.materi.download');

    // Tugas Pembelajaran
    Route::post('/kelas/{id}/tugas', [GuruBkClassController::class, 'storeTask'])->name('kelas.tugas.store');
    Route::put('/kelas/{id}/tugas/{taskId}', [GuruBkClassController::class, 'updateTask'])->name('kelas.tugas.update');
    Route::delete('/kelas/{id}/tugas/{taskId}', [GuruBkClassController::class, 'destroyTask'])->name('kelas.tugas.destroy');
    Route::get('/kelas/{id}/tugas/{taskId}/lampiran', [GuruBkClassController::class, 'downloadTaskAttachment'])->name('kelas.tugas.download_attachment');
    Route::post('/kelas/{id}/tugas/submission/{submissionId}/nilai', [GuruBkClassController::class, 'gradeSubmission'])->name('kelas.tugas.grade');
    Route::post('/kelas/{id}/tugas/{taskId}/nilai-siswa', [GuruBkClassController::class, 'gradeTaskStudent'])->name('kelas.tugas.grade_student');
    Route::get('/kelas/{id}/tugas/submission/{submissionId}/unduh', [GuruBkClassController::class, 'downloadSubmissionFile'])->name('kelas.tugas.download_submission');
    Route::get('/kelas/{id}/tugas/submission/{submissionId}/lihat', [GuruBkClassController::class, 'viewSubmissionFile'])->name('kelas.tugas.view_submission');

    // Koreksi Presensi
    Route::post('/kelas/{id}/presensi/koreksi', [GuruBkClassController::class, 'correctAttendance'])->name('kelas.presensi.correct');

    // 14 - 15. Peminatan & Rencana Masa Depan
    Route::get('/peminatan', [GuruFuturePlanController::class, 'index'])->name('peminatan.index');

    // 16 - 18. Perguruan Tinggi, Perusahaan & Relasi Mitra
    Route::get('/mitra', [GuruPartnerController::class, 'index'])->name('mitra.index');
    Route::post('/mitra', [GuruPartnerController::class, 'store'])->name('mitra.store');
    Route::get('/mitra/{id}', [GuruPartnerController::class, 'show'])->name('mitra.show');
    Route::put('/mitra/{id}', [GuruPartnerController::class, 'update'])->name('mitra.update');
    Route::delete('/mitra/{id}', [GuruPartnerController::class, 'destroy'])->name('mitra.destroy');
    Route::get('/mitra-kegiatan', [GuruPartnerController::class, 'activities'])->name('mitra.activities');
    Route::post('/mitra-kegiatan', [GuruPartnerController::class, 'storeActivity'])->name('mitra.activity.store');
    Route::delete('/mitra-kegiatan/bulk-destroy', [GuruPartnerController::class, 'bulkDestroyActivities'])->name('mitra.activity.bulk_destroy');
    Route::post('/mitra-kegiatan/bulk-status', [GuruPartnerController::class, 'bulkStatusActivities'])->name('mitra.activity.bulk_status');
    Route::put('/mitra-kegiatan/{id}', [GuruPartnerController::class, 'updateActivity'])->name('mitra.activity.update');
    Route::delete('/mitra-kegiatan/{id}', [GuruPartnerController::class, 'destroyActivity'])->name('mitra.activity.destroy');

    // 19. Peluang Siswa
    Route::get('/peluang', [GuruOpportunityController::class, 'index'])->name('peluang.index');
    Route::post('/peluang', [GuruOpportunityController::class, 'store'])->name('peluang.store');
    Route::get('/peluang/{id}', [GuruOpportunityController::class, 'show'])->name('peluang.show');
    Route::put('/peluang/{id}', [GuruOpportunityController::class, 'update'])->name('peluang.update');
    Route::delete('/peluang/{id}', [GuruOpportunityController::class, 'destroy'])->name('peluang.destroy');
    Route::post('/peluang/registrasi/{regId}/status', [GuruOpportunityController::class, 'updateRegistrationStatus'])->name('peluang.reg_status');

    // 23 - 25. Kelulusan & Alumni (Tracer Study)
    Route::get('/alumni', [GuruAlumniController::class, 'index'])->name('alumni.index');
    Route::delete('/alumni/bulk-destroy', [GuruAlumniController::class, 'bulkDestroy'])->name('alumni.bulk_destroy');
    Route::post('/alumni/bulk-status', [GuruAlumniController::class, 'bulkStatus'])->name('alumni.bulk_status');
    Route::put('/alumni/{id}', [GuruAlumniController::class, 'update'])->name('alumni.update');
    Route::delete('/alumni/{id}', [GuruAlumniController::class, 'destroy'])->name('alumni.destroy');
    Route::post('/siswa/{studentId}/graduate', [GuruAlumniController::class, 'graduateStudent'])->name('alumni.graduate');
    Route::post('/alumni/{id}/toggle-showcase', [GuruAlumniController::class, 'toggleShowcase'])->name('alumni.toggle_showcase');

    // 26 - 30. Laporan (Paket A Kesiswaan, Paket B Kurikulum, Paket C Kepala Sekolah, Laporan Berkala)
    Route::get('/laporan', [GuruReportController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/cetak', [GuruReportController::class, 'generate'])->name('laporan.generate');
    Route::get('/laporan/cetak-berkala', [GuruReportController::class, 'generatePeriodic'])->name('laporan.generate_periodic');

    // ==========================================
    // KHUSUS GURU KOORDINATOR BK
    // ==========================================
    Route::middleware(['role:koordinator_bk'])->group(function () {
        // Pembagian Tugas Guru BK (Berdasarkan Kelas & Jurusan)
        Route::get('/penugasan', [KoordinatorAssignmentController::class, 'index'])->name('penugasan.index');
        Route::post('/penugasan/assign', [KoordinatorAssignmentController::class, 'assign'])->name('penugasan.assign');
        Route::post('/penugasan/unassign', [KoordinatorAssignmentController::class, 'unassign'])->name('penugasan.unassign');
        Route::post('/penugasan/reassign-massal', [KoordinatorAssignmentController::class, 'reassignMassal'])->name('penugasan.reassign_massal');

        // Manajemen Akun Guru BK
        Route::get('/koordinator/guru-bk', [KoordinatorGuruBkController::class, 'index'])->name('koordinator.guru_bk.index');
        Route::get('/koordinator/guru-bk/tambah', [KoordinatorGuruBkController::class, 'create'])->name('koordinator.guru_bk.create');
        Route::post('/koordinator/guru-bk', [KoordinatorGuruBkController::class, 'store'])->name('koordinator.guru_bk.store');
        Route::get('/koordinator/guru-bk/{id}', [KoordinatorGuruBkController::class, 'show'])->name('koordinator.guru_bk.show');
        Route::get('/koordinator/guru-bk/{id}/edit', [KoordinatorGuruBkController::class, 'edit'])->name('koordinator.guru_bk.edit');
        Route::put('/koordinator/guru-bk/{id}', [KoordinatorGuruBkController::class, 'update'])->name('koordinator.guru_bk.update');
        Route::delete('/koordinator/guru-bk/{id}', [KoordinatorGuruBkController::class, 'destroy'])->name('koordinator.guru_bk.destroy');

        // 41. Audit Log Aktivitas & Perubahan Data (Khusus Koordinator BK)
        Route::get('/audit', [GuruAuditLogController::class, 'index'])->name('audit.index');
        Route::delete('/audit/bulk', [GuruAuditLogController::class, 'bulkDestroy'])->name('audit.bulk-destroy');
        Route::post('/audit/clear', [GuruAuditLogController::class, 'clear'])->name('audit.clear');
        Route::delete('/audit/{id}', [GuruAuditLogController::class, 'destroy'])->name('audit.destroy');

        // 42. Pengaturan Identitas Sekolah & Kop Surat (Khusus Koordinator BK)
        Route::get('/koordinator/pengaturan', [KoordinatorSettingController::class, 'index'])->name('koordinator.pengaturan.index');
        Route::put('/koordinator/pengaturan', [KoordinatorSettingController::class, 'update'])->name('koordinator.pengaturan.update');
    });
});

// ==========================================
// ROLE 2: SISWA
// ==========================================
Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    // 37. Dashboard Siswa
    Route::get('/dashboard', [SiswaDashboardController::class, 'index'])->name('dashboard');

    // Profil Siswa
    Route::get('/profil', [SiswaProfileController::class, 'show'])->name('profil.show');
    Route::put('/profil', [SiswaProfileController::class, 'update'])->name('profil.update');
    Route::post('/profil/foto', [SiswaProfileController::class, 'updatePhoto'])->name('profil.photo.update');
    Route::delete('/profil/foto', [SiswaProfileController::class, 'destroyPhoto'])->name('profil.photo.destroy');

    // 8. Pengajuan & Jadwal Konseling
    Route::get('/konseling', [SiswaCounselingController::class, 'index'])->name('konseling.index');
    Route::get('/konseling/ajukan', [SiswaCounselingController::class, 'create'])->name('konseling.create');
    Route::post('/konseling', [SiswaCounselingController::class, 'store'])->name('konseling.store');
    Route::get('/konseling/{id}', [SiswaCounselingController::class, 'show'])->name('konseling.show');

    // 6. Asesmen Siswa
    Route::get('/asesmen', [SiswaAssessmentController::class, 'index'])->name('asesmen.index');
    Route::get('/asesmen/{id}/kerjakan', [SiswaAssessmentController::class, 'take'])->name('asesmen.take');
    Route::post('/asesmen/{id}/kirim', [SiswaAssessmentController::class, 'submit'])->name('asesmen.submit');
    Route::get('/asesmen/hasil/{id}', [SiswaAssessmentController::class, 'result'])->name('asesmen.result');

    // 38. Rute Masa Depan
    Route::get('/rencana-masa-depan', [SiswaFuturePlanController::class, 'show'])->name('rencana.show');
    Route::post('/rencana-masa-depan', [SiswaFuturePlanController::class, 'update'])->name('rencana.update');
    Route::delete('/rencana-masa-depan/{id}', [SiswaFuturePlanController::class, 'destroy'])->name('rencana.destroy');

    // 19 - 20. Peluang & Pendaftaran
    Route::get('/peluang', [SiswaOpportunityController::class, 'index'])->name('peluang.index');
    Route::get('/peluang/{id}', [SiswaOpportunityController::class, 'show'])->name('peluang.show');
    Route::post('/peluang/{id}/daftar', [SiswaOpportunityController::class, 'register'])->name('peluang.register');

    // 25. Jejak Alumni & Pembaruan Tracer Lulusan
    Route::get('/alumni', [SiswaAlumniController::class, 'index'])->name('alumni.index');
    Route::post('/alumni/tracer', [SiswaAlumniController::class, 'updateTracer'])->name('alumni.tracer.update');

    // Kelas BK Online (Siswa)
    Route::get('/kelas', [SiswaBkClassController::class, 'index'])->name('kelas.index');
    Route::get('/kelas/{id}', [SiswaBkClassController::class, 'show'])->name('kelas.show');
    Route::post('/kelas/{id}/presensi', [SiswaBkClassController::class, 'storeAttendance'])->name('kelas.attendance.store');
    Route::post('/kelas/{id}/tugas/{taskId}/kirim', [SiswaBkClassController::class, 'submitTask'])->name('kelas.tugas.submit');
    Route::get('/kelas/{id}/materi/{materialId}/unduh', [SiswaBkClassController::class, 'downloadMaterial'])->name('kelas.materi.download');
    Route::get('/kelas/{id}/tugas/{taskId}/lampiran', [SiswaBkClassController::class, 'downloadTaskAttachment'])->name('kelas.tugas.download_attachment');
    Route::get('/kelas/{id}/tugas/{taskId}/jawaban-saya', [SiswaBkClassController::class, 'downloadMySubmission'])->name('kelas.tugas.download_my_submission');
});

// ==========================================
// PUBLIC AVATAR ROUTE (Shared Hosting / Fallback Support)
// ==========================================
Route::get('/storage/avatars/{filename}', function ($filename) {
    $path = 'avatars/' . $filename;
    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
        return \Illuminate\Support\Facades\Storage::disk('public')->response($path, null, [
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    $publicPath = public_path('storage/avatars/' . $filename);
    if (file_exists($publicPath)) {
        return response()->file($publicPath, [
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    abort(404);
})->where('filename', '[A-Za-z0-9_\-\.]+')->name('storage.avatar');

Route::get('/storage/alumni_photos/{filename}', function ($filename) {
    $path = 'alumni_photos/' . $filename;
    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
        return \Illuminate\Support\Facades\Storage::disk('public')->response($path, null, [
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    $publicPath = public_path('storage/alumni_photos/' . $filename);
    if (file_exists($publicPath)) {
        return response()->file($publicPath, [
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    abort(404);
})->where('filename', '[A-Za-z0-9_\-\.]+')->name('storage.alumni_photo');

// ==========================================
// ROLE 3: KEPALA KAMPUS (PEMANTAUAN SEKOLAH)
// ==========================================
Route::middleware(['auth', 'role:kepala_kampus'])->prefix('kepala-kampus')->name('kepala_kampus.')->group(function () {
    Route::get('/' . SecurePath::DASHBOARD, [PemantauController::class, 'dashboard'])->name('dashboard');
    Route::get('/' . SecurePath::SISWA, [PemantauController::class, 'students'])->name('siswa.index');
    Route::get('/' . SecurePath::SISWA . '/{id}', [PemantauController::class, 'studentDetail'])->name('siswa.show');
    Route::get('/' . SecurePath::EKSPOR, [PemantauController::class, 'exportIndex'])->name('ekspor.index');
    Route::get('/' . SecurePath::EKSPOR . '/' . SecurePath::EKSPOR_COUNT, [PemantauController::class, 'exportPreviewCount'])->name('ekspor.count');
    Route::get('/' . SecurePath::EKSPOR . '/' . SecurePath::EKSPOR_DOWNLOAD, [PemantauController::class, 'exportDownload'])->name('ekspor.download');
    Route::get('/' . SecurePath::LOG, [PemantauController::class, 'auditLogs'])->name('log.index');
    Route::get('/' . SecurePath::PROFIL, [PemantauController::class, 'profileShow'])->name('profil.show');
    Route::put('/' . SecurePath::PROFIL, [PemantauController::class, 'profileUpdate'])->name('profil.update');
    Route::post('/' . SecurePath::PROFIL . '/' . SecurePath::PROFIL_PHOTO, [PemantauController::class, 'updatePhoto'])->name('profil.photo.update');
    Route::delete('/' . SecurePath::PROFIL . '/' . SecurePath::PROFIL_PHOTO, [PemantauController::class, 'destroyPhoto'])->name('profil.photo.destroy');
});

// ==========================================
// ROLE 4: ADMIN DICO (PEMANTAUAN SEKOLAH)
// ==========================================
Route::middleware(['auth', 'role:admin_dico'])->prefix('admin-dico')->name('admin_dico.')->group(function () {
    Route::get('/' . SecurePath::DASHBOARD, [PemantauController::class, 'dashboard'])->name('dashboard');
    Route::get('/' . SecurePath::SISWA, [PemantauController::class, 'students'])->name('siswa.index');
    Route::get('/' . SecurePath::SISWA . '/{id}', [PemantauController::class, 'studentDetail'])->name('siswa.show');
    Route::get('/' . SecurePath::EKSPOR, [PemantauController::class, 'exportIndex'])->name('ekspor.index');
    Route::get('/' . SecurePath::EKSPOR . '/' . SecurePath::EKSPOR_COUNT, [PemantauController::class, 'exportPreviewCount'])->name('ekspor.count');
    Route::get('/' . SecurePath::EKSPOR . '/' . SecurePath::EKSPOR_DOWNLOAD, [PemantauController::class, 'exportDownload'])->name('ekspor.download');
    Route::get('/' . SecurePath::LOG, [PemantauController::class, 'auditLogs'])->name('log.index');
    Route::get('/' . SecurePath::PROFIL, [PemantauController::class, 'profileShow'])->name('profil.show');
    Route::put('/' . SecurePath::PROFIL, [PemantauController::class, 'profileUpdate'])->name('profil.update');
    Route::post('/' . SecurePath::PROFIL . '/' . SecurePath::PROFIL_PHOTO, [PemantauController::class, 'updatePhoto'])->name('profil.photo.update');
    Route::delete('/' . SecurePath::PROFIL . '/' . SecurePath::PROFIL_PHOTO, [PemantauController::class, 'destroyPhoto'])->name('profil.photo.destroy');
});


