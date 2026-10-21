<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\Counseling;
use App\Models\Partner;
use App\Models\PartnerActivity;
use App\Models\StudentFuturePlan;
use App\Models\AlumniTracking;
use App\Models\CounselingCategory;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $selectedGrade = $request->input('grade') ?: $request->input('tingkat');
        $selectedMajor = $request->input('major') ?: $request->input('jurusan');
        $selectedPeriod = $request->input('period') ?: $request->input('periode');

        $user = auth()->user();

        // 1. Base Queries with Filtering & Hak Akses
        $studentsQuery = Student::accessibleBy($user)->where('status', 'aktif')->with(['studentClass', 'futurePlan']);
        $counselingsQuery = Counseling::whereHas('student', fn($sq) => $sq->accessibleBy($user))->with(['student.studentClass', 'category']);
        $alumniQuery = AlumniTracking::whereHas('student', fn($sq) => $sq->accessibleBy($user))->with('student.studentClass');

        if ($selectedGrade) {
            $studentsQuery->whereHas('studentClass', fn($q) => $q->where('grade', $selectedGrade));
            $counselingsQuery->whereHas('student.studentClass', fn($q) => $q->where('grade', $selectedGrade));
        }

        if ($selectedMajor) {
            $studentsQuery->whereHas('studentClass', fn($q) => $q->where('major', $selectedMajor));
            $counselingsQuery->whereHas('student.studentClass', fn($q) => $q->where('major', $selectedMajor));
            $alumniQuery->whereHas('student.studentClass', fn($q) => $q->where('major', $selectedMajor));
        }

        if ($selectedPeriod) {
            $cutoff = match ($selectedPeriod) {
                '1_bulan' => Carbon::now()->subMonth(),
                '3_bulan' => Carbon::now()->subMonths(3),
                '6_bulan' => Carbon::now()->subMonths(6),
                '1_tahun' => Carbon::now()->subYear(),
                default => null,
            };
            if ($cutoff) {
                $counselingsQuery->where('created_at', '>=', $cutoff);
            }
        }

        $filteredStudents = $studentsQuery->get();
        $totalFilteredStudents = $filteredStudents->count();

        $filteredCounselings = $counselingsQuery->get();
        $totalFilteredCounselings = $filteredCounselings->count();

        // 2. Metrics for KPI Cards
        $attentionStudentsCount = $filteredStudents->filter(fn($s) => in_array($s->attention_level, ['perlu_perhatian', 'prioritas', 'segera_ditindaklanjuti']))->count();
        $activePartnersCount = Partner::where('partnership_status', 'aktif')->count();
        $totalActivitiesCount = PartnerActivity::whereNotIn('status', ['dibatalkan'])->count();

        // 3. Diagram 1: Kategori Kasus Bimbingan (Column Chart - dari Beranda)
        $counselingCategories = CounselingCategory::orderBy('id', 'asc')->get();
        $categoryShortNameMap = [
            'Masalah Belajar & Akademik' => 'Belajar',
            'Pengembangan Pribadi' => 'Pribadi',
            'Hubungan Sosial & Teman Sebaya' => 'Sosial',
            'Keluarga & Lingkungan Rumah' => 'Keluarga',
            'Perencanaan Karier & Masa Depan' => 'Karier',
        ];
        $categoryChartLabels = [];
        $categoryChartFullNames = [];
        $categoryChartData = [];
        foreach ($counselingCategories as $cat) {
            $short = $categoryShortNameMap[$cat->name] ?? \Illuminate\Support\Str::limit($cat->name, 10);
            $categoryChartLabels[] = $short;
            $categoryChartFullNames[] = $cat->name;
            $count = $filteredCounselings->filter(fn($c) => $c->category_id == $cat->id)->count();
            $categoryChartData[] = $count;
        }

        // 4. Diagram 2: Peta Rencana Masa Depan Siswa (Donut Chart - dari Beranda)
        $collegeCount = $filteredStudents->filter(fn($s) => $s->futurePlan && $s->futurePlan->primary_goal === 'kuliah')->count();
        $workCount = $filteredStudents->filter(fn($s) => $s->futurePlan && $s->futurePlan->primary_goal === 'bekerja')->count();
        $businessCount = $filteredStudents->filter(fn($s) => $s->futurePlan && $s->futurePlan->primary_goal === 'wirausaha')->count();
        $workAndStudyCount = $filteredStudents->filter(fn($s) => $s->futurePlan && $s->futurePlan->primary_goal === 'kuliah_kerja')->count();
        $undecidedCount = $filteredStudents->filter(fn($s) => !$s->futurePlan || $s->futurePlan->primary_goal === 'belum_menentukan')->count();

        $futurePlanChartLabels = ['Target Kuliah', 'Target Bekerja', 'Target Wirausaha', 'Belum Menentukan'];
        $futurePlanChartData = [$collegeCount, $workCount, $businessCount, $undecidedCount];
        if ($workAndStudyCount > 0) {
            array_splice($futurePlanChartLabels, 3, 0, 'Kuliah & Kerja');
            array_splice($futurePlanChartData, 3, 0, $workAndStudyCount);
        }
        $totalPlanStudents = $totalFilteredStudents;

        // 5. Diagram 3: Status Penyelesaian Layanan Konseling (Donut Chart)
        $counselingStatusCounts = [
            'Selesai' => $filteredCounselings->filter(fn($c) => $c->status === 'selesai')->count(),
            'Dilaksanakan' => $filteredCounselings->filter(fn($c) => in_array($c->status, ['dilaksanakan', 'dijadwalkan']))->count(),
            'Tindak Lanjut' => $filteredCounselings->filter(fn($c) => $c->status === 'tindak_lanjut')->count(),
            'Menunggu / Baru' => $filteredCounselings->filter(fn($c) => in_array($c->status, ['diajukan', 'ditinjau']))->count(),
            'Dibatalkan' => $filteredCounselings->filter(fn($c) => $c->status === 'dibatalkan')->count(),
        ];

        // 6. Diagram 4: Distribusi Tingkat Perhatian Siswa (Attention Level Bar Chart)
        $attentionLevelCounts = [
            'Aman / Normal' => $filteredStudents->filter(fn($s) => in_array($s->attention_level, ['aman', 'normal']) || empty($s->attention_level))->count(),
            'Perlu Perhatian' => $filteredStudents->filter(fn($s) => $s->attention_level === 'perlu_perhatian')->count(),
            'Prioritas' => $filteredStudents->filter(fn($s) => $s->attention_level === 'prioritas')->count(),
            'Tindak Lanjut Cepat' => $filteredStudents->filter(fn($s) => $s->attention_level === 'segera_ditindaklanjuti')->count(),
        ];

        // 7. Diagram 5: Distribusi Siswa per Jenjang Kelas (Bar Chart)
        $gradeCounts = [
            'Kelas X' => $filteredStudents->filter(fn($s) => $s->studentClass && $s->studentClass->grade === 'X')->count(),
            'Kelas XI' => $filteredStudents->filter(fn($s) => $s->studentClass && $s->studentClass->grade === 'XI')->count(),
            'Kelas XII' => $filteredStudents->filter(fn($s) => $s->studentClass && $s->studentClass->grade === 'XII')->count(),
        ];

        // 8. Diagram 6: Peta Penyerapan Alumni (Tracer Study Donut)
        $alumniList = $alumniQuery->get();
        $totalAlumni = $alumniList->count();
        $alumniStatusCounts = [
            'Bekerja' => $alumniList->filter(fn($a) => $a->current_status === 'bekerja')->count(),
            'Kuliah' => $alumniList->filter(fn($a) => $a->current_status === 'kuliah')->count(),
            'Wirausaha' => $alumniList->filter(fn($a) => $a->current_status === 'wirausaha')->count(),
            'Mencari Kerja' => $alumniList->filter(fn($a) => $a->current_status === 'mencari_kerja')->count(),
            'Belum Terlacak' => $alumniList->filter(fn($a) => in_array($a->current_status, ['belum_terlacak', 'belum_bekerja']) || empty($a->current_status))->count(),
        ];

        // Filter Dropdown Choices
        $availableGrades = ['X', 'XI', 'XII'];
        $availableMajors = StudentClass::distinct()->whereNotNull('major')->where('major', '!=', '')->orderBy('major')->pluck('major');

        return view('guru.laporan.index', compact(
            'selectedGrade',
            'selectedMajor',
            'selectedPeriod',
            'availableGrades',
            'availableMajors',
            'totalFilteredStudents',
            'totalFilteredCounselings',
            'attentionStudentsCount',
            'activePartnersCount',
            'totalActivitiesCount',
            'categoryChartLabels',
            'categoryChartFullNames',
            'categoryChartData',
            'futurePlanChartLabels',
            'futurePlanChartData',
            'totalPlanStudents',
            'counselingStatusCounts',
            'attentionLevelCounts',
            'gradeCounts',
            'alumniStatusCounts',
            'totalAlumni'
        ));
    }

    public function generate(Request $request)
    {
        $package = $request->query('package', 'paket_a');
        $today = Carbon::today();
        $user = auth()->user();

        if ($package === 'paket_a') {
            // Paket A: Laporan Wakasek Kesiswaan (Kondisi & Pembinaan Siswa)
            $totalActive = Student::accessibleBy($user)->where('status', 'aktif')->count();
            $studentsByGrade = [
                'X' => Student::accessibleBy($user)->where('status', 'aktif')->whereHas('studentClass', fn($q) => $q->where('grade', 'X'))->count(),
                'XI' => Student::accessibleBy($user)->where('status', 'aktif')->whereHas('studentClass', fn($q) => $q->where('grade', 'XI'))->count(),
                'XII' => Student::accessibleBy($user)->where('status', 'aktif')->whereHas('studentClass', fn($q) => $q->where('grade', 'XII'))->count(),
            ];

            $attentionList = Student::accessibleBy($user)->with('studentClass')
                ->where('status', 'aktif')
                ->whereIn('attention_level', ['perlu_perhatian', 'prioritas', 'segera_ditindaklanjuti'])
                ->get();

            $counselingStats = [
                'total' => Counseling::whereHas('student', fn($sq) => $sq->accessibleBy($user))->count(),
                'selesai' => Counseling::whereHas('student', fn($sq) => $sq->accessibleBy($user))->where('status', 'selesai')->count(),
                'proses' => Counseling::whereHas('student', fn($sq) => $sq->accessibleBy($user))->whereIn('status', ['diajukan', 'ditinjau', 'dijadwalkan', 'dilaksanakan', 'tindak_lanjut'])->count(),
            ];

            $counselingList = Counseling::whereHas('student', fn($sq) => $sq->accessibleBy($user))
                ->with(['student.studentClass', 'category'])
                ->latest()
                ->take(20)
                ->get();

            return view('guru.laporan.paket_a', compact(
                'totalActive',
                'studentsByGrade',
                'attentionList',
                'counselingStats',
                'counselingList',
                'today'
            ));
        } elseif ($package === 'paket_b') {
            // Paket B: Laporan Wakasek Kurikulum (Jadwal & Kemitraan Akademik/Industri)
            $activities = PartnerActivity::with(['partner', 'targetClass'])
                ->orderBy('date', 'asc')
                ->orderBy('start_time', 'asc')
                ->get();

            $activePartners = Partner::where('partnership_status', 'aktif')->withCount('activities')->get();

            return view('guru.laporan.paket_b', compact('activities', 'activePartners', 'today'));
        } else {
            // Paket C: Laporan Eksekutif Kepala Sekolah
            $totalStudents = Student::accessibleBy($user)->where('status', 'aktif')->count();
            $totalCounselings = Counseling::whereHas('student', fn($sq) => $sq->accessibleBy($user))->count();
            
            $gradeXII = Student::accessibleBy($user)->where('status', 'aktif')
                ->whereHas('studentClass', fn($q) => $q->where('grade', 'XII'))
                ->with('futurePlan')
                ->get();

            $totalXII = max(1, $gradeXII->count());
            $kuliah = $gradeXII->filter(fn($s) => $s->futurePlan && $s->futurePlan->primary_goal === 'kuliah')->count();
            $kerja = $gradeXII->filter(fn($s) => $s->futurePlan && $s->futurePlan->primary_goal === 'bekerja')->count();
            $wirausaha = $gradeXII->filter(fn($s) => $s->futurePlan && $s->futurePlan->primary_goal === 'wirausaha')->count();
            $undecided = $gradeXII->filter(fn($s) => !$s->futurePlan || $s->futurePlan->primary_goal === 'belum_menentukan')->count();

            $planPercentages = [
                'kuliah' => round(($kuliah / $totalXII) * 100, 1),
                'kerja' => round(($kerja / $totalXII) * 100, 1),
                'wirausaha' => round(($wirausaha / $totalXII) * 100, 1),
                'undecided' => round(($undecided / $totalXII) * 100, 1),
            ];

            $partnerStats = [
                'ptn_pts' => Partner::where('type', 'perguruan_tinggi')->count(),
                'industri' => Partner::where('type', 'perusahaan')->count(),
            ];

            $alumniStats = [
                'total' => AlumniTracking::count(),
                'bekerja' => AlumniTracking::where('current_status', 'bekerja')->count(),
                'kuliah' => AlumniTracking::where('current_status', 'kuliah')->count(),
                'wirausaha' => AlumniTracking::where('current_status', 'wirausaha')->count(),
            ];

            return view('guru.laporan.paket_c', compact(
                'totalStudents',
                'totalCounselings',
                'totalXII',
                'kuliah',
                'kerja',
                'wirausaha',
                'undecided',
                'planPercentages',
                'partnerStats',
                'alumniStats',
                'today'
            ));
        }
    }
}
