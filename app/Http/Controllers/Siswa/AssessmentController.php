<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assessment;
use App\Models\AssessmentOption;
use App\Models\StudentAssessmentResult;
use App\Models\AuditLog;

class AssessmentController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;

        $assessments = Assessment::where('is_active', true)
            ->with(['studentResults' => function ($q) use ($student) {
                $q->where('student_id', $student->id);
            }])
            ->get();

        return view('siswa.asesmen.index', compact('assessments'));
    }

    public function take($id)
    {
        $student = auth()->user()->student;
        $assessment = Assessment::with('questions.options')->where('is_active', true)->findOrFail($id);

        $existingResult = StudentAssessmentResult::where('student_id', $student->id)
            ->where('assessment_id', $assessment->id)
            ->first();

        return view('siswa.asesmen.take', compact('assessment', 'existingResult'));
    }

    public function submit(Request $request, $id)
    {
        $student = auth()->user()->student;
        $assessment = Assessment::with('questions.options')->findOrFail($id);

        $answers = $request->input('answers', []);
        $totalScore = 0;
        $dimensionCounts = [];

        foreach ($answers as $questionId => $optionId) {
            $opt = AssessmentOption::find($optionId);
            if ($opt) {
                $totalScore += $opt->score_value;
                $dim = $opt->dimension_code ?: 'GENERAL';
                $dimensionCounts[$dim] = ($dimensionCounts[$dim] ?? 0) + 1;
            }
        }

        arsort($dimensionCounts);
        $topDimension = key($dimensionCounts) ?: 'Investigative';

        $dimensionMap = [
            'R' => 'Realistic (Praktis Mekanis)',
            'I' => 'Investigative (Analitis Ilmiah)',
            'A' => 'Artistic (Kreatif Ekspresif)',
            'S' => 'Social (Pemberi Layanan & Edukasi)',
            'E' => 'Enterprising (Pemimpin & Wirausaha)',
            'C' => 'Conventional (Terstruktur & Tertib)',
        ];

        $categoryName = $dimensionMap[$topDimension] ?? $topDimension;
        $summary = "Berdasarkan jawaban Anda, orientasi minat utama Anda condong pada tipe {$categoryName}.";
        $recommendations = "Sangat disarankan untuk mengeksplorasi pilihan studi lanjut atau karier yang melibatkan pemecahan masalah serta pengembangan keterampilan di bidang ini.";

        $result = StudentAssessmentResult::updateOrCreate(
            [
                'student_id' => $student->id,
                'assessment_id' => $assessment->id,
            ],
            [
                'total_score' => $totalScore,
                'result_category' => $categoryName,
                'summary' => $summary,
                'recommendations' => $recommendations,
                'is_published' => true,
            ]
        );

        AuditLog::log('SELESAI_ASESMEN', 'StudentAssessmentResult', $result->id, "Siswa {$student->name} menyelesaikan asesmen {$assessment->title}.");

        return redirect()->route('siswa.asesmen.result', $result->id)->with('success', 'Asesmen berhasil diselesaikan dan hasil telah dianalisis.');
    }

    public function result($id)
    {
        $student = auth()->user()->student;
        $result = StudentAssessmentResult::where('student_id', $student->id)
            ->with('assessment')
            ->findOrFail($id);

        return view('siswa.asesmen.result', compact('result'));
    }
}
