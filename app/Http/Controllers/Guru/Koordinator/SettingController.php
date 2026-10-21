<?php

namespace App\Http\Controllers\Guru\Koordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SchoolSetting;
use App\Models\AuditLog;

class SettingController extends Controller
{
    public function index()
    {
        $setting = SchoolSetting::get();
        return view('guru.koordinator.pengaturan.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'school_name' => 'required|string|max:255',
            'provincial_agency' => 'required|string|max:255',
            'education_branch' => 'required|string|max:255',
            'unit_name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'city' => 'nullable|string|max:100',
            'website' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'postal_code' => 'nullable|string|max:20',
            'headmaster_name' => 'nullable|string|max:255',
            'headmaster_nip' => 'nullable|string|max:50',
            'vice_headmaster_kesiswaan_name' => 'nullable|string|max:255',
            'vice_headmaster_kesiswaan_nip' => 'nullable|string|max:50',
            'vice_headmaster_kurikulum_name' => 'nullable|string|max:255',
            'vice_headmaster_kurikulum_nip' => 'nullable|string|max:50',
        ], [
            'school_name.required' => 'Nama sekolah wajib diisi.',
            'provincial_agency.required' => 'Dinas Pendidikan / Pemda wajib diisi.',
            'education_branch.required' => 'Cabang Dinas Pendidikan wajib diisi.',
            'unit_name.required' => 'Nama Unit Layanan BK wajib diisi.',
            'address.required' => 'Alamat dan jalan sekolah wajib diisi.',
        ]);

        $setting = SchoolSetting::get();
        $setting->update($validated);

        AuditLog::log(
            'PERBARUI',
            'SchoolSetting',
            $setting->id,
            "Memperbarui identitas sekolah dan kop surat: {$setting->school_name} ({$setting->education_branch})."
        );

        return redirect()->route('guru.koordinator.pengaturan.index')
            ->with('success', 'Identitas sekolah, cabang dinas, alamat, dan kop surat resmi berhasil diperbarui.');
    }
}
