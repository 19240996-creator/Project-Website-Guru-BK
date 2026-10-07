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
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        return view('guru.laporan.index');
    }

    public function generate(Request $request)
    {
        $package = $request->query('package', 'paket_a');
        $today = Carbon::today();

        if ($package === 'paket_a') {
            // Paket A: Laporan Wakasek Kesiswaan (Kondisi & Pembinaan Siswa)
            $totalActive = Student::where('status', 'aktif')->count();
            $studentsByGrade = [
                'X' => Student::where('status', 'aktif')->whereHas('studentClass', fn($q) => $q->where('grade', 'X'))->count(),
                'XI' => Student::where('status', 'aktif')->whereHas('studentClass', fn($q) => $q->where('grade', 'XI'))->count(),
                'XII' => Student::where('status', 'aktif')->whereHas('studentClass', fn($q) => $q->where('grade', 'XII'))->count(),
            ];

            $attentionList = Student::with('studentClass')
                ->where('status', 'aktif')
                ->whereIn('attention_level', ['perlu_perhatian', 'prioritas', 'segera_ditindaklanjuti'])
                ->get();

            $counselingStats = [
                'total' => Counseling::count(),
                'selesai' => Counseling::where('status', 'selesai')->count(),
                'proses' => Counseling::whereIn('status', ['diajukan', 'ditinjau', 'dijadwalkan', 'dilaksanakan', 'tindak_lanjut'])->count(),
            ];

            $counselingList = Counseling::with(['student.studentClass', 'category'])
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
            $totalStudents = Student::where('status', 'aktif')->count();
            $totalCounselings = Counseling::count();
            
            $gradeXII = Student::where('status', 'aktif')
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
