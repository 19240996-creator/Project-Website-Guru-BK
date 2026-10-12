<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\AcademicYear;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Services\AvatarService;

class ProfilePhotoManagementTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_default_avatar_url_rendered_when_avatar_is_null()
    {
        $guru = new User(['role' => 'guru_bk', 'avatar' => null]);
        $this->assertStringContainsString('images/default-avatar-guru.svg', $guru->avatar_url);
        $this->assertFalse($guru->hasCustomAvatar());

        $siswa = new Student(['avatar' => null]);
        $this->assertStringContainsString('images/default-avatar-student.svg', $siswa->avatar_url);
        $this->assertFalse($siswa->hasCustomAvatar());
    }

    public function test_guru_bk_can_upload_and_delete_own_photo()
    {
        $guru = User::where('role', 'guru_bk')->first();
        if (!$guru) {
            $guru = User::create([
                'name' => 'Guru Test BK',
                'username' => 'gurutest',
                'email' => 'gurutest@sekolah.sch.id',
                'password' => bcrypt('password123'),
                'role' => 'guru_bk',
                'is_active' => true,
            ]);
        }

        // Test base64 cropped upload
        $dummyBase64 = 'data:image/png;base64,' . base64_encode(UploadedFile::fake()->image('avatar.png', 100, 100)->getContent());

        $response = $this->actingAs($guru)
            ->postJson(route('guru.profil.photo.update'), [
                'cropped_image' => $dummyBase64,
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $guru->refresh();
        $this->assertNotNull($guru->avatar);
        $this->assertTrue(Storage::disk('public')->exists($guru->avatar));
        $this->assertTrue($guru->hasCustomAvatar());

        // Test delete photo
        $deleteResponse = $this->actingAs($guru)
            ->deleteJson(route('guru.profil.photo.destroy'));

        $deleteResponse->assertStatus(200);
        $deleteResponse->assertJson(['success' => true]);

        $guru->refresh();
        $this->assertNull($guru->avatar);
        $this->assertFalse($guru->hasCustomAvatar());
        $this->assertStringContainsString('images/default-avatar-guru.svg', $guru->avatar_url);
    }

    public function test_siswa_can_upload_and_delete_own_photo()
    {
        $student = Student::with('user')->first();
        if (!$student || !$student->user) {
            $user = User::create([
                'name' => 'Siswa Test Photo',
                'username' => '0098765432',
                'email' => '0098765432@siswa.sch.id',
                'password' => bcrypt('0098765432'),
                'role' => 'siswa',
                'is_active' => true,
            ]);
            $student = Student::create([
                'user_id' => $user->id,
                'nis' => '12345678',
                'nisn' => '0098765432',
                'name' => 'Siswa Test Photo',
                'gender' => 'L',
                'attention_level' => 'normal',
                'status' => 'aktif',
            ]);
        }

        $dummyBase64 = 'data:image/jpeg;base64,' . base64_encode(UploadedFile::fake()->image('profile.jpg', 120, 120)->getContent());

        $response = $this->actingAs($student->user)
            ->postJson(route('siswa.profil.photo.update'), [
                'cropped_image' => $dummyBase64,
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $student->refresh();
        $student->user->refresh();

        $this->assertNotNull($student->avatar);
        $this->assertEquals($student->avatar, $student->user->avatar);
        $this->assertTrue(Storage::disk('public')->exists($student->avatar));

        // Test delete photo
        $deleteResponse = $this->actingAs($student->user)
            ->deleteJson(route('siswa.profil.photo.destroy'));

        $deleteResponse->assertStatus(200);
        $student->refresh();
        $student->user->refresh();

        $this->assertNull($student->avatar);
        $this->assertNull($student->user->avatar);
    }

    public function test_guru_bk_can_manage_student_photo()
    {
        $guru = User::where('role', 'guru_bk')->first();
        $student = Student::first();

        $dummyBase64 = 'data:image/webp;base64,' . base64_encode(UploadedFile::fake()->image('siswa.webp', 100, 100)->getContent());

        // Guru uploads student photo
        $response = $this->actingAs($guru)
            ->postJson(route('guru.siswa.photo.update', $student->id), [
                'cropped_image' => $dummyBase64,
            ]);

        $response->assertStatus(200);
        $student->refresh();
        $this->assertNotNull($student->avatar);
        $this->assertTrue(Storage::disk('public')->exists($student->avatar));

        // Guru deletes student photo
        $deleteResponse = $this->actingAs($guru)
            ->deleteJson(route('guru.siswa.photo.destroy', $student->id));

        $deleteResponse->assertStatus(200);
        $student->refresh();
        $this->assertNull($student->avatar);
    }

    public function test_photo_validation_rejects_oversized_and_invalid_formats()
    {
        $guru = User::where('role', 'guru_bk')->first();

        // Test file too big (> 5MB)
        $bigFile = UploadedFile::fake()->create('large.jpg', 5500); // 5.5 MB

        $response = $this->actingAs($guru)
            ->postJson(route('guru.profil.photo.update'), [
                'photo' => $bigFile,
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['photo']);

        // Test invalid format (e.g. text/pdf)
        $pdfFile = UploadedFile::fake()->create('document.pdf', 500, 'application/pdf');

        $pdfResponse = $this->actingAs($guru)
            ->postJson(route('guru.profil.photo.update'), [
                'photo' => $pdfFile,
            ]);

        $pdfResponse->assertStatus(422);
        $pdfResponse->assertJsonValidationErrors(['photo']);
    }

    public function test_guru_can_create_student_with_photo()
    {
        $guru = User::where('role', 'guru_bk')->first();
        $class = StudentClass::first();

        $dummyBase64 = 'data:image/png;base64,' . base64_encode(UploadedFile::fake()->image('newstudent.png', 100, 100)->getContent());

        $nisn = '00' . rand(10000000, 99999999);
        $nis = 'NIS' . rand(1000, 9999);

        $response = $this->actingAs($guru)
            ->post(route('guru.siswa.store'), [
                'nis' => $nis,
                'nisn' => $nisn,
                'name' => 'Siswa Baru Foto',
                'gender' => 'L',
                'student_class_id' => $class ? $class->id : 1,
                'attention_level' => 'normal',
                'cropped_image' => $dummyBase64,
            ]);

        $response->assertRedirect(route('guru.siswa.index'));

        $newStudent = Student::where('nisn', $nisn)->first();
        $this->assertNotNull($newStudent);
        $this->assertNotNull($newStudent->avatar);
        $this->assertTrue(Storage::disk('public')->exists($newStudent->avatar));
        $this->assertTrue($newStudent->hasCustomAvatar());

        $this->assertNotNull($newStudent->user);
        $this->assertEquals($newStudent->avatar, $newStudent->user->avatar);
    }

    public function test_guru_can_create_student_without_photo_defaults_to_null()
    {
        $guru = User::where('role', 'guru_bk')->first();
        $class = StudentClass::first();

        $nisn = '00' . rand(10000000, 99999999);
        $nis = 'NIS' . rand(1000, 9999);

        $response = $this->actingAs($guru)
            ->post(route('guru.siswa.store'), [
                'nis' => $nis,
                'nisn' => $nisn,
                'name' => 'Siswa Baru Default',
                'gender' => 'P',
                'student_class_id' => $class ? $class->id : 1,
                'attention_level' => 'normal',
            ]);

        $response->assertRedirect(route('guru.siswa.index'));

        $newStudent = Student::where('nisn', $nisn)->first();
        $this->assertNotNull($newStudent);
        $this->assertNull($newStudent->avatar);
        $this->assertFalse($newStudent->hasCustomAvatar());
        $this->assertStringContainsString('images/default-avatar-student.svg', $newStudent->avatar_url);

        $this->assertNotNull($newStudent->user);
        $this->assertNull($newStudent->user->avatar);
    }

    public function test_siswa_dashboard_shows_default_avatar_when_student_has_no_photo()
    {
        $student = Student::whereNull('avatar')->first();
        $this->assertNotNull($student);
        $this->assertNotNull($student->user);

        $response = $this->actingAs($student->user)->get(route('siswa.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('images/default-avatar-student.svg', false);
        $response->assertDontSee('student-hero.jpg', false);
    }

    public function test_student_login_with_registered_nisn_and_registered_phone()
    {
        $student = Student::whereNotNull('phone')->whereNotNull('nisn')->first();
        $this->assertNotNull($student);

        // Logout first
        $this->post('/logout');

        // Login with registered NISN and registered phone
        $response = $this->post('/login', [
            'login' => $student->nisn,
            'password' => $student->phone,
        ]);

        $response->assertRedirect(route('siswa.dashboard'));
        $this->assertAuthenticatedAs($student->user);
    }

    public function test_student_cannot_login_with_unregistered_nisn()
    {
        $this->post('/logout');

        $response = $this->post('/login', [
            'login' => '9999999999', // unregistered NISN
            'password' => '081234567899',
        ]);

        $response->assertSessionHasErrors(['login']);
        $this->assertGuest();
    }

    public function test_student_cannot_login_with_wrong_phone_password()
    {
        $student = Student::first();
        $this->assertNotNull($student);

        $this->post('/logout');

        $response = $this->post('/login', [
            'login' => $student->nisn,
            'password' => '089999999999', // wrong phone number
        ]);

        $response->assertSessionHasErrors(['login']);
        $this->assertGuest();
    }

    public function test_public_avatar_route_serves_stored_file()
    {
        Storage::disk('public')->put('avatars/test_unit_avatar.jpg', 'avatar_binary_content');

        $response = $this->get('/storage/avatars/test_unit_avatar.jpg');
        $response->assertStatus(200);

        $notFound = $this->get('/storage/avatars/non_existent_avatar.jpg');
        $notFound->assertStatus(404);
    }
}
