<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\CounselorAssignment;
use App\Models\CounselorClassAssignment;

class KoordinatorAndAssignmentTest extends TestCase
{
    protected $koordinator;
    protected $guruA;
    protected $guruB;
    protected $student1;
    protected $student2;
    protected $classX;
    protected $classXI;

    protected function setUp(): void
    {
        parent::setUp();

        $this->koordinator = User::where('username', 'koordinator')->first();
        $this->guruA = User::where('username', 'gurubk')->first();
        $this->guruB = User::where('username', 'gurubk2')->first();

        $this->classX = StudentClass::where('grade', 'X')->first();
        $this->classXI = StudentClass::where('grade', 'XI')->first() ?: StudentClass::where('grade', 'XII')->first();

        $this->student1 = Student::where('student_class_id', $this->classX->id)->first();
        if (!$this->student1) {
            $this->student1 = Student::first();
        }

        $this->student2 = Student::where('id', '!=', $this->student1->id)->first();
    }

    protected function tearDown(): void
    {
        if ($this->student1 && $this->guruA && $this->koordinator) {
            CounselorAssignment::updateOrCreate(
                ['student_id' => $this->student1->id],
                [
                    'counselor_id' => $this->guruA->id,
                    'student_class_id' => $this->student1->student_class_id,
                    'assigned_by' => $this->koordinator->id,
                ]
            );
        }
        if ($this->guruB) {
            CounselorAssignment::where('counselor_id', $this->guruB->id)->delete();
        }

        parent::tearDown();
    }

    public function test_koordinator_can_access_assignment_and_guru_management_pages()
    {
        $response = $this->actingAs($this->koordinator)->get(route('guru.penugasan.index'));
        $response->assertStatus(200);
        $response->assertSee('Pembagian Tugas');

        $responseGuruList = $this->actingAs($this->koordinator)->get(route('guru.koordinator.guru_bk.index'));
        $responseGuruList->assertStatus(200);
        $responseGuruList->assertSee('Daftar Guru Bimbingan Konseling');
    }

    public function test_regular_guru_bk_cannot_access_koordinator_pages()
    {
        $response = $this->actingAs($this->guruA)->get(route('guru.penugasan.index'));
        $response->assertStatus(403);

        $responseGuruList = $this->actingAs($this->guruA)->get(route('guru.koordinator.guru_bk.index'));
        $responseGuruList->assertStatus(403);
    }

    public function test_koordinator_can_assign_student_to_guru_a()
    {
        // Unassign first to ensure clean state
        CounselorAssignment::where('student_id', $this->student1->id)->delete();

        $response = $this->actingAs($this->koordinator)->post(route('guru.penugasan.assign'), [
            'assignment_mode' => 'selected_students',
            'counselor_id' => $this->guruA->id,
            'student_ids' => [$this->student1->id],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('counselor_assignments', [
            'counselor_id' => $this->guruA->id,
            'student_id' => $this->student1->id,
        ]);

        // Guru A should now be able to access student1
        $resA = $this->actingAs($this->guruA)->get(route('guru.siswa.show', $this->student1->id));
        $resA->assertStatus(200);

        // Guru B must NOT be able to access student1 (403)
        $resB = $this->actingAs($this->guruB)->get(route('guru.siswa.show', $this->student1->id));
        $resB->assertStatus(403);
    }

    public function test_reassigning_student_revokes_previous_guru_access()
    {
        // Reassign student1 from Guru A to Guru B
        $response = $this->actingAs($this->koordinator)->post(route('guru.penugasan.assign'), [
            'assignment_mode' => 'selected_students',
            'counselor_id' => $this->guruB->id,
            'student_ids' => [$this->student1->id],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('counselor_assignments', [
            'counselor_id' => $this->guruB->id,
            'student_id' => $this->student1->id,
        ]);

        // Guru A's access is immediately revoked -> 403
        $resA = $this->actingAs($this->guruA)->get(route('guru.siswa.show', $this->student1->id));
        $resA->assertStatus(403);

        // Guru B now has access -> 200
        $resB = $this->actingAs($this->guruB)->get(route('guru.siswa.show', $this->student1->id));
        $resB->assertStatus(200);
    }

    public function test_guru_bk_cannot_edit_unassigned_student()
    {
        // Ensure student1 is assigned to Guru B
        CounselorAssignment::updateOrCreate(
            ['student_id' => $this->student1->id],
            [
                'counselor_id' => $this->guruB->id,
                'student_class_id' => $this->student1->student_class_id,
                'assigned_by' => $this->koordinator->id,
            ]
        );

        // Guru A tries to access edit page of student1 -> 403
        $resEdit = $this->actingAs($this->guruA)->get(route('guru.siswa.edit', $this->student1->id));
        $resEdit->assertStatus(403);

        // Guru A tries to POST update to student1 -> 403
        $resUpdate = $this->actingAs($this->guruA)->put(route('guru.siswa.update', $this->student1->id), [
            'name' => 'Hacked Name',
        ]);
        $resUpdate->assertStatus(403);
    }

    public function test_koordinator_sees_all_students_on_student_index()
    {
        $response = $this->actingAs($this->koordinator)->get(route('guru.siswa.index'));
        $response->assertStatus(200);
        $totalStudents = Student::count();
        $this->assertEquals($totalStudents, $response->viewData('students')->total());
    }

    public function test_guru_bk_only_sees_assigned_students_on_student_index()
    {
        // Give Guru B only student1
        CounselorAssignment::where('counselor_id', $this->guruB->id)->delete();
        CounselorAssignment::updateOrCreate(
            ['student_id' => $this->student1->id],
            [
                'counselor_id' => $this->guruB->id,
                'student_class_id' => $this->student1->student_class_id,
                'assigned_by' => $this->koordinator->id,
            ]
        );

        $response = $this->actingAs($this->guruB)->get(route('guru.siswa.index'));
        $response->assertStatus(200);
        $this->assertEquals(1, $response->viewData('students')->total());
        $this->assertEquals($this->student1->id, $response->viewData('students')->first()->id);
    }

    public function test_koordinator_can_create_and_manage_guru_bk_account()
    {
        $testUsername = 'gurubk_test_' . time();
        $response = $this->actingAs($this->koordinator)->post(route('guru.koordinator.guru_bk.store'), [
            'name' => 'Guru Pengujian Otomatis',
            'username' => $testUsername,
            'email' => $testUsername . '@smk.belajar.id',
            'nip' => '199999999999999999',
            'phone' => '08999999999',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect(route('guru.koordinator.guru_bk.index'));
        $this->assertDatabaseHas('users', [
            'username' => $testUsername,
            'role' => 'guru_bk',
        ]);

        $createdGuru = User::where('username', $testUsername)->first();

        // Update
        $resUpdate = $this->actingAs($this->koordinator)->put(route('guru.koordinator.guru_bk.update', $createdGuru->id), [
            'name' => 'Guru Pengujian Otomatis Terupdate',
            'username' => $testUsername,
            'email' => $testUsername . '@smk.belajar.id',
            'nip' => '199999999999999999',
            'phone' => '08999999999',
        ]);
        $resUpdate->assertRedirect(route('guru.koordinator.guru_bk.index'));

        // Delete
        $resDelete = $this->actingAs($this->koordinator)->delete(route('guru.koordinator.guru_bk.destroy', $createdGuru->id));
        $resDelete->assertRedirect(route('guru.koordinator.guru_bk.index'));
        $this->assertDatabaseMissing('users', ['id' => $createdGuru->id]);
    }

    public function test_penugasan_page_supports_pagination_and_query_filters()
    {
        // Test pagination with page 2 and per_page 10
        $response = $this->actingAs($this->koordinator)->get(route('guru.penugasan.index', ['per_page' => 10, 'page' => 2]));
        $response->assertStatus(200);
        $paginated = $response->viewData('students');
        $this->assertEquals(2, $paginated->currentPage());
        $this->assertEquals(10, $paginated->perPage());

        // Test filter with search query
        $responseSearch = $this->actingAs($this->koordinator)->get(route('guru.penugasan.index', ['q' => 'Ahmad']));
        $responseSearch->assertStatus(200);

        // Test filter by assignment status
        $responseStatus = $this->actingAs($this->koordinator)->get(route('guru.penugasan.index', ['assignment_status' => 'unassigned']));
        $responseStatus->assertStatus(200);
    }

    public function test_dashboard_renders_navy_card_headers()
    {
        $response = $this->actingAs($this->koordinator)->get(route('guru.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('card-header-navy');
        $response->assertSee('Distribusi Kategori Layanan Konseling');
        $response->assertSee('Peta Rencana Masa Depan Siswa');
        $response->assertSee('Pengajuan Konseling Terbaru');
        $response->assertSee('Jadwal Konseling Mendatang');
        $response->assertSee('Aktivitas Terkini Sistem');
    }

    public function test_audit_log_only_accessible_to_koordinator_and_hidden_for_regular_guru()
    {
        // 1. Koordinator can access audit log page
        $resKoordinator = $this->actingAs($this->koordinator)->get(route('guru.audit.index'));
        $resKoordinator->assertStatus(200);
        $resKoordinator->assertSee('Audit Log Aktivitas');

        // 2. Koordinator sees Audit Log link in sidebar
        $dashKoordinator = $this->actingAs($this->koordinator)->get(route('guru.dashboard'));
        $dashKoordinator->assertStatus(200);
        $dashKoordinator->assertSee(route('guru.audit.index'));

        // 3. Regular Guru BK CANNOT access audit log page (403)
        $resGuru = $this->actingAs($this->guruA)->get(route('guru.audit.index'));
        $resGuru->assertStatus(403);

        // 4. Regular Guru BK sidebar does NOT have audit log link
        $dashGuru = $this->actingAs($this->guruA)->get(route('guru.dashboard'));
        $dashGuru->assertStatus(200);
        $dashGuru->assertDontSee(route('guru.audit.index'));
    }
}
