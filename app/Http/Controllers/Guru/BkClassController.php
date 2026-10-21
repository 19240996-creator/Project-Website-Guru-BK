<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\BkClass;
use App\Models\BkClassMaterial;
use App\Models\BkClassTask;
use App\Models\BkClassTaskSubmission;
use App\Models\BkClassAttendance;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\CounselorAssignment;
use App\Models\CounselorClassAssignment;
use App\Models\AuditLog;

class BkClassController extends Controller
{
    /**
     * Dapatkan daftar ID kelas (rombel) yang berada dalam lingkup binaan Guru BK yang sedang login.
     * Jika koordinator, mengembalikan null (memiliki akses ke seluruh rombel sekolah).
     */
    protected function getAllowedClassIds(): ?array
    {
        $user = auth()->user();
        if ($user->isKoordinatorBk()) {
            return null; // Bebas akses semua kelas
        }

        $assignedClassIds = CounselorAssignment::where('counselor_id', $user->id)
            ->pluck('student_class_id')
            ->unique()
            ->filter()
            ->values()
            ->toArray();

        $classAssignedIds = CounselorClassAssignment::where('counselor_id', $user->id)
            ->pluck('student_class_id')
            ->unique()
            ->filter()
            ->values()
            ->toArray();

        return array_values(array_unique(array_merge($assignedClassIds, $classAssignedIds)));
    }

    /**
     * Tampilkan daftar Kelas BK Online
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = BkClass::with(['counselor', 'materials', 'tasks', 'attendances']);

        // Batasi cakupan: Guru BK biasa hanya melihat kelas buatannya sendiri
        if ($user->role === 'guru_bk') {
            $query->where('counselor_id', $user->id);
        }

        // Filter pencarian
        if ($request->filled('q')) {
            $search = trim($request->q);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter tanggal
        if ($request->filled('date_filter')) {
            $today = Carbon::now('Asia/Jakarta')->toDateString();
            if ($request->date_filter === 'today') {
                $query->whereDate('class_date', $today);
            } elseif ($request->date_filter === 'upcoming') {
                $query->whereDate('class_date', '>=', $today);
            } elseif ($request->date_filter === 'past') {
                $query->whereDate('class_date', '<', $today);
            }
        }

        // Ambil data terurut dari tanggal kelas terbaru
        $classes = $query->orderBy('class_date', 'desc')
                         ->orderBy('start_time', 'desc')
                         ->paginate(10)
                         ->withQueryString();

        // Filter status tab jika ada
        $activeTab = $request->input('tab', 'all');

        // Statistik ringkas untuk widget atas
        $baseStatQuery = BkClass::query();
        if ($user->role === 'guru_bk') {
            $baseStatQuery->where('counselor_id', $user->id);
        }
        $allClasses = (clone $baseStatQuery)->get();
        $totalClassesCount = $allClasses->count();
        $terjadwalCount = $allClasses->filter(fn($c) => $c->computed_status === 'terjadwal')->count();
        $berlangsungCount = $allClasses->filter(fn($c) => $c->computed_status === 'berlangsung')->count();
        $selesaiCount = $allClasses->filter(fn($c) => $c->computed_status === 'selesai')->count();

        return view('guru.kelas.index', compact(
            'classes',
            'activeTab',
            'totalClassesCount',
            'terjadwalCount',
            'berlangsungCount',
            'selesaiCount'
        ));
    }

    /**
     * Form tambah Kelas BK baru
     */
    public function create()
    {
        $allowedClassIds = $this->getAllowedClassIds();

        $classesQuery = StudentClass::withCount('students')->orderBy('grade')->orderBy('name');
        if ($allowedClassIds !== null) {
            $classesQuery->whereIn('id', $allowedClassIds);
        }
        $availableClasses = $classesQuery->get();

        $grades = ['X', 'XI', 'XII'];
        $majors = StudentClass::whereNotNull('major')
            ->where('major', '!=', '')
            ->distinct()
            ->orderBy('major')
            ->pluck('major');

        return view('guru.kelas.create', compact('availableClasses', 'grades', 'majors'));
    }

    /**
     * Simpan Kelas BK baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_grade' => 'nullable|string|in:X,XI,XII',
            'target_major' => 'nullable|string|max:100',
            'target_class_ids' => 'nullable|array',
            'target_class_ids.*' => 'integer|exists:student_classes,id',
            'class_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'attendance_start_time' => 'required',
            'attendance_end_time' => 'required|after:attendance_start_time',
        ]);

        $allowedClassIds = $this->getAllowedClassIds();
        $targetClassIds = $request->target_class_ids;

        // Validasi batasan kewenangan rombel bagi guru BK non-koordinator
        if ($allowedClassIds !== null && !empty($targetClassIds)) {
            foreach ($targetClassIds as $cId) {
                if (!in_array((int)$cId, $allowedClassIds)) {
                    return back()->withInput()->with('error', 'Anda tidak memiliki hak akses penugasan untuk memilih kelas tersebut.');
                }
            }
        }

        $class = BkClass::create([
            'counselor_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'target_grade' => $request->target_grade,
            'target_major' => $request->target_major,
            'target_class_ids' => $targetClassIds,
            'class_date' => $request->class_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'attendance_start_time' => $request->attendance_start_time,
            'attendance_end_time' => $request->attendance_end_time,
            'status' => 'terjadwal',
        ]);

        AuditLog::log('CREATE', 'BkClass', $class->id, "Membuat kelas BK Online: {$class->title} ({$class->target_classes_text})");

        return redirect()->route('guru.kelas.show', $class->id)
                         ->with('success', 'Kelas BK Online berhasil dibuat dan dijadwalkan.');
    }

    /**
     * Detail dan Manajemen Kelas BK (Ringkasan, Materi, Tugas, Presensi)
     */
    public function show(Request $request, $id)
    {
        $class = BkClass::with(['counselor', 'materials', 'tasks.submissions.student', 'attendances.student.studentClass'])->findOrFail($id);

        $user = auth()->user();
        if ($user->role === 'guru_bk' && $class->counselor_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk mengelola kelas ini.');
        }

        // Ambil seluruh siswa target yang eligible
        $targetStudents = $class->getTargetStudents();
        $targetStudentsCount = $targetStudents->count();

        // Mapping presensi
        $attendanceMap = $class->attendances->keyBy('student_id');
        $attendedCount = $class->attendances->where('status', 'hadir')->count();
        $lateCount = $class->attendances->where('status', 'terlambat')->count();
        $absentCount = $class->attendances->whereIn('status', ['izin', 'sakit', 'alpha'])->count();

        $activeTab = $request->input('tab', 'materi');

        return view('guru.kelas.show', compact(
            'class',
            'targetStudents',
            'targetStudentsCount',
            'attendanceMap',
            'attendedCount',
            'lateCount',
            'absentCount',
            'activeTab'
        ));
    }

    /**
     * Form edit Kelas BK
     */
    public function edit($id)
    {
        $class = BkClass::findOrFail($id);

        $user = auth()->user();
        if ($user->role === 'guru_bk' && $class->counselor_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit kelas ini.');
        }

        $allowedClassIds = $this->getAllowedClassIds();
        $classesQuery = StudentClass::withCount('students')->orderBy('grade')->orderBy('name');
        if ($allowedClassIds !== null) {
            $classesQuery->whereIn('id', $allowedClassIds);
        }
        $availableClasses = $classesQuery->get();

        $grades = ['X', 'XI', 'XII'];
        $majors = StudentClass::whereNotNull('major')
            ->where('major', '!=', '')
            ->distinct()
            ->orderBy('major')
            ->pluck('major');

        return view('guru.kelas.edit', compact('class', 'availableClasses', 'grades', 'majors'));
    }

    /**
     * Update data Kelas BK
     */
    public function update(Request $request, $id)
    {
        $class = BkClass::findOrFail($id);

        $user = auth()->user();
        if ($user->role === 'guru_bk' && $class->counselor_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk mengupdate kelas ini.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_grade' => 'nullable|string|in:X,XI,XII',
            'target_major' => 'nullable|string|max:100',
            'target_class_ids' => 'nullable|array',
            'target_class_ids.*' => 'integer|exists:student_classes,id',
            'class_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'attendance_start_time' => 'required',
            'attendance_end_time' => 'required|after:attendance_start_time',
            'status' => 'nullable|in:terjadwal,berlangsung,selesai,dibatalkan',
        ]);

        $allowedClassIds = $this->getAllowedClassIds();
        $targetClassIds = $request->target_class_ids;

        if ($allowedClassIds !== null && !empty($targetClassIds)) {
            foreach ($targetClassIds as $cId) {
                if (!in_array((int)$cId, $allowedClassIds)) {
                    return back()->withInput()->with('error', 'Anda tidak memiliki hak akses penugasan untuk memilih kelas tersebut.');
                }
            }
        }

        $class->update([
            'title' => $request->title,
            'description' => $request->description,
            'target_grade' => $request->target_grade,
            'target_major' => $request->target_major,
            'target_class_ids' => $targetClassIds,
            'class_date' => $request->class_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'attendance_start_time' => $request->attendance_start_time,
            'attendance_end_time' => $request->attendance_end_time,
            'status' => $request->status ?: $class->status,
        ]);

        AuditLog::log('UPDATE', 'BkClass', $class->id, "Memperbarui data kelas BK Online: {$class->title}");

        return redirect()->route('guru.kelas.show', $class->id)
                         ->with('success', 'Perubahan informasi kelas BK berhasil disimpan.');
    }

    /**
     * Hapus Kelas BK
     */
    public function destroy($id)
    {
        $class = BkClass::findOrFail($id);

        $user = auth()->user();
        if ($user->role === 'guru_bk' && $class->counselor_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus kelas ini.');
        }

        $title = $class->title;
        $class->delete();

        AuditLog::log('DELETE', 'BkClass', $id, "Menghapus kelas BK Online: {$title}");

        return redirect()->route('guru.kelas.index')
                         ->with('success', "Kelas BK Online '{$title}' berhasil dihapus.");
    }

    // ==========================================
    // MANAJEMEN MATERI PEMBELAJARAN
    // ==========================================

    public function storeMaterial(Request $request, $id)
    {
        $class = BkClass::findOrFail($id);
        $user = auth()->user();
        if ($user->role === 'guru_bk' && $class->counselor_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'material_type' => 'required|in:slide,video,document',
            'file' => 'nullable|file|max:51200', // max 50MB
            'video_url' => 'nullable|url|max:500',
        ]);

        $filePath = null;
        $fileName = null;
        $fileSize = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $filePath = $file->store('kelas_bk/materi', 'public');
        }

        $material = BkClassMaterial::create([
            'bk_class_id' => $class->id,
            'title' => $request->title,
            'description' => $request->description,
            'material_type' => $request->material_type,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_size' => $fileSize,
            'video_url' => $request->video_url,
            'order_num' => $class->materials()->count() + 1,
        ]);

        AuditLog::log('CREATE', 'BkClassMaterial', $material->id, "Menambahkan materi '{$material->title}' pada kelas BK '{$class->title}'");

        return redirect()->route('guru.kelas.show', ['id' => $class->id, 'tab' => 'materi'])
                         ->with('success', "Materi pembelajaran '{$material->title}' berhasil ditambahkan.");
    }

    public function updateMaterial(Request $request, $id, $materialId)
    {
        $class = BkClass::findOrFail($id);
        $user = auth()->user();
        if ($user->role === 'guru_bk' && $class->counselor_id !== $user->id) {
            abort(403);
        }

        $material = BkClassMaterial::where('bk_class_id', $class->id)->findOrFail($materialId);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'material_type' => 'required|in:slide,video,document',
            'file' => 'nullable|file|max:51200',
            'video_url' => 'nullable|url|max:500',
        ]);

        $updateData = [
            'title' => $request->title,
            'description' => $request->description,
            'material_type' => $request->material_type,
            'video_url' => $request->video_url,
        ];

        if ($request->hasFile('file')) {
            if ($material->file_path && Storage::disk('public')->exists($material->file_path)) {
                Storage::disk('public')->delete($material->file_path);
            }
            $file = $request->file('file');
            $updateData['file_name'] = $file->getClientOriginalName();
            $updateData['file_size'] = $file->getSize();
            $updateData['file_path'] = $file->store('kelas_bk/materi', 'public');
        }

        $material->update($updateData);

        AuditLog::log('UPDATE', 'BkClassMaterial', $material->id, "Memperbarui materi '{$material->title}' pada kelas BK '{$class->title}'");

        return redirect()->route('guru.kelas.show', ['id' => $class->id, 'tab' => 'materi'])
                         ->with('success', "Materi pembelajaran '{$material->title}' berhasil diperbarui.");
    }

    public function reorderMaterial(Request $request, $id, $materialId)
    {
        $class = BkClass::findOrFail($id);
        $user = auth()->user();
        if ($user->role === 'guru_bk' && $class->counselor_id !== $user->id) {
            abort(403);
        }

        $material = BkClassMaterial::where('bk_class_id', $class->id)->findOrFail($materialId);
        $direction = $request->input('direction', 'up');

        $allMaterials = $class->materials()->orderBy('order_num')->orderBy('id')->get();
        $index = $allMaterials->search(fn($m) => $m->id === $material->id);

        if ($index !== false) {
            if ($direction === 'up' && $index > 0) {
                $prev = $allMaterials[$index - 1];
                $tempOrder = $material->order_num ?: ($index + 1);
                $prevOrder = $prev->order_num ?: $index;
                if ($tempOrder === $prevOrder) {
                    $material->order_num = $index;
                    $prev->order_num = $index + 1;
                } else {
                    $material->order_num = $prevOrder;
                    $prev->order_num = $tempOrder;
                }
                $material->save();
                $prev->save();
            } elseif ($direction === 'down' && $index < $allMaterials->count() - 1) {
                $next = $allMaterials[$index + 1];
                $tempOrder = $material->order_num ?: ($index + 1);
                $nextOrder = $next->order_num ?: ($index + 2);
                if ($tempOrder === $nextOrder) {
                    $material->order_num = $index + 2;
                    $next->order_num = $index + 1;
                } else {
                    $material->order_num = $nextOrder;
                    $next->order_num = $tempOrder;
                }
                $material->save();
                $next->save();
            }
        }

        return redirect()->route('guru.kelas.show', ['id' => $class->id, 'tab' => 'materi'])
                         ->with('success', 'Urutan materi pembelajaran berhasil disesuaikan.');
    }

    public function destroyMaterial($id, $materialId)
    {
        $class = BkClass::findOrFail($id);
        $user = auth()->user();
        if ($user->role === 'guru_bk' && $class->counselor_id !== $user->id) {
            abort(403);
        }

        $material = BkClassMaterial::where('bk_class_id', $class->id)->findOrFail($materialId);

        if ($material->file_path && Storage::disk('public')->exists($material->file_path)) {
            Storage::disk('public')->delete($material->file_path);
        }

        $title = $material->title;
        $material->delete();

        AuditLog::log('DELETE', 'BkClassMaterial', $materialId, "Menghapus materi '{$title}' dari kelas BK '{$class->title}'");

        return redirect()->route('guru.kelas.show', ['id' => $class->id, 'tab' => 'materi'])
                         ->with('success', "Materi pembelajaran '{$title}' berhasil dihapus.");
    }

    // ==========================================
    // MANAJEMEN TUGAS PEMBELAJARAN
    // ==========================================

    public function storeTask(Request $request, $id)
    {
        $class = BkClass::findOrFail($id);
        $user = auth()->user();
        if ($user->role === 'guru_bk' && $class->counselor_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'instructions' => 'required|string',
            'start_time' => 'required|date',
            'due_time' => 'required|date|after:start_time',
            'attachment' => 'nullable|file|max:20480', // 20MB
            'allowed_file_types' => 'nullable|string|max:255',
        ]);

        $attachmentPath = null;
        $attachmentName = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentName = $file->getClientOriginalName();
            $attachmentPath = $file->store('kelas_bk/tugas', 'public');
        }

        $task = BkClassTask::create([
            'bk_class_id' => $class->id,
            'title' => $request->title,
            'instructions' => $request->instructions,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'start_time' => $request->start_time,
            'due_time' => $request->due_time,
            'allowed_file_types' => $request->allowed_file_types ?: 'pdf,docx,doc,ppt,pptx,zip,jpg,jpeg,png',
            'max_file_size_kb' => 20480,
        ]);

        AuditLog::log('CREATE', 'BkClassTask', $task->id, "Membuat tugas '{$task->title}' pada kelas BK '{$class->title}'");

        return redirect()->route('guru.kelas.show', ['id' => $class->id, 'tab' => 'tugas'])
                         ->with('success', "Tugas '{$task->title}' berhasil dibuat.");
    }

    public function updateTask(Request $request, $id, $taskId)
    {
        $class = BkClass::findOrFail($id);
        $user = auth()->user();
        if ($user->role === 'guru_bk' && $class->counselor_id !== $user->id) {
            abort(403);
        }

        $task = BkClassTask::where('bk_class_id', $class->id)->findOrFail($taskId);

        $request->validate([
            'title' => 'required|string|max:255',
            'instructions' => 'required|string',
            'start_time' => 'required|date',
            'due_time' => 'required|date|after:start_time',
            'attachment' => 'nullable|file|max:20480',
            'allowed_file_types' => 'nullable|string|max:255',
        ]);

        $updateData = [
            'title' => $request->title,
            'instructions' => $request->instructions,
            'start_time' => $request->start_time,
            'due_time' => $request->due_time,
            'allowed_file_types' => $request->allowed_file_types ?: 'pdf,docx,doc,ppt,pptx,zip,jpg,jpeg,png',
        ];

        if ($request->hasFile('attachment')) {
            if ($task->attachment_path && Storage::disk('public')->exists($task->attachment_path)) {
                Storage::disk('public')->delete($task->attachment_path);
            }
            $file = $request->file('attachment');
            $updateData['attachment_name'] = $file->getClientOriginalName();
            $updateData['attachment_path'] = $file->store('kelas_bk/tugas', 'public');
        }

        $task->update($updateData);

        AuditLog::log('UPDATE', 'BkClassTask', $task->id, "Memperbarui tugas '{$task->title}' pada kelas BK '{$class->title}'");

        return redirect()->route('guru.kelas.show', ['id' => $class->id, 'tab' => 'tugas'])
                         ->with('success', "Tugas '{$task->title}' berhasil diperbarui.");
    }

    public function destroyTask($id, $taskId)
    {
        $class = BkClass::findOrFail($id);
        $user = auth()->user();
        if ($user->role === 'guru_bk' && $class->counselor_id !== $user->id) {
            abort(403);
        }

        $task = BkClassTask::where('bk_class_id', $class->id)->findOrFail($taskId);

        if ($task->attachment_path && Storage::disk('public')->exists($task->attachment_path)) {
            Storage::disk('public')->delete($task->attachment_path);
        }

        // Hapus file tugas yang diunggah siswa
        foreach ($task->submissions as $submission) {
            if ($submission->file_path && Storage::disk('public')->exists($submission->file_path)) {
                Storage::disk('public')->delete($submission->file_path);
            }
        }

        $title = $task->title;
        $task->delete();

        AuditLog::log('DELETE', 'BkClassTask', $taskId, "Menghapus tugas '{$title}' dari kelas BK '{$class->title}'");

        return redirect()->route('guru.kelas.show', ['id' => $class->id, 'tab' => 'tugas'])
                         ->with('success', "Tugas '{$title}' beserta berkas pengumpulan berhasil dihapus.");
    }

    /**
     * Penilaian Tugas Siswa
     */
    public function gradeSubmission(Request $request, $id, $submissionId)
    {
        $class = BkClass::findOrFail($id);
        $user = auth()->user();
        if ($user->role === 'guru_bk' && $class->counselor_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'score' => 'nullable|integer|min:0|max:100',
            'status' => 'required|in:dinilai,perlu_revisi,diserahkan',
            'counselor_feedback' => 'nullable|string',
        ]);

        $submission = BkClassTaskSubmission::with('student', 'task')->findOrFail($submissionId);

        $submission->update([
            'score' => $request->score,
            'status' => $request->status,
            'counselor_feedback' => $request->counselor_feedback,
            'graded_at' => Carbon::now('Asia/Jakarta'),
            'graded_by' => $user->id,
        ]);

        AuditLog::log('UPDATE', 'BkClassTaskSubmission', $submission->id, "Menilai tugas {$submission->task->title} untuk siswa {$submission->student->name}: Nilai {$request->score}");

        return back()->with('success', "Hasil tugas siswa {$submission->student->name} berhasil diperbarui.");
    }

    // ==========================================
    // REKAP & KOREKSI MANUAL PRESENSI
    // ==========================================

    /**
     * Koreksi manual status kehadiran siswa oleh Guru BK dengan pencatatan alasan resmi
     */
    public function correctAttendance(Request $request, $id)
    {
        $class = BkClass::findOrFail($id);
        $user = auth()->user();
        if ($user->role === 'guru_bk' && $class->counselor_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'status' => 'required|in:hadir,terlambat,izin,sakit,alpha',
            'correction_reason' => 'required|string|max:500',
        ]);

        $student = Student::findOrFail($request->student_id);

        if (!$class->isStudentEligible($student)) {
            return back()->with('error', 'Siswa tersebut bukan sasaran kelas ini.');
        }

        $attendance = BkClassAttendance::updateOrCreate(
            [
                'bk_class_id' => $class->id,
                'student_id' => $student->id,
            ],
            [
                'status' => $request->status,
                'attended_at' => Carbon::now('Asia/Jakarta'),
                'is_manual_correction' => true,
                'correction_reason' => $request->correction_reason,
                'corrected_by' => $user->id,
            ]
        );

        AuditLog::log('UPDATE', 'BkClassAttendance', $attendance->id, "Koreksi kehadiran kelas BK '{$class->title}' untuk siswa {$student->name} menjadi {$request->status}. Alasan: {$request->correction_reason}");

        return back()->with('success', "Status presensi untuk {$student->name} berhasil dikoreksi menjadi '{$attendance->status_label}'.");
    }

    // ==========================================
    // UNDUH BERKAS (AMAN)
    // ==========================================

    public function downloadMaterial($id, $materialId)
    {
        $class = BkClass::findOrFail($id);
        $material = BkClassMaterial::where('bk_class_id', $class->id)->findOrFail($materialId);

        if (!$material->file_path || !Storage::disk('public')->exists($material->file_path)) {
            abort(404, 'Berkas materi tidak ditemukan pada penyimpanan server.');
        }

        return Storage::disk('public')->download($material->file_path, $material->file_name ?: basename($material->file_path));
    }

    public function downloadTaskAttachment($id, $taskId)
    {
        $class = BkClass::findOrFail($id);
        $task = BkClassTask::where('bk_class_id', $class->id)->findOrFail($taskId);

        if (!$task->attachment_path || !Storage::disk('public')->exists($task->attachment_path)) {
            abort(404, 'Berkas lampiran tugas tidak ditemukan pada penyimpanan server.');
        }

        return Storage::disk('public')->download($task->attachment_path, $task->attachment_name ?: basename($task->attachment_path));
    }

    public function downloadSubmissionFile($id, $submissionId)
    {
        $class = BkClass::findOrFail($id);
        $submission = BkClassTaskSubmission::findOrFail($submissionId);

        if (!$submission->file_path || !Storage::disk('public')->exists($submission->file_path)) {
            abort(404, 'Berkas jawaban siswa tidak ditemukan.');
        }

        return Storage::disk('public')->download($submission->file_path, $submission->file_name ?: basename($submission->file_path));
    }

    /**
     * Lihat / Stream berkas tugas siswa secara inline (foto/dokumen/PDF)
     */
    public function viewSubmissionFile($id, $submissionId)
    {
        $class = BkClass::findOrFail($id);
        $submission = BkClassTaskSubmission::findOrFail($submissionId);

        if (!$submission->file_path || !Storage::disk('public')->exists($submission->file_path)) {
            abort(404, 'Berkas jawaban siswa tidak ditemukan.');
        }

        $path = Storage::disk('public')->path($submission->file_path);
        $mime = Storage::disk('public')->mimeType($submission->file_path) ?: 'application/octet-stream';

        return response()->file($path, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . ($submission->file_name ?: basename($submission->file_path)) . '"',
        ]);
    }

    /**
     * Penilaian Tugas Siswa secara manual (baik yang sudah upload maupun penilaian offline/tatap muka)
     */
    public function gradeTaskStudent(Request $request, $id, $taskId)
    {
        $class = BkClass::findOrFail($id);
        $user = auth()->user();
        if ($user->role === 'guru_bk' && $class->counselor_id !== $user->id) {
            abort(403);
        }

        $task = BkClassTask::where('bk_class_id', $class->id)->findOrFail($taskId);

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'score' => 'nullable|integer|min:0|max:100',
            'status' => 'required|in:dinilai,perlu_revisi,diserahkan',
            'counselor_feedback' => 'nullable|string',
        ]);

        $student = Student::findOrFail($request->student_id);

        $submission = BkClassTaskSubmission::firstOrNew([
            'bk_class_task_id' => $task->id,
            'student_id' => $student->id,
        ]);

        if (!$submission->exists) {
            $submission->submitted_at = Carbon::now('Asia/Jakarta');
            $submission->submission_text = $request->submission_text ?: '(Penilaian langsung / manual oleh Guru BK)';
        }

        $submission->score = $request->score;
        $submission->status = $request->status;
        $submission->counselor_feedback = $request->counselor_feedback;
        $submission->graded_at = Carbon::now('Asia/Jakarta');
        $submission->graded_by = $user->id;
        $submission->save();

        AuditLog::log('UPDATE', 'BkClassTaskSubmission', $submission->id, "Menilai tugas {$task->title} untuk siswa {$student->name}: Nilai {$request->score}");

        return redirect()->route('guru.kelas.show', ['id' => $class->id, 'tab' => 'tugas'])
                         ->with('success', "Nilai tugas untuk {$student->name} berhasil disimpan.");
    }
}
