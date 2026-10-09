<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentOption;
use App\Models\StudentAssessmentResult;
use App\Models\StudentClass;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;

class AssessmentController extends Controller
{
    public function index(Request $request)
    {
        $assessments = Assessment::withCount(['questions', 'studentResults'])->get();
        $classes = StudentClass::orderBy('grade')->orderBy('name')->get();
        $majors = StudentClass::whereNotNull('major')
            ->where('major', '!=', '')
            ->distinct()
            ->orderBy('major')
            ->pluck('major');

        $query = StudentAssessmentResult::with(['student.studentClass', 'assessment'])->latest();

        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($sub) use ($q) {
                $sub->whereHas('student', function ($sq) use ($q) {
                    $sq->where('name', 'like', "%{$q}%")
                        ->orWhere('nisn', 'like', "%{$q}%")
                        ->orWhere('nis', 'like', "%{$q}%");
                })
                ->orWhereHas('student.studentClass', function ($cq) use ($q) {
                    $cq->where('name', 'like', "%{$q}%")
                        ->orWhere('major', 'like', "%{$q}%");
                })
                ->orWhere('result_category', 'like', "%{$q}%");
            });
        }

        if ($request->filled('class_id')) {
            $classId = $request->class_id;
            if (str_starts_with($classId, 'major:')) {
                $majorName = substr($classId, 6);
                $query->whereHas('student.studentClass', function ($sq) use ($majorName) {
                    $sq->where('major', $majorName);
                });
            } else {
                $query->whereHas('student', function ($sq) use ($classId) {
                    $sq->where('student_class_id', $classId);
                });
            }
        }

        if ($request->filled('assessment_id')) {
            $query->where('assessment_id', $request->assessment_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            }
        }

        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [5, 10, 25, 50])) {
            $perPage = 10;
        }

        $recentResults = $query->paginate($perPage)->withQueryString();

        return view('guru.asesmen.index', compact('assessments', 'recentResults', 'classes', 'majors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'description' => 'required|string|max:1000',
            'instructions' => 'nullable|string|max:1000',
        ], [
            'title.required' => 'Judul instrumen asesmen wajib diisi.',
            'category.required' => 'Kategori asesmen wajib diisi.',
            'description.required' => 'Deskripsi asesmen wajib diisi.',
        ]);

        $assessment = Assessment::create([
            'title' => $validated['title'],
            'category' => $validated['category'],
            'description' => $validated['description'],
            'instructions' => $validated['instructions'] ?? 'Pilih jawaban yang paling mencerminkan diri Anda secara jujur dan objektif.',
            'is_active' => true,
        ]);

        AuditLog::log('TAMBAH_ASESMEN', 'Assessment', $assessment->id, "Membuat instrumen asesmen baru: {$assessment->title}.");

        return redirect()->route('guru.asesmen.show', $assessment->id)
            ->with('success', "Instrumen asesmen '{$assessment->title}' berhasil dibuat. Silakan tambahkan butir pertanyaan.");
    }

    public function destroy($id)
    {
        $assessment = Assessment::withCount('studentResults')->findOrFail($id);

        if ($assessment->student_results_count > 0) {
            return back()->with('error', "Asesmen '{$assessment->title}' tidak dapat dihapus karena sudah memiliki {$assessment->student_results_count} riwayat pengerjaan siswa.");
        }

        DB::transaction(function () use ($assessment) {
            foreach ($assessment->questions as $q) {
                $q->options()->delete();
                $q->delete();
            }
            $assessment->delete();

            AuditLog::log('HAPUS_ASESMEN', 'Assessment', $assessment->id, "Menghapus instrumen asesmen: {$assessment->title}.");
        });

        return redirect()->route('guru.asesmen.index')->with('success', "Instrumen asesmen '{$assessment->title}' berhasil dihapus.");
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
