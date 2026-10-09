<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentFuturePlan;
use App\Models\Partner;
use App\Models\AuditLog;

class FuturePlanController extends Controller
{
    public function show()
    {
        $student = auth()->user()->student;
        $history = $student->futurePlans()->latest()->paginate(5);
        $currentPlan = $student->futurePlan;

        $campusPartners = Partner::where('type', 'perguruan_tinggi')->get();
        $companyPartners = Partner::where('type', 'perusahaan')->get();

        return view('siswa.rencana.show', compact('student', 'currentPlan', 'campusPartners', 'companyPartners', 'history'));
    }

    public function update(Request $request)
    {
        $student = auth()->user()->student;

        // Mendukung input khusus sub-bagian saat memilih kuliah_kerja
        if ($request->input('primary_goal') === 'kuliah_kerja') {
            $request->merge([
                'college_target' => $request->filled('college_target_kk') ? $request->input('college_target_kk') : $request->input('college_target'),
                'study_program' => $request->filled('study_program_kk') ? $request->input('study_program_kk') : $request->input('study_program'),
                'entry_path' => $request->filled('entry_path_kk') ? $request->input('entry_path_kk') : $request->input('entry_path'),
                'work_target_field' => $request->filled('work_target_field_kk') ? $request->input('work_target_field_kk') : $request->input('work_target_field'),
                'work_target_company' => $request->filled('work_target_company_kk') ? $request->input('work_target_company_kk') : $request->input('work_target_company'),
            ]);
        }

        $validated = $request->validate([
            'primary_goal' => 'required|in:kuliah,bekerja,kuliah_kerja,wirausaha,pelatihan,belum_menentukan',
            'college_target' => 'nullable|string|max:255',
            'study_program' => 'nullable|string|max:255',
            'entry_path' => 'nullable|string|max:100',
            'work_target_field' => 'nullable|string|max:255',
            'work_target_company' => 'nullable|string|max:255',
            'business_field' => 'nullable|string|max:255',
            'business_idea' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $latestVersion = $student->futurePlans()->max('version') ?? 0;
        $validated['student_id'] = $student->id;
        $validated['version'] = $latestVersion + 1;

        $newPlan = StudentFuturePlan::create($validated);

        AuditLog::log('PERBARUI_RENCANA_MASA_DEPAN', 'StudentFuturePlan', $newPlan->id, "Siswa {$student->name} memperbarui rencana masa depan versi {$newPlan->version} dengan pilihan: {$newPlan->primary_goal}.");

        return back()->with('success', 'Rencana masa depan Anda berhasil disimpan dan riwayat tersimpan untuk bimbingan karier.');
    }
}
