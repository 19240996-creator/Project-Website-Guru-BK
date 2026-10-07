<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assessment;
use App\Models\StudentAssessmentResult;
use App\Models\AuditLog;

class AssessmentController extends Controller
{
    public function index()
    {
        $assessments = Assessment::withCount(['questions', 'studentResults'])->get();
        $recentResults = StudentAssessmentResult::with(['student.studentClass', 'assessment'])
            ->latest()
            ->take(20)
            ->get();

        return view('guru.asesmen.index', compact('assessments', 'recentResults'));
    }

    public function show($id)
    {
        $assessment = Assessment::with(['questions.options', 'studentResults.student.studentClass'])->findOrFail($id);
        return view('guru.asesmen.show', compact('assessment'));
    }

    public function showResult($id)
    {
        $result = StudentAssessmentResult::with(['student.studentClass', 'student.futurePlan', 'assessment.questions.options'])->findOrFail($id);
        return view('guru.asesmen.result', compact($result));
    }

    public function updateResult(Request $request, $id)
    {
        $result = StudentAssessmentResult::findOrFail($id);

        $validated = $request->validate([
            'recommendations' => 'nullable|string',
            'summary' => 'nullable|string',
            'is_published' => 'required|boolean',
        ]);

        $result->update($validated);

        AuditLog::log('PERBARUI_HASIL_ASESMEN', 'StudentAssessmentResult', $result->id, "Memperbarui rekomendasi hasil asesmen {$result->student->name}.");

        return back()->with('success', 'Rekomendasi dan status publikasi asesmen berhasil diperbarui.');
    }
}
