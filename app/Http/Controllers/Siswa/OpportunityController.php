<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Opportunity;
use App\Models\OpportunityRegistration;
use App\Models\AuditLog;
use Carbon\Carbon;

class OpportunityController extends Controller
{
    public function index(Request $request)
    {
        $student = auth()->user()->student->load(['studentClass', 'futurePlan']);
        $today = Carbon::today()->toDateString();

        $query = Opportunity::with('partner')
            ->where('status', 'dipublikasikan')
            ->where('deadline', '>=', $today)
            ->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $opportunities = $query->paginate(12)->withQueryString();

        // My registered opportunity IDs
        $myRegistrations = OpportunityRegistration::where('student_id', $student->id)
            ->pluck('status', 'opportunity_id')
            ->toArray();

        return view('siswa.peluang.index', compact('opportunities', 'student', 'myRegistrations'));
    }

    public function show($id)
    {
        $student = auth()->user()->student;
        $opportunity = Opportunity::with('partner')->findOrFail($id);
        $registration = OpportunityRegistration::where('opportunity_id', $opportunity->id)
            ->where('student_id', $student->id)
            ->first();

        return view('siswa.peluang.show', compact('opportunity', 'registration'));
    }

    public function register(Request $request, $id)
    {
        $student = auth()->user()->student;
        $opportunity = Opportunity::findOrFail($id);

        if ($opportunity->status !== 'dipublikasikan' || ($opportunity->deadline && $opportunity->deadline < Carbon::today())) {
            return back()->with('error', 'Pendaftaran untuk peluang ini sudah ditutup.');
        }

        // Check quota if specified
        if ($opportunity->quota) {
            $currentRegistrations = OpportunityRegistration::where('opportunity_id', $opportunity->id)->count();
            if ($currentRegistrations >= $opportunity->quota) {
                return back()->with('error', 'Mohon maaf, kuota pendaftaran untuk peluang ini telah penuh.');
            }
        }

        $existing = OpportunityRegistration::where('opportunity_id', $opportunity->id)
            ->where('student_id', $student->id)
            ->first();

        if ($existing) {
            return back()->with('error', 'Anda telah terdaftar pada kegiatan/peluang ini sebelumnya.');
        }

        $reg = OpportunityRegistration::create([
            'opportunity_id' => $opportunity->id,
            'student_id' => $student->id,
            'status' => 'terdaftar',
            'notes' => $request->input('notes'),
        ]);

        AuditLog::log('DAFTAR_PELUANG', 'OpportunityRegistration', $reg->id, "Siswa {$student->name} mendaftar pada peluang {$opportunity->title}.");

        return back()->with('success', 'Pendaftaran berhasil diajukan! Pantau status pendaftaran Anda di halaman ini.');
    }
}
