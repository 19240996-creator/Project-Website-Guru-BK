<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Counseling;
use App\Models\CounselingFollowUp;
use App\Models\StudentFuturePlan;
use App\Models\Partner;
use App\Models\PartnerActivity;
use App\Models\Opportunity;
use App\Models\AlumniTracking;
use App\Models\AcademicYear;
use App\Models\CounselingCategory;
use App\Models\AuditLog;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();

        // Core aggregate statistics
        $totalStudents = Student::where('status', 'aktif')->count();
        $attentionStudentsCount = Student::where('status', 'aktif')
            ->whereIn('attention_level', ['perlu_perhatian', 'prioritas', 'segera_ditindaklanjuti'])
            ->count();
        
        $newCounselingCount = Counseling::where('status', 'diajukan')->count();
        $todayCounselingCount = Counseling::where('status', 'dijadwalkan')
            ->where('scheduled_date', $today)
            ->count();
        
        $overdueFollowUpsCount = CounselingFollowUp::where('status', 'belum_dilakukan')
            ->where('target_date', '<=', $today)
            ->count();

        // Future Plan distributions for active students (harmonized with Peminatan & Masa Depan)
        $allActiveStudents = Student::where('status', 'aktif')
            ->with(['studentClass', 'futurePlan'])
            ->get();

        $totalPlanStudents = $allActiveStudents->count();
        $totalGradeXII = $totalPlanStudents;

        $collegeCount = $allActiveStudents->filter(fn($s) => $s->futurePlan && $s->futurePlan->primary_goal === 'kuliah')->count();
        $workCount = $allActiveStudents->filter(fn($s) => $s->futurePlan && $s->futurePlan->primary_goal === 'bekerja')->count();
        $businessCount = $allActiveStudents->filter(fn($s) => $s->futurePlan && $s->futurePlan->primary_goal === 'wirausaha')->count();
        $workAndStudyCount = $allActiveStudents->filter(fn($s) => $s->futurePlan && $s->futurePlan->primary_goal === 'kuliah_kerja')->count();
        $undecidedGradeXIICount = $allActiveStudents->filter(fn($s) => !$s->futurePlan || $s->futurePlan->primary_goal === 'belum_menentukan')->count();

        $activePartnersCount = Partner::where('partnership_status', 'aktif')->count();
        $activeOpportunitiesCount = Opportunity::where('status', 'dipublikasikan')->count();
        $upcomingActivitiesCount = PartnerActivity::where('date', '>=', $today)
            ->whereNotIn('status', ['dibatalkan'])
            ->count();

        // Section 36: PRIORITIZED TASK ENGINE ("Yang Perlu Saya Kerjakan")
        $priorityTasks = [];

        // 1. Pengajuan konseling belum diproses
        $pendingCounselings = Counseling::with(['student.studentClass', 'category'])
            ->where('status', 'diajukan')
            ->orderBy('created_at', 'asc')
            ->get();
        foreach ($pendingCounselings as $c) {
            $priorityTasks[] = [
                'type' => 'counseling_pending',
                'priority' => 1,
                'badge' => 'Pengajuan Baru',
                'badge_color' => 'warning',
                'title' => "Permohonan Konseling: {$c->student->name} ({$c->student->studentClass->name})",
                'desc' => "Topik: {$c->topic}. Urgensi: " . ucfirst($c->urgency),
                'action_url' => route('guru.konseling.show', $c->id),
                'action_text' => 'Tinjau & Jadwalkan',
                'date' => $c->created_at->translatedFormat('d M Y, H:i'),
            ];
        }

        // 2. Tindak lanjut yang jatuh tempo
        $dueFollowUps = CounselingFollowUp::with(['counseling.student.studentClass'])
            ->where('status', 'belum_dilakukan')
            ->where('target_date', '<=', $today)
            ->get();
        foreach ($dueFollowUps as $fu) {
            $priorityTasks[] = [
                'type' => 'follow_up_due',
                'priority' => 2,
                'badge' => 'Tindak Lanjut Jatuh Tempo',
                'badge_color' => 'danger',
                'title' => "Tindak Lanjut: {$fu->counseling->student->name}",
                'desc' => $fu->action_description,
                'action_url' => route('guru.konseling.show', $fu->counseling_id),
                'action_text' => 'Buka Catatan Konseling',
                'date' => Carbon::parse($fu->target_date)->translatedFormat('d M Y'),
            ];
        }

        // 3. Konseling hari ini
        $todayCounselings = Counseling::with(['student.studentClass', 'category'])
            ->where('status', 'dijadwalkan')
            ->where('scheduled_date', $today)
            ->orderBy('scheduled_time', 'asc')
            ->get();
        foreach ($todayCounselings as $tc) {
            $priorityTasks[] = [
                'type' => 'counseling_today',
                'priority' => 3,
                'badge' => 'Konseling Hari Ini',
                'badge_color' => 'primary',
                'title' => "Sesi Pukul " . substr($tc->scheduled_time, 0, 5) . " WIB: {$tc->student->name}",
                'desc' => "Lokasi: {$tc->scheduled_location}. Topik: {$tc->topic}",
                'action_url' => route('guru.konseling.show', $tc->id),
                'action_text' => 'Buka Sesi',
                'date' => 'Hari Ini',
            ];
        }

        // 4. Siswa kelas XII tanpa rencana masa depan
        $undecidedStudents = Student::with('studentClass')
            ->where('status', 'aktif')
            ->whereHas('studentClass', function ($q) {
                $q->where('grade', 'XII');
            })
            ->where(function ($q) {
                $q->whereDoesntHave('futurePlans')
                  ->orWhereHas('futurePlan', function ($sub) {
                      $sub->where('primary_goal', 'belum_menentukan');
                  });
            })
            ->take(5)
            ->get();
        foreach ($undecidedStudents as $us) {
            $priorityTasks[] = [
                'type' => 'student_undecided',
                'priority' => 4,
                'badge' => 'Kelas Akhir Belum Ada Rencana',
                'badge_color' => 'secondary',
                'title' => "{$us->name} ({$us->studentClass->name})",
                'desc' => "Siswa kelas XII belum menetapkan rencana arah kuliah, kerja, atau wirausaha.",
                'action_url' => route('guru.siswa.show', $us->id),
                'action_text' => 'Profil Siswa',
                'date' => 'Perlu Pendampingan',
            ];
        }

        // Upcoming Partner Activities in next 7 days
        $upcomingActivities = PartnerActivity::with(['partner', 'targetClass'])
            ->where('date', '>=', $today)
            ->where('date', '<=', Carbon::today()->addDays(7)->toDateString())
            ->whereNotIn('status', ['dibatalkan'])
            ->orderBy('date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        $counselor = auth()->user();
        $counselorCounselingsCount = $counselor ? Counseling::where('counselor_id', $counselor->id)->count() : 0;
        $activeAcademicYear = AcademicYear::where('is_active', true)->first();

        // Chart 1: Kategori Kasus Bimbingan & Konseling (Column Chart)
        $counselingCategories = CounselingCategory::withCount('counselings')
            ->orderBy('id', 'asc')
            ->get();

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
            $categoryChartData[] = (int) $cat->counselings_count;
        }

        // Chart 2: Peta Rencana Masa Depan Siswa (Pie / Donut Chart)
        $futurePlanChartLabels = [
            'Target Kuliah',
            'Target Bekerja',
            'Target Wirausaha',
            'Belum Menentukan',
        ];
        $futurePlanChartData = [
            $collegeCount,
            $workCount,
            $businessCount,
            $undecidedGradeXIICount,
        ];
        if ($workAndStudyCount > 0) {
            array_splice($futurePlanChartLabels, 3, 0, 'Kuliah & Kerja');
            array_splice($futurePlanChartData, 3, 0, $workAndStudyCount);
        }

        // Section 3: Recent Priority Feeds matching reference UI
        $recentCounselings = Counseling::with(['student.studentClass', 'category'])
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        $upcomingCounselings = Counseling::with(['student.studentClass', 'category'])
            ->whereIn('status', ['dijadwalkan', 'diajukan'])
            ->orderBy('scheduled_date', 'asc')
            ->orderBy('scheduled_time', 'asc')
            ->take(3)
            ->get();

        $recentActivities = AuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        return view('guru.dashboard', compact(
            'totalStudents',
            'attentionStudentsCount',
            'newCounselingCount',
            'todayCounselingCount',
            'overdueFollowUpsCount',
            'totalGradeXII',
            'collegeCount',
            'workCount',
            'businessCount',
            'undecidedGradeXIICount',
            'activePartnersCount',
            'activeOpportunitiesCount',
            'upcomingActivitiesCount',
            'priorityTasks',
            'upcomingActivities',
            'counselor',
            'counselorCounselingsCount',
            'activeAcademicYear',
            'categoryChartLabels',
            'categoryChartFullNames',
            'categoryChartData',
            'futurePlanChartLabels',
            'futurePlanChartData',
            'totalPlanStudents',
            'recentCounselings',
            'upcomingCounselings',
            'recentActivities'
        ));
    }
}
