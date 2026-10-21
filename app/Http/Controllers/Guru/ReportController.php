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
use App\Models\User;
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

        $periodicData = $this->getPeriodicReportData($request, $user);
        $activeTab = $request->input('tab', ($request->has('period_type') || $request->has('week_date') || $request->has('month') || $request->has('year') || $request->has('counselor_id')) ? 'berkala' : 'grafik');

        return view('guru.laporan.index', array_merge(
            compact(
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
                'totalAlumni',
                'activeTab',
                'user'
            ),
            $periodicData
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

    public function generatePeriodic(Request $request)
    {
        $user = auth()->user();
        $periodicData = $this->getPeriodicReportData($request, $user);
        $today = Carbon::today();
        $koordinator = User::where('role', 'koordinator_bk')->first();

        return view('guru.laporan.berkala_print', array_merge($periodicData, [
            'today' => $today,
            'user' => $user,
            'koordinator' => $koordinator,
        ]));
    }

    private function getPeriodicReportData(Request $request, $user): array
    {
        $periodType = $request->input('period_type', 'bulanan');
        if (!in_array($periodType, ['mingguan', 'bulanan', 'tahunan'])) {
            $periodType = 'bulanan';
        }

        $now = Carbon::now();
        $weekDate = $request->input('week_date', $now->format('Y-m-d'));
        $selectedMonth = (int) $request->input('month', $now->month);
        $selectedYear = (int) $request->input('year', $now->year);
        $selectedGrade = $request->input('berkala_grade', '');
        $selectedMajor = $request->input('berkala_major', '');
        $selectedCounselorId = $request->input('counselor_id', 'semua');

        // Rentang Waktu berdasarkan Jenis Periode
        if ($periodType === 'mingguan') {
            try {
                $base = Carbon::parse($weekDate);
            } catch (\Exception $e) {
                $base = $now->copy();
            }
            $startDate = $base->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
            $endDate = $base->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();
            $periodLabel = 'Minggu ' . $startDate->translatedFormat('d F Y') . ' s.d. ' . $endDate->translatedFormat('d F Y');
            $reportTitle = 'Laporan Mingguan Layanan Bimbingan & Konseling';
        } elseif ($periodType === 'tahunan') {
            $startDate = Carbon::create($selectedYear, 1, 1, 0, 0, 0)->startOfYear();
            $endDate = Carbon::create($selectedYear, 12, 31, 23, 59, 59)->endOfYear();
            $periodLabel = 'Tahun Kalender / Ajaran ' . $selectedYear;
            $reportTitle = 'Laporan Tahunan Layanan Bimbingan & Konseling';
        } else { // Bulanan (default)
            $startDate = Carbon::create($selectedYear, $selectedMonth, 1, 0, 0, 0)->startOfMonth();
            $endDate = Carbon::create($selectedYear, $selectedMonth, 1, 23, 59, 59)->endOfMonth();
            $periodLabel = 'Bulan ' . $startDate->translatedFormat('F Y');
            $reportTitle = 'Laporan Bulanan Layanan Bimbingan & Konseling';
        }

        // Penentuan Role dan Sasaran Konselor
        $isKoordinator = $user->isKoordinatorBk();
        $allCounselors = User::whereIn('role', ['guru_bk', 'koordinator_bk'])
            ->orderBy('role', 'desc')
            ->orderBy('name', 'asc')
            ->get();

        $targetCounselor = null;
        if ($isKoordinator) {
            if ($selectedCounselorId && $selectedCounselorId !== 'semua') {
                $targetCounselor = User::find($selectedCounselorId);
            }
        } else {
            $targetCounselor = $user;
        }

        // Query Konseling dalam Periode
        $counselingQuery = Counseling::with(['student.studentClass', 'counselor', 'category', 'followUps'])
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])
                  ->orWhere(function ($sq) use ($startDate, $endDate) {
                      $sq->whereNotNull('scheduled_date')
                         ->whereBetween('scheduled_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
                  });
            });

        if ($targetCounselor) {
            $counselingQuery->where(function ($q) use ($targetCounselor) {
                $q->where('counselor_id', $targetCounselor->id)
                  ->orWhereHas('student', fn($sq) => $sq->accessibleBy($targetCounselor));
            });
        }

        if ($selectedGrade) {
            $counselingQuery->whereHas('student.studentClass', fn($q) => $q->where('grade', $selectedGrade));
        }
        if ($selectedMajor) {
            $counselingQuery->whereHas('student.studentClass', fn($q) => $q->where('major', $selectedMajor));
        }

        $counselings = $counselingQuery->orderBy('created_at', 'desc')->get();
        $totalCases = $counselings->count();

        // Statistik Penanganan Kasus
        $completedCases = $counselings->where('status', 'selesai')->count();
        $inProgressCases = $counselings->filter(fn($c) => in_array($c->status, ['dilaksanakan', 'dijadwalkan']))->count();
        $followUpCases = $counselings->where('status', 'tindak_lanjut')->count();
        $newCases = $counselings->filter(fn($c) => in_array($c->status, ['diajukan', 'ditinjau']))->count();
        $cancelledCases = $counselings->where('status', 'dibatalkan')->count();

        $completionRate = $totalCases > 0 ? round(($completedCases / $totalCases) * 100, 1) : 0;

        // Breakdown Kategori Masalah
        $categories = CounselingCategory::orderBy('id')->get();
        $categoryBreakdown = [];
        foreach ($categories as $cat) {
            $cnt = $counselings->where('category_id', $cat->id)->count();
            $pct = $totalCases > 0 ? round(($cnt / $totalCases) * 100, 1) : 0;
            $categoryBreakdown[] = [
                'id' => $cat->id,
                'name' => $cat->name,
                'count' => $cnt,
                'percentage' => $pct,
            ];
        }

        // Breakdown Tingkat Urgensi Kasus
        $urgencyBreakdown = [
            'rendah' => $counselings->where('urgency', 'rendah')->count(),
            'sedang' => $counselings->where('urgency', 'sedang')->count(),
            'tinggi' => $counselings->where('urgency', 'tinggi')->count(),
            'darurat' => $counselings->where('urgency', 'darurat')->count(),
        ];

        // Siswa Perlu Perhatian Khusus dalam Lingkup
        $attentionStudentsQuery = Student::with('studentClass')->where('status', 'aktif')
            ->whereIn('attention_level', ['perlu_perhatian', 'prioritas', 'segera_ditindaklanjuti']);

        if ($targetCounselor) {
            $attentionStudentsQuery->accessibleBy($targetCounselor);
        }
        if ($selectedGrade) {
            $attentionStudentsQuery->whereHas('studentClass', fn($q) => $q->where('grade', $selectedGrade));
        }
        if ($selectedMajor) {
            $attentionStudentsQuery->whereHas('studentClass', fn($q) => $q->where('major', $selectedMajor));
        }
        $attentionStudents = $attentionStudentsQuery->get();

        // Agenda / Kegiatan Mitra & Karier dalam Periode
        $activitiesInPeriod = PartnerActivity::with(['partner', 'targetClass'])
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();

        // Rekapitulasi Beban & Kinerja Guru BK (Khusus Koordinator melihat seluruh sekolah)
        $counselorWorkload = [];
        if ($isKoordinator && (!$targetCounselor || $selectedCounselorId === 'semua')) {
            foreach ($allCounselors as $counselor) {
                $assignedCount = Student::accessibleBy($counselor)->where('status', 'aktif')->count();
                $handledCounselings = Counseling::where(function ($q) use ($counselor) {
                    $q->where('counselor_id', $counselor->id)
                      ->orWhereHas('student', fn($sq) => $sq->accessibleBy($counselor));
                })->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate])
                      ->orWhere(function ($sq) use ($startDate, $endDate) {
                          $sq->whereNotNull('scheduled_date')
                             ->whereBetween('scheduled_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
                      });
                })->get();

                $handledTotal = $handledCounselings->count();
                $handledDone = $handledCounselings->where('status', 'selesai')->count();
                $handledRate = $handledTotal > 0 ? round(($handledDone / $handledTotal) * 100, 1) : 0;

                $counselorWorkload[] = [
                    'counselor' => $counselor,
                    'assigned_students' => $assignedCount,
                    'total_cases' => $handledTotal,
                    'completed_cases' => $handledDone,
                    'completion_rate' => $handledRate,
                ];
            }
        }

        return compact(
            'periodType',
            'weekDate',
            'selectedMonth',
            'selectedYear',
            'selectedGrade',
            'selectedMajor',
            'selectedCounselorId',
            'startDate',
            'endDate',
            'periodLabel',
            'reportTitle',
            'isKoordinator',
            'allCounselors',
            'targetCounselor',
            'counselings',
            'totalCases',
            'completedCases',
            'inProgressCases',
            'followUpCases',
            'newCases',
            'cancelledCases',
            'completionRate',
            'categoryBreakdown',
            'urgencyBreakdown',
            'attentionStudents',
            'activitiesInPeriod',
            'counselorWorkload'
        );
    }
}
