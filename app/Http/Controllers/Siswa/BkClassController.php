<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\BkClass;
use App\Models\BkClassMaterial;
use App\Models\BkClassTask;
use App\Models\BkClassTaskSubmission;
use App\Models\BkClassAttendance;

class BkClassController extends Controller
{
    /**
     * Ambil instance siswa yang sedang login
     */
    protected function getStudent()
    {
        $user = auth()->user();
        $student = $user->student;
        if (!$student) {
            abort(403, 'Data profil siswa tidak ditemukan.');
        }
        return $student;
    }

    /**
     * Daftar Kelas BK Online yang ditujukan kepada siswa ini
     */
    public function index(Request $request)
    {
        $student = $this->getStudent();
        $studentClassId = $student->student_class_id;
        $grade = optional($student->studentClass)->grade;
        $major = optional($student->studentClass)->major;

        // Ambil ID Guru BK yang ditugaskan kepada siswa ini
        $assignedCounselorId = optional($student->counselorAssignment)->counselor_id;

        // Query kelas BK yang sasaran pesertanya mencakup siswa ini
        $classes = BkClass::with(['counselor', 'materials', 'tasks'])
            ->where('status', '!=', 'dibatalkan')
            ->where(function ($query) use ($assignedCounselorId) {
                // Kelas dari koordinator BK terbuka bagi seluruh target rombel, 
                // sedangkan kelas dari guru BK hanya untuk siswa binaannya
                $query->whereHas('counselor', function ($q) {
                    $q->where('role', 'koordinator_bk');
                });

                if ($assignedCounselorId) {
                    $query->orWhere('counselor_id', $assignedCounselorId);
                }
            })
            ->orderBy('class_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get()
            ->filter(function ($class) use ($student) {
                return $class->isStudentEligible($student);
            });

        // Filter tab status
        $tab = $request->input('tab', 'semua');
        if ($tab === 'berlangsung') {
            $classes = $classes->filter(fn($c) => $c->computed_status === 'berlangsung');
        } elseif ($tab === 'terjadwal') {
            $classes = $classes->filter(fn($c) => $c->computed_status === 'terjadwal');
        } elseif ($tab === 'selesai') {
            $classes = $classes->filter(fn($c) => $c->computed_status === 'selesai');
        }

        // Ambil ID kelas untuk rekap presensi dan tugas
        $classIds = $classes->pluck('id')->toArray();
        $attendances = BkClassAttendance::whereIn('bk_class_id', $classIds)
            ->where('student_id', $student->id)
            ->get()
            ->keyBy('bk_class_id');

        return view('siswa.kelas.index', compact('classes', 'attendances', 'tab'));
    }

    /**
     * Halaman Ruang Kelas BK Online Siswa (Materi, Video, Tugas, Presensi)
     */
    public function show(Request $request, $id)
    {
        $student = $this->getStudent();
        $class = BkClass::with(['counselor', 'materials', 'tasks.submissions' => function ($q) use ($student) {
            $q->where('student_id', $student->id);
        }])->findOrFail($id);

        if (!$class->isStudentEligible($student)) {
            abort(403, 'Anda tidak terdaftar sebagai peserta dalam kelas BK ini.');
        }

        // Data presensi siswa ini pada kelas bersangkutan
        $attendance = BkClassAttendance::where('bk_class_id', $class->id)
            ->where('student_id', $student->id)
            ->first();

        // Server time WIB
        $now = Carbon::now('Asia/Jakarta');
        $isAttendanceOpen = $class->isAttendanceOpen();

        return view('siswa.kelas.show', compact('class', 'student', 'attendance', 'isAttendanceOpen', 'now'));
    }

    /**
     * Siswa melakukan presensi mandiri pada jam yang diizinkan
     */
    public function storeAttendance(Request $request, $id)
    {
        $student = $this->getStudent();
        $class = BkClass::findOrFail($id);

        if (!$class->isStudentEligible($student)) {
            abort(403, 'Anda tidak terdaftar sebagai peserta pada kelas ini.');
        }

        // Cek apakah sudah pernah absen
        $existing = BkClassAttendance::where('bk_class_id', $class->id)
            ->where('student_id', $student->id)
            ->first();

        if ($existing) {
            return back()->with('info', "Anda sudah melakukan presensi sebelumnya (Status: {$existing->status_label}).");
        }

        // Validasi waktu server WIB
        $now = Carbon::now('Asia/Jakarta');

        if ($now->lt($class->attendance_start_date_time)) {
            return back()->with('error', "Waktu absensi belum dibuka. Presensi dibuka pada pukul {$class->attendance_start_time} WIB.");
        }

        if ($now->gt($class->attendance_end_date_time)) {
            return back()->with('error', 'Waktu absensi telah berakhir. Anda tidak dapat melakukan absensi untuk kelas ini.');
        }

        BkClassAttendance::create([
            'bk_class_id' => $class->id,
            'student_id' => $student->id,
            'status' => 'hadir',
            'attended_at' => $now,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'is_manual_correction' => false,
        ]);

        return back()->with('success', 'Presensi kehadiran Anda berhasil dicatat. Terima kasih telah hadir tepat waktu.');
    }

    /**
     * Pengumpulan Tugas oleh Siswa
     */
    public function submitTask(Request $request, $id, $taskId)
    {
        $student = $this->getStudent();
        $class = BkClass::findOrFail($id);

        if (!$class->isStudentEligible($student)) {
            abort(403, 'Anda tidak memiliki hak akses pengerjaan tugas kelas ini.');
        }

        $task = BkClassTask::where('bk_class_id', $class->id)->findOrFail($taskId);
        $task->setRelation('bkClass', $class);

        $request->validate([
            'submission_text' => 'nullable|string',
            'file' => 'nullable|file|max:' . ($task->max_file_size_kb ?: 20480),
        ]);

        if (!$request->filled('submission_text') && !$request->hasFile('file')) {
            return back()->with('error', 'Mohon tulis jawaban atau unggah berkas tugas Anda sebelum mengirimkan.');
        }

        if ($request->hasFile('file') && $task->allowed_file_types) {
            $allowed = array_map('trim', explode(',', strtolower($task->allowed_file_types)));
            $clientExt = strtolower($request->file('file')->getClientOriginalExtension());
            if (!in_array($clientExt, $allowed)) {
                return back()->with('error', "Format berkas .{$clientExt} tidak diizinkan. Format yang diperbolehkan: {$task->allowed_file_types}");
            }
        }

        $now = Carbon::now('Asia/Jakarta');
        if (!$task->isOpen()) {
            return back()->with('error', 'Waktu pengerjaan tugas ini belum dibuka (jadwal buka: ' . ($task->start_time ? $task->start_time->format('d/m/Y H:i') : '-') . ' WIB).');
        }

        $filePath = null;
        $fileName = null;
        $fileSize = null;

        // Ambil submission lama jika ingin revisi/re-upload
        $existing = BkClassTaskSubmission::where('bk_class_task_id', $task->id)
            ->where('student_id', $student->id)
            ->first();

        if ($request->hasFile('file')) {
            // Hapus file lama jika ada
            if ($existing && $existing->file_path && Storage::disk('public')->exists($existing->file_path)) {
                Storage::disk('public')->delete($existing->file_path);
            }

            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $filePath = $file->store('kelas_bk/pengumpulan', 'public');
        } elseif ($existing) {
            $filePath = $existing->file_path;
            $fileName = $existing->file_name;
            $fileSize = $existing->file_size;
        }

        $status = $now->gt($task->due_time) ? 'terlambat' : 'diserahkan';

        BkClassTaskSubmission::updateOrCreate(
            [
                'bk_class_task_id' => $task->id,
                'student_id' => $student->id,
            ],
            [
                'submission_text' => $request->submission_text,
                'file_path' => $filePath,
                'file_name' => $fileName,
                'file_size' => $fileSize,
                'submitted_at' => $now,
                'status' => $status,
            ]
        );

        $statusMsg = $status === 'terlambat' ? ' (Tercatat Terlambat)' : '';
        return back()->with('success', "Tugas '{$task->title}' berhasil dikumpulkan{$statusMsg}.");
    }

    // ==========================================
    // UNDUH BERKAS (AMAN UNTUK SISWA)
    // ==========================================

    public function downloadMaterial($id, $materialId)
    {
        $student = $this->getStudent();
        $class = BkClass::findOrFail($id);

        if (!$class->isStudentEligible($student)) {
            abort(403);
        }

        $material = BkClassMaterial::where('bk_class_id', $class->id)->findOrFail($materialId);

        if (!$material->file_path || !Storage::disk('public')->exists($material->file_path)) {
            abort(404, 'Berkas materi tidak ditemukan.');
        }

        return Storage::disk('public')->download($material->file_path, $material->file_name ?: basename($material->file_path));
    }

    public function downloadTaskAttachment($id, $taskId)
    {
        $student = $this->getStudent();
        $class = BkClass::findOrFail($id);

        if (!$class->isStudentEligible($student)) {
            abort(403);
        }

        $task = BkClassTask::where('bk_class_id', $class->id)->findOrFail($taskId);

        if (!$task->attachment_path || !Storage::disk('public')->exists($task->attachment_path)) {
            abort(404, 'Berkas lampiran tugas tidak ditemukan.');
        }

        return Storage::disk('public')->download($task->attachment_path, $task->attachment_name ?: basename($task->attachment_path));
    }

    public function downloadMySubmission($id, $taskId)
    {
        $student = $this->getStudent();
        $class = BkClass::findOrFail($id);

        if (!$class->isStudentEligible($student)) {
            abort(403);
        }

        $task = BkClassTask::where('bk_class_id', $class->id)->findOrFail($taskId);
        $submission = BkClassTaskSubmission::where('bk_class_task_id', $task->id)
            ->where('student_id', $student->id)
            ->firstOrFail();

        if (!$submission->file_path || !Storage::disk('public')->exists($submission->file_path)) {
            abort(404, 'Berkas jawaban Anda tidak ditemukan.');
        }

        return Storage::disk('public')->download($submission->file_path, $submission->file_name ?: basename($submission->file_path));
    }
}
