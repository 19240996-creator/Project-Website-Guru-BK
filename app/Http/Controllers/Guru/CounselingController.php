<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Counseling;
use App\Models\CounselingCategory;
use App\Models\CounselingFollowUp;
use App\Models\Student;
use App\Models\Notification;
use App\Models\AuditLog;
use Carbon\Carbon;

class CounselingController extends Controller
{
    public function index(Request $request)
    {
        $query = Counseling::with(['student.studentClass', 'category', 'counselor'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('urgency')) {
            $query->where('urgency', $request->urgency);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('topic', 'like', "%{$q}%")
                    ->orWhere('code', 'like', "%{$q}%")
                    ->orWhereHas('student', function ($s) use ($q) {
                        $s->where('name', 'like', "%{$q}%")->orWhere('nisn', 'like', "%{$q}%");
                    });
            });
        }

        $counselings = $query->paginate(15)->withQueryString();
        $categories = CounselingCategory::all();

        return view('guru.konseling.index', compact('counselings', 'categories'));
    }

    public function show($id)
    {
        $counseling = Counseling::with([
            'student.studentClass',
            'student.achievements',
            'student.futurePlan',
            'category',
            'counselor',
            'followUps' => function ($q) {
                $q->latest();
            }
        ])->findOrFail($id);

        $categories = CounselingCategory::all();

        return view('guru.konseling.show', compact('counseling', 'categories'));
    }

    public function schedule(Request $request, $id)
    {
        $counseling = Counseling::findOrFail($id);

        $validated = $request->validate([
            'scheduled_date' => 'required|date',
            'scheduled_time' => 'required',
            'scheduled_location' => 'required|string|max:255',
            'counselor_notes' => 'nullable|string',
            'student_action_plan' => 'nullable|string',
        ]);

        $counseling->update([
            'scheduled_date' => $validated['scheduled_date'],
            'scheduled_time' => $validated['scheduled_time'],
            'scheduled_location' => $validated['scheduled_location'],
            'counselor_id' => auth()->id(),
            'status' => 'dijadwalkan',
            'counselor_notes' => $validated['counselor_notes'] ?? $counseling->counselor_notes,
            'student_action_plan' => $validated['student_action_plan'] ?? $counseling->student_action_plan,
        ]);

        // Send non-confidential notification to student
        if ($counseling->student && $counseling->student->user_id) {
            Notification::create([
                'user_id' => $counseling->student->user_id,
                'title' => 'Jadwal Layanan Konseling Telah Ditetapkan',
                'message' => "Konseling dijadwalkan pada " . Carbon::parse($validated['scheduled_date'])->translatedFormat('d M Y') . " pukul {$validated['scheduled_time']} WIB di {$validated['scheduled_location']}.",
                'url' => route('siswa.konseling.show', $counseling->id),
            ]);
        }

        AuditLog::log('PENJADWALAN', 'Counseling', $counseling->id, "Menjadwalkan sesi konseling {$counseling->code} untuk siswa {$counseling->student->name}.");

        return redirect()->route('guru.konseling.show', $counseling->id)->with('success', 'Sesi konseling berhasil dijadwalkan dan notifikasi telah dikirim ke siswa.');
    }

    public function updateNotes(Request $request, $id)
    {
        $counseling = Counseling::findOrFail($id);

        if (!$counseling->scheduled_date) {
            return redirect()->route('guru.konseling.show', $counseling->id)
                ->with('warning', 'Peringatan: Guru BK harus mengisi jadwal sesi konseling terlebih dahulu sebelum mengisi Catatan Internal Konseling & Privasi Rahasia Guru BK.')
                ->withInput();
        }

        $validated = $request->validate([
            'status' => 'required|in:diajukan,ditinjau,dijadwalkan,dilaksanakan,tindak_lanjut,selesai,dialihkan',
            'confidential_level' => 'required|in:umum,terbatas,rahasia',
            'counselor_notes' => 'nullable|string',
            'student_action_plan' => 'nullable|string',
        ]);

        $oldStatus = $counseling->status;
        $counseling->update($validated);

        if ($oldStatus !== $validated['status'] && $counseling->student && $counseling->student->user_id) {
            Notification::create([
                'user_id' => $counseling->student->user_id,
                'title' => 'Pembaruan Status Layanan Konseling',
                'message' => "Status sesi konseling Anda ({$counseling->code}) kini telah diperbarui menjadi " . ucfirst(str_replace('_', ' ', $validated['status'])) . ".",
                'url' => route('siswa.konseling.show', $counseling->id),
            ]);
        }

        AuditLog::log('PERBARUI_CATATAN', 'Counseling', $counseling->id, "Memperbarui catatan dan status konseling {$counseling->code} dari {$oldStatus} ke {$validated['status']}.");

        return redirect()->route('guru.konseling.show', $counseling->id)->with('success', 'Catatan konseling dan status berhasil diperbarui.');
    }

    public function storeFollowUp(Request $request, $id)
    {
        $counseling = Counseling::findOrFail($id);

        $validated = $request->validate([
            'action_description' => 'required|string',
            'target_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $followUp = CounselingFollowUp::create([
            'counseling_id' => $counseling->id,
            'action_description' => $validated['action_description'],
            'target_date' => $validated['target_date'],
            'status' => 'belum_dilakukan',
            'notes' => $validated['notes'],
        ]);

        // Auto transition status to tindak_lanjut if not finished
        if ($counseling->status !== 'selesai') {
            $counseling->update(['status' => 'tindak_lanjut']);
        }

        AuditLog::log('TAMBAH_TINDAK_LANJUT', 'CounselingFollowUp', $followUp->id, "Menambahkan butir tindak lanjut pada konseling {$counseling->code}.");

        return redirect()->route('guru.konseling.show', $counseling->id)->with('success', 'Tindak lanjut konseling berhasil disimpan dan masuk ke dalam agenda tindak lanjut.');
    }

    public function updateFollowUp(Request $request, $followUpId)
    {
        $followUp = CounselingFollowUp::findOrFail($followUpId);

        $validated = $request->validate([
            'status' => 'required|in:belum_dilakukan,sedang_dilakukan,selesai,ditunda',
            'notes' => 'nullable|string',
        ]);

        $followUp->update($validated);

        AuditLog::log('STATUS_TINDAK_LANJUT', 'CounselingFollowUp', $followUp->id, "Mengubah status tindak lanjut menjadi {$validated['status']}.");

        return back()->with('success', 'Status tindak lanjut berhasil diperbarui.');
    }
}
