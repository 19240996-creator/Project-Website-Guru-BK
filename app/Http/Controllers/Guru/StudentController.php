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

        $students = $query->paginate(15)->withQueryString();
        $classes = StudentClass::orderBy('grade')->orderBy('name')->get();

        return view('guru.siswa.index', compact('students', 'classes'));
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
        return view('guru.siswa.edit', compact('student', 'classes'));
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
            'student_class_id' => 'required|exists:student_classes,id',
            'csv_file' => 'required|file|mimes:csv,txt',
        ], [
            'student_class_id.required' => 'Pilih kelas tujuan sebelum mengunggah file.',
            'csv_file.required' => 'Pilih file CSV / Excel ekspor terlebih dahulu.',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle, 1000, ',');

        $imported = 0;
        $classId = $request->student_class_id;

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
}
