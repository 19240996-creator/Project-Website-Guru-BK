<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\User;
use App\Models\StudentAchievement;
use App\Models\AuditLog;
use App\Models\AcademicYear;
use App\Models\AlumniTracking;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['studentClass', 'user'])->latest();

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('nisn', 'like', "%{$q}%")
                    ->orWhere('nis', 'like', "%{$q}%");
            });
        }

        if ($request->filled('class_id')) {
            $query->where('student_class_id', $request->class_id);
        }

        if ($request->filled('grade')) {
            $query->whereHas('studentClass', function ($q) use ($request) {
                $q->where('grade', $request->grade);
            });
        }

        if ($request->filled('attention_level')) {
            $query->where('attention_level', $request->attention_level);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perPage = (int) $request->input('per_page', 5);
        if (!in_array($perPage, [5, 10, 25, 50])) {
            $perPage = 5;
        }

        $students = $query->paginate($perPage)->withQueryString();
        $classes = StudentClass::orderBy('grade')->orderBy('name')->get();
        $majors = StudentClass::whereNotNull('major')
            ->where('major', '!=', '')
            ->distinct()
            ->orderBy('major')
            ->pluck('major');
        $allStudents = Student::with('studentClass')->where('status', 'aktif')->orderBy('name')->get();

        return view('guru.siswa.index', compact('students', 'classes', 'majors', 'allStudents'));
    }

    public function show($id)
    {
        $student = Student::with([
            'studentClass.academicYear',
            'user',
            'achievements',
            'counselings.category',
            'counselings.followUps',
            'assessmentResults.assessment',
            'futurePlans' => function ($q) {
                $q->latest();
            },
            'opportunityRegistrations.opportunity',
            'alumniTracking'
        ])->findOrFail($id);

        $classes = StudentClass::orderBy('grade')->orderBy('name')->get();

        return view('guru.siswa.show', compact('student', 'classes'));
    }

    public function create()
    {
        $classes = StudentClass::orderBy('grade')->orderBy('name')->get();
        return view('guru.siswa.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nis' => 'required|string|unique:students,nis',
            'nisn' => 'required|string|unique:students,nisn',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'student_class_id' => 'required|exists:student_classes,id',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'phone' => 'nullable|string|max:25',
            'address' => 'nullable|string',
            'parent_name' => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:25',
            'parent_job' => 'nullable|string|max:100',
            'attention_level' => 'required|in:normal,perlu_perhatian,prioritas,segera_ditindaklanjuti',
            'special_notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            // Create user account for student
            $user = User::create([
                'name' => $validated['name'],
                'username' => $validated['nisn'],
                'email' => $validated['nisn'] . '@siswa.sch.id',
                'password' => Hash::make('password123'),
                'role' => 'siswa',
                'phone' => $validated['phone'],
                'is_active' => true,
            ]);

            $studentData = $validated;
            $studentData['user_id'] = $user->id;
            $student = Student::create($studentData);

            AuditLog::log('SIMPAN', 'Student', $student->id, "Menambahkan data siswa baru: {$student->name} ({$student->nisn}).");
        });

        return redirect()->route('guru.siswa.index')->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $student = Student::findOrFail($id);
        $classes = StudentClass::orderBy('grade')->orderBy('name')->get();
        $allStudents = Student::with('studentClass')->where('status', 'aktif')->orderBy('name')->get();
        return view('guru.siswa.edit', compact('student', 'classes', 'allStudents'));
    }

    public function massPromote(Request $request)
    {
        $validated = $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
            'target_class' => 'nullable|string',
            'target_class_id' => 'nullable',
        ], [
            'student_ids.required' => 'Pilih minimal satu siswa untuk dinaikkan kelas atau dijadikan alumni.',
            'student_ids.min' => 'Pilih minimal satu siswa untuk dinaikkan kelas atau dijadikan alumni.',
        ]);

        $rawTarget = $request->input('target_class') ?: $request->input('target_class_id');
        if (!$rawTarget) {
            return redirect()->back()->withErrors(['target_class' => 'Pilih kelas tujuan kenaikan kelas (10, 11, 12, atau Alumni).']);
        }

        $isAlumni = in_array(strtolower((string) $rawTarget), ['alumni', 'lulus']);
        $targetGrade = null;
        $gradeLabel = '';
        if ($isAlumni) {
            $gradeLabel = 'Alumni (Lulus)';
        } elseif (in_array((string) $rawTarget, ['10', 'grade:10', 'grade:X', 'X'])) {
            $targetGrade = 'X';
            $gradeLabel = 'Kelas 10 (Tingkat X)';
        } elseif (in_array((string) $rawTarget, ['11', 'grade:11', 'grade:XI', 'XI'])) {
            $targetGrade = 'XI';
            $gradeLabel = 'Kelas 11 (Tingkat XI)';
        } elseif (in_array((string) $rawTarget, ['12', 'grade:12', 'grade:XII', 'XII'])) {
            $targetGrade = 'XII';
            $gradeLabel = 'Kelas 12 (Tingkat XII)';
        }

        $students = Student::with(['studentClass', 'futurePlan', 'alumniTracking'])->whereIn('id', $validated['student_ids'])->get();
        $count = $students->count();

        DB::transaction(function () use ($students, $isAlumni, $targetGrade, $rawTarget, $count) {
            if ($isAlumni) {
                $gradYear = (int) date('Y');
                foreach ($students as $student) {
                    $student->status = 'lulus';
                    $student->save();

                    if (!$student->alumniTracking) {
                        $latestPlan = $student->futurePlan;
                        $currentStatus = 'belum_terlacak';
                        $institutionOrCompany = null;
                        $majorOrPosition = null;

                        if ($latestPlan && in_array($latestPlan->career_category, ['bekerja', 'kuliah', 'wirausaha'])) {
                            $currentStatus = $latestPlan->career_category;
                            $institutionOrCompany = $latestPlan->target_name;
                            $majorOrPosition = $latestPlan->major_or_position;
                        }

                        $code = 'ALS-' . $gradYear . '-' . str_pad(AlumniTracking::count() + 1, 5, '0', STR_PAD_LEFT);
                        AlumniTracking::create([
                            'code' => $code,
                            'student_id' => $student->id,
                            'graduation_year' => $gradYear,
                            'tracking_period' => '6_bulan',
                            'current_status' => $currentStatus,
                            'institution_or_company' => $institutionOrCompany,
                            'major_or_position' => $majorOrPosition,
                            'notes' => 'Status kelulusan massal via kenaikan kelas BK.',
                            'allow_public_showcase' => false,
                        ]);
                    }
                }

                AuditLog::log(
                    'PERBARUI',
                    'Student',
                    null,
                    "Kenaikan kelas massal: {$count} siswa berhasil diubah statusnya menjadi Alumni (Lulus)."
                );
            } else {
                foreach ($students as $student) {
                    $targetClassId = null;

                    if ($targetGrade) {
                        $currentMajor = $student->studentClass ? $student->studentClass->major : null;
                        $matchedClass = null;
                        if ($currentMajor) {
                            $matchedClass = StudentClass::where('grade', $targetGrade)
                                ->where('major', $currentMajor)
                                ->first();
                        }
                        if (!$matchedClass) {
                            $matchedClass = StudentClass::where('grade', $targetGrade)->first();
                        }
                        if ($matchedClass) {
                            $targetClassId = $matchedClass->id;
                        }
                    } else {
                        $targetClassId = (int) $rawTarget;
                    }

                    if ($targetClassId) {
                        $student->update([
                            'student_class_id' => $targetClassId,
                            'status' => 'aktif',
                        ]);
                    }
                }

                $label = $targetGrade ? "Tingkat {$targetGrade}" : "ID Kelas {$rawTarget}";
                AuditLog::log(
                    'PERBARUI',
                    'Student',
                    null,
                    "Kenaikan kelas massal: {$count} siswa berhasil dinaikkan ke {$label}."
                );
            }
        });

        $displayTarget = $gradeLabel ?: "kelas tujuan yang dipilih";
        return redirect()->back()->with(
            'success',
            "Berhasil memproses kenaikan kelas massal untuk {$count} siswa ke {$displayTarget}. Data kelas siswa di profil dan seluruh sistem telah otomatis berubah."
        );
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $validated = $request->validate([
            'nis' => 'required|string|unique:students,nis,' . $student->id,
            'nisn' => 'required|string|unique:students,nisn,' . $student->id,
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'student_class_id' => 'required|exists:student_classes,id',
            'status' => 'required|in:aktif,lulus,pindah,tidak_aktif',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'phone' => 'nullable|string|max:25',
            'address' => 'nullable|string',
            'parent_name' => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:25',
            'parent_job' => 'nullable|string|max:100',
            'attention_level' => 'required|in:normal,perlu_perhatian,prioritas,segera_ditindaklanjuti',
            'special_notes' => 'nullable|string',
        ]);

        $student->update($validated);

        if ($student->status === 'lulus' && !$student->alumniTracking) {
            $gradYear = (int) date('Y');
            $latestPlan = $student->futurePlan;
            $currentStatus = 'belum_terlacak';
            $institutionOrCompany = null;
            $majorOrPosition = null;

            if ($latestPlan && in_array($latestPlan->career_category, ['bekerja', 'kuliah', 'wirausaha'])) {
                $currentStatus = $latestPlan->career_category;
                $institutionOrCompany = $latestPlan->target_name;
                $majorOrPosition = $latestPlan->major_or_position;
            }

            $code = 'ALS-' . $gradYear . '-' . str_pad(AlumniTracking::count() + 1, 5, '0', STR_PAD_LEFT);
            AlumniTracking::create([
                'code' => $code,
                'student_id' => $student->id,
                'graduation_year' => $gradYear,
                'tracking_period' => '6_bulan',
                'current_status' => $currentStatus,
                'institution_or_company' => $institutionOrCompany,
                'major_or_position' => $majorOrPosition,
                'notes' => 'Status siswa diperbarui menjadi Alumni (Lulus).',
                'allow_public_showcase' => false,
            ]);
        }

        if ($student->user) {
            $student->user->update([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
            ]);
        }

        AuditLog::log('PERBARUI', 'Student', $student->id, "Memperbarui data siswa: {$student->name}.");

        return redirect()->route('guru.siswa.show', $student->id)->with('success', 'Data profil siswa berhasil diperbarui.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'grade' => 'required_without:student_class_id|nullable|in:X,XI,XII',
            'csv_file' => 'required|file|mimes:csv,txt',
        ], [
            'grade.required_without' => 'Pilih jenjang kelas (X, XI, atau XII) sebelum mengunggah file.',
            'grade.in' => 'Pilihan kelas hanya boleh X, XI, atau XII.',
            'csv_file.required' => 'Pilih file CSV / Excel ekspor terlebih dahulu.',
        ]);

        if ($request->filled('student_class_id')) {
            $classId = $request->student_class_id;
        } else {
            $major = trim($request->custom_major ?: $request->major);
            if (empty($major)) {
                return back()->withErrors(['major' => 'Pilih jurusan yang tersedia atau masukkan jurusan baru secara manual.'])->withInput();
            }

            $grade = $request->grade;
            $academicYear = AcademicYear::where('is_active', true)->first();

            // Cari kelas dengan jenjang dan jurusan tersebut
            $studentClass = StudentClass::where('grade', $grade)
                ->where('major', $major)
                ->first();

            if (!$studentClass) {
                $countExisting = StudentClass::where('grade', $grade)->where('major', $major)->count();
                $className = "{$grade} {$major}" . ($countExisting > 0 ? ' ' . ($countExisting + 1) : '');

                $studentClass = StudentClass::create([
                    'academic_year_id' => $academicYear ? $academicYear->id : null,
                    'grade' => $grade,
                    'major' => $major,
                    'name' => $className,
                ]);
            }

            $classId = $studentClass->id;
        }

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle, 1000, ',');

        $imported = 0;

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            if (count($row) < 3) continue;

            $nis = trim($row[0]);
            $nisn = trim($row[1]);
            $name = trim($row[2]);
            $gender = isset($row[3]) && in_array(strtoupper(trim($row[3])), ['L', 'P']) ? strtoupper(trim($row[3])) : 'L';
            $phone = isset($row[4]) ? trim($row[4]) : null;

            if (empty($nis) || empty($nisn) || empty($name)) continue;

            // Prevent duplicate NIS / NISN
            $existing = Student::where('nis', $nis)->orWhere('nisn', $nisn)->first();
            if ($existing) continue;

            $user = User::create([
                'name' => $name,
                'username' => $nisn,
                'email' => $nisn . '@siswa.sch.id',
                'password' => Hash::make('password123'),
                'role' => 'siswa',
                'phone' => $phone,
                'is_active' => true,
            ]);

            Student::create([
                'user_id' => $user->id,
                'student_class_id' => $classId,
                'nis' => $nis,
                'nisn' => $nisn,
                'name' => $name,
                'gender' => $gender,
                'phone' => $phone,
                'status' => 'aktif',
                'attention_level' => 'normal',
            ]);

            $imported++;
        }

        fclose($handle);

        AuditLog::log('IMPORT', 'Student', null, "Melakukan impor massal {$imported} data siswa ke kelas ID {$classId}.");

        return redirect()->route('guru.siswa.index')->with('success', "Berhasil mengimpor {$imported} data siswa beserta pembuatan akun login default.");
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_impor_siswa.csv"',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            // Byte Order Mark (BOM) UTF-8 agar kompatibel saat dibuka di Microsoft Excel
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Baris header
            fputcsv($handle, ['NIS', 'NISN', 'Nama Lengkap', 'Jenis Kelamin (L/P)', 'No. HP']);

            // Baris data contoh realistis
            fputcsv($handle, ['25261001', '0081234501', 'Aditya Pratama', 'L', '081234567801']);
            fputcsv($handle, ['25261002', '0081234502', 'Nabila Putri Cahyani', 'P', '081234567802']);
            fputcsv($handle, ['25261003', '0081234503', 'Rian Hidayat', 'L', '081234567803']);

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $studentName = $student->name;
        $studentNisn = $student->nisn;

        // Jika siswa memiliki akun user, hapus akun loginnya
        if ($student->user) {
            $student->user->delete();
        }

        $student->delete();

        AuditLog::log('HAPUS', 'Student', $id, "Menghapus data siswa {$studentName} (NISN: {$studentNisn}) beserta seluruh data terkait.");

        return redirect()->route('guru.siswa.index')->with('success', "Data siswa {$studentName} berhasil dihapus dari sistem.");
    }
}
