<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\SchoolSetting;
use App\Models\AuditLog;

class SchoolSettingTest extends TestCase
{
    protected $koordinator;
    protected $guruA;

    protected function setUp(): void
    {
        parent::setUp();

        $this->koordinator = User::where('role', 'koordinator_bk')->first();
        if (!$this->koordinator) {
            $this->koordinator = User::create([
                'name' => 'Koordinator BK Test',
                'username' => 'koordinatortest_setting',
                'email' => 'koordinatortest_setting@sekolah.sch.id',
                'password' => bcrypt('password'),
                'role' => 'koordinator_bk',
                'nip' => '197501012000031001',
            ]);
        }

        $this->guruA = User::where('role', 'guru_bk')->first();
        if (!$this->guruA) {
            $this->guruA = User::create([
                'name' => 'Guru BK A Test',
                'username' => 'gurubka_setting',
                'email' => 'gurubka_setting@sekolah.sch.id',
                'password' => bcrypt('password'),
                'role' => 'guru_bk',
                'nip' => '198001012005011002',
            ]);
        }

        $this->resetSchoolSetting();
    }

    protected function tearDown(): void
    {
        $this->resetSchoolSetting();
        parent::tearDown();
    }

    private function resetSchoolSetting(): void
    {
        SchoolSetting::updateOrCreate(
            ['id' => 1],
            [
                'school_name' => 'SMK NEGERI 1 SUMEDANG',
                'provincial_agency' => 'PEMERINTAH DAERAH PROVINSI JAWA BARAT — DINAS PENDIDIKAN',
                'education_branch' => 'CABANG DINAS PENDIDIKAN WILAYAH VIII',
                'unit_name' => 'UNIT LAYANAN BIMBINGAN, KONSELING & PENGEMBANGAN KARIER SISWA',
                'address' => 'Jalan Mayor Abdurrachman No. 209, Kotakaler, Kec. Sumedang Utara, Kabupaten Sumedang, Jawa Barat 45322',
                'city' => 'Sumedang',
                'website' => 'dico-ruangbk.my.id',
                'phone' => '(0261) 201531',
                'postal_code' => '45322',
                'headmaster_name' => 'Dra. Hj. Elis Herawati, M.Pd.',
                'headmaster_nip' => '196804151994032008',
            ]
        );
    }

    public function test_koordinator_can_access_school_settings_page(): void
    {
        $response = $this->actingAs($this->koordinator)->get(route('guru.koordinator.pengaturan.index'));

        $response->assertStatus(200);
        $response->assertSee('Pengaturan Identitas Sekolah &amp; Kop Surat Resmi', false);
        $response->assertSee('Formulir Identitas Sekolah &amp; Cabang Pendidikan', false);
        $response->assertSee('Pratinjau Nyata Kop Surat Resmi');
    }

    public function test_regular_guru_bk_cannot_access_school_settings_page(): void
    {
        $response = $this->actingAs($this->guruA)->get(route('guru.koordinator.pengaturan.index'));

        $response->assertStatus(403);
    }

    public function test_guest_is_redirected_from_school_settings(): void
    {
        $response = $this->get(route('guru.koordinator.pengaturan.index'));

        $response->assertRedirect('/login');
    }

    public function test_koordinator_can_update_school_settings_and_audit_log_is_recorded(): void
    {
        $payload = [
            'school_name' => 'SMK MERDEKA MANDIRI',
            'provincial_agency' => 'PEMERINTAH DAERAH PROVINSI JAWA BARAT — DINAS PENDIDIKAN',
            'education_branch' => 'CABANG DINAS PENDIDIKAN WILAYAH IX',
            'unit_name' => 'UNIT LAYANAN BIMBINGAN DAN KONSELING',
            'address' => 'Jl. Pendidikan No. 45, Kecamatan Cimalaka, Kabupaten Sumedang',
            'city' => 'Sumedang',
            'website' => 'smkmerdekamandiri.sch.id',
            'phone' => '(0261) 888999',
            'postal_code' => '45353',
            'headmaster_name' => 'Dr. H. Rahmat Hidayat, M.Si.',
            'headmaster_nip' => '197005101997021003',
        ];

        $response = $this->actingAs($this->koordinator)->put(route('guru.koordinator.pengaturan.update'), $payload);

        $response->assertRedirect(route('guru.koordinator.pengaturan.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('school_settings', [
            'id' => 1,
            'school_name' => 'SMK MERDEKA MANDIRI',
            'education_branch' => 'CABANG DINAS PENDIDIKAN WILAYAH IX',
            'address' => 'Jl. Pendidikan No. 45, Kecamatan Cimalaka, Kabupaten Sumedang',
            'headmaster_name' => 'Dr. H. Rahmat Hidayat, M.Si.',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'PERBARUI',
            'entity_type' => 'SchoolSetting',
        ]);
    }

    public function test_validation_fails_when_required_fields_are_missing(): void
    {
        $response = $this->actingAs($this->koordinator)->put(route('guru.koordinator.pengaturan.update'), [
            'school_name' => '',
            'provincial_agency' => '',
            'education_branch' => '',
            'unit_name' => '',
            'address' => '',
        ]);

        $response->assertSessionHasErrors([
            'school_name',
            'provincial_agency',
            'education_branch',
            'unit_name',
            'address',
        ]);
    }

    public function test_periodic_report_print_reflects_updated_school_settings(): void
    {
        // Set custom school setting
        $setting = SchoolSetting::get();
        $setting->update([
            'school_name' => 'SMK TELADAN NUSANTARA',
            'education_branch' => 'CABANG DINAS WILAYAH X',
            'address' => 'Jalan Pahlawan No. 99, Kabupaten Sumedang',
            'website' => 'smkteladan.sch.id',
            'headmaster_name' => 'Prof. H. Ahmad Fauzi, M.Pd.',
            'headmaster_nip' => '196503201990031005',
        ]);

        $response = $this->actingAs($this->koordinator)->get(route('guru.laporan.generate_periodic', [
            'period_type' => 'bulanan',
        ]));

        $response->assertStatus(200);
        $response->assertSee('SMK TELADAN NUSANTARA');
        $response->assertSee('CABANG DINAS WILAYAH X');
        $response->assertSee('Jalan Pahlawan No. 99, Kabupaten Sumedang');
        $response->assertSee('smkteladan.sch.id');
        $response->assertSee('Prof. H. Ahmad Fauzi, M.Pd.');
    }

    public function test_executive_packages_reflect_updated_school_settings(): void
    {
        $setting = SchoolSetting::get();
        $setting->update([
            'school_name' => 'SMK INOVASI DIGITAL',
            'education_branch' => 'CABANG DINAS WILAYAH XII',
        ]);

        $responseA = $this->actingAs($this->koordinator)->get(route('guru.laporan.generate', ['package' => 'paket_a']));
        $responseA->assertStatus(200);
        $responseA->assertSee('SMK INOVASI DIGITAL');
        $responseA->assertSee('CABANG DINAS WILAYAH XII');

        $responseC = $this->actingAs($this->koordinator)->get(route('guru.laporan.generate', ['package' => 'paket_c']));
        $responseC->assertStatus(200);
        $responseC->assertSee('SMK INOVASI DIGITAL');
    }
}
