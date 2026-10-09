<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Opportunity;
use App\Models\OpportunityRegistration;
use App\Models\Partner;
use App\Models\AuditLog;

class OpportunityController extends Controller
{
    public function index(Request $request)
    {
        $query = Opportunity::with(['partner', 'registrations.student.studentClass'])->latest();

        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('code', 'like', "%{$q}%")
                    ->orWhere('target_audience', 'like', "%{$q}%")
                    ->orWhereHas('partner', function ($pq) use ($q) {
                        $pq->where('name', 'like', "%{$q}%");
                    });
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('partner_id')) {
            $query->where('partner_id', $request->partner_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $opportunities = $query->paginate(15)->withQueryString();
        $partners = Partner::orderBy('name')->get();

        return view('guru.peluang.index', compact('opportunities', 'partners'));
    }

    public function show($id)
    {
        $opportunity = Opportunity::with(['partner', 'registrations.student.studentClass'])->findOrFail($id);
        return view('guru.peluang.show', compact('opportunity'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'partner_id' => 'nullable|exists:partners,id',
            'title' => 'required|string|max:255',
            'type' => 'required|in:beasiswa,magang,lowongan_kerja,pelatihan,sertifikasi,kompetisi,campus_visit',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'target_audience' => 'nullable|string|max:255',
            'deadline' => 'nullable|date',
            'quota' => 'nullable|integer|min:1',
            'registration_link' => 'nullable|url',
            'status' => 'required|in:draf,dipublikasikan,ditutup,selesai',
        ]);

        $code = 'PEL-' . date('Y') . '-' . str_pad(Opportunity::count() + 1, 5, '0', STR_PAD_LEFT);
        $validated['code'] = $code;

        $op = Opportunity::create($validated);
        AuditLog::log('SIMPAN', 'Opportunity', $op->id, "Membuat pengumuman peluang baru {$op->title} ({$op->code}).");

        return redirect()->route('guru.peluang.show', $op->id)->with('success', 'Peluang berhasil dibuat dan dipublikasikan.');
    }

    public function updateRegistrationStatus(Request $request, $regId)
    {
        $reg = OpportunityRegistration::findOrFail($regId);

        $validated = $request->validate([
            'status' => 'required|in:terdaftar,menunggu,diterima,ditolak,selesai',
            'notes' => 'nullable|string',
        ]);

        $reg->update($validated);
        AuditLog::log('STATUS_PENDAFTARAN', 'OpportunityRegistration', $reg->id, "Mengubah status pendaftaran peluang siswa {$reg->student->name} menjadi {$validated['status']}.");

        return back()->with('success', 'Status pendaftaran siswa berhasil diperbarui.');
    }
}
