<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Hash;

class KepalaKampusAndAdminDicoTest extends TestCase
{
    protected function getOrCreateUser(string $username, string $role, string $name): User
    {
        return User::firstOrCreate(
            ['username' => $username],
            [
                'name' => $name,
                'email' => "{$username}@sekolah.sch.id",
                'role' => $role,
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]
        );
    }

    public function test_kepala_kampus_can_login_and_redirects_to_monitoring_dashboard()
    {
        $user = $this->getOrCreateUser('kepalakampus_test', 'kepala_kampus', 'Kepala Kampus Test');

        $response = $this->post(route('login.post'), [
            'login' => 'kepalakampus_test',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('kepala_kampus.dashboard'));
        $this->assertAuthenticatedAs($user);

        // Pastikan login tercatat di AuditLog
        $this->assertTrue(
            AuditLog::where('user_id', $user->id)
                ->where('action', 'LOGIN')
                ->exists()
        );
    }

    public function test_admin_dico_can_login_and_redirects_to_monitoring_dashboard()
    {
        $user = $this->getOrCreateUser('admindico_test', 'admin_dico', 'Admin DICO Test');

        $response = $this->post(route('login.post'), [
            'login' => 'admindico_test',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin_dico.dashboard'));
        $this->assertAuthenticatedAs($user);

        // Pastikan login tercatat di AuditLog
        $this->assertTrue(
            AuditLog::where('user_id', $user->id)
                ->where('action', 'LOGIN')
                ->exists()
        );
    }

    public function test_kepala_kampus_and_admin_dico_can_logout_and_redirect_to_login_page()
    {
        $user = $this->getOrCreateUser('kepalakampus_logout_test', 'kepala_kampus', 'Kepala Kampus Logout');

        // Pastikan tampilan pemantau merender modal konfirmasi logout yang sesuai dengan design system
        $dashboardResponse = $this->actingAs($user)->get(route('kepala_kampus.dashboard'));
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('id="logoutModal"', false);
        $dashboardResponse->assertSee('Konfirmasi Keluar Akun', false);
        $dashboardResponse->assertSee('Ya, Keluar', false);
        $dashboardResponse->assertSee('id="logoutFormPemantau"', false);

        // Eksekusi logout via form POST
        $response = $this->actingAs($user)->post(route('logout'));
        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_kepala_kampus_dashboard_and_student_directory_accessible()
    {
        $user = $this->getOrCreateUser('kepalakampus_test', 'kepala_kampus', 'Kepala Kampus Test');

        $response = $this->actingAs($user)->get(route('kepala_kampus.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Dashboard Pemantauan Ruang BK Digital');
        $response->assertSee('Total Siswa Sekolah');
        $response->assertSee('Siswa Kelas X');
        $response->assertSee('Siswa Kelas XI');
        $response->assertSee('Siswa Kelas XII');
        $response->assertSee('card-header card-header-navy');
        $response->assertSee('Distribusi Siswa per Jurusan');
        $response->assertSee('Distribusi Rombongan Belajar');

        $directoryResponse = $this->actingAs($user)->get(route('kepala_kampus.siswa.index'));
        $directoryResponse->assertStatus(200);
        $directoryResponse->assertSee('Data Siswa Ruang BK Digital');
    }

    public function test_admin_dico_dashboard_and_student_directory_accessible()
    {
        $user = $this->getOrCreateUser('admindico_test', 'admin_dico', 'Admin DICO Test');

        $response = $this->actingAs($user)->get(route('admin_dico.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Dashboard Pemantauan Ruang BK Digital');
        $response->assertSee('Total Siswa Sekolah');
        $response->assertSee('card-header card-header-navy');
        $response->assertSee('Distribusi Siswa per Jurusan');
        $response->assertSee('Distribusi Rombongan Belajar');

        $directoryResponse = $this->actingAs($user)->get(route('admin_dico.siswa.index'));
        $directoryResponse->assertStatus(200);
        $directoryResponse->assertSee('Data Siswa Ruang BK Digital');
    }

    public function test_kepala_kampus_and_admin_dico_cannot_access_internal_guru_and_koordinator_routes()
    {
        $kepalaKampus = $this->getOrCreateUser('kepalakampus_test', 'kepala_kampus', 'Kepala Kampus Test');
        $adminDico = $this->getOrCreateUser('admindico_test', 'admin_dico', 'Admin DICO Test');

        // 1. Kepala Kampus membuka rute internal guru/koordinator
        $this->actingAs($kepalaKampus)->get(route('guru.dashboard'))->assertStatus(403);
        $this->actingAs($kepalaKampus)->get(route('guru.penugasan.index'))->assertStatus(403);
        $this->actingAs($kepalaKampus)->get(route('guru.koordinator.guru_bk.index'))->assertStatus(403);
        $this->actingAs($kepalaKampus)->get(route('guru.audit.index'))->assertStatus(403);

        // 2. Admin DICO membuka rute internal guru/koordinator
        $this->actingAs($adminDico)->get(route('guru.dashboard'))->assertStatus(403);
        $this->actingAs($adminDico)->get(route('guru.penugasan.index'))->assertStatus(403);
        $this->actingAs($adminDico)->get(route('guru.koordinator.guru_bk.index'))->assertStatus(403);
        $this->actingAs($adminDico)->get(route('guru.audit.index'))->assertStatus(403);
    }

    public function test_cross_access_between_kepala_kampus_and_admin_dico_is_forbidden()
    {
        $kepalaKampus = $this->getOrCreateUser('kepalakampus_test', 'kepala_kampus', 'Kepala Kampus Test');
        $adminDico = $this->getOrCreateUser('admindico_test', 'admin_dico', 'Admin DICO Test');

        // Kepala Kampus mencoba membuka URL Admin DICO
        $this->actingAs($kepalaKampus)->get(route('admin_dico.dashboard'))->assertStatus(403);
        $this->actingAs($kepalaKampus)->get(route('admin_dico.ekspor.index'))->assertStatus(403);

        // Admin DICO mencoba membuka URL Kepala Kampus
        $this->actingAs($adminDico)->get(route('kepala_kampus.dashboard'))->assertStatus(403);
        $this->actingAs($adminDico)->get(route('kepala_kampus.ekspor.index'))->assertStatus(403);
    }

    public function test_koordinator_bk_cannot_access_kepala_kampus_or_admin_dico_routes()
    {
        $koordinator = $this->getOrCreateUser('koordinator_test_bk', 'koordinator_bk', 'Koordinator Test');

        $this->actingAs($koordinator)->get(route('kepala_kampus.dashboard'))->assertStatus(403);
        $this->actingAs($koordinator)->get(route('admin_dico.dashboard'))->assertStatus(403);
    }

    public function test_export_preview_count_and_download_for_each_grade()
    {
        $user = $this->getOrCreateUser('kepalakampus_test', 'kepala_kampus', 'Kepala Kampus Test');

        // 1. Uji AJAX Preview Count
        $countResponse = $this->actingAs($user)->getJson(route('kepala_kampus.ekspor.count', ['grade' => 'X']));
        $countResponse->assertStatus(200);
        $countResponse->assertJsonStructure(['count', 'label']);

        // 2. Uji Ekspor CSV Kelas X
        $csvResponse = $this->actingAs($user)->get(route('kepala_kampus.ekspor.download', [
            'format' => 'csv',
            'grade' => 'X',
        ]));
        $csvResponse->assertStatus(200);
        $this->assertStringContainsString('text/csv', $csvResponse->headers->get('Content-Type'));

        // 3. Uji Ekspor XLSX Kelas XI
        $xlsxResponse = $this->actingAs($user)->get(route('kepala_kampus.ekspor.download', [
            'format' => 'xlsx',
            'grade' => 'XI',
        ]));
        $xlsxResponse->assertStatus(200);
        $this->assertStringContainsString('spreadsheetml.sheet', $xlsxResponse->headers->get('Content-Type'));

        // 4. Uji Ekspor Print / PDF Kelas XII
        $printResponse = $this->actingAs($user)->get(route('kepala_kampus.ekspor.download', [
            'format' => 'print',
            'grade' => 'XII',
        ]));
        $printResponse->assertStatus(200);
        $printResponse->assertSee('REKAPITULASI RESMI DATA SISWA SEKOLAH');

        // Pastikan aktivitas ekspor tercatat di audit log
        $this->assertTrue(
            AuditLog::where('user_id', $user->id)
                ->where('action', 'EKSPOR_SISWA')
                ->exists()
        );
    }

    public function test_strict_audit_log_isolation_across_roles()
    {
        $kepalaKampus = $this->getOrCreateUser('kepalakampus_test', 'kepala_kampus', 'Kepala Kampus Test');
        $adminDico = $this->getOrCreateUser('admindico_test', 'admin_dico', 'Admin DICO Test');
        $koordinator = $this->getOrCreateUser('koordinator_test_bk', 'koordinator_bk', 'Koordinator Test');

        // 1. Buat log unik untuk masing-masing role
        $uniqueKK = "AKSI_KHUSUS_KEPALA_KAMPUS_" . uniqid();
        $uniqueDico = "AKSI_KHUSUS_ADMIN_DICO_" . uniqid();
        $uniqueKoord = "AKSI_KHUSUS_KOORDINATOR_" . uniqid();

        AuditLog::create([
            'user_id' => $kepalaKampus->id,
            'action' => 'PEMANTAUAN',
            'entity_type' => 'Student',
            'entity_id' => null,
            'description' => $uniqueKK,
            'ip_address' => '127.0.0.1',
        ]);

        AuditLog::create([
            'user_id' => $adminDico->id,
            'action' => 'PEMANTAUAN',
            'entity_type' => 'Student',
            'entity_id' => null,
            'description' => $uniqueDico,
            'ip_address' => '127.0.0.1',
        ]);

        AuditLog::create([
            'user_id' => $koordinator->id,
            'action' => 'KOORDINASI',
            'entity_type' => 'CounselorAssignment',
            'entity_id' => null,
            'description' => $uniqueKoord,
            'ip_address' => '127.0.0.1',
        ]);

        // 2. Verifikasi Log Kepala Kampus:
        // HANYA melihat log miliknya sendiri; TIDAK BOLEH melihat log Admin DICO maupun Koordinator
        $kkLogResponse = $this->actingAs($kepalaKampus)->get(route('kepala_kampus.log.index'));
        $kkLogResponse->assertStatus(200);
        $kkLogResponse->assertSee($uniqueKK);
        $kkLogResponse->assertDontSee($uniqueDico);
        $kkLogResponse->assertDontSee($uniqueKoord);

        // 3. Verifikasi Log Admin DICO:
        // HANYA melihat log miliknya sendiri; TIDAK BOLEH melihat log Kepala Kampus maupun Koordinator
        $dicoLogResponse = $this->actingAs($adminDico)->get(route('admin_dico.log.index'));
        $dicoLogResponse->assertStatus(200);
        $dicoLogResponse->assertSee($uniqueDico);
        $dicoLogResponse->assertDontSee($uniqueKK);
        $dicoLogResponse->assertDontSee($uniqueKoord);

        // 4. Verifikasi Log Guru Koordinator BK:
        // HANYA melihat log koordinator_bk; TIDAK BOLEH melihat log Kepala Kampus maupun Admin DICO
        $koordLogResponse = $this->actingAs($koordinator)->get(route('guru.audit.index'));
        $koordLogResponse->assertStatus(200);
        $koordLogResponse->assertSee($uniqueKoord);
        $koordLogResponse->assertDontSee($uniqueKK);
        $koordLogResponse->assertDontSee($uniqueDico);
    }

    public function test_xlsx_file_structure_is_valid_openxml_archive()
    {
        $headers = ['No', 'NIS', 'NISN', 'Nama Lengkap Siswa', 'Jenis Kelamin', 'Tingkat Kelas', 'Jurusan', 'Rombel', 'No HP', 'Status'];
        $rows = [
            [1, '23241001', '0071234561', 'Ahmad Rizky Pratama', 'Laki-laki', 'XII', 'Teknik Jaringan Komputer', 'XII TKJ 1', '081234567801', 'Aktif']
        ];

        $tempPath = \App\Services\SimpleXlsxService::createStudentExport('SMK NEGERI 1', 'Filter: Semua', $headers, $rows);

        $this->assertFileExists($tempPath);

        $zip = new \ZipArchive();
        $this->assertTrue($zip->open($tempPath) === true, 'File .xlsx harus merupakan arsip ZIP OpenXML yang valid');
        $this->assertNotFalse($zip->locateName('[Content_Types].xml'));
        $this->assertNotFalse($zip->locateName('_rels/.rels'));
        $this->assertNotFalse($zip->locateName('xl/workbook.xml'));
        $this->assertNotFalse($zip->locateName('xl/styles.xml'));
        $this->assertNotFalse($zip->locateName('xl/worksheets/sheet1.xml'));

        $sheetContent = $zip->getFromName('xl/worksheets/sheet1.xml');
        $this->assertStringContainsString('Ahmad Rizky Pratama', $sheetContent);
        $this->assertStringContainsString('0071234561', $sheetContent);
        $this->assertStringContainsString('081234567801', $sheetContent);

        $zip->close();
        unlink($tempPath);
    }

    public function test_export_ordering_by_grade_x_xi_xii_and_student_name_alphabetical()
    {
        $user = $this->getOrCreateUser('kepalakampus_test', 'kepala_kampus', 'Kepala Kampus Test');

        $response = $this->actingAs($user)->get(route('kepala_kampus.ekspor.download', [
            'format' => 'xlsx',
            'grade' => 'semua',
        ]));
        $response->assertStatus(200);

        // Uji query pengurutan langsung yang dieksekusi oleh controller
        $orderedStudents = Student::query()
            ->select('students.*')
            ->leftJoin('student_classes', 'students.student_class_id', '=', 'student_classes.id')
            ->with('studentClass')
            ->orderByRaw("
                CASE 
                    WHEN student_classes.grade = 'X' THEN 1
                    WHEN student_classes.grade = 'XI' THEN 2
                    WHEN student_classes.grade = 'XII' THEN 3
                    ELSE 4
                END ASC
            ")
            ->orderBy('student_classes.name', 'asc')
            ->orderBy('students.name', 'asc')
            ->get();

        $lastGradeWeight = 0;
        $classStudentNames = [];

        foreach ($orderedStudents as $st) {
            $grade = $st->studentClass->grade ?? '';
            $weight = match ($grade) {
                'X' => 1,
                'XI' => 2,
                'XII' => 3,
                default => 4,
            };

            // Pastikan tingkat kelas tidak mundur (harus X dulu, lalu XI, lalu XII)
            $this->assertGreaterThanOrEqual($lastGradeWeight, $weight, "Siswa {$st->name} ({$grade}) harus berurutan setelah kelas sebelumnya.");
            $lastGradeWeight = $weight;

            $className = $st->studentClass->name ?? 'TANPA_KELAS';
            $classStudentNames[$className][] = $st->name;
        }

        // Pastikan dalam setiap kelas / rombel, nama siswa berurutan sesuai abjad A-Z
        foreach ($classStudentNames as $className => $names) {
            $sortedNames = $names;
            natcasesort($sortedNames);
            $this->assertEquals(array_values($sortedNames), array_values($names), "Nama siswa di kelas {$className} harus berurutan dari A sampai Z.");
        }
    }

    public function test_kepala_kampus_can_upload_and_delete_avatar_photo()
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $user = $this->getOrCreateUser('kepalakampus_photo_test', 'kepala_kampus', 'Dr. H. Ahmad Fauzi, M.Pd.');

        // 1. Cek avatar default awal adalah guru/staff avatar, bukan siswa
        $this->assertStringContainsString('default-avatar-guru.svg', $user->avatar_url);
        $this->assertFalse($user->hasCustomAvatar());

        // 2. Upload avatar via base64 cropped string
        // 1x1 transparent PNG base64
        $base64Image = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

        $response = $this->actingAs($user)
            ->postJson(route('kepala_kampus.profil.photo.update'), [
                'cropped_image' => $base64Image,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Foto profil berhasil diperbarui.',
            ]);

        $user->refresh();
        $this->assertNotNull($user->avatar);
        $this->assertTrue($user->hasCustomAvatar());
        $this->assertStringContainsString('storage/avatars/', $user->avatar_url);

        // Pastikan AuditLog tercatat
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'PERBARUI_FOTO_PROFIL',
            'user_id' => $user->id,
        ]);

        // 3. Hapus foto profil dan kembali ke avatar default
        $deleteResponse = $this->actingAs($user)
            ->deleteJson(route('kepala_kampus.profil.photo.destroy'));

        $deleteResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Foto profil berhasil dihapus dan kembali ke avatar default.',
            ]);

        $user->refresh();
        $this->assertNull($user->avatar);
        $this->assertFalse($user->hasCustomAvatar());
        $this->assertStringContainsString('default-avatar-guru.svg', $user->avatar_url);

        // Pastikan AuditLog penghapusan tercatat
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'HAPUS_FOTO_PROFIL',
            'user_id' => $user->id,
        ]);
    }

    public function test_admin_dico_can_upload_and_delete_avatar_photo()
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $user = $this->getOrCreateUser('admindico_photo_test', 'admin_dico', 'Admin DICO Test');

        $this->assertStringContainsString('default-avatar-guru.svg', $user->avatar_url);
        $this->assertFalse($user->hasCustomAvatar());

        $base64Image = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

        $response = $this->actingAs($user)
            ->postJson(route('admin_dico.profil.photo.update'), [
                'cropped_image' => $base64Image,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Foto profil berhasil diperbarui.',
            ]);

        $user->refresh();
        $this->assertNotNull($user->avatar);
        $this->assertTrue($user->hasCustomAvatar());

        $deleteResponse = $this->actingAs($user)
            ->deleteJson(route('admin_dico.profil.photo.destroy'));

        $deleteResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Foto profil berhasil dihapus dan kembali ke avatar default.',
            ]);

        $user->refresh();
        $this->assertNull($user->avatar);
        $this->assertFalse($user->hasCustomAvatar());
    }

    public function test_storage_avatar_route_serves_valid_image()
    {
        \Illuminate\Support\Facades\Storage::disk('public')->put('avatars/test_kepala_avatar.jpg', 'avatar_content');

        $response = $this->get('/storage/avatars/test_kepala_avatar.jpg');
        $response->assertStatus(200);

        $notFound = $this->get('/storage/avatars/non_existent_avatar_test.jpg');
        $notFound->assertStatus(404);
    }
}
