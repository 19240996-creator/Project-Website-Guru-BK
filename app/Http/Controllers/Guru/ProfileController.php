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
}
