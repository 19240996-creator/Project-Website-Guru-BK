<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Counseling;
use App\Models\CounselingCategory;
use App\Models\Notification;
use App\Models\User;
use App\Models\AuditLog;

class CounselingController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;
        $counselings = Counseling::where('student_id', $student->id)
            ->with(['category', 'counselor'])
            ->latest()
            ->paginate(10);

        return view('siswa.konseling.index', compact('counselings'));
    }

    public function create()
    {
        $categories = CounselingCategory::all();
        return view('siswa.konseling.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $student = auth()->user()->student;

        $validated = $request->validate([
            'category_id' => 'required|exists:counseling_categories,id',
            'topic' => 'required|string|max:255',
            'story' => 'required|string',
            'urgency' => 'required|in:rendah,sedang,tinggi,mendesak',
            'preferred_schedule' => 'nullable|string|max:255',
        ], [
            'category_id.required' => 'Pilih kategori bimbingan yang sesuai.',
            'topic.required' => 'Tuliskan topik atau judul konsultasi Anda.',
            'story.required' => 'Ceritakan secara singkat hal yang ingin Anda konsultasikan.',
        ]);

        $code = 'KSL-' . date('Y') . '-' . str_pad(Counseling::count() + 1, 5, '0', STR_PAD_LEFT);

        $counseling = Counseling::create([
            'code' => $code,
            'student_id' => $student->id,
            'category_id' => $validated['category_id'],
            'topic' => $validated['topic'],
            'story' => $validated['story'],
            'urgency' => $validated['urgency'],
            'preferred_schedule' => $validated['preferred_schedule'],
            'status' => 'diajukan',
            'confidential_level' => 'rahasia',
        ]);

        // Send notification to Guru BK
        $guruUsers = User::where('role', 'guru_bk')->get();
        foreach ($guruUsers as $guru) {
            Notification::create([
                'user_id' => $guru->id,
                'title' => 'Pengajuan Konseling Baru',
                'message' => "Siswa {$student->name} ({$student->studentClass->name}) mengajukan konseling: {$counseling->topic}.",
                'url' => route('guru.konseling.show', $counseling->id),
            ]);
        }

        AuditLog::log('PENGAJUAN_KONSELING', 'Counseling', $counseling->id, "Siswa {$student->name} mengajukan konseling {$counseling->code}.");

        return redirect()->route('siswa.konseling.show', $counseling->id)->with('success', 'Pengajuan konseling berhasil dikirim ke Guru BK. Anda akan menerima pemberitahuan jadwal.');
    }

    public function show($id)
    {
        $student = auth()->user()->student;

        // STRICT PRIVACY: Student can only view their own counseling!
        $counseling = Counseling::where('student_id', $student->id)
            ->with(['category', 'counselor'])
            ->findOrFail($id);

        return view('siswa.konseling.show', compact('counseling'));
    }
}
