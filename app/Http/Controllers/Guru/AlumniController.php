<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\AlumniTracking;
use App\Models\AuditLog;

class AlumniController extends Controller
{
    public function index(Request $request)
    {
        $query = AlumniTracking::with(['student.studentClass'])->latest();

        if ($request->filled('status')) {
            $query->where('current_status', $request->status);
        }

        if ($request->filled('year')) {
            $query->where('graduation_year', $request->year);
        }

        $alumni = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => AlumniTracking::count(),
            'bekerja' => AlumniTracking::where('current_status', 'bekerja')->count(),
            'kuliah' => AlumniTracking::where('current_status', 'kuliah')->count(),
            'wirausaha' => AlumniTracking::where('current_status', 'wirausaha')->count(),
            'belum_terlacak' => AlumniTracking::where('current_status', 'belum_terlacak')->count(),
        ];

        return view('guru.alumni.index', compact('alumni', 'stats'));
    }

    public function graduateStudent(Request $request, $studentId)
    {
        $student = Student::findOrFail($studentId);

        $validated = $request->validate([
            'graduation_year' => 'required|digits:4',
            'current_status' => 'required|in:bekerja,kuliah,wirausaha,mencari_kerja,belum_bekerja,belum_terlacak',
            'institution_or_company' => 'nullable|string|max:255',
            'major_or_position' => 'nullable|string|max:255',
            'monthly_income_range' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'allow_public_showcase' => 'nullable|boolean',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('alumni_photos', 'public');
            $student->avatar = $photoPath;
            if ($student->user) {
                $student->user->update(['avatar' => $photoPath]);
            }
        }

        $student->status = 'lulus';
        $student->save();

        $code = 'ALS-' . $validated['graduation_year'] . '-' . str_pad(AlumniTracking::count() + 1, 5, '0', STR_PAD_LEFT);

        $trackingData = [
            'code' => $code,
            'graduation_year' => $validated['graduation_year'],
            'tracking_period' => '6_bulan',
            'current_status' => $validated['current_status'],
            'institution_or_company' => $validated['institution_or_company'] ?? null,
            'major_or_position' => $validated['major_or_position'] ?? null,
            'monthly_income_range' => $validated['monthly_income_range'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'allow_public_showcase' => $request->has('allow_public_showcase'),
        ];

        if ($photoPath) {
            $trackingData['photo'] = $photoPath;
        } elseif ($student->avatar) {
            $trackingData['photo'] = $student->avatar;
        }

        AlumniTracking::updateOrCreate(
            ['student_id' => $student->id],
            $trackingData
        );

        AuditLog::log('KELULUSAN', 'Student', $student->id, "Mengubah status siswa {$student->name} menjadi lulus dan membuat data pelacakan alumni.");

        return back()->with('success', 'Status siswa berhasil diubah menjadi alumni dan data pelacakan tersimpan.');
    }

    public function toggleShowcase($id)
    {
        $tracking = AlumniTracking::findOrFail($id);
        $tracking->allow_public_showcase = !$tracking->allow_public_showcase;
        $tracking->save();

        AuditLog::log('TOGGLE_SHOWCASE', 'AlumniTracking', $tracking->id, "Mengubah izin tampilan jejak alumni {$tracking->student->name} menjadi " . ($tracking->allow_public_showcase ? 'tampil' : 'sembunyi') . ".");

        return back()->with('success', 'Status tampilan jejak alumni berhasil diubah.');
    }
}
