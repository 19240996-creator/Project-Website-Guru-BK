<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Counseling;
use App\Models\Assessment;
use App\Models\Opportunity;
use App\Models\OpportunityRegistration;
use App\Models\PartnerActivity;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $student = $user->student ? $user->student->load(['studentClass', 'futurePlan']) : null;

        if (!$student) {
            return view('siswa.no_student_profile');
        }

        $today = Carbon::today()->toDateString();

        // 1. Upcoming or active counseling sessions
        $upcomingCounseling = Counseling::where('student_id', $student->id)
            ->whereIn('status', ['diajukan', 'ditinjau', 'dijadwalkan', 'dilaksanakan', 'tindak_lanjut'])
            ->latest()
            ->first();

        // 2. Available Assessments
        $availableAssessments = Assessment::where('is_active', true)
            ->with(['studentResults' => function ($q) use ($student) {
                $q->where('student_id', $student->id);
            }])
            ->get();

        // 3. Recommended Opportunities based on Class & Major
        $major = $student->studentClass ? $student->studentClass->major : '';
        $opportunities = Opportunity::where('status', 'dipublikasikan')
            ->where('deadline', '>=', $today)
            ->with('partner')
            ->take(4)
            ->get();

        // 4. Partner Activities for Student's class
        $myClassActivities = PartnerActivity::where('date', '>=', $today)
            ->whereNotIn('status', ['dibatalkan'])
            ->where(function ($q) use ($student) {
                $q->where('target_class_id', $student->student_class_id)
                  ->orWhereNull('target_class_id');
            })
            ->take(3)
            ->get();

        return view('siswa.dashboard', compact(
            'student',
            'upcomingCounseling',
            'availableAssessments',
            'opportunities',
            'myClassActivities'
        ));
    }
}
