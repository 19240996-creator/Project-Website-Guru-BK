<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AlumniTracking;
use App\Models\AuditLog;

class AlumniController extends Controller
{
    public function index()
    {
        // Public alumni inspiration showcase
        $showcaseAlumni = AlumniTracking::with(['student.studentClass'])
            ->where('allow_public_showcase', true)
            ->latest()
            ->paginate(12);

        $student = auth()->user()->student;
        $myTracer = $student && $student->status === 'lulus' ? $student->alumniTracking : null;

        return view('siswa.alumni.index', compact('showcaseAlumni', 'student', 'myTracer'));
    }

    public function updateTracer(Request $request)
    {
        $student = auth()->user()->student;

        if (!$student || $student->status !== 'lulus') {
            return back()->with('error', 'Hanya siswa berstatus lulus yang dapat memperbarui data pelacakan alumni.');
        }

        $validated = $request->validate([
            'graduation_year' => 'required|digits:4',
            'tracking_period' => 'required|in:3_bulan,6_bulan,12_bulan,24_bulan',
            'current_status' => 'required|in:bekerja,kuliah,wirausaha,mencari_kerja,belum_bekerja,belum_terlacak',
            'institution_or_company' => 'nullable|string|max:255',
            'major_or_position' => 'nullable|string|max:255',
            'monthly_income_range' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'allow_public_showcase' => 'nullable|boolean',
        ]);

        $code = 'ALS-' . $validated['graduation_year'] . '-' . str_pad(AlumniTracking::count() + 1, 5, '0', STR_PAD_LEFT);

        $tracer = AlumniTracking::updateOrCreate(
            ['student_id' => $student->id],
            [
                'code' => $code,
                'graduation_year' => $validated['graduation_year'],
                'tracking_period' => $validated['tracking_period'],
                'current_status' => $validated['current_status'],
                'institution_or_company' => $validated['institution_or_company'],
                'major_or_position' => $validated['major_or_position'],
                'monthly_income_range' => $validated['monthly_income_range'],
                'notes' => $validated['notes'],
                'allow_public_showcase' => $request->has('allow_public_showcase'),
            ]
        );

        AuditLog::log('PERBARUI_TRACER_ALUMNI', 'AlumniTracking', $tracer->id, "Alumni {$student->name} memperbarui data tracer study.");

        return back()->with('success', 'Data pelacakan alumni Anda berhasil diperbarui. Terima kasih atas partisipasi Anda.');
    }
}
