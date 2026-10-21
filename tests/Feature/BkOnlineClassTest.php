<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\AcademicYear;
use App\Models\CounselorAssignment;
use App\Models\BkClass;
use App\Models\BkClassMaterial;
use App\Models\BkClassTask;
use App\Models\BkClassTaskSubmission;
use App\Models\BkClassAttendance;
use App\Models\AuditLog;

class BkOnlineClassTest extends TestCase
{
    protected $counselor;
    protected $otherCounselor;
    protected $koordinator;
    protected $studentClassA;
    protected $studentClassB;
    protected $targetStudent;
    protected $nonTargetStudent;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $academicYear = AcademicYear::firstOrCreate(
            ['name' => '2026/2027'],
            ['semester' => 'ganjil', 'is_active' => true]
        );

        $this->counselor = User::firstOrCreate(
            ['username' => 'guru_pembina_a'],
            [
                'name' => 'Guru BK Pembina A',
                'email' => 'guru_pembina_a@test.sch.id',
                'password' => 'password123',
                'role' => 'guru_bk',
                'is_active' => true,
            ]
        );

        $this->otherCounselor = User::firstOrCreate(
            ['username' => 'guru_bk_lain'],
            [
                'name' => 'Guru BK Lain',
                'email' => 'guru_bk_lain@test.sch.id',
                'password' => 'password123',
                'role' => 'guru_bk',
                'is_active' => true,
            ]
        );

        $this->koordinator = User::firstOrCreate(
            ['username' => 'koordinator_test_bk'],
            [
                'name' => 'Koordinator BK Test',
                'email' => 'koordinator_test_bk@test.sch.id',
                'password' => 'password123',
                'role' => 'koordinator_bk',
                'is_active' => true,
            ]
        );

        $this->studentClassA = StudentClass::firstOrCreate(
            ['name' => 'X PPLG 1 Test'],
            [
                'academic_year_id' => $academicYear->id,
                'grade' => 'X',
                'major' => 'PPLG',
            ]
        );

        $this->studentClassB = StudentClass::firstOrCreate(
            ['name' => 'XI TKJ 1 Test'],
            [
                'academic_year_id' => $academicYear->id,
                'grade' => 'XI',
                'major' => 'TKJ',
            ]
        );

        // Siswa sasaran: Di kelas A dan ditugaskan ke Guru A
        $userStudentA = User::firstOrCreate(
            ['username' => 'siswa_target_test'],
            [
                'name' => 'Siswa Sasaran Test',
                'email' => 'siswa_target_test@test.sch.id',
                'password' => 'password123',
                'role' => 'siswa',
                'is_active' => true,
            ]
        );

        $this->targetStudent = Student::firstOrCreate(
            ['nisn' => '001001999'],
            [
                'user_id' => $userStudentA->id,
                'student_class_id' => $this->studentClassA->id,
                'nis' => '1001999',
                'name' => 'Siswa Sasaran Test',
                'gender' => 'L',
                'status' => 'aktif',
            ]
        );

        CounselorAssignment::firstOrCreate(
            [
                'counselor_id' => $this->counselor->id,
                'student_id' => $this->targetStudent->id,
            ],
            [
                'student_class_id' => $this->studentClassA->id,
                'assigned_by' => $this->koordinator->id,
            ]
        );

        // Siswa bukan sasaran: Di kelas B dan ditugaskan ke Guru Lain
        $userStudentB = User::firstOrCreate(
            ['username' => 'siswa_non_target_test'],
            [
                'name' => 'Siswa Bukan Sasaran Test',
                'email' => 'siswa_non_target_test@test.sch.id',
                'password' => 'password123',
                'role' => 'siswa',
                'is_active' => true,
            ]
        );

        $this->nonTargetStudent = Student::firstOrCreate(
            ['nisn' => '002001999'],
            [
                'user_id' => $userStudentB->id,
                'student_class_id' => $this->studentClassB->id,
                'nis' => '2001999',
                'name' => 'Siswa Bukan Sasaran Test',
                'gender' => 'P',
                'status' => 'aktif',
            ]
        );

        CounselorAssignment::firstOrCreate(
            [
                'counselor_id' => $this->otherCounselor->id,
                'student_id' => $this->nonTargetStudent->id,
            ],
            [
                'student_class_id' => $this->studentClassB->id,
                'assigned_by' => $this->koordinator->id,
            ]
        );
    }

    protected function tearDown(): void
    {
        // Clean up created classes and related rows
        BkClass::whereIn('counselor_id', [$this->counselor->id, $this->otherCounselor->id, $this->koordinator->id])->delete();
        parent::tearDown();
    }

    /**
     * Skenario 1: Guru BK berhasil membuat kelas dan mengatur jadwal serta presensi
     */
    public function test_guru_bk_can_create_class_and_set_schedule()
    {
        $response = $this->actingAs($this->counselor)->post(route('guru.kelas.store'), [
            'title' => 'Bimbingan Karier Kelas X Test',
            'description' => 'Materi persiapan eksplorasi perguruan tinggi dan industri.',
            'target_grade' => 'X',
            'target_major' => 'PPLG',
            'target_class_ids' => [$this->studentClassA->id],
            'class_date' => '2026-10-21',
            'start_time' => '08:00',
            'end_time' => '09:30',
            'attendance_start_time' => '07:55',
            'attendance_end_time' => '08:15',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bk_classes', [
            'title' => 'Bimbingan Karier Kelas X Test',
            'counselor_id' => $this->counselor->id,
            'class_date' => '2026-10-21 00:00:00',
        ]);
    }

    /**
     * Skenario 2: Siswa yang sesuai sasaran dapat melihat dan mengakses kelas tersebut
     */
    public function test_target_student_can_see_and_access_class()
    {
        $class = BkClass::create([
            'counselor_id' => $this->counselor->id,
            'title' => 'Kelas Eksplorasi Minat Test',
            'target_grade' => 'X',
            'target_major' => 'PPLG',
            'target_class_ids' => [$this->studentClassA->id],
            'class_date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'start_time' => '08:00',
            'end_time' => '09:30',
            'attendance_start_time' => '07:55',
            'attendance_end_time' => '08:15',
            'status' => 'terjadwal',
        ]);

        // Akses daftar kelas oleh siswa target
        $resIndex = $this->actingAs($this->targetStudent->user)->get(route('siswa.kelas.index'));
        $resIndex->assertStatus(200);
        $resIndex->assertSee('Kelas Eksplorasi Minat Test');

        // Akses detail kelas oleh siswa target
        $resShow = $this->actingAs($this->targetStudent->user)->get(route('siswa.kelas.show', $class->id));
        $resShow->assertStatus(200);
        $resShow->assertSee('Kelas Eksplorasi Minat Test');
    }

    /**
     * Skenario 3: Siswa di luar sasaran tidak dapat melihat atau mengakses kelas tersebut
     */
    public function test_non_target_student_cannot_see_or_access_class()
    {
        $class = BkClass::create([
            'counselor_id' => $this->counselor->id,
            'title' => 'Kelas Eksplorasi Minat Khusus A Test',
            'target_grade' => 'X',
            'target_major' => 'PPLG',
            'target_class_ids' => [$this->studentClassA->id],
            'class_date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'start_time' => '08:00',
            'end_time' => '09:30',
            'attendance_start_time' => '07:55',
            'attendance_end_time' => '08:15',
            'status' => 'terjadwal',
        ]);

        // Siswa non target membuka daftar kelas: tidak boleh melihat kelas ini
        $resIndex = $this->actingAs($this->nonTargetStudent->user)->get(route('siswa.kelas.index'));
        $resIndex->assertStatus(200);
        $resIndex->assertDontSee('Kelas Eksplorasi Minat Khusus A Test');

        // Siswa non target langsung tembak URL show: ditolak 403 Forbidden
        $resShow = $this->actingAs($this->nonTargetStudent->user)->get(route('siswa.kelas.show', $class->id));
        $resShow->assertStatus(403);
    }

    /**
     * Skenario 4: Materi slide, video, dan dokumen dapat diunggah dan diakses
     */
    public function test_materials_can_be_uploaded_and_downloaded()
    {
        $class = BkClass::create([
            'counselor_id' => $this->counselor->id,
            'title' => 'Kelas Materi Slide & Video Test',
            'target_class_ids' => [$this->studentClassA->id],
            'class_date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'start_time' => '08:00',
            'end_time' => '09:30',
            'attendance_start_time' => '07:55',
            'attendance_end_time' => '08:15',
        ]);

        $file = UploadedFile::fake()->create('slide_materi.pdf', 1024, 'application/pdf');

        $resUpload = $this->actingAs($this->counselor)->post(route('guru.kelas.materi.store', $class->id), [
            'title' => 'Slide Modul 1 Test',
            'description' => 'Materi utama dalam format PDF',
            'material_type' => 'slide',
            'file' => $file,
        ]);
        $resUpload->assertRedirect();

        $material = BkClassMaterial::where('bk_class_id', $class->id)->first();
        $this->assertNotNull($material);
        $this->assertEquals('Slide Modul 1 Test', $material->title);

        // Siswa target mengunduh materi
        $resDown = $this->actingAs($this->targetStudent->user)->get(route('siswa.kelas.materi.download', [
            'id' => $class->id,
            'materialId' => $material->id,
        ]));
        $resDown->assertStatus(200);

        // Siswa non target dilarang mengunduh
        $resDownForbidden = $this->actingAs($this->nonTargetStudent->user)->get(route('siswa.kelas.materi.download', [
            'id' => $class->id,
            'materialId' => $material->id,
        ]));
        $resDownForbidden->assertStatus(403);

        // Guru BK melihat tombol tambah materi dan modal pembuatan materi
        $resGuruShow = $this->actingAs($this->counselor)->get(route('guru.kelas.show', ['id' => $class->id, 'tab' => 'materi']));
        $resGuruShow->assertStatus(200);
        $resGuruShow->assertSee('Tambah Materi Pembelajaran');
        $resGuruShow->assertSee('modalCreateMaterial');
        $resGuruShow->assertSee('openCreateMaterialModal');
    }

    /**
     * Skenario 5: Presensi berhasil dilakukan ketika waktu absensi dibuka
     */
    public function test_attendance_succeeds_when_window_is_open()
    {
        $now = Carbon::now('Asia/Jakarta');
        $startTime = $now->copy()->subMinutes(5)->format('H:i');
        $endTime = $now->copy()->addMinutes(15)->format('H:i');

        $class = BkClass::create([
            'counselor_id' => $this->counselor->id,
            'title' => 'Kelas Jam Aktif Presensi Test',
            'target_class_ids' => [$this->studentClassA->id],
            'class_date' => $now->toDateString(),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'attendance_start_time' => $startTime,
            'attendance_end_time' => $endTime,
        ]);

        $res = $this->actingAs($this->targetStudent->user)->post(route('siswa.kelas.attendance.store', $class->id));
        $res->assertRedirect();
        $res->assertSessionHas('success');

        $this->assertDatabaseHas('bk_class_attendances', [
            'bk_class_id' => $class->id,
            'student_id' => $this->targetStudent->id,
            'status' => 'hadir',
        ]);
    }

    /**
     * Skenario 6: Presensi ditolak sebelum waktu dibuka dan setelah waktu ditutup
     */
    public function test_attendance_rejected_before_opening_and_after_closing()
    {
        $now = Carbon::now('Asia/Jakarta');

        // Kasus A: Sebelum waktu dibuka
        $futureStart = $now->copy()->addMinutes(10)->format('H:i');
        $futureEnd = $now->copy()->addMinutes(30)->format('H:i');

        $classEarly = BkClass::create([
            'counselor_id' => $this->counselor->id,
            'title' => 'Kelas Belum Buka Absen Test',
            'target_class_ids' => [$this->studentClassA->id],
            'class_date' => $now->toDateString(),
            'start_time' => $futureStart,
            'end_time' => $futureEnd,
            'attendance_start_time' => $futureStart,
            'attendance_end_time' => $futureEnd,
        ]);

        $resEarly = $this->actingAs($this->targetStudent->user)->post(route('siswa.kelas.attendance.store', $classEarly->id));
        $resEarly->assertRedirect();
        $resEarly->assertSessionHas('error');
        $this->assertDatabaseMissing('bk_class_attendances', [
            'bk_class_id' => $classEarly->id,
            'student_id' => $this->targetStudent->id,
        ]);

        // Kasus B: Setelah waktu ditutup
        $pastStart = $now->copy()->subMinutes(40)->format('H:i');
        $pastEnd = $now->copy()->subMinutes(10)->format('H:i');

        $classLate = BkClass::create([
            'counselor_id' => $this->counselor->id,
            'title' => 'Kelas Sudah Tutup Absen Test',
            'target_class_ids' => [$this->studentClassA->id],
            'class_date' => $now->toDateString(),
            'start_time' => $pastStart,
            'end_time' => $pastEnd,
            'attendance_start_time' => $pastStart,
            'attendance_end_time' => $pastEnd,
        ]);

        $resLate = $this->actingAs($this->targetStudent->user)->post(route('siswa.kelas.attendance.store', $classLate->id));
        $resLate->assertRedirect();
        $resLate->assertSessionHas('error');
        $this->assertDatabaseMissing('bk_class_attendances', [
            'bk_class_id' => $classLate->id,
            'student_id' => $this->targetStudent->id,
        ]);
    }

    /**
     * Skenario 7: Siswa tidak dapat melakukan presensi dua kali untuk kelas yang sama
     */
    public function test_student_cannot_attend_twice()
    {
        $now = Carbon::now('Asia/Jakarta');
        $startTime = $now->copy()->subMinutes(5)->format('H:i');
        $endTime = $now->copy()->addMinutes(15)->format('H:i');

        $class = BkClass::create([
            'counselor_id' => $this->counselor->id,
            'title' => 'Kelas Cek Absen Ganda Test',
            'target_class_ids' => [$this->studentClassA->id],
            'class_date' => $now->toDateString(),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'attendance_start_time' => $startTime,
            'attendance_end_time' => $endTime,
        ]);

        // Absensi pertama sukses
        $this->actingAs($this->targetStudent->user)->post(route('siswa.kelas.attendance.store', $class->id));

        // Absensi kedua
        $resSecond = $this->actingAs($this->targetStudent->user)->post(route('siswa.kelas.attendance.store', $class->id));
        $resSecond->assertRedirect();
        $resSecond->assertSessionHas('info');

        $this->assertEquals(1, BkClassAttendance::where('bk_class_id', $class->id)->where('student_id', $this->targetStudent->id)->count());
    }

    /**
     * Skenario 8: Siswa yang terlambat absen tetap dapat mengakses materi dan mengumpulkan tugas
     */
    public function test_student_with_missed_attendance_can_still_submit_tasks()
    {
        $now = Carbon::now('Asia/Jakarta');
        $pastStart = $now->copy()->subHours(2)->format('H:i');
        $pastEnd = $now->copy()->subHour()->format('H:i');

        $class = BkClass::create([
            'counselor_id' => $this->counselor->id,
            'title' => 'Kelas Absen Ditutup Tapi Tugas Buka Test',
            'target_class_ids' => [$this->studentClassA->id],
            'class_date' => $now->toDateString(),
            'start_time' => $pastStart,
            'end_time' => $pastEnd,
            'attendance_start_time' => $pastStart,
            'attendance_end_time' => $pastEnd,
        ]);

        // Tugas dibuka sampai besok
        $task = BkClassTask::create([
            'bk_class_id' => $class->id,
            'title' => 'Tugas Refleksi Diri Test',
            'instructions' => 'Jawab pertanyaan refleksi.',
            'start_time' => $now->copy()->subHours(2),
            'due_time' => $now->copy()->addDay(),
        ]);

        // Siswa tidak memiliki presensi (alpa/terlambat), tetapi mengirim tugas
        $resTask = $this->actingAs($this->targetStudent->user)->post(route('siswa.kelas.tugas.submit', [
            'id' => $class->id,
            'taskId' => $task->id,
        ]), [
            'submission_text' => 'Ini jawaban refleksi saya meskipun terlambat absen.',
        ]);

        $resTask->assertRedirect();
        $resTask->assertSessionHas('success');

        $this->assertDatabaseHas('bk_class_task_submissions', [
            'bk_class_task_id' => $task->id,
            'student_id' => $this->targetStudent->id,
            'status' => 'diserahkan',
        ]);
    }

    /**
     * Skenario 9: Guru BK dapat melihat rekap presensi dan menilai pengumpulan tugas
     */
    public function test_guru_bk_can_view_recap_and_grade_task()
    {
        $class = BkClass::create([
            'counselor_id' => $this->counselor->id,
            'title' => 'Kelas Penilaian Test',
            'target_class_ids' => [$this->studentClassA->id],
            'class_date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'start_time' => '08:00',
            'end_time' => '09:30',
            'attendance_start_time' => '07:55',
            'attendance_end_time' => '08:15',
        ]);

        $task = BkClassTask::create([
            'bk_class_id' => $class->id,
            'title' => 'Tugas Pemahaman Test',
            'instructions' => 'Kerjakan tugas.',
            'start_time' => Carbon::now('Asia/Jakarta')->subHours(1),
            'due_time' => Carbon::now('Asia/Jakarta')->addDays(2),
        ]);

        $submission = BkClassTaskSubmission::create([
            'bk_class_task_id' => $task->id,
            'student_id' => $this->targetStudent->id,
            'submission_text' => 'Jawaban pemahaman',
            'submitted_at' => Carbon::now('Asia/Jakarta'),
            'status' => 'diserahkan',
        ]);

        // Guru BK menilai tugas
        $resGrade = $this->actingAs($this->counselor)->post(route('guru.kelas.tugas.grade', [
            'id' => $class->id,
            'submissionId' => $submission->id,
        ]), [
            'score' => 95,
            'status' => 'dinilai',
            'counselor_feedback' => 'Sangat baik dan terstruktur.',
        ]);

        $resGrade->assertRedirect();
        $this->assertDatabaseHas('bk_class_task_submissions', [
            'id' => $submission->id,
            'score' => 95,
            'status' => 'dinilai',
        ]);
    }

    /**
     * Skenario 10: Manipulasi URL atau Guru BK lain tidak dapat mengelola kelas yang bukan miliknya
     */
    public function test_counselor_isolation_enforced()
    {
        $class = BkClass::create([
            'counselor_id' => $this->counselor->id,
            'title' => 'Kelas Guru A Test',
            'target_class_ids' => [$this->studentClassA->id],
            'class_date' => '2026-10-21',
            'start_time' => '08:00',
            'end_time' => '09:30',
            'attendance_start_time' => '07:55',
            'attendance_end_time' => '08:15',
        ]);

        // Guru lain mencoba mengakses detail kelas Guru A: 403 Forbidden
        $res = $this->actingAs($this->otherCounselor)->get(route('guru.kelas.show', $class->id));
        $res->assertStatus(403);
    }

    /**
     * Skenario 11: Koreksi presensi manual oleh Guru BK berhasil dengan pencatatan alasan resmi
     */
    public function test_guru_bk_can_correct_attendance_manually_with_reason()
    {
        $class = BkClass::create([
            'counselor_id' => $this->counselor->id,
            'title' => 'Kelas Koreksi Manual Test',
            'target_class_ids' => [$this->studentClassA->id],
            'class_date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'start_time' => '08:00',
            'end_time' => '09:30',
            'attendance_start_time' => '07:55',
            'attendance_end_time' => '08:15',
        ]);

        // Guru BK mengoreksi status presensi siswa menjadi 'izin'
        $resCorrect = $this->actingAs($this->counselor)->post(route('guru.kelas.presensi.correct', $class->id), [
            'student_id' => $this->targetStudent->id,
            'status' => 'izin',
            'correction_reason' => 'Siswa mengikuti lomba mewakili sekolah tingkat kabupaten.',
        ]);

        $resCorrect->assertRedirect();
        $this->assertDatabaseHas('bk_class_attendances', [
            'bk_class_id' => $class->id,
            'student_id' => $this->targetStudent->id,
            'status' => 'izin',
            'is_manual_correction' => true,
            'correction_reason' => 'Siswa mengikuti lomba mewakili sekolah tingkat kabupaten.',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'UPDATE',
            'entity_type' => 'BkClassAttendance',
        ]);
    }

    /**
     * Skenario 12: Guru BK dapat mengubah dan mengurutkan materi pembelajaran
     */
    public function test_guru_bk_can_update_and_reorder_materials()
    {
        $class = BkClass::create([
            'counselor_id' => $this->counselor->id,
            'title' => 'Kelas Reorder Test',
            'target_class_ids' => [$this->studentClassA->id],
            'class_date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'start_time' => '08:00',
            'end_time' => '09:30',
            'attendance_start_time' => '07:55',
            'attendance_end_time' => '08:15',
        ]);

        $mat1 = BkClassMaterial::create([
            'bk_class_id' => $class->id,
            'title' => 'Materi Pertama',
            'material_type' => 'slide',
            'order_num' => 1,
        ]);

        $mat2 = BkClassMaterial::create([
            'bk_class_id' => $class->id,
            'title' => 'Materi Kedua',
            'material_type' => 'video',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'order_num' => 2,
        ]);

        // 1. Edit materi 1
        $resUpdate = $this->actingAs($this->counselor)->put(route('guru.kelas.materi.update', [
            'id' => $class->id,
            'materialId' => $mat1->id,
        ]), [
            'title' => 'Materi Pertama Diperbarui',
            'description' => 'Deskripsi baru materi 1',
            'material_type' => 'slide',
        ]);
        $resUpdate->assertRedirect();
        $this->assertDatabaseHas('bk_class_materials', [
            'id' => $mat1->id,
            'title' => 'Materi Pertama Diperbarui',
        ]);

        // 2. Reorder materi 2 ke atas (direction = up)
        $resReorder = $this->actingAs($this->counselor)->post(route('guru.kelas.materi.reorder', [
            'id' => $class->id,
            'materialId' => $mat2->id,
        ]), [
            'direction' => 'up',
        ]);
        $resReorder->assertRedirect();

        $mat1->refresh();
        $mat2->refresh();
        $this->assertTrue($mat2->order_num < $mat1->order_num);
    }

    /**
     * Skenario 13: Guru BK dapat mengubah penugasan pembelajaran
     */
    public function test_guru_bk_can_update_task()
    {
        $class = BkClass::create([
            'counselor_id' => $this->counselor->id,
            'title' => 'Kelas Tugas Update Test',
            'target_class_ids' => [$this->studentClassA->id],
            'class_date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'start_time' => '08:00',
            'end_time' => '09:30',
            'attendance_start_time' => '07:55',
            'attendance_end_time' => '08:15',
        ]);

        $task = BkClassTask::create([
            'bk_class_id' => $class->id,
            'title' => 'Tugas Awal',
            'instructions' => 'Instruksi awal',
            'start_time' => Carbon::now('Asia/Jakarta'),
            'due_time' => Carbon::now('Asia/Jakarta')->addDays(2),
        ]);

        $resUpdate = $this->actingAs($this->counselor)->put(route('guru.kelas.tugas.update', [
            'id' => $class->id,
            'taskId' => $task->id,
        ]), [
            'title' => 'Tugas Revisi Perpanjangan Waktu',
            'instructions' => 'Instruksi yang sudah diperbarui dengan format PDF',
            'start_time' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s'),
            'due_time' => Carbon::now('Asia/Jakarta')->addDays(5)->format('Y-m-d H:i:s'),
            'allowed_file_types' => 'pdf',
        ]);

        $resUpdate->assertRedirect();
        $this->assertDatabaseHas('bk_class_tasks', [
            'id' => $task->id,
            'title' => 'Tugas Revisi Perpanjangan Waktu',
            'allowed_file_types' => 'pdf',
        ]);
    }

    /**
     * Skenario 14: Guru BK dapat melihat file/foto jawaban tugas siswa secara inline
     */
    public function test_guru_bk_can_view_submission_file_inline()
    {
        $class = BkClass::create([
            'counselor_id' => $this->counselor->id,
            'title' => 'Kelas Pratinjau Berkas',
            'target_class_ids' => [$this->studentClassA->id],
            'class_date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'start_time' => '08:00',
            'end_time' => '09:30',
            'attendance_start_time' => '07:55',
            'attendance_end_time' => '08:15',
        ]);

        $task = BkClassTask::create([
            'bk_class_id' => $class->id,
            'title' => 'Tugas Mengunggah Foto Refleksi',
            'instructions' => 'Kirim foto lembar kerja LKPD',
            'start_time' => Carbon::now('Asia/Jakarta')->subHours(1),
            'due_time' => Carbon::now('Asia/Jakarta')->addDays(1),
        ]);

        $file = UploadedFile::fake()->image('lembar_refleksi.jpg');
        $filePath = $file->store('bk_classes/submissions', 'public');

        $submission = BkClassTaskSubmission::create([
            'bk_class_task_id' => $task->id,
            'student_id' => $this->targetStudent->id,
            'file_path' => $filePath,
            'file_name' => 'lembar_refleksi.jpg',
            'file_size' => 1024,
            'submission_text' => 'Berikut foto lembar refleksi saya bu guru.',
            'submitted_at' => Carbon::now('Asia/Jakarta'),
            'status' => 'diserahkan',
        ]);

        $res = $this->actingAs($this->counselor)->get(route('guru.kelas.tugas.view_submission', [
            'id' => $class->id,
            'submissionId' => $submission->id,
        ]));

        $res->assertStatus(200);
        $this->assertStringContainsString('inline', $res->headers->get('Content-Disposition') ?? '');
    }

    /**
     * Skenario 15: Guru BK dapat memberikan penilaian tugas secara manual baik daring maupun luring
     */
    public function test_guru_bk_can_grade_student_manually()
    {
        $class = BkClass::create([
            'counselor_id' => $this->counselor->id,
            'title' => 'Kelas Penilaian Manual',
            'target_class_ids' => [$this->studentClassA->id],
            'class_date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'start_time' => '08:00',
            'end_time' => '09:30',
            'attendance_start_time' => '07:55',
            'attendance_end_time' => '08:15',
        ]);

        $task = BkClassTask::create([
            'bk_class_id' => $class->id,
            'title' => 'Tugas Refleksi Manual',
            'instructions' => 'Kerjakan refleksi',
            'start_time' => Carbon::now('Asia/Jakarta')->subHours(1),
            'due_time' => Carbon::now('Asia/Jakarta')->addDays(1),
        ]);

        // 1. Berikan nilai secara manual untuk siswa yang belum mengumpulkan online
        $resManual = $this->actingAs($this->counselor)->post(route('guru.kelas.tugas.grade_student', [
            'id' => $class->id,
            'taskId' => $task->id,
        ]), [
            'student_id' => $this->targetStudent->id,
            'score' => 92,
            'status' => 'dinilai',
            'counselor_feedback' => 'Refleksi tatap muka sangat baik dan menunjukkan pemahaman mendalam.',
        ]);

        $resManual->assertRedirect(route('guru.kelas.show', ['id' => $class->id, 'tab' => 'tugas']));
        $this->assertDatabaseHas('bk_class_task_submissions', [
            'bk_class_task_id' => $task->id,
            'student_id' => $this->targetStudent->id,
            'score' => 92,
            'status' => 'dinilai',
            'counselor_feedback' => 'Refleksi tatap muka sangat baik dan menunjukkan pemahaman mendalam.',
        ]);

        // 2. Berikan opsi revisi dengan catatan bimbingan
        $resRevisi = $this->actingAs($this->counselor)->post(route('guru.kelas.tugas.grade_student', [
            'id' => $class->id,
            'taskId' => $task->id,
        ]), [
            'student_id' => $this->targetStudent->id,
            'score' => 70,
            'status' => 'perlu_revisi',
            'counselor_feedback' => 'Tolong lengkapi poin tindakan nyata di masa depan.',
        ]);

        $resRevisi->assertRedirect();
        $this->assertDatabaseHas('bk_class_task_submissions', [
            'bk_class_task_id' => $task->id,
            'student_id' => $this->targetStudent->id,
            'score' => 70,
            'status' => 'perlu_revisi',
        ]);
    }

    /**
     * Skenario 16: Siswa dapat mengumpulkan tugas saat sesi kelas aktif meskipun start_time penugasan di masa depan beberapa menit
     */
    public function test_student_can_submit_task_in_active_class_even_if_task_start_time_in_future()
    {
        $now = Carbon::now('Asia/Jakarta');

        $class = BkClass::create([
            'counselor_id' => $this->counselor->id,
            'title' => 'Kelas Masa Depan Aktif',
            'target_class_ids' => [$this->studentClassA->id],
            'class_date' => $now->toDateString(),
            'start_time' => $now->copy()->subMinutes(10)->format('H:i'),
            'end_time' => $now->copy()->addMinutes(50)->format('H:i'),
            'attendance_start_time' => $now->copy()->subMinutes(10)->format('H:i'),
            'attendance_end_time' => $now->copy()->addMinutes(50)->format('H:i'),
        ]);

        // Tugas dengan start_time 15 menit ke depan (misal kasus guru mengeset waktu 17:01 saat kelas mulai 16:50)
        $task = BkClassTask::create([
            'bk_class_id' => $class->id,
            'title' => 'Tugas Kelas Sedang Berlangsung',
            'instructions' => 'Tuliskan refleksi di kertas atau teks',
            'start_time' => $now->copy()->addMinutes(15),
            'due_time' => $now->copy()->addHours(2),
        ]);

        $this->assertTrue($task->isOpen());

        $res = $this->actingAs($this->targetStudent->user)->post(route('siswa.kelas.tugas.submit', [
            'id' => $class->id,
            'taskId' => $task->id,
        ]), [
            'submission_text' => 'Jawaban siswa saat kelas sedang berlangsung.',
        ]);

        $res->assertRedirect();
        $res->assertSessionHas('success');
        $this->assertDatabaseHas('bk_class_task_submissions', [
            'bk_class_task_id' => $task->id,
            'student_id' => $this->targetStudent->id,
            'status' => 'diserahkan',
        ]);
    }

    /**
     * Skenario 17: Siswa melihat pemberitahuan terjadwal jika kelas di masa depan dan tugas belum buka
     */
    public function test_student_future_class_task_shows_notice_and_rejects_with_exact_time()
    {
        $futureDate = Carbon::now('Asia/Jakarta')->addDays(5);

        $futureClass = BkClass::create([
            'counselor_id' => $this->counselor->id,
            'title' => 'Kelas Minggu Depan',
            'target_class_ids' => [$this->studentClassA->id],
            'class_date' => $futureDate->toDateString(),
            'start_time' => '09:00',
            'end_time' => '10:30',
            'attendance_start_time' => '08:55',
            'attendance_end_time' => '09:15',
        ]);

        $futureTask = BkClassTask::create([
            'bk_class_id' => $futureClass->id,
            'title' => 'Tugas Minggu Depan',
            'instructions' => 'Instruksi minggu depan',
            'start_time' => $futureDate->copy()->setTime(9, 0),
            'due_time' => $futureDate->copy()->setTime(23, 59),
        ]);

        $this->assertFalse($futureTask->isOpen());

        // Halaman siswa menampilkan keterangan belum dibuka
        $resView = $this->actingAs($this->targetStudent->user)->get(route('siswa.kelas.show', $futureClass->id));
        $resView->assertStatus(200);
        $resView->assertSee('Waktu Pengerjaan Tugas Belum Dibuka');

        // Submit sebelum dibuka ditolak dengan pesan waktu yang ramah
        $resSubmit = $this->actingAs($this->targetStudent->user)->post(route('siswa.kelas.tugas.submit', [
            'id' => $futureClass->id,
            'taskId' => $futureTask->id,
        ]), [
            'submission_text' => 'Coba kirim tugas lebih awal.',
        ]);

        $resSubmit->assertRedirect();
        $resSubmit->assertSessionHas('error');
    }

    public function test_teacher_sees_student_submission_status_and_time()
    {
        $class = BkClass::create([
            'counselor_id' => $this->counselor->id,
            'title' => 'Masa Depan',
            'target_class_ids' => [$this->studentClassA->id],
            'class_date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'start_time' => '16:50',
            'end_time' => '18:00',
            'attendance_start_time' => '16:50',
            'attendance_end_time' => '18:00',
        ]);

        $task = BkClassTask::create([
            'bk_class_id' => $class->id,
            'title' => 'masa depan',
            'instructions' => 'kerjakan di kertas selembar',
            'start_time' => Carbon::now('Asia/Jakarta')->subHours(2),
            'due_time' => Carbon::now('Asia/Jakarta')->subMinute(),
        ]);

        $resSubmit = $this->actingAs($this->targetStudent->user)->post(route('siswa.kelas.tugas.submit', [
            'id' => $class->id,
            'taskId' => $task->id,
        ]), [
            'submission_text' => 'Sudah saya kerjakan',
        ]);
        $resSubmit->assertSessionHas('success');

        // Teacher visits
        $resGuru = $this->actingAs($this->counselor)->get(route('guru.kelas.show', [
            'id' => $class->id,
            'tab' => 'tugas',
        ]));
        $resGuru->assertStatus(200);
        $resGuru->assertSee('1 Siswa Mengumpulkan');
        $resGuru->assertDontSee('Belum kumpul');
        $resGuru->assertSee('searchStudentTask_' . $task->id);
        $resGuru->assertSee('filterTaskStudents(' . $task->id);
        $resGuru->assertSee('student-task-row-' . $task->id);
        $resGuru->assertSee('Cari nama atau NISN siswa...');

        $resAbsensi = $this->actingAs($this->counselor)->get(route('guru.kelas.show', [
            'id' => $class->id,
            'tab' => 'absensi',
        ]));
        $resAbsensi->assertStatus(200);
        $resAbsensi->assertSee('searchAttendanceStudent');
        $resAbsensi->assertSee('filterAttendanceStudents');
        $resAbsensi->assertSee('attendance-student-row');
    }
}
