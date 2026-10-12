<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\AuditLog;

class ProfileController extends Controller
{
    public function show()
    {
        $student = auth()->user()->student->load(['studentClass.academicYear', 'achievements', 'futurePlan']);
        return view('siswa.profil.show', compact('student'));
    }

    public function update(Request $request)
    {
        $student = auth()->user()->student;

        $validated = $request->validate([
            'phone' => 'nullable|string|max:25',
            'address' => 'nullable|string',
            'parent_phone' => 'nullable|string|max:25',
        ]);

        $oldPhone = $student->phone;
        $student->update($validated);

        if ($student->user) {
            $userData = [
                'phone' => $validated['phone'],
            ];

            // Jika siswa memperbarui nomor HP, perbarui kata sandi ke nomor baru sehingga nomor lama tidak bisa login lagi
            if ($validated['phone'] !== $oldPhone) {
                $newPassword = !empty($validated['phone']) ? $validated['phone'] : $student->nisn;
                $userData['password'] = Hash::make($newPassword);
            }

            $student->user->update($userData);
        }

        AuditLog::log('PERBARUI_PROFIL_MANDIRI', 'Student', $student->id, "Siswa {$student->name} memperbarui data kontak pribadi.");

        $message = 'Data kontak Anda berhasil diperbarui.';
        if (!empty($validated['phone']) && $validated['phone'] !== $oldPhone) {
            $message .= ' Kata sandi login Anda otomatis diperbarui ke nomor baru (' . $validated['phone'] . '). Nomor lama tidak dapat digunakan untuk login lagi.';
        }

        return back()->with('success', $message);
    }

    public function updatePhoto(Request $request)
    {
        $student = auth()->user()->student;
        $user = auth()->user();

        if (!$student) {
            abort(404, 'Data siswa tidak ditemukan.');
        }

        $request->validate([
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'cropped_image' => 'nullable|string',
        ], [
            'photo.image' => 'File yang diunggah harus berupa gambar.',
            'photo.mimes' => 'Format foto harus berupa JPG, JPEG, PNG, atau WebP.',
            'photo.max' => 'Ukuran file foto tidak boleh lebih dari 5 MB.',
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
            $newPath = \App\Services\AvatarService::storeAvatar($source, $student->avatar);

            $student->update(['avatar' => $newPath]);
            $user->update(['avatar' => $newPath]);

            AuditLog::log('PERBARUI_FOTO_PROFIL_MANDIRI', 'Student', $student->id, "Siswa {$student->name} memperbarui foto profil akun.");

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Foto profil berhasil diperbarui.',
                    'avatar_url' => asset('storage/' . $newPath),
                ]);
            }

            return back()->with('success', 'Foto profil Anda berhasil diperbarui.');
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

    public function destroyPhoto(Request $request)
    {
        $student = auth()->user()->student;
        $user = auth()->user();

        if (!$student) {
            abort(404, 'Data siswa tidak ditemukan.');
        }

        if ($student->avatar) {
            \App\Services\AvatarService::deleteOldAvatar($student->avatar);
            $student->update(['avatar' => null]);
            $user->update(['avatar' => null]);

            AuditLog::log('HAPUS_FOTO_PROFIL_MANDIRI', 'Student', $student->id, "Siswa {$student->name} menghapus foto profil dan kembali ke avatar default.");
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Foto profil berhasil dihapus dan kembali ke avatar default.',
                'avatar_url' => asset('images/default-avatar-student.svg'),
            ]);
        }

        return back()->with('success', 'Foto profil berhasil dihapus dan kembali ke avatar default.');
    }
}
