<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Counseling;
use App\Models\AcademicYear;
use App\Models\AuditLog;

class ProfileController extends Controller
{
    public function show()
    {
        $counselor = auth()->user();
        $handledCounselingsCount = Counseling::where('counselor_id', $counselor->id)->count();
        $scheduledCounselingsCount = Counseling::where('counselor_id', $counselor->id)
            ->where('status', 'dijadwalkan')
            ->count();
        $activeAcademicYear = AcademicYear::where('is_active', true)->first();

        return view('guru.profil.show', compact(
            'counselor',
            'handledCounselingsCount',
            'scheduledCounselingsCount',
            'activeAcademicYear'
        ));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:25',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        AuditLog::log('PERBARUI_PROFIL_GURU', 'User', $user->id, "Guru BK {$user->name} memperbarui data profil akun.");

        return back()->with('success', 'Profil Guru BK berhasil diperbarui.');
    }

    public function updatePhoto(Request $request)
    {
        $user = auth()->user();

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
            $newPath = \App\Services\AvatarService::storeAvatar($source, $user->avatar);
            $user->update(['avatar' => $newPath]);

            AuditLog::log('PERBARUI_FOTO_PROFIL_GURU', 'User', $user->id, "Guru BK {$user->name} memperbarui foto profil akun.");

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Foto profil Guru BK berhasil diperbarui.',
                    'avatar_url' => asset('storage/' . $newPath),
                ]);
            }

            return back()->with('success', 'Foto profil Guru BK berhasil diperbarui.');
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
        $user = auth()->user();

        if ($user->avatar) {
            \App\Services\AvatarService::deleteOldAvatar($user->avatar);
            $user->update(['avatar' => null]);

            AuditLog::log('HAPUS_FOTO_PROFIL_GURU', 'User', $user->id, "Guru BK {$user->name} menghapus foto profil dan kembali ke avatar default.");
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Foto profil berhasil dihapus dan kembali ke avatar default.',
                'avatar_url' => asset('images/default-avatar-guru.svg'),
            ]);
        }

        return back()->with('success', 'Foto profil berhasil dihapus dan kembali ke avatar default.');
    }
}
