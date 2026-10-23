<?php

namespace App\Http\Controllers\Pemantau;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\AuditLog;
use App\Models\SchoolSetting;
use App\Services\SimpleXlsxService;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PemantauController extends Controller
{
    /**
     * Dashboard Pemantauan Sekolah untuk Kepala Kampus dan Admin DICO
     */
    public function dashboard(Request $request)
    {
        $user = auth()->user();
        $rolePrefix = $user->role;

        // 1. Ringkasan Total Siswa Sekolah & Tingkat Kelas
        $totalStudents = Student::count();
        $totalGradeX = Student::whereHas('studentClass', fn($q) => $q->where('grade', 'X'))->count();
        $totalGradeXI = Student::whereHas('studentClass', fn($q) => $q->where('grade', 'XI'))->count();
        $totalGradeXII = Student::whereHas('studentClass', fn($q) => $q->where('grade', 'XII'))->count();

        // 2. Statistik Distribusi Siswa Berdasarkan Jurusan
        $majors = StudentClass::select('major')
            ->distinct()
            ->whereNotNull('major')
            ->where('major', '!=', '')
            ->orderBy('major')
            ->pluck('major');

        $majorStats = [];
        foreach ($majors as $m) {
            $count = Student::whereHas('studentClass', fn($q) => $q->where('major', $m))->count();
            $majorStats[] = [
                'major' => $m,
                'count' => $count,
                'percentage' => $totalStudents > 0 ? round(($count / $totalStudents) * 100, 1) : 0,
            ];
        }

        // 3. Statistik Distribusi Siswa Berdasarkan Rombongan Belajar (Rombel)
        $rombelList = StudentClass::withCount('students')
            ->orderByRaw("
                CASE 
                    WHEN grade = 'X' THEN 1
                    WHEN grade = 'XI' THEN 2
                    WHEN grade = 'XII' THEN 3
                    ELSE 4
                END ASC
            ")
            ->orderBy('name', 'asc')
            ->get();

        // 4. Tabel Data Siswa dengan Urutan Kelas X, XI, XII dan Abjad Nama (A-Z)
        $query = Student::query()
            ->select('students.*')
            ->leftJoin('student_classes', 'students.student_class_id', '=', 'student_classes.id')
            ->with(['studentClass', 'counselor']);

        if ($request->filled('q')) {
            $keyword = trim($request->q);
            $query->where(function ($sub) use ($keyword) {
                $sub->where('students.name', 'like', "%{$keyword}%")
                    ->orWhere('students.nisn', 'like', "%{$keyword}%")
                    ->orWhere('students.nis', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('grade') && $request->grade !== 'semua') {
            $query->where('student_classes.grade', $request->grade);
        }

        if ($request->filled('major') && $request->major !== 'semua') {
            $query->where('student_classes.major', $request->major);
        }

        if ($request->filled('student_class_id') && $request->student_class_id !== 'semua') {
            $query->where('students.student_class_id', $request->student_class_id);
        }

        $students = $query
            ->orderByRaw("
                CASE 
                    WHEN student_classes.grade = 'X' THEN 1
                    WHEN student_classes.grade = 'XI' THEN 2
                    WHEN student_classes.grade = 'XII' THEN 3
                    ELSE 4
                END ASC
            ")
            ->orderBy('student_classes.name', 'asc')
            ->orderBy('students.name', 'asc')
            ->paginate(15)
            ->withQueryString();

        return view('pemantau.dashboard', compact(
            'totalStudents',
            'totalGradeX',
            'totalGradeXI',
            'totalGradeXII',
            'majorStats',
            'rombelList',
            'students',
            'majors',
            'rolePrefix'
        ));
    }

    /**
     * Halaman Data Siswa (Pemantauan Lengkap & Filter Lanjutan)
     */
    public function students(Request $request)
    {
        $user = auth()->user();
        $rolePrefix = $user->role;

        $query = Student::query()
            ->select('students.*')
            ->leftJoin('student_classes', 'students.student_class_id', '=', 'student_classes.id')
            ->with(['studentClass', 'counselor']);

        if ($request->filled('q')) {
            $keyword = trim($request->q);
            $query->where(function ($sub) use ($keyword) {
                $sub->where('students.name', 'like', "%{$keyword}%")
                    ->orWhere('students.nisn', 'like', "%{$keyword}%")
                    ->orWhere('students.nis', 'like', "%{$keyword}%")
                    ->orWhere('students.phone', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('grade') && $request->grade !== 'semua') {
            $query->where('student_classes.grade', $request->grade);
        }

        if ($request->filled('major') && $request->major !== 'semua') {
            $query->where('student_classes.major', $request->major);
        }

        if ($request->filled('student_class_id') && $request->student_class_id !== 'semua') {
            $query->where('students.student_class_id', $request->student_class_id);
        }

        if ($request->filled('gender') && $request->gender !== 'semua') {
            $query->where('students.gender', $request->gender);
        }

        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('students.status', $request->status);
        }

        $students = $query
            ->orderByRaw("
                CASE 
                    WHEN student_classes.grade = 'X' THEN 1
                    WHEN student_classes.grade = 'XI' THEN 2
                    WHEN student_classes.grade = 'XII' THEN 3
                    ELSE 4
                END ASC
            ")
            ->orderBy('student_classes.name', 'asc')
            ->orderBy('students.name', 'asc')
            ->paginate(20)
            ->withQueryString();

        $majors = StudentClass::select('major')
            ->distinct()
            ->whereNotNull('major')
            ->where('major', '!=', '')
            ->orderBy('major')
            ->pluck('major');

        $classes = StudentClass::orderBy('grade')->orderBy('name')->get();

        return view('pemantau.siswa.index', compact('students', 'majors', 'classes', 'rolePrefix'));
    }

    /**
     * Pratinjau Detail Profil Siswa (Read-Only)
     */
    public function studentDetail($id)
    {
        $user = auth()->user();
        $rolePrefix = $user->role;

        $student = Student::with(['studentClass', 'counselor'])->findOrFail($id);

        return view('pemantau.siswa.show', compact('student', 'rolePrefix'));
    }

    /**
     * Halaman Menu Ekspor Data Siswa Kelas X, XI, dan XII
     */
    public function exportIndex(Request $request)
    {
        $user = auth()->user();
        $rolePrefix = $user->role;

        $majors = StudentClass::select('major')
            ->distinct()
            ->whereNotNull('major')
            ->where('major', '!=', '')
            ->orderBy('major')
            ->pluck('major');

        $classes = StudentClass::orderBy('grade')->orderBy('name')->get();

        // Hitung estimasi awal siswa keseluruhan
        $initialCount = Student::count();

        return view('pemantau.ekspor.index', compact('majors', 'classes', 'initialCount', 'rolePrefix'));
    }

    /**
     * API Pratinjau Jumlah Siswa Sesuai Filter (AJAX)
     */
    public function exportPreviewCount(Request $request)
    {
        $query = Student::query();

        if ($request->filled('grade') && $request->grade !== 'semua') {
            $query->whereHas('studentClass', fn($q) => $q->where('grade', $request->grade));
        }

        if ($request->filled('major') && $request->major !== 'semua') {
            $query->whereHas('studentClass', fn($q) => $q->where('major', $request->major));
        }

        if ($request->filled('student_class_id') && $request->student_class_id !== 'semua') {
            $query->where('student_class_id', $request->student_class_id);
        }

        $count = $query->count();

        $gradeLabel = $request->grade && $request->grade !== 'semua' ? 'Kelas ' . $request->grade : 'Semua Tingkat Kelas';

        return response()->json([
            'count' => $count,
            'label' => $gradeLabel,
        ]);
    }

    /**
     * Proses Unduh File Ekspor Data Siswa (CSV, Excel .xlsx, atau Laporan Cetak PDF)
     */
    public function exportDownload(Request $request)
    {
        $request->validate([
            'format' => 'required|string|in:csv,xlsx,print',
            'grade' => 'nullable|string',
            'major' => 'nullable|string',
            'student_class_id' => 'nullable|string',
        ]);

        $user = auth()->user();
        $roleLabel = $user->role_label;

        $query = Student::query()
            ->select('students.*')
            ->leftJoin('student_classes', 'students.student_class_id', '=', 'student_classes.id')
            ->with(['studentClass', 'counselor']);

        if ($request->filled('grade') && $request->grade !== 'semua') {
            $query->where('student_classes.grade', $request->grade);
        }

        if ($request->filled('major') && $request->major !== 'semua') {
            $query->where('student_classes.major', $request->major);
        }

        if ($request->filled('student_class_id') && $request->student_class_id !== 'semua') {
            $query->where('students.student_class_id', $request->student_class_id);
        }

        // Urutan ekspor: Mulai dari Kelas X, kemudian Kelas XI, kemudian Kelas XII, lalu berdasarkan nama Rombel, lalu Nama Siswa sesuai abjad (A-Z)
        $students = $query
            ->orderByRaw("
                CASE 
                    WHEN student_classes.grade = 'X' THEN 1
                    WHEN student_classes.grade = 'XI' THEN 2
                    WHEN student_classes.grade = 'XII' THEN 3
                    ELSE 4
                END ASC
            ")
            ->orderBy('student_classes.name', 'asc')
            ->orderBy('students.name', 'asc')
            ->get();
        $totalCount = $students->count();

        // Label Filter untuk Deskripsi Audit Log dan Judul Laporan
        $gradeFilter = $request->filled('grade') && $request->grade !== 'semua' ? "Kelas {$request->grade}" : "Semua Kelas";
        $majorFilter = $request->filled('major') && $request->major !== 'semua' ? "Jurusan {$request->major}" : "Semua Jurusan";
        $filterDescription = "{$gradeFilter}, {$majorFilter}";

        // Catat ke Audit Log sesuai identitas akun yang melakukan ekspor (PRD Bagian 7)
        AuditLog::log(
            'EKSPOR_SISWA',
            'Student',
            null,
            "{$roleLabel} ({$user->name}) mengekspor data siswa format {$request->format} ({$totalCount} siswa, filter: {$filterDescription})."
        );

        $schoolSetting = SchoolSetting::first();
        $schoolName = $schoolSetting->school_name ?? 'SMK Negeri 1 Indonesia';
        $timestamp = date('Ymd_His');

        // Siapkan baris data terstruktur
        $rows = [];
        $no = 1;
        foreach ($students as $st) {
            $gender = match ($st->gender) {
                'L' => 'Laki-laki',
                'P' => 'Perempuan',
                default => $st->gender ?? '-',
            };

            $rows[] = [
                $no++,
                (string) ($st->nis ?? '-'),
                (string) ($st->nisn ?? '-'),
                (string) $st->name,
                $gender,
                (string) ($st->studentClass->grade ?? '-'),
                (string) ($st->studentClass->major ?? '-'),
                (string) ($st->studentClass->name ?? '-'),
                (string) ($st->phone ?? '-'),
                ucfirst((string) ($st->status ?? 'aktif')),
            ];
        }

        // 1. Format Cetak / Laporan Siap PDF
        if ($request->format === 'print') {
            return view('pemantau.ekspor.print', compact('students', 'schoolSetting', 'filterDescription', 'gradeFilter', 'majorFilter'));
        }

        // 2. Format CSV (Dengan kompatibilitas Excel: UTF-8 BOM dan pencegahan notasi ilmiah)
        if ($request->format === 'csv') {
            $fileName = "ekspor_siswa_{$request->grade}_{$timestamp}.csv";
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0',
            ];

            return new StreamedResponse(function () use ($rows) {
                $handle = fopen('php://output', 'w');
                // Tulis BOM UTF-8 agar Microsoft Excel membaca karakter Indonesia dan angka dengan presisi
                fputs($handle, "\xEF\xBB\xBF");

                // Header Kolom Aktual Database (PRD Bagian 6)
                fputcsv($handle, [
                    'No',
                    'NIS',
                    'NISN',
                    'Nama Lengkap Siswa',
                    'Jenis Kelamin',
                    'Tingkat Kelas',
                    'Jurusan',
                    'Rombongan Belajar',
                    'Nomor Kontak (HP)',
                    'Status Siswa',
                ]);

                foreach ($rows as $row) {
                    $csvRow = $row;
                    // Format NISN dan No HP sebagai string formula agar Excel tidak mengubahnya menjadi notasi ilmiah (8.12E+10)
                    if (!empty($csvRow[2]) && $csvRow[2] !== '-') {
                        $csvRow[2] = '="' . $csvRow[2] . '"';
                    }
                    if (!empty($csvRow[8]) && $csvRow[8] !== '-') {
                        $csvRow[8] = '="' . $csvRow[8] . '"';
                    }
                    fputcsv($handle, $csvRow);
                }

                fclose($handle);
            }, 200, $headers);
        }

        // 3. Format Excel (.xlsx resmi OpenXML murni yang dapat dibuka langsung tanpa peringatan korup)
        $headersList = [
            'No',
            'NIS',
            'NISN',
            'Nama Lengkap Siswa',
            'Jenis Kelamin',
            'Tingkat Kelas',
            'Konsentrasi Keahlian / Jurusan',
            'Rombongan Belajar',
            'Nomor Kontak (HP)',
            'Status Siswa',
        ];

        $sheetTitle = "DATA SISWA RUANG BK DIGITAL - " . strtoupper($schoolName);
        $sheetSubtitle = "Filter: {$filterDescription} | Total: {$totalCount} Siswa | Diunduh: " . date('d F Y H:i') . " WIB";

        $xlsxTempPath = SimpleXlsxService::createStudentExport(
            $sheetTitle,
            $sheetSubtitle,
            $headersList,
            $rows
        );

        $fileName = "ekspor_siswa_{$request->grade}_{$timestamp}.xlsx";

        return response()->download($xlsxTempPath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Log Aktivitas Akun Sendiri (Pemisahan Log Sesuai PRD Bagian 7)
     * Kepala Kampus / Admin DICO HANYA dapat melihat log aktivitas milik akun mereka sendiri!
     */
    public function auditLogs(Request $request)
    {
        $user = auth()->user();
        $rolePrefix = $user->role;

        // Query dibatasi secara mutlak pada ID pengguna akun login (Backend level enforcement)
        $query = AuditLog::where('user_id', $user->id)->latest();

        if ($request->filled('q')) {
            $keyword = trim($request->q);
            $query->where(function ($sub) use ($keyword) {
                $sub->where('description', 'like', "%{$keyword}%")
                    ->orWhere('action', 'like', "%{$keyword}%")
                    ->orWhere('entity_type', 'like', "%{$keyword}%")
                    ->orWhere('ip_address', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $logs = $query->paginate(25)->withQueryString();

        $actions = AuditLog::where('user_id', $user->id)
            ->select('action')
            ->distinct()
            ->whereNotNull('action')
            ->where('action', '!=', '')
            ->orderBy('action')
            ->pluck('action');

        return view('pemantau.log.index', compact('logs', 'actions', 'rolePrefix'));
    }

    /**
     * Tampilan Profil Akun
     */
    public function profileShow()
    {
        $user = auth()->user();
        $rolePrefix = $user->role;

        return view('pemantau.profil.show', compact('user', 'rolePrefix'));
    }

    /**
     * Perbarui Profil Akun Sendiri
     */
    public function profileUpdate(Request $request)
    {
        $user = auth()->user();
        $rolePrefix = $user->role;

        $request->validate([
            'name' => 'required|string|max:150',
            'phone' => 'nullable|string|max:25',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:6|confirmed',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'new_password.min' => 'Kata sandi baru minimal 6 karakter.',
            'new_password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            'current_password.required_with' => 'Masukkan kata sandi saat ini untuk mengubah kata sandi.',
        ]);

        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Kata sandi saat ini yang Anda masukkan salah.']);
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->save();

        AuditLog::log('PERBARUI_PROFIL', 'User', $user->id, "Pengguna {$user->role_label} ({$user->name}) memperbarui profil akun.");

        return back()->with('success', 'Profil akun Anda berhasil diperbarui.');
    }

    /**
     * Unggah atau Perbarui Foto Profil (Mendukung File Upload & Base64 Cropped Canvas)
     */
    public function updatePhoto(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'photo' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:5120',
            'cropped_image' => 'nullable|string',
        ]);

        $source = $request->input('cropped_image') ?: $request->file('photo');

        if (!$source) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silakan pilih foto profil yang ingin diunggah.',
                ], 422);
            }
            return back()->withErrors(['photo' => 'Silakan pilih foto profil yang ingin diunggah.']);
        }

        try {
            $newPath = \App\Services\AvatarService::storeAvatar($source, $user->avatar);
            $user->update(['avatar' => $newPath]);

            AuditLog::log('PERBARUI_FOTO_PROFIL', 'User', $user->id, "Pengguna {$user->role_label} ({$user->name}) memperbarui foto profil akun.");

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Foto profil berhasil diperbarui.',
                    'avatar_url' => asset('storage/' . $newPath),
                ]);
            }

            return back()->with('success', 'Foto profil berhasil diperbarui.');
        } catch (\Throwable $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }
            return back()->withErrors(['photo' => $e->getMessage()]);
        }
    }

    /**
     * Hapus Foto Profil Khusus dan Kembali ke Avatar Default
     */
    public function destroyPhoto(Request $request)
    {
        $user = auth()->user();

        if ($user->avatar) {
            \App\Services\AvatarService::deleteOldAvatar($user->avatar);
            $user->update(['avatar' => null]);

            AuditLog::log('HAPUS_FOTO_PROFIL', 'User', $user->id, "Pengguna {$user->role_label} ({$user->name}) menghapus foto profil dan kembali ke avatar default.");
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Foto profil berhasil dihapus dan kembali ke avatar default.',
                'avatar_url' => \App\Services\AvatarService::getAvatarUrl(null, $user->role),
            ]);
        }

        return back()->with('success', 'Foto profil berhasil dihapus dan kembali ke avatar default.');
    }
}

