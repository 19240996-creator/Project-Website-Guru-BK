<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Counseling;

class SystemVerificationTest extends TestCase
{
    public function test_login_page_is_accessible()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('SIM BK', false);
    }

    public function test_guru_bk_can_login_and_access_dashboard()
    {
        $guru = User::where('username', 'gurubk')->first();
        $this->assertNotNull($guru);

        $response = $this->actingAs($guru)->get('/guru/dashboard');
        $response->assertStatus(200);
        $response->assertSee('YANG PERLU SAYA KERJAKAN', false);
        $response->assertSee('Total Siswa Aktif', false);
    }

    public function test_guru_bk_can_access_student_360_profile()
    {
        $guru = User::where('username', 'gurubk')->first();
        $student = Student::first();

        $response = $this->actingAs($guru)->get("/guru/siswa/{$student->id}");
        $response->assertStatus(200);
        $response->assertSee('Profil Siswa 360 Derajat', false);
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
}
