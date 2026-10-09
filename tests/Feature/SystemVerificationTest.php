<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\Counseling;
use Illuminate\Http\UploadedFile;

class SystemVerificationTest extends TestCase
{
    public function test_login_page_is_accessible()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('SIM BK', false);
        $response->assertSee('Username', false);
        $response->assertSee('Password', false);
        $response->assertSee('Masukkan username', false);
        $response->assertSee('Masukkan password', false);
    }

    public function test_guru_bk_can_login_and_access_dashboard()
    {
        $guru = User::where('username', 'gurubk')->first();
        $this->assertNotNull($guru);

        $response = $this->actingAs($guru)->get('/guru/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Selamat Bertugas', false);
        $response->assertSee($guru->name, false);
        $response->assertSee('YANG PERLU SAYA KERJAKAN', false);
        $response->assertSee('Total Siswa Aktif', false);
        $response->assertSee(route('guru.profil.show'), false);
        $response->assertSee('counselingCategoryColumnChart', false);
        $response->assertSee('futurePlanPieChart', false);
        $response->assertSee('Distribusi Kategori Layanan Konseling', false);
        $response->assertSee('kasus', false);
        $response->assertSee('Belajar', false);
        $response->assertSee('Pribadi', false);
        $response->assertSee('Sosial', false);
        $response->assertSee('Keluarga', false);
        $response->assertSee('Karier', false);
        $response->assertSee('Peta Rencana Masa Depan Kelas XII', false);
        $response->assertSee('<svg viewBox="0 0 140 140"', false);
        $response->assertSee('Target Kuliah', false);
        $response->assertSee('Target Bekerja', false);
        $response->assertSee('Target Wirausaha', false);
        $response->assertSee('Belum Menentukan', false);
        $response->assertSee('5', false);
    }

    public function test_guru_bk_can_access_and_update_profile()
    {
        $guru = User::where('username', 'gurubk')->first();

        // 1. Can view profile page
        $response = $this->actingAs($guru)->get('/guru/profil');
        $response->assertStatus(200);
        $response->assertSee('Profil Guru Bimbingan Konseling', false);
        $response->assertSee($guru->name, false);
        $response->assertSee($guru->email, false);
        $response->assertSee('Aktif Bertugas', false);

        // 2. Can update profile information
        $updateResponse = $this->actingAs($guru)->put('/guru/profil', [
            'name' => 'Dra. Endang Sri Rahayu, M.Pd.',
            'email' => 'guru@sekolah.sch.id',
            'phone' => '081299998888',
        ]);
        $updateResponse->assertRedirect();
        $updateResponse->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $guru->id,
            'phone' => '081299998888',
        ]);
    }

    public function test_guru_bk_can_access_student_360_profile()
    {
        $guru = User::where('username', 'gurubk')->first();
        $student = Student::first();

        $response = $this->actingAs($guru)->get("/guru/siswa/{$student->id}");
        $response->assertStatus(200);
        $response->assertSee('Profil Siswa', false);
        $response->assertSee($student->name, false);
    }

    public function test_guru_bk_can_view_confidential_counseling_notes()
    {
        $guru = User::where('username', 'gurubk')->first();
        $counseling = Counseling::first();

        $response = $this->actingAs($guru)->get("/guru/konseling/{$counseling->id}");
        $response->assertStatus(200);
        $response->assertSee('Catatan Internal Konseling', false);
        $response->assertSee($counseling->code, false);
    }

    public function test_guru_bk_reports_paket_a_b_c_generate_correctly()
    {
        $guru = User::where('username', 'gurubk')->first();

        $resA = $this->actingAs($guru)->get('/guru/laporan/cetak?package=paket_a');
        $resA->assertStatus(200);
        $resA->assertSee('LAPORAN PERKEMBANGAN', false);

        $resB = $this->actingAs($guru)->get('/guru/laporan/cetak?package=paket_b');
        $resB->assertStatus(200);
        $resB->assertSee('LAPORAN JADWAL', false);

        $resC = $this->actingAs($guru)->get('/guru/laporan/cetak?package=paket_c');
        $resC->assertStatus(200);
        $resC->assertSee('LAPORAN EKSEKUTIF KEPALA SEKOLAH', false);
    }

    public function test_siswa_can_login_and_access_dashboard()
    {
        $siswaUser = User::where('username', '0071234561')->first();
        $this->assertNotNull($siswaUser);

        $response = $this->actingAs($siswaUser)->get('/siswa/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Ruang BK', false);
        $response->assertSee('Rencana Masa Depan Saya', false);
    }

    public function test_siswa_cannot_access_guru_routes()
    {
        $siswaUser = User::where('username', '0071234561')->first();

        $response = $this->actingAs($siswaUser)->get('/guru/dashboard');
        $response->assertRedirect('/siswa/dashboard');
    }

    public function test_siswa_cannot_view_confidential_counselor_notes()
    {
        $siswaUser = User::where('username', '0071234561')->first();
        $counseling = Counseling::where('student_id', $siswaUser->student->id)->first();

        if ($counseling) {
            $response = $this->actingAs($siswaUser)->get("/siswa/konseling/{$counseling->id}");
            $response->assertStatus(200);
            $response->assertDontSee('Catatan Internal Konseling', false);
            $response->assertDontSee('Catatan internal:', false);
        }
    }

    public function test_siswa_can_view_future_plan_with_4_options_and_no_ragu_option()
    {
        $siswaUser = User::where('username', '0071234561')->first();

        $response = $this->actingAs($siswaUser)->get('/siswa/rencana-masa-depan');
        $response->assertStatus(200);
        $response->assertSee('1. Ingin Kuliah', false);
        $response->assertSee('2. Ingin Bekerja', false);
        $response->assertSee('3. Ingin Kuliah Sambil Bekerja', false);
        $response->assertSee('4. Ingin Wirausaha', false);
        $response->assertDontSee('Saya masih ragu dan belum menentukan pilihan pasti.', false);
        $response->assertDontSee('Bingung Menentukan Pilihan?', false);
    }

    public function test_siswa_can_submit_kuliah_sambil_bekerja_plan()
    {
        $siswaUser = User::where('username', '0071234561')->first();

        $response = $this->actingAs($siswaUser)->post('/siswa/rencana-masa-depan', [
            'primary_goal' => 'kuliah_kerja',
            'college_target_kk' => 'Universitas Terbuka',
            'study_program_kk' => 'Sistem Informasi',
            'entry_path_kk' => 'Kuliah Daring / Hybrid',
            'work_target_field_kk' => 'Junior Web Developer',
            'work_target_company_kk' => 'Software House Nusantara',
            'notes' => 'Ingin mandiri membiayai kuliah sambil mengasah kemampuan teknis di industri.',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('student_future_plans', [
            'student_id' => $siswaUser->student->id,
            'primary_goal' => 'kuliah_kerja',
            'college_target' => 'Universitas Terbuka',
            'study_program' => 'Sistem Informasi',
            'work_target_field' => 'Junior Web Developer',
            'work_target_company' => 'Software House Nusantara',
        ]);
    }

    public function test_siswa_can_access_assessment_take_page_without_sql_error()
    {
        $siswaUser = User::where('username', '0071234561')->first();

        $response = $this->actingAs($siswaUser)->get('/siswa/asesmen/1/kerjakan');
        $response->assertStatus(200);
        $response->assertSee('Pertanyaan 1 dari', false);
    }

    public function test_siswa_can_view_assessment_result_page()
    {
        $siswaUser = User::where('username', '0071234561')->first();

        $response = $this->actingAs($siswaUser)->get('/siswa/asesmen/hasil/1');
        $response->assertStatus(200);
        $response->assertSee('Hasil Pemetaan Asesmen Diri', false);
        $response->assertSee('Isi Ulang Asesmen', false);
    }

    public function test_guru_bk_can_import_students_with_grade_and_major()
    {
        $guru = User::where('username', 'gurubk')->first();

        // 1. Check form shows X, XI, XII and major controls
        $indexResponse = $this->actingAs($guru)->get('/guru/siswa');
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Pilihan Kelas', false);
        $indexResponse->assertSee('<option value="X">Kelas X</option>', false);
        $indexResponse->assertSee('<option value="XI">Kelas XI</option>', false);
        $indexResponse->assertSee('<option value="XII">Kelas XII</option>', false);
        $indexResponse->assertSee('Klik untuk memasukkan jurusan manual', false);

        // 2. Perform import with grade and manual custom major
        $csvContent = "NIS,NISN,Nama Lengkap,Jenis Kelamin,No. HP\n25261099,0081234599,Bintang Pratama,L,081234567899\n";
        $file = UploadedFile::fake()->createWithContent('siswa_baru.csv', $csvContent);

        $importResponse = $this->actingAs($guru)->post('/guru/siswa/import', [
            'grade' => 'X',
            'custom_major' => 'Desain Komunikasi Visual',
            'csv_file' => $file,
        ]);

        $importResponse->assertRedirect(route('guru.siswa.index'));
        $importResponse->assertSessionHas('success');

        // Verify class was created and student imported
        $this->assertDatabaseHas('student_classes', [
            'grade' => 'X',
            'major' => 'Desain Komunikasi Visual',
        ]);

        $this->assertDatabaseHas('students', [
            'nis' => '25261099',
            'nisn' => '0081234599',
            'name' => 'Bintang Pratama',
        ]);
    }

    public function test_guru_bk_can_download_student_import_template()
    {
        $guru = User::where('username', 'gurubk')->first();

        // 1. Template download link is visible on student index
        $indexResponse = $this->actingAs($guru)->get('/guru/siswa');
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee(route('guru.siswa.template'), false);
        $indexResponse->assertSee('Unduh Template CSV', false);

        // 2. Download template file response
        $templateResponse = $this->actingAs($guru)->get('/guru/siswa/template-impor');
        $templateResponse->assertStatus(200);
        $templateResponse->assertHeader('Content-Disposition', 'attachment; filename="template_impor_siswa.csv"');

        // 3. Verify content has CSV headers and sample data
        $content = $templateResponse->streamedContent();
        $this->assertStringContainsString('NIS,NISN', $content);
        $this->assertStringContainsString('Nama Lengkap', $content);
        $this->assertStringContainsString('Jenis Kelamin (L/P)', $content);
        $this->assertStringContainsString('Aditya Pratama', $content);
        $this->assertStringContainsString('Nabila Putri Cahyani', $content);
    }

    public function test_guru_bk_student_list_has_active_pagination(): void
    {
        $guru = User::where('username', 'gurubk')->first();

        // 1. Visit student index with default per_page=5
        $response = $this->actingAs($guru)->get('/guru/siswa?per_page=5');
        $response->assertStatus(200);
        $response->assertSee('pagination-container', false);
        $response->assertSee('pagination-list', false);
        $response->assertSee('Menampilkan', false);
        $response->assertSee('data siswa', false);
        $response->assertSee('Baris per Halaman', false);
        $response->assertSee('?per_page=5&amp;page=2', false);

        // 2. Navigate to page 2
        $page2Response = $this->actingAs($guru)->get('/guru/siswa?per_page=5&page=2');
        $page2Response->assertStatus(200);
        $page2Response->assertSee('pagination-container', false);
        $page2Response->assertSee('?per_page=5&amp;page=1', false);
    }

    public function test_guru_bk_can_mass_promote_students_to_next_class()
    {
        $guru = User::where('username', 'gurubk')->first();
        // Ensure active students exist for the test
        Student::where('status', '!=', 'aktif')->update(['status' => 'aktif']);
        $students = Student::where('status', 'aktif')->take(2)->get();
        $targetClass = StudentClass::where('id', '!=', $students->first()->student_class_id)->first();

        // 1. Visit edit page with tab mass
        $response = $this->actingAs($guru)->get("/guru/siswa/{$students->first()->id}/edit?tab=mass");
        $response->assertStatus(200);
        $response->assertSee('Fitur Kenaikan Kelas & Kelulusan Alumni', false);
        $response->assertSee('Kelas 10', false);
        $response->assertSee('Kelas 11', false);
        $response->assertSee('Kelas 12', false);
        $response->assertSee('Alumni', false);

        // 2. Perform mass promotion post using target_class = 11
        $postResponse = $this->actingAs($guru)->post('/guru/siswa/kenaikan-kelas-massal', [
            'student_ids' => $students->pluck('id')->toArray(),
            'target_class' => '11',
        ]);

        $postResponse->assertSessionHas('success');

        // 3. Verify students were updated to grade XI
        foreach ($students as $s) {
            $this->assertEquals('XI', $s->fresh()->studentClass->grade);
        }

        // 4. Test mass promotion to Alumni
        $alumniResponse = $this->actingAs($guru)->post('/guru/siswa/kenaikan-kelas-massal', [
            'student_ids' => [$students->first()->id],
            'target_class' => 'alumni',
        ]);
        $alumniResponse->assertSessionHas('success');
        $this->assertEquals('lulus', $students->first()->fresh()->status);
        $this->assertNotNull($students->first()->fresh()->alumniTracking);
    }

    public function test_guru_bk_can_customize_assessment_questions_and_point_values(): void
    {
        $guru = User::where('role', 'guru_bk')->first();
        $assessment = \App\Models\Assessment::first();

        // 1. Visit assessment detail page and see button & question structure
        $response = $this->actingAs($guru)->get("/guru/asesmen/{$assessment->id}");
        $response->assertStatus(200);
        $response->assertSee('Tambah Butir Pertanyaan', false);
        $response->assertSee('Poin', false);

        // 2. Add custom question with point-based options (no right/wrong answer)
        $storeResponse = $this->actingAs($guru)->post("/guru/asesmen/{$assessment->id}/pertanyaan", [
            'question_text' => 'Bagaimana gaya Anda saat menyelesaikan proyek baru?',
            'options' => [
                ['option_text' => 'Merakit dan mencoba langsung dengan alat', 'score_value' => 5, 'dimension_code' => 'R'],
                ['option_text' => 'Menganalisis dan membaca dokumentasi teknis', 'score_value' => 4, 'dimension_code' => 'I'],
                ['option_text' => 'Mendesain tata letak visual agar menarik', 'score_value' => 3, 'dimension_code' => 'A'],
                ['option_text' => 'Mendiskusikan pembagian peran bersama tim', 'score_value' => 2, 'dimension_code' => 'S'],
            ],
        ]);
        $storeResponse->assertRedirect("/guru/asesmen/{$assessment->id}");
        $storeResponse->assertSessionHas('success');

        $newQuestion = \App\Models\AssessmentQuestion::where('assessment_id', $assessment->id)
            ->where('question_text', 'Bagaimana gaya Anda saat menyelesaikan proyek baru?')
            ->first();
        $this->assertNotNull($newQuestion);
        $this->assertEquals(4, $newQuestion->options()->count());
        $this->assertEquals(5, $newQuestion->options()->first()->score_value);

        // 3. Update the custom question & change points
        $updateResponse = $this->actingAs($guru)->put("/guru/asesmen/{$assessment->id}/pertanyaan/{$newQuestion->id}", [
            'question_text' => 'Ketika menghadapi tantangan baru di sekolah, Anda cenderung:',
            'options' => [
                ['option_text' => 'Sangat suka mencoba langsung', 'score_value' => 4, 'dimension_code' => 'R'],
                ['option_text' => 'Menganalisis akar penyebabnya', 'score_value' => 3, 'dimension_code' => 'I'],
            ],
        ]);
        $updateResponse->assertRedirect("/guru/asesmen/{$assessment->id}");
        $updateResponse->assertSessionHas('success');
        $this->assertEquals('Ketika menghadapi tantangan baru di sekolah, Anda cenderung:', $newQuestion->fresh()->question_text);
        $this->assertEquals(2, $newQuestion->fresh()->options()->count());

        // 4. Delete the custom question
        $deleteResponse = $this->actingAs($guru)->delete("/guru/asesmen/{$assessment->id}/pertanyaan/{$newQuestion->id}");
        $deleteResponse->assertRedirect("/guru/asesmen/{$assessment->id}");
        $deleteResponse->assertSessionHas('success');
        $this->assertNull(\App\Models\AssessmentQuestion::find($newQuestion->id));
    }

    public function test_guru_bk_can_schedule_partner_activity_with_multiple_target_classes(): void
    {
        $guru = User::where('role', 'guru_bk')->first();
        $partner = \App\Models\Partner::first();
        $classes = \App\Models\StudentClass::take(2)->get();

        // 1. Visit activities page and see multiple checkboxes
        $response = $this->actingAs($guru)->get('/guru/mitra-kegiatan');
        $response->assertStatus(200);
        $response->assertSee('target_class_ids[]', false);
        $response->assertSee('Semua', false);
        $response->assertSee('Kosongkan', false);

        $suffix = uniqid();
        $testDate = '2029-10-' . str_pad((string) (rand(1, 28)), 2, '0', STR_PAD_LEFT);
        \App\Models\PartnerActivity::where('date', $testDate)->delete();
        $postResponse = $this->actingAs($guru)->post('/guru/mitra-kegiatan', [
            'partner_id' => $partner->id,
            'title' => 'Workshop Kolaborasi Multi Kelas ' . $suffix,
            'activity_type' => 'seminar',
            'date' => $testDate,
            'start_time' => '09:00',
            'end_time' => '11:00',
            'room_location' => 'Auditorium ' . $suffix,
            'target_class_ids' => [$classes[0]->id, $classes[1]->id],
            'max_participants' => 80,
            'status' => 'terkonfirmasi',
        ]);

        $postResponse->assertSessionHas('success');

        $act = \App\Models\PartnerActivity::where('title', 'Workshop Kolaborasi Multi Kelas ' . $suffix)->first();
        $this->assertNotNull($act);
        $this->assertIsArray($act->target_class_ids);
        $this->assertCount(2, $act->target_class_ids);
        $this->assertContains($classes[0]->id, $act->target_class_ids);
        $this->assertContains($classes[1]->id, $act->target_class_ids);
    }

    public function test_guru_bk_can_edit_and_delete_partner_activity_with_audit_logs(): void
    {
        $guru = User::where('role', 'guru_bk')->first();
        $partner = \App\Models\Partner::first();
        $classes = \App\Models\StudentClass::take(2)->get();

        // 1. Create activity to edit
        $suffix = uniqid();
        $testDate = '2030-05-' . str_pad((string) (rand(1, 28)), 2, '0', STR_PAD_LEFT);
        $act = \App\Models\PartnerActivity::create([
            'partner_id' => $partner->id,
            'code' => 'KGT-2030-' . rand(1000, 9999),
            'title' => 'Kegiatan Uji Coba Edit ' . $suffix,
            'activity_type' => 'sosialisasi',
            'date' => $testDate,
            'start_time' => '10:00',
            'end_time' => '12:00',
            'room_location' => 'Lab Komputer ' . $suffix,
            'target_class_ids' => [$classes[0]->id],
            'max_participants' => 50,
            'pic_name' => $guru->name,
            'status' => 'rencana',
            'notes' => 'Catatan awal',
        ]);

        // 2. Perform PUT update
        $updatedTitle = 'Kegiatan Terupdate ' . $suffix;
        $responseUpdate = $this->actingAs($guru)->put("/guru/mitra-kegiatan/{$act->id}", [
            'partner_id' => $partner->id,
            'title' => $updatedTitle,
            'activity_type' => 'seminar',
            'date' => $testDate,
            'start_time' => '10:00',
            'end_time' => '12:30',
            'room_location' => 'Aula Utama ' . $suffix,
            'target_class_ids' => [$classes[0]->id, $classes[1]->id],
            'max_participants' => 75,
            'pic_name' => $guru->name,
            'status' => 'terkonfirmasi',
            'notes' => 'Catatan diperbarui',
        ]);

        $responseUpdate->assertSessionHas('success');

        // Verify updated record in DB
        $act->refresh();
        $this->assertEquals($updatedTitle, $act->title);
        $this->assertEquals('seminar', $act->activity_type);
        $this->assertEquals(75, $act->max_participants);

        // Verify Audit Log for PERBARUI
        $updateLog = \App\Models\AuditLog::where('action', 'PERBARUI')
            ->where('entity_type', 'PartnerActivity')
            ->where('entity_id', $act->id)
            ->latest()
            ->first();

        $this->assertNotNull($updateLog);
        $this->assertStringContainsString($updatedTitle, $updateLog->description);

        // 3. Perform DELETE
        $responseDelete = $this->actingAs($guru)->delete("/guru/mitra-kegiatan/{$act->id}");
        $responseDelete->assertSessionHas('success');

        // Verify deleted from DB
        $this->assertNull(\App\Models\PartnerActivity::find($act->id));

        // Verify Audit Log for HAPUS
        $deleteLog = \App\Models\AuditLog::where('action', 'HAPUS')
            ->where('entity_type', 'PartnerActivity')
            ->where('entity_id', $act->id)
            ->latest()
            ->first();

        $this->assertNotNull($deleteLog);
        $this->assertStringContainsString($updatedTitle, $deleteLog->description);

        // 4. Verify Audit Log index page displays the logs
        $auditPageResponse = $this->actingAs($guru)->get('/guru/audit');
        $auditPageResponse->assertStatus(200);
        $auditPageResponse->assertSee($updatedTitle, false);
    }

    public function test_guru_bk_can_edit_and_delete_alumni_tracking()
    {
        $guru = User::where('username', 'gurubk')->first();
        $this->assertNotNull($guru);

        // 1. Alumni index page displays action buttons and modals
        $indexResponse = $this->actingAs($guru)->get('/guru/alumni');
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Ubah', false);
        $indexResponse->assertSee('Hapus', false);
        $indexResponse->assertSee('editAlumniModal', false);
        $indexResponse->assertSee('deleteAlumniModal', false);

        // Get an existing alumni tracking record or find one
        $tracking = \App\Models\AlumniTracking::first();
        $this->assertNotNull($tracking);

        // 2. Perform UPDATE on alumni tracking
        $updatedCompany = 'PT Teknologi Nusantara Global';
        $updatedPosition = 'Software Engineer Junior';

        $responseUpdate = $this->actingAs($guru)->put("/guru/alumni/{$tracking->id}", [
            'graduation_year' => $tracking->graduation_year,
            'tracking_period' => '12_bulan',
            'current_status' => 'bekerja',
            'institution_or_company' => $updatedCompany,
            'major_or_position' => $updatedPosition,
            'monthly_income_range' => '5_sd_10_juta',
            'notes' => 'Catatan pelacakan diperbarui oleh konselor',
            'allow_public_showcase' => '1',
        ]);

        $responseUpdate->assertRedirect();
        $responseUpdate->assertSessionHas('success');

        $tracking->refresh();
        $this->assertEquals('bekerja', $tracking->current_status);
        $this->assertEquals('12_bulan', $tracking->tracking_period);
        $this->assertEquals($updatedCompany, $tracking->institution_or_company);
        $this->assertEquals($updatedPosition, $tracking->major_or_position);
        $this->assertTrue((bool)$tracking->allow_public_showcase);

        // Verify Audit Log for UPDATE_ALUMNI
        $updateLog = \App\Models\AuditLog::where('action', 'UPDATE_ALUMNI')
            ->where('entity_id', $tracking->id)
            ->latest()
            ->first();
        $this->assertNotNull($updateLog);
        $this->assertStringContainsString($tracking->code, $updateLog->description);

        // 3. Create a temporary tracking record to test DELETE cleanly
        $student = Student::whereDoesntHave('alumniTracking')->first();
        if (!$student) {
            $student = Student::latest()->first();
        }

        $tempTracking = \App\Models\AlumniTracking::create([
            'student_id' => $student->id,
            'code' => 'ALS-TEST-' . rand(1000, 9999),
            'graduation_year' => 2026,
            'tracking_period' => '6_bulan',
            'current_status' => 'belum_terlacak',
        ]);

        $deleteId = $tempTracking->id;
        $deleteCode = $tempTracking->code;

        $responseDelete = $this->actingAs($guru)->delete("/guru/alumni/{$deleteId}");
        $responseDelete->assertRedirect();
        $responseDelete->assertSessionHas('success');

        $this->assertNull(\App\Models\AlumniTracking::find($deleteId));

        // Verify Audit Log for DELETE_ALUMNI
        $deleteLog = \App\Models\AuditLog::where('action', 'DELETE_ALUMNI')
            ->where('entity_id', $deleteId)
            ->latest()
            ->first();
        $this->assertNotNull($deleteLog);
        $this->assertStringContainsString($deleteCode, $deleteLog->description);
    }

    public function test_guru_bk_must_schedule_before_filling_confidential_notes()
    {
        $guru = User::where('username', 'gurubk')->first();
        $this->assertNotNull($guru);

        // Find or create unscheduled counseling session
        $counseling = Counseling::whereNull('scheduled_date')->first();
        if (!$counseling) {
            $student = Student::first();
            $counseling = Counseling::create([
                'code' => 'KSL-TEST-' . rand(1000, 9999),
                'student_id' => $student->id,
                'topic' => 'Tes Konseling Belum Terjadwal',
                'story' => 'Deskripsi konsultasi siswa untuk pengujian alur wajib jadwal.',
                'urgency' => 'sedang',
                'status' => 'diajukan',
                'confidential_level' => 'rahasia',
            ]);
        }

        // 1. Visit show page and verify warnings & locked state are displayed
        $responseShow = $this->actingAs($guru)->get("/guru/konseling/{$counseling->id}");
        $responseShow->assertStatus(200);
        $responseShow->assertSee('Peringatan: Jadwal Sesi Pertemuan Belum Ditetapkan!', false);
        $responseShow->assertSee('Formulir Catatan Terkunci Sementara', false);
        $responseShow->assertSee('scheduleRequiredModal', false);

        // 2. Try to submit internal notes without scheduling - must be rejected with warning
        $attemptNotes = $this->actingAs($guru)->post("/guru/konseling/{$counseling->id}/notes", [
            'status' => 'dilaksanakan',
            'confidential_level' => 'rahasia',
            'counselor_notes' => 'Catatan sebelum ada jadwal - tidak boleh tersimpan.',
        ]);

        $attemptNotes->assertRedirect();
        $attemptNotes->assertSessionHas('warning');

        $counseling->refresh();
        $this->assertNotEquals('Catatan sebelum ada jadwal - tidak boleh tersimpan.', $counseling->counselor_notes);

        // 3. Now schedule the counseling session
        $scheduleResponse = $this->actingAs($guru)->post("/guru/konseling/{$counseling->id}/schedule", [
            'scheduled_date' => date('Y-m-d'),
            'scheduled_time' => '10:30',
            'scheduled_location' => 'Ruang Konseling BK 1',
        ]);
        $scheduleResponse->assertRedirect();
        $scheduleResponse->assertSessionHas('success');

        $counseling->refresh();
        $this->assertNotNull($counseling->scheduled_date);
        $this->assertEquals('dijadwalkan', $counseling->status);

        // 4. Now internal notes can be submitted successfully
        $validNotes = $this->actingAs($guru)->post("/guru/konseling/{$counseling->id}/notes", [
            'status' => 'dilaksanakan',
            'confidential_level' => 'rahasia',
            'counselor_notes' => 'Catatan setelah jadwal ditetapkan berhasil tersimpan.',
        ]);
        $validNotes->assertRedirect();
        $validNotes->assertSessionHas('success');

        $counseling->refresh();
        $this->assertEquals('Catatan setelah jadwal ditetapkan berhasil tersimpan.', $counseling->counselor_notes);
        $this->assertEquals('dilaksanakan', $counseling->status);
    }

    public function test_guru_bk_can_create_new_assessment_and_search_results(): void
    {
        $guru = User::where('role', 'guru_bk')->first();

        // 1. Check assessment index page has create button & search input
        $indexResponse = $this->actingAs($guru)->get('/guru/asesmen');
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Buat Asesmen Baru', false);
        $indexResponse->assertSee('Cari Siswa / NISN / Kelas', false);

        // 2. Create a new custom assessment
        $createResponse = $this->actingAs($guru)->post('/guru/asesmen', [
            'title' => 'Asesmen Gaya Belajar VAK Uji Coba',
            'category' => 'Gaya Belajar',
            'description' => 'Instrumen untuk memetakan kecenderungan gaya belajar visual, auditori, dan kinestetik siswa.',
            'instructions' => 'Jawab dengan jujur sesuai kebiasaan belajar Anda sehari-hari.',
        ]);

        $created = \App\Models\Assessment::where('title', 'Asesmen Gaya Belajar VAK Uji Coba')->first();
        $this->assertNotNull($created);
        $createResponse->assertRedirect("/guru/asesmen/{$created->id}");
        $createResponse->assertSessionHas('success');

        // 3. Search and filter results
        $searchResponse = $this->actingAs($guru)->get('/guru/asesmen?q=Bintang&per_page=5');
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('Hasil Pengerjaan Asesmen Siswa Terbaru', false);

        // 4. Delete the empty custom assessment
        $deleteResponse = $this->actingAs($guru)->delete("/guru/asesmen/{$created->id}");
        $deleteResponse->assertRedirect('/guru/asesmen');
        $deleteResponse->assertSessionHas('success');
        $this->assertNull(\App\Models\Assessment::find($created->id));
    }

    public function test_guru_bk_can_filter_alumni_with_horizontal_filters(): void
    {
        $guru = User::where('role', 'guru_bk')->first();

        // 1. Visit alumni page without filter
        $response = $this->actingAs($guru)->get('/guru/alumni');
        $response->assertStatus(200);
        $response->assertSee('Cari Alumni / Instansi / NISN', false);
        $response->assertSee('Status Terkini', false);
        $response->assertSee('Tahun Lulus', false);
        $response->assertSee('Jurusan', false);
        $response->assertSee('Periode Pelacakan', false);

        // 2. Filter by status
        $responseStatus = $this->actingAs($guru)->get('/guru/alumni?status=bekerja');
        $responseStatus->assertStatus(200);
        $responseStatus->assertSee('Reset Filter', false);

        // 3. Filter by query
        $responseQuery = $this->actingAs($guru)->get('/guru/alumni?q=Fajar');
        $responseQuery->assertStatus(200);
        $responseQuery->assertSee('Fajar Nugraha', false);
    }

    public function test_guru_bk_can_filter_audit_logs_with_horizontal_filters(): void
    {
        $guru = User::where('role', 'guru_bk')->first();

        // 1. Visit audit page without filter
        $response = $this->actingAs($guru)->get('/guru/audit');
        $response->assertStatus(200);
        $response->assertSee('Cari Deskripsi / IP / Pelaksana', false);
        $response->assertSee('Kategori Aksi', false);
        $response->assertSee('Entitas Data', false);
        $response->assertSee('Tanggal Aktivitas', false);

        // 2. Filter by action
        $responseAction = $this->actingAs($guru)->get('/guru/audit?action=SIMPAN');
        $responseAction->assertStatus(200);
        $responseAction->assertSee('Reset Filter', false);

        // 3. Filter by query
        $responseQuery = $this->actingAs($guru)->get('/guru/audit?q=konseling');
        $responseQuery->assertStatus(200);
    }
}

