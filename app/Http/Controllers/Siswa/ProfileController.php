<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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

        $student->update($validated);

        if ($student->user && !empty($validated['phone'])) {
            $student->user->update(['phone' => $validated['phone']]);
        }

        AuditLog::log('PERBARUI_PROFIL_MANDIRI', 'Student', $student->id, "Siswa {$student->name} memperbarui data kontak pribadi.");

        return back()->with('success', 'Data kontak Anda berhasil diperbarui.');
    }
}
