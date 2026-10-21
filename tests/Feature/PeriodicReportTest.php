<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Counseling;
use App\Models\CounselorAssignment;
use Carbon\Carbon;

class PeriodicReportTest extends TestCase
{
    protected $koordinator;
    protected $guruA;
    protected $guruB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->koordinator = User::where('role', 'koordinator_bk')->first();
        if (!$this->koordinator) {
            $this->koordinator = User::create([
                'name' => 'Koordinator BK Test',
                'username' => 'koordinatortest',
                'email' => 'koordinatortest@sekolah.sch.id',
                'password' => bcrypt('password'),
                'role' => 'koordinator_bk',
                'nip' => '197501012000031001',
            ]);
        }

        $this->guruA = User::where('role', 'guru_bk')->first();
        if (!$this->guruA) {
            $this->guruA = User::create([
                'name' => 'Guru BK A Test',
                'username' => 'gurubka',
                'email' => 'gurubka@sekolah.sch.id',
                'password' => bcrypt('password'),
                'role' => 'guru_bk',
                'nip' => '198001012005011002',
            ]);
        }

        \App\Models\SchoolSetting::get()->update([
            'school_name' => 'SMK NEGERI 1 SUMEDANG',
        ]);
    }

    public function test_guru_bk_can_view_periodic_reports_tab(): void
    {
        $response = $this->actingAs($this->guruA)->get(route('guru.laporan.index', ['tab' => 'berkala']));

        $response->assertStatus(200);
        $response->assertSee('Laporan Berkala (Mingguan, Bulanan, Tahunan)');
        $response->assertSee('Parameter Generator Laporan Berkala Terintegrasi');
        $response->assertSee('Laporan Mingguan');
        $response->assertSee('Laporan Bulanan');
        $response->assertSee('Laporan Tahunan');
        $response->assertSee('Daftar Rinci Penanganan Konseling Siswa (Data Riil Database)');
    }

    public function test_guru_bk_sees_navy_headers_on_chart_analytics(): void
    {
        $response = $this->actingAs($this->guruA)->get(route('guru.laporan.index'));

        $response->assertStatus(200);
        $response->assertSee('card-header card-header-navy');
        $response->assertSee('Distribusi Kategori Layanan Konseling');
        $response->assertSee('Peta Rencana Masa Depan Siswa');
        $response->assertSee('Status Penyelesaian Layanan Konseling');
        $response->assertSee('Distribusi Tingkat Perhatian &amp; Pembinaan Siswa', false);
        $response->assertSee('Distribusi Siswa per Jenjang Kelas');
        $response->assertSee('Peta Penyerapan Alumni (Tracer Study)');
    }

    public function test_guru_bk_can_filter_mingguan_bulanan_and_tahunan(): void
    {
        // 1. Mingguan
        $resMingguan = $this->actingAs($this->guruA)->get(route('guru.laporan.index', [
            'tab' => 'berkala',
            'period_type' => 'mingguan',
            'week_date' => '2026-10-21',
        ]));
        $resMingguan->assertStatus(200);
        $resMingguan->assertSee('Laporan Mingguan Layanan Bimbingan & Konseling');

        // 2. Bulanan
        $resBulanan = $this->actingAs($this->guruA)->get(route('guru.laporan.index', [
            'tab' => 'berkala',
            'period_type' => 'bulanan',
            'month' => 10,
            'year' => 2026,
        ]));
        $resBulanan->assertStatus(200);
        $resBulanan->assertSee('Laporan Bulanan Layanan Bimbingan & Konseling');

        // 3. Tahunan
        $resTahunan = $this->actingAs($this->guruA)->get(route('guru.laporan.index', [
            'tab' => 'berkala',
            'period_type' => 'tahunan',
            'year' => 2026,
        ]));
        $resTahunan->assertStatus(200);
        $resTahunan->assertSee('Laporan Tahunan Layanan Bimbingan & Konseling');

        // 4. Kustom Tanggal (misal hari ini dan 2 hari sebelumnya)
        $resCustom = $this->actingAs($this->guruA)->get(route('guru.laporan.index', [
            'tab' => 'berkala',
            'period_type' => 'custom',
            'custom_start_date' => '2026-10-19',
            'custom_end_date' => '2026-10-21',
        ]));
        $resCustom->assertStatus(200);
        $resCustom->assertSee('Laporan Kustom Layanan Bimbingan & Konseling');
        $resCustom->assertSee('19 Oktober 2026 s.d. 21 Oktober 2026');
    }

    public function test_koordinator_sees_all_counselor_filter_and_workload_table(): void
    {
        $response = $this->actingAs($this->koordinator)->get(route('guru.laporan.index', [
            'tab' => 'berkala',
            'period_type' => 'bulanan',
            'month' => 10,
            'year' => 2026,
            'counselor_id' => 'semua',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Sasaran Konselor / Guru BK');
        $response->assertSee('Semua Guru BK (Laporan Komprehensif Sekolah)');
        $response->assertSee('Rekapitulasi Kinerja &amp; Beban Kasus Seluruh Guru BK', false);

        // Test filtering specific counselor
        $resSpecific = $this->actingAs($this->koordinator)->get(route('guru.laporan.index', [
            'tab' => 'berkala',
            'period_type' => 'bulanan',
            'month' => 10,
            'year' => 2026,
            'counselor_id' => $this->guruA->id,
        ]));
        $resSpecific->assertStatus(200);
        $resSpecific->assertSee($this->guruA->name);
    }

    public function test_generate_periodic_printable_official_document(): void
    {
        // 1. Guru BK prints monthly report
        $resPrintGuru = $this->actingAs($this->guruA)->get(route('guru.laporan.generate_periodic', [
            'period_type' => 'bulanan',
            'month' => 10,
            'year' => 2026,
        ]));
        $resPrintGuru->assertStatus(200);
        $resPrintGuru->assertSee('LAPORAN BULANAN LAYANAN BIMBINGAN &amp; KONSELING', false);
        $resPrintGuru->assertSee('SMK NEGERI 1 SUMEDANG', false);
        $resPrintGuru->assertSee('Guru BK Pembimbing / Konselor', false);
        $resPrintGuru->assertSee('Mengetahui,', false);

        // 2. Koordinator prints weekly report
        $resPrintKoord = $this->actingAs($this->koordinator)->get(route('guru.laporan.generate_periodic', [
            'period_type' => 'mingguan',
            'week_date' => '2026-10-21',
            'counselor_id' => 'semua',
        ]));
        $resPrintKoord->assertStatus(200);
        $resPrintKoord->assertSee('LAPORAN MINGGUAN LAYANAN BIMBINGAN &amp; KONSELING', false);
        $resPrintKoord->assertSee('Kepala Sekolah', false);

        // 3. Guru BK prints custom date report
        $resPrintCustom = $this->actingAs($this->guruA)->get(route('guru.laporan.generate_periodic', [
            'period_type' => 'custom',
            'custom_start_date' => '2026-10-19',
            'custom_end_date' => '2026-10-21',
        ]));
        $resPrintCustom->assertStatus(200);
        $resPrintCustom->assertSee('LAPORAN KUSTOM LAYANAN BIMBINGAN &amp; KONSELING', false);
        $resPrintCustom->assertSee('19 Oktober 2026 s.d. 21 Oktober 2026');
    }
}
