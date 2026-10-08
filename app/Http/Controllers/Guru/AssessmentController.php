<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentOption;
use App\Models\StudentAssessmentResult;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;

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
        return view('guru.asesmen.result', compact('result'));
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

    public function storeQuestion(Request $request, $id)
    {
        $assessment = Assessment::findOrFail($id);

        $validated = $request->validate([
            'question_text' => 'required|string|max:1000',
            'options' => 'required|array|min:2',
            'options.*.option_text' => 'required|string|max:255',
            'options.*.score_value' => 'required|integer|min:0',
            'options.*.dimension_code' => 'nullable|string|max:50',
        ], [
            'question_text.required' => 'Teks pertanyaan wajib diisi.',
            'options.required' => 'Pilihan jawaban wajib diisi minimal 2 opsi.',
            'options.min' => 'Pilihan jawaban wajib diisi minimal 2 opsi.',
            'options.*.option_text.required' => 'Setiap pilihan jawaban harus memiliki teks jawaban.',
            'options.*.score_value.required' => 'Setiap pilihan jawaban harus memiliki nilai bobot poin.',
        ]);

        DB::transaction(function () use ($assessment, $validated) {
            $maxOrder = $assessment->questions()->max('sort_order') ?? 0;
            $question = $assessment->questions()->create([
                'question_text' => $validated['question_text'],
                'sort_order' => $maxOrder + 1,
            ]);

            foreach ($validated['options'] as $opt) {
                $question->options()->create([
                    'option_text' => $opt['option_text'],
                    'score_value' => (int) $opt['score_value'],
                    'dimension_code' => !empty($opt['dimension_code']) ? strtoupper(trim($opt['dimension_code'])) : null,
                ]);
            }

            AuditLog::log(
                'SIMPAN_PERTANYAAN_ASESMEN',
                'AssessmentQuestion',
                $question->id,
                "Menambahkan butir pertanyaan kustom baru pada asesmen {$assessment->title}."
            );
        });

        return redirect()->route('guru.asesmen.show', $assessment->id)->with('success', 'Butir pertanyaan dan bobot poin jawaban berhasil ditambahkan.');
    }

    public function updateQuestion(Request $request, $id, $questionId)
    {
        $assessment = Assessment::findOrFail($id);
        $question = $assessment->questions()->findOrFail($questionId);

        $validated = $request->validate([
            'question_text' => 'required|string|max:1000',
            'options' => 'required|array|min:2',
            'options.*.option_text' => 'required|string|max:255',
            'options.*.score_value' => 'required|integer|min:0',
            'options.*.dimension_code' => 'nullable|string|max:50',
        ], [
            'question_text.required' => 'Teks pertanyaan wajib diisi.',
            'options.required' => 'Pilihan jawaban wajib diisi minimal 2 opsi.',
            'options.min' => 'Pilihan jawaban wajib diisi minimal 2 opsi.',
            'options.*.option_text.required' => 'Setiap pilihan jawaban harus memiliki teks jawaban.',
            'options.*.score_value.required' => 'Setiap pilihan jawaban harus memiliki nilai bobot poin.',
        ]);

        DB::transaction(function () use ($assessment, $question, $validated) {
            $question->update([
                'question_text' => $validated['question_text'],
            ]);

            $question->options()->delete();

            foreach ($validated['options'] as $opt) {
                $question->options()->create([
                    'option_text' => $opt['option_text'],
                    'score_value' => (int) $opt['score_value'],
                    'dimension_code' => !empty($opt['dimension_code']) ? strtoupper(trim($opt['dimension_code'])) : null,
                ]);
            }

            AuditLog::log(
                'PERBARUI_PERTANYAAN_ASESMEN',
                'AssessmentQuestion',
                $question->id,
                "Memperbarui butir pertanyaan #{$question->id} pada asesmen {$assessment->title}."
            );
        });

        return redirect()->route('guru.asesmen.show', $assessment->id)->with('success', 'Butir pertanyaan dan bobot poin jawaban berhasil diperbarui.');
    }

    public function destroyQuestion($id, $questionId)
    {
        $assessment = Assessment::findOrFail($id);
        $question = $assessment->questions()->findOrFail($questionId);

        DB::transaction(function () use ($assessment, $question, $questionId) {
            $question->options()->delete();
            $question->delete();

            AuditLog::log(
                'HAPUS_PERTANYAAN_ASESMEN',
                'AssessmentQuestion',
                $questionId,
                "Menghapus butir pertanyaan pada asesmen {$assessment->title}."
            );
        });

        return redirect()->route('guru.asesmen.show', $assessment->id)->with('success', 'Butir pertanyaan berhasil dihapus.');
    }
}
