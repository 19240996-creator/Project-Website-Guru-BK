<?php

namespace App\Http\Controllers\Guru\Koordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\User;
use App\Models\CounselorAssignment;
use App\Models\CounselorClassAssignment;
use App\Models\AuditLog;
use App\Models\Notification;

class AssignmentController extends Controller
{
    /**
     * Tampilan utama Pembagian Tugas Guru BK:
     * - Filter interaktif: Tingkat (Grade), Jurusan (Major), Kelas (Class), Status Penugasan
     * - Tabel daftar siswa yang sesuai filter dengan status penugasan saat ini
     * - Pemilihan Guru BK untuk penugasan
     * - Ringkasan beban kerja penugasan per Guru BK
     * - Ringkasan penugasan per Kelas
     */
    public function index(Request $request)
    {
        $selectedGrade = $request->input('grade');
        $selectedMajor = $request->input('major');
        $selectedClassId = $request->input('class_id');
        $assignmentStatus = $request->input('assignment_status'); // 'all', 'assigned', 'unassigned'
        $search = trim((string) $request->input('q'));

        // Ambil daftar tingkat yang tersedia
        $grades = ['X', 'XI', 'XII'];

        // Ambil daftar jurusan unik dari database
        $majors = StudentClass::whereNotNull('major')
            ->where('major', '!=', '')
            ->distinct()
            ->orderBy('major')
            ->pluck('major');

        // Query kelas berdasarkan filter tingkat/jurusan jika ada
        $classesQuery = StudentClass::withCount('students')->orderBy('grade')->orderBy('name');
        if ($selectedGrade) {
            $classesQuery->where('grade', $selectedGrade);
        }
        if ($selectedMajor) {
            $classesQuery->where('major', $selectedMajor);
        }
        $availableClasses = $classesQuery->get();

        // Ambil seluruh daftar Guru BK aktif untuk dropdown penugasan
        $counselors = User::where('role', 'guru_bk')
            ->where('is_active', true)
            ->withCount(['studentAssignments', 'classAssignments'])
            ->orderBy('name')
            ->get();

        // Query Siswa berdasarkan filter kelas dan jurusan yang dipilih
        $studentsQuery = Student::with(['studentClass', 'counselorAssignment.counselor'])
            ->where('status', 'aktif');

        if ($selectedGrade) {
            $studentsQuery->whereHas('studentClass', function ($q) use ($selectedGrade) {
                $q->where('grade', $selectedGrade);
            });
        }

        if ($selectedMajor) {
            $studentsQuery->whereHas('studentClass', function ($q) use ($selectedMajor) {
                $q->where('major', $selectedMajor);
            });
        }

        if ($selectedClassId) {
            $studentsQuery->where('student_class_id', $selectedClassId);
        }

        if ($search !== '') {
            $studentsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        if ($assignmentStatus === 'assigned') {
            $studentsQuery->whereHas('counselorAssignment');
        } elseif ($assignmentStatus === 'unassigned') {
            $studentsQuery->whereDoesntHave('counselorAssignment');
        }

        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $students = $studentsQuery->orderBy('name')->paginate($perPage)->withQueryString();

        // Statistik Keseluruhan Penugasan Sekolah
        $totalActiveStudents = Student::where('status', 'aktif')->count();
        $totalAssignedStudents = CounselorAssignment::whereHas('student', function ($q) {
            $q->where('status', 'aktif');
        })->count();
        $totalUnassignedStudents = max(0, $totalActiveStudents - $totalAssignedStudents);
        $assignmentPercentage = $totalActiveStudents > 0 
            ? round(($totalAssignedStudents / $totalActiveStudents) * 100, 1) 
            : 0;

        // Ringkasan per Guru BK (untuk tab beban kerja & ringkasan)
        $counselorWorkloads = User::where('role', 'guru_bk')
            ->with(['studentAssignments.studentClass', 'classAssignments.studentClass'])
            ->get()
            ->map(function ($guru) {
                $studentCount = $guru->studentAssignments()->count();
                $assignedClasses = $guru->studentAssignments()
                    ->with('studentClass')
                    ->get()
                    ->pluck('studentClass')
                    ->filter()
                    ->unique('id');
                
                return [
                    'guru' => $guru,
                    'student_count' => $studentCount,
                    'classes' => $assignedClasses,
                ];
            });

        // Ringkasan penugasan seluruh kelas
        $allClassesOverview = StudentClass::withCount('students')
            ->with(['classAssignment.counselor'])
            ->orderBy('grade')
            ->orderBy('name')
            ->get()
            ->map(function ($cls) {
                // Hitung berapa siswa di kelas ini yang sudah ditugaskan
                $assignedCount = CounselorAssignment::where('student_class_id', $cls->id)->count();
                // Deteksi counselor yang menangani mayoritas siswa kelas ini
                $dominantCounselor = CounselorAssignment::where('student_class_id', $cls->id)
                    ->select('counselor_id', DB::raw('count(*) as count'))
                    ->groupBy('counselor_id')
                    ->orderByDesc('count')
                    ->with('counselor')
                    ->first();

                return [
                    'class' => $cls,
                    'total_students' => $cls->students_count,
                    'assigned_count' => $assignedCount,
                    'counselor' => $cls->classAssignment?->counselor ?? $dominantCounselor?->counselor,
                    'is_fully_assigned' => $cls->students_count > 0 && $assignedCount >= $cls->students_count,
                ];
            });

        return view('guru.penugasan.index', compact(
            'grades',
            'majors',
            'availableClasses',
            'counselors',
            'students',
            'perPage',
            'selectedGrade',
            'selectedMajor',
            'selectedClassId',
            'assignmentStatus',
            'search',
            'totalActiveStudents',
            'totalAssignedStudents',
            'totalUnassignedStudents',
            'assignmentPercentage',
            'counselorWorkloads',
            'allClassesOverview'
        ));
    }

    /**
     * Menyimpan atau memperbarui penugasan siswa ke Guru BK terpilih.
     * Mencegah penugasan ganda karena kolom student_id bersifat unik di database.
     * Ketika dipindahkan dari Guru BK lain, hak akses guru sebelumnya dicabut secara otomatis.
     */
    public function assign(Request $request)
    {
        $validated = $request->validate([
            'counselor_id' => 'required|exists:users,id',
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
            'assign_whole_class' => 'nullable|boolean',
            'notes' => 'nullable|string|max:500',
        ], [
            'counselor_id.required' => 'Pilih Guru BK penanggung jawab terlebih dahulu.',
            'counselor_id.exists' => 'Guru BK yang dipilih tidak valid dalam sistem.',
            'student_ids.required' => 'Pilih minimal satu siswa untuk ditugaskan.',
            'student_ids.min' => 'Pilih minimal satu siswa untuk ditugaskan.',
        ]);

        $counselor = User::where('id', $validated['counselor_id'])
            ->where('role', 'guru_bk')
            ->firstOrFail();

        $studentIds = $validated['student_ids'];
        $students = Student::whereIn('id', $studentIds)->with('studentClass')->get();

        $reassignedCount = 0;
        $newAssignedCount = 0;
        $affectedClassIds = [];

        DB::transaction(function () use ($students, $counselor, $validated, &$reassignedCount, &$newAssignedCount, &$affectedClassIds) {
            foreach ($students as $student) {
                $affectedClassIds[] = $student->student_class_id;

                $existing = CounselorAssignment::where('student_id', $student->id)->first();

                if ($existing) {
                    if ($existing->counselor_id !== $counselor->id) {
                        $oldCounselorId = $existing->counselor_id;
                        $oldCounselor = User::find($oldCounselorId);
                        
                        $existing->update([
                            'counselor_id' => $counselor->id,
                            'student_class_id' => $student->student_class_id,
                            'assigned_by' => auth()->id(),
                            'notes' => $validated['notes'] ?? $existing->notes,
                        ]);

                        $reassignedCount++;
                        
                        // Notifikasi pemindahan ke guru lama
                        if ($oldCounselor) {
                            Notification::create([
                                'user_id' => $oldCounselor->id,
                                'title' => 'Perubahan Penugasan Siswa',
                                'message' => "Tugas penanganan siswa {$student->name} ({$student->studentClass->name}) telah dialihkan ke {$counselor->name} oleh Koordinator BK.",
                                'url' => route('guru.siswa.index'),
                            ]);
                        }
                    }
                } else {
                    CounselorAssignment::create([
                        'counselor_id' => $counselor->id,
                        'student_id' => $student->id,
                        'student_class_id' => $student->student_class_id,
                        'assigned_by' => auth()->id(),
                        'notes' => $validated['notes'] ?? null,
                    ]);
                    $newAssignedCount++;
                }
            }

            // Update penugasan kelas induk jika diminta atau jika seluruh siswa kelas terikat ke guru ini
            $uniqueClassIds = array_unique(array_filter($affectedClassIds));
            foreach ($uniqueClassIds as $classId) {
                $totalInClass = Student::where('student_class_id', $classId)->where('status', 'aktif')->count();
                $assignedToThisCounselorInClass = CounselorAssignment::where('student_class_id', $classId)
                    ->where('counselor_id', $counselor->id)
                    ->count();

                if ($totalInClass > 0 && $assignedToThisCounselorInClass >= $totalInClass) {
                    CounselorClassAssignment::updateOrCreate(
                        ['student_class_id' => $classId],
                        ['counselor_id' => $counselor->id, 'assigned_by' => auth()->id()]
                    );
                }
            }

            // Notifikasi ke Guru BK penerima tugas
            Notification::create([
                'user_id' => $counselor->id,
                'title' => 'Penugasan Siswa Baru',
                'message' => "Koordinator BK telah menugaskan " . count($students) . " siswa kepada Anda.",
                'url' => route('guru.siswa.index'),
            ]);

            AuditLog::log(
                'PENUGASAN_GURU_BK',
                'CounselorAssignment',
                $counselor->id,
                "Koordinator BK menugaskan " . count($students) . " siswa kepada {$counselor->name} ({$newAssignedCount} penugasan baru, {$reassignedCount} pemindahan)."
            );
        });

        $msg = "Berhasil membagikan tugas: " . count($students) . " siswa kini resmi ditugaskan kepada {$counselor->name}.";
        if ($reassignedCount > 0) {
            $msg .= " ({$reassignedCount} siswa dipindahkan dari Guru BK sebelumnya dan hak akses lama telah dicabut).";
        }

        return back()->with('success', $msg);
    }

    /**
     * Menghapus / melepas penugasan siswa (kembali ke status belum ditugaskan).
     */
    public function unassign(Request $request)
    {
        $validated = $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
        ], [
            'student_ids.required' => 'Pilih siswa yang ingin dilepas penugasannya.',
        ]);

        $studentIds = $validated['student_ids'];
        $count = 0;

        DB::transaction(function () use ($studentIds, &$count) {
            $assignments = CounselorAssignment::whereIn('student_id', $studentIds)->with(['counselor', 'student'])->get();
            $count = $assignments->count();

            foreach ($assignments as $a) {
                // Hapus penugasan kelas jika tidak ada lagi siswa yang ditugaskan ke guru ini di kelas tersebut
                $classId = $a->student_class_id;
                $counselorId = $a->counselor_id;

                $a->delete();

                $remainingInClass = CounselorAssignment::where('student_class_id', $classId)
                    ->where('counselor_id', $counselorId)
                    ->count();

                if ($remainingInClass === 0) {
                    CounselorClassAssignment::where('student_class_id', $classId)
                        ->where('counselor_id', $counselorId)
                        ->delete();
                }
            }

            AuditLog::log('LEPAS_PENUGASAN', 'CounselorAssignment', auth()->id(), "Koordinator BK melepas penugasan untuk {$count} siswa.");
        });

        return back()->with('success', "Penugasan untuk {$count} siswa berhasil dilepas. Data siswa kini berstatus belum ditugaskan.");
    }

    /**
     * Memindahkan seluruh penugasan dari satu Guru BK ke Guru BK lain secara massal.
     */
    public function reassignMassal(Request $request)
    {
        $validated = $request->validate([
            'from_counselor_id' => 'required|exists:users,id',
            'to_counselor_id' => 'required|exists:users,id|different:from_counselor_id',
            'notes' => 'nullable|string|max:500',
        ], [
            'from_counselor_id.required' => 'Pilih Guru BK asal yang tugasnya ingin dipindahkan.',
            'to_counselor_id.required' => 'Pilih Guru BK tujuan pemindahan tugas.',
            'to_counselor_id.different' => 'Guru BK tujuan harus berbeda dengan Guru BK asal.',
        ]);

        $fromCounselor = User::findOrFail($validated['from_counselor_id']);
        $toCounselor = User::findOrFail($validated['to_counselor_id']);

        $movedCount = 0;

        DB::transaction(function () use ($fromCounselor, $toCounselor, $validated, &$movedCount) {
            $assignments = CounselorAssignment::where('counselor_id', $fromCounselor->id)->get();
            $movedCount = $assignments->count();

            foreach ($assignments as $assignment) {
                $assignment->update([
                    'counselor_id' => $toCounselor->id,
                    'assigned_by' => auth()->id(),
                    'notes' => $validated['notes'] ?? $assignment->notes,
                ]);
            }

            // Pindahkan juga class assignments
            CounselorClassAssignment::where('counselor_id', $fromCounselor->id)
                ->update(['counselor_id' => $toCounselor->id, 'assigned_by' => auth()->id()]);

            Notification::create([
                'user_id' => $fromCounselor->id,
                'title' => 'Pengalihan Seluruh Tugas Siswa',
                'message' => "Seluruh siswa binaan Anda ({$movedCount} siswa) telah dialihkan ke {$toCounselor->name} oleh Koordinator BK.",
                'url' => route('guru.siswa.index'),
            ]);

            Notification::create([
                'user_id' => $toCounselor->id,
                'title' => 'Penerimaan Seluruh Tugas Siswa Baru',
                'message' => "Koordinator BK telah mengalihkan {$movedCount} siswa dari {$fromCounselor->name} kepada Anda.",
                'url' => route('guru.siswa.index'),
            ]);

            AuditLog::log(
                'PINDAH_PENUGASAN_MASSAL',
                'CounselorAssignment',
                $toCounselor->id,
                "Koordinator BK mengalihkan seluruh tugas ({$movedCount} siswa) dari {$fromCounselor->name} ke {$toCounselor->name}."
            );
        });

        return back()->with('success', "Berhasil memindahkan seluruh tugas ({$movedCount} siswa) dari {$fromCounselor->name} ke {$toCounselor->name}. Hak akses Guru BK lama telah dicabut secara otomatis.");
    }
}
