<?php

namespace App\Http\Controllers\Guru\Koordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\CounselorAssignment;
use App\Models\CounselorClassAssignment;
use App\Models\AuditLog;

class GuruBkController extends Controller
{
    /**
     * Menampilkan daftar seluruh Guru BK yang terdaftar di aplikasi.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('q'));

        $query = User::where('role', 'guru_bk')
            ->withCount(['studentAssignments', 'classAssignments'])
            ->latest();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $counselors = $query->paginate(10)->withQueryString();

        // Statistik ringkas
        $totalGuruBk = User::where('role', 'guru_bk')->count();
        $activeGuruBk = User::where('role', 'guru_bk')->where('is_active', true)->count();
        $totalAssignedStudents = CounselorAssignment::count();

        return view('guru.koordinator.guru_bk.index', compact(
            'counselors',
            'search',
            'totalGuruBk',
            'activeGuruBk',
            'totalAssignedStudents'
        ));
    }

    /**
     * Form tambah akun Guru BK baru.
     */
    public function create()
    {
        return view('guru.koordinator.guru_bk.create');
    }

    /**
     * Menyimpan akun Guru BK baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50',
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string|max:25',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'cropped_image' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ], [
            'username.unique' => 'Username ini sudah digunakan oleh akun lain.',
            'email.unique' => 'Alamat email ini sudah terdaftar.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'photo.image' => 'File foto harus berupa gambar.',
            'photo.max' => 'Ukuran foto maksimal 3 MB.',
        ]);

        $photoPath = null;
        $source = $request->input('cropped_image') ?: $request->file('photo');
        if ($source) {
            $photoPath = \App\Services\AvatarService::storeAvatar($source, null);
        }

        $user = User::create([
            'name' => $validated['name'],
            'nip' => $validated['nip'] ?? null,
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'guru_bk',
            'phone' => $validated['phone'] ?? null,
            'avatar' => $photoPath,
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : true,
        ]);

        AuditLog::log(
            'TAMBAH_GURU_BK',
            'User',
            $user->id,
            "Guru Koordinator BK menambahkan akun Guru BK baru: {$user->name} ({$user->username})."
        );

        return redirect()->route('guru.koordinator.guru_bk.index')->with('success', "Akun Guru BK baru berhasil dibuat: {$user->name}. Anda kini dapat membagikan tugas siswa kepada beliau.");
    }

    /**
     * Melihat detail profil Guru BK serta daftar kelas, jurusan, dan siswa yang ditugaskan kepadanya.
     */
    public function show($id)
    {
        $counselor = User::where('id', $id)->where('role', 'guru_bk')->firstOrFail();

        // Ambil kelas yang ditugaskan
        $assignedClasses = $counselor->studentAssignments()
            ->with('studentClass')
            ->get()
            ->pluck('studentClass')
            ->filter()
            ->unique('id');

        // Ambil siswa yang ditugaskan dengan pagination
        $assignedStudents = Student::whereHas('counselorAssignment', function ($q) use ($counselor) {
            $q->where('counselor_id', $counselor->id);
        })
        ->with('studentClass')
        ->orderBy('name')
        ->paginate(15);

        // Riwayat konseling yang ditangani
        $counselingCount = $counselor->counselingsHandled()->count();

        return view('guru.koordinator.guru_bk.show', compact(
            'counselor',
            'assignedClasses',
            'assignedStudents',
            'counselingCount'
        ));
    }

    /**
     * Form edit akun Guru BK.
     */
    public function edit($id)
    {
        $counselor = User::where('id', $id)->where('role', 'guru_bk')->firstOrFail();
        return view('guru.koordinator.guru_bk.edit', compact('counselor'));
    }

    /**
     * Memperbarui informasi akun Guru BK.
     */
    public function update(Request $request, $id)
    {
        $counselor = User::where('id', $id)->where('role', 'guru_bk')->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50',
            'username' => "required|string|max:50|unique:users,username,{$counselor->id}",
            'email' => "required|email|max:255|unique:users,email,{$counselor->id}",
            'password' => 'nullable|string|min:6|confirmed',
            'phone' => 'nullable|string|max:25',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'cropped_image' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ], [
            'username.unique' => 'Username ini sudah digunakan oleh akun lain.',
            'email.unique' => 'Alamat email ini sudah terdaftar.',
            'password.min' => 'Kata sandi minimal 6 karakter jika ingin diubah.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'nip' => $validated['nip'] ?? null,
            'username' => $validated['username'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : $counselor->is_active,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $source = $request->input('cropped_image') ?: $request->file('photo');
        if ($source) {
            $updateData['avatar'] = \App\Services\AvatarService::storeAvatar($source, $counselor->avatar);
        }

        $counselor->update($updateData);

        AuditLog::log(
            'UBAH_GURU_BK',
            'User',
            $counselor->id,
            "Guru Koordinator BK memperbarui data akun Guru BK: {$counselor->name}."
        );

        return redirect()->route('guru.koordinator.guru_bk.index')->with('success', "Data akun Guru BK {$counselor->name} berhasil diperbarui.");
    }

    /**
     * Menghapus akun Guru BK secara aman.
     */
    public function destroy(Request $request, $id)
    {
        $counselor = User::where('id', $id)->where('role', 'guru_bk')->firstOrFail();

        $assignedCount = $counselor->studentAssignments()->count();
        if ($assignedCount > 0) {
            return back()->with('error', "Tidak dapat menghapus Guru BK ini karena masih memiliki {$assignedCount} siswa binaan. Silakan alihkan atau lepas penugasan siswa terlebih dahulu melalui menu Pembagian Tugas.");
        }

        $name = $counselor->name;
        $counselor->delete();

        AuditLog::log(
            'HAPUS_GURU_BK',
            'User',
            $id,
            "Guru Koordinator BK menghapus akun Guru BK: {$name}."
        );

        return redirect()->route('guru.koordinator.guru_bk.index')->with('success', "Akun Guru BK {$name} telah berhasil dihapus dari sistem.");
    }
}
