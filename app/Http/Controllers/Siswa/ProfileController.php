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
}
