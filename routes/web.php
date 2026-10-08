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

use App\Http\Controllers\Siswa\DashboardController as SiswaDashboardController;
use App\Http\Controllers\Siswa\ProfileController as SiswaProfileController;
use App\Http\Controllers\Siswa\CounselingController as SiswaCounselingController;
use App\Http\Controllers\Siswa\AssessmentController as SiswaAssessmentController;
use App\Http\Controllers\Siswa\FuturePlanController as SiswaFuturePlanController;
use App\Http\Controllers\Siswa\OpportunityController as SiswaOpportunityController;
use App\Http\Controllers\Siswa\AlumniController as SiswaAlumniController;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Informasi Bimbingan Konseling & Karier Siswa
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->role === 'guru_bk'
            ? redirect()->route('guru.dashboard')
            : redirect()->route('siswa.dashboard');
    }
    return redirect()->route('login');
});

// Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Shared Notifications (Authenticated users)
Route::middleware(['auth'])->group(function () {
    Route::get('/notifikasi', [NotificationController::class, 'index'])->name('notifikasi.index');
    Route::get('/notifikasi/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifikasi.read');
    Route::post('/notifikasi/read-all', [NotificationController::class, 'markAllRead'])->name('notifikasi.read_all');
});

// ==========================================
// ROLE 1: GURU BK
// ==========================================
Route::middleware(['auth', 'role:guru_bk'])->prefix('guru')->name('guru.')->group(function () {
    // 4. Beranda & "Yang Perlu Saya Kerjakan"
    Route::get('/dashboard', [GuruDashboardController::class, 'index'])->name('dashboard');

    // Profil Guru BK
    Route::get('/profil', [GuruProfileController::class, 'show'])->name('profil.show');
    Route::put('/profil', [GuruProfileController::class, 'update'])->name('profil.update');

    // 5. Data Siswa & Profil 360 Derajat
    Route::get('/siswa', [GuruStudentController::class, 'index'])->name('siswa.index');
    Route::get('/siswa/tambah', [GuruStudentController::class, 'create'])->name('siswa.create');
    Route::post('/siswa', [GuruStudentController::class, 'store'])->name('siswa.store');
    Route::post('/siswa/import', [GuruStudentController::class, 'import'])->name('siswa.import');
    Route::get('/siswa/template-impor', [GuruStudentController::class, 'downloadTemplate'])->name('siswa.template');
    Route::post('/siswa/kenaikan-kelas-massal', [GuruStudentController::class, 'massPromote'])->name('siswa.mass_promote');
    Route::get('/siswa/{id}', [GuruStudentController::class, 'show'])->name('siswa.show');
    Route::get('/siswa/{id}/edit', [GuruStudentController::class, 'edit'])->name('siswa.edit');
    Route::put('/siswa/{id}', [GuruStudentController::class, 'update'])->name('siswa.update');
    Route::delete('/siswa/{id}', [GuruStudentController::class, 'destroy'])->name('siswa.destroy');

    // 7 - 12. Bimbingan & Konseling
    Route::get('/konseling', [GuruCounselingController::class, 'index'])->name('konseling.index');
    Route::get('/konseling/{id}', [GuruCounselingController::class, 'show'])->name('konseling.show');
    Route::post('/konseling/{id}/schedule', [GuruCounselingController::class, 'schedule'])->name('konseling.schedule');
    Route::post('/konseling/{id}/notes', [GuruCounselingController::class, 'updateNotes'])->name('konseling.notes');
    Route::post('/konseling/{id}/follow-up', [GuruCounselingController::class, 'storeFollowUp'])->name('konseling.follow_up.store');
    Route::post('/konseling/follow-up/{followUpId}/status', [GuruCounselingController::class, 'updateFollowUp'])->name('konseling.follow_up.update');

    // 6. Asesmen Siswa
    Route::get('/asesmen', [GuruAssessmentController::class, 'index'])->name('asesmen.index');
    Route::get('/asesmen/{id}', [GuruAssessmentController::class, 'show'])->name('asesmen.show');
    Route::get('/asesmen/hasil/{id}', [GuruAssessmentController::class, 'showResult'])->name('asesmen.result');
    Route::post('/asesmen/hasil/{id}/update', [GuruAssessmentController::class, 'updateResult'])->name('asesmen.result.update');
    Route::post('/asesmen/{id}/pertanyaan', [GuruAssessmentController::class, 'storeQuestion'])->name('asesmen.pertanyaan.store');
    Route::put('/asesmen/{id}/pertanyaan/{questionId}', [GuruAssessmentController::class, 'updateQuestion'])->name('asesmen.pertanyaan.update');
    Route::delete('/asesmen/{id}/pertanyaan/{questionId}', [GuruAssessmentController::class, 'destroyQuestion'])->name('asesmen.pertanyaan.destroy');

    // 14 - 15. Peminatan & Rencana Masa Depan
    Route::get('/peminatan', [GuruFuturePlanController::class, 'index'])->name('peminatan.index');

    // 16 - 18. Perguruan Tinggi, Perusahaan & Relasi Mitra
    Route::get('/mitra', [GuruPartnerController::class, 'index'])->name('mitra.index');
    Route::post('/mitra', [GuruPartnerController::class, 'store'])->name('mitra.store');
    Route::get('/mitra/{id}', [GuruPartnerController::class, 'show'])->name('mitra.show');
    Route::get('/mitra-kegiatan', [GuruPartnerController::class, 'activities'])->name('mitra.activities');
    Route::post('/mitra-kegiatan', [GuruPartnerController::class, 'storeActivity'])->name('mitra.activity.store');
    Route::put('/mitra-kegiatan/{id}', [GuruPartnerController::class, 'updateActivity'])->name('mitra.activity.update');
    Route::delete('/mitra-kegiatan/{id}', [GuruPartnerController::class, 'destroyActivity'])->name('mitra.activity.destroy');

    // 19. Peluang Siswa
    Route::get('/peluang', [GuruOpportunityController::class, 'index'])->name('peluang.index');
    Route::post('/peluang', [GuruOpportunityController::class, 'store'])->name('peluang.store');
    Route::get('/peluang/{id}', [GuruOpportunityController::class, 'show'])->name('peluang.show');
    Route::post('/peluang/registrasi/{regId}/status', [GuruOpportunityController::class, 'updateRegistrationStatus'])->name('peluang.reg_status');

    // 23 - 25. Kelulusan & Alumni (Tracer Study)
    Route::get('/alumni', [GuruAlumniController::class, 'index'])->name('alumni.index');
    Route::post('/siswa/{studentId}/graduate', [GuruAlumniController::class, 'graduateStudent'])->name('alumni.graduate');
    Route::post('/alumni/{id}/toggle-showcase', [GuruAlumniController::class, 'toggleShowcase'])->name('alumni.toggle_showcase');

    // 26 - 30. Laporan (Paket A Kesiswaan, Paket B Kurikulum, Paket C Kepala Sekolah)
    Route::get('/laporan', [GuruReportController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/cetak', [GuruReportController::class, 'generate'])->name('laporan.generate');

    // 41. Audit Log
    Route::get('/audit', [GuruAuditLogController::class, 'index'])->name('audit.index');
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

    // 19 - 20. Peluang & Pendaftaran
    Route::get('/peluang', [SiswaOpportunityController::class, 'index'])->name('peluang.index');
    Route::get('/peluang/{id}', [SiswaOpportunityController::class, 'show'])->name('peluang.show');
    Route::post('/peluang/{id}/daftar', [SiswaOpportunityController::class, 'register'])->name('peluang.register');

    // 25. Jejak Alumni & Pembaruan Tracer Lulusan
    Route::get('/alumni', [SiswaAlumniController::class, 'index'])->name('alumni.index');
    Route::post('/alumni/tracer', [SiswaAlumniController::class, 'updateTracer'])->name('alumni.tracer.update');
});
