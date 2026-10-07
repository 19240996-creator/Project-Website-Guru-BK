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

        // Future Plan distributions for grade XII
        $gradeXIIStudents = Student::where('status', 'aktif')
            ->whereHas('studentClass', function ($q) {
                $q->where('grade', 'XII');
            })->get();

        $totalGradeXII = $gradeXIIStudents->count();

        $plans = StudentFuturePlan::whereIn('student_id', $gradeXIIStudents->pluck('id'))->get();
        $collegeCount = $plans->where('primary_goal', 'kuliah')->count();
        $workCount = $plans->where('primary_goal', 'bekerja')->count();
        $businessCount = $plans->where('primary_goal', 'wirausaha')->count();
        
        $studentsWithPlanIds = $plans->where('primary_goal', '!=', 'belum_menentukan')->pluck('student_id')->unique();
        $undecidedGradeXIICount = $gradeXIIStudents->whereNotIn('id', $studentsWithPlanIds)->count();

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
            'upcomingActivities'
        ));
    }
}
