<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Partner;
use App\Models\PartnerActivity;
use App\Models\StudentClass;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PartnerController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type', 'all');
        $status = $request->query('status', 'all');
        $category = $request->query('category', 'all');
        $city = $request->query('city', 'all');
        $q = $request->query('q', '');

        $query = Partner::withCount(['activities', 'opportunities'])->latest();

        if ($type !== 'all' && in_array($type, ['perguruan_tinggi', 'perusahaan'])) {
            $query->where('type', $type);
        }

        if ($status !== 'all' && in_array($status, ['aktif', 'akan_berakhir', 'berakhir', 'tidak_aktif'])) {
            $query->where('partnership_status', $status);
        }

        if ($category !== 'all' && filled($category)) {
            $query->where('category', $category);
        }

        if ($city !== 'all' && filled($city)) {
            $query->where('city', $city);
        }

        if ($request->filled('q')) {
            $search = trim($request->q);
            $query->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('contact_person', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('partnership_doc_number', 'like', "%{$search}%");
            });
        }

        $partners = $query->paginate(15)->withQueryString();

        $stats = [
            'total_pt' => Partner::where('type', 'perguruan_tinggi')->count(),
            'total_perusahaan' => Partner::where('type', 'perusahaan')->count(),
            'total_aktif' => Partner::where('partnership_status', 'aktif')->count(),
            'total_kegiatan' => PartnerActivity::count(),
        ];

        $categories = Partner::whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $cities = Partner::whereNotNull('city')
            ->where('city', '!=', '')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');

        return view('guru.mitra.index', compact('partners', 'stats', 'type', 'status', 'category', 'city', 'q', 'categories', 'cities'));
    }

    public function show($id)
    {
        $partner = Partner::with(['activities.targetClass', 'opportunities'])->findOrFail($id);
        $classes = StudentClass::orderBy('grade')->orderBy('name')->get();
        return view('guru.mitra.show', compact('partner', 'classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:perguruan_tinggi,perusahaan',
            'category' => 'nullable|string|max:100',
            'name' => 'required|string|max:255',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'website' => 'nullable|url',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:25',
            'email' => 'nullable|email',
            'partnership_doc_number' => 'nullable|string|max:100',
            'partnership_status' => 'required|in:aktif,akan_berakhir,berakhir,tidak_aktif',
            'partnership_start_date' => 'nullable|date',
            'partnership_end_date' => 'nullable|date|after_or_equal:partnership_start_date',
            'notes' => 'nullable|string',
        ]);

        $code = 'MIT-' . date('Y') . '-' . str_pad(Partner::count() + 1, 5, '0', STR_PAD_LEFT);
        $validated['code'] = $code;

        $partner = Partner::create($validated);
        AuditLog::log('SIMPAN', 'Partner', $partner->id, "Menambahkan data mitra baru: {$partner->name} ({$partner->code}).");

        return redirect()->route('guru.mitra.show', $partner->id)->with('success', 'Data mitra baru berhasil disimpan.');
    }

    public function update(Request $request, $id)
    {
        $partner = Partner::findOrFail($id);

        $validated = $request->validate([
            'type' => 'required|in:perguruan_tinggi,perusahaan',
            'category' => 'nullable|string|max:100',
            'name' => 'required|string|max:255',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'website' => 'nullable|url',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:25',
            'email' => 'nullable|email',
            'partnership_doc_number' => 'nullable|string|max:100',
            'partnership_status' => 'required|in:aktif,akan_berakhir,berakhir,tidak_aktif',
            'partnership_start_date' => 'nullable|date',
            'partnership_end_date' => 'nullable|date|after_or_equal:partnership_start_date',
            'notes' => 'nullable|string',
        ]);

        $partner->update($validated);

        AuditLog::log('PERBARUI', 'Partner', $partner->id, "Memperbarui data mitra: {$partner->name} ({$partner->code}).");

        return back()->with('success', "Data mitra {$partner->name} ({$partner->code}) berhasil diperbarui.");
    }

    public function destroy($id)
    {
        $partner = Partner::findOrFail($id);
        $name = $partner->name;
        $code = $partner->code;
        $partnerId = $partner->id;

        DB::transaction(function () use ($partner, $name, $code, $partnerId) {
            $partner->delete();
            AuditLog::log('HAPUS', 'Partner', $partnerId, "Menghapus data mitra: {$name} ({$code}).");
        });

        return redirect()->route('guru.mitra.index')->with('success', "Data mitra {$name} ({$code}) berhasil dihapus dari sistem.");
    }

    public function activities(Request $request)
    {
        $query = PartnerActivity::with(['partner', 'targetClass'])->orderBy('date', 'desc')->orderBy('start_time', 'asc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('activity_type', $request->type);
        }

        $activities = $query->paginate(15)->withQueryString();
        $partners = Partner::orderBy('name')->get();
        $classes = StudentClass::orderBy('grade')->orderBy('name')->get();

        return view('guru.mitra.activities', compact('activities', 'partners', 'classes'));
    }

    public function storeActivity(Request $request)
    {
        $validated = $request->validate([
            'partner_id' => 'required|exists:partners,id',
            'title' => 'required|string|max:255',
            'activity_type' => 'required|in:campus_visit,seminar,sosialisasi,kunjungan_industri,magang,rekrutmen,pelatihan',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'room_location' => 'required|string|max:255',
            'target_class_id' => 'nullable',
            'target_class_ids' => 'nullable|array',
            'target_class_ids.*' => 'exists:student_classes,id',
            'max_participants' => 'required|integer|min:1',
            'pic_name' => 'nullable|string|max:255',
            'status' => 'required|in:rencana,menunggu_konfirmasi,terkonfirmasi,terlaksana,ditunda,dibatalkan',
            'notes' => 'nullable|string',
        ]);

        $targetClassIds = [];
        if (!empty($validated['target_class_ids'])) {
            $targetClassIds = array_values(array_unique(array_map('intval', $validated['target_class_ids'])));
        } elseif (!empty($validated['target_class_id'])) {
            $targetClassIds = [(int) $validated['target_class_id']];
        }

        $validated['target_class_ids'] = !empty($targetClassIds) ? $targetClassIds : null;
        $validated['target_class_id'] = !empty($targetClassIds) ? $targetClassIds[0] : null;

        // Anti-conflict schedule check engine!
        $conflicts = PartnerActivity::checkConflicts(
            $validated['date'],
            $validated['start_time'],
            $validated['end_time'],
            $validated['room_location'],
            $targetClassIds
        );

        if (!empty($conflicts)) {
            return back()->withInput()->withErrors([
                'schedule_conflict' => 'Peringatan Benturan Jadwal: ' . implode(' ', $conflicts),
            ]);
        }

        $seq = (int) (PartnerActivity::max('id') ?? 0);
        do {
            $seq++;
            $code = 'KGT-' . date('Y') . '-' . str_pad($seq, 5, '0', STR_PAD_LEFT);
        } while (PartnerActivity::where('code', $code)->exists());

        $validated['code'] = $code;

        $act = PartnerActivity::create($validated);
        AuditLog::log('SIMPAN', 'PartnerActivity', $act->id, "Menjadwalkan kegiatan mitra {$act->title} pada {$act->date}.");

        return back()->with('success', 'Agenda kegiatan mitra berhasil dijadwalkan tanpa benturan jadwal.');
    }

    public function updateActivity(Request $request, $id)
    {
        $activity = PartnerActivity::findOrFail($id);

        $validated = $request->validate([
            'partner_id' => 'required|exists:partners,id',
            'title' => 'required|string|max:255',
            'activity_type' => 'required|in:campus_visit,seminar,sosialisasi,kunjungan_industri,magang,rekrutmen,pelatihan',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'room_location' => 'required|string|max:255',
            'target_class_id' => 'nullable',
            'target_class_ids' => 'nullable|array',
            'target_class_ids.*' => 'exists:student_classes,id',
            'max_participants' => 'required|integer|min:1',
            'pic_name' => 'nullable|string|max:255',
            'status' => 'required|in:rencana,menunggu_konfirmasi,terkonfirmasi,terlaksana,ditunda,dibatalkan',
            'notes' => 'nullable|string',
        ]);

        $targetClassIds = [];
        if (!empty($validated['target_class_ids'])) {
            $targetClassIds = array_values(array_unique(array_map('intval', $validated['target_class_ids'])));
        } elseif (!empty($validated['target_class_id'])) {
            $targetClassIds = [(int) $validated['target_class_id']];
        }

        $validated['target_class_ids'] = !empty($targetClassIds) ? $targetClassIds : null;
        $validated['target_class_id'] = !empty($targetClassIds) ? $targetClassIds[0] : null;

        // Anti-conflict schedule check engine (excluding this activity itself)
        $conflicts = PartnerActivity::checkConflicts(
            $validated['date'],
            $validated['start_time'],
            $validated['end_time'],
            $validated['room_location'],
            $targetClassIds,
            (int) $activity->id
        );

        if (!empty($conflicts)) {
            return back()->withInput()->withErrors([
                'schedule_conflict' => 'Peringatan Benturan Jadwal: ' . implode(' ', $conflicts),
            ]);
        }

        $activity->update($validated);

        AuditLog::log(
            'PERBARUI',
            'PartnerActivity',
            $activity->id,
            "Memperbarui agenda kegiatan mitra '{$activity->title}' ({$activity->code}) pada {$activity->date->translatedFormat('d M Y')}."
        );

        return back()->with('success', "Agenda kegiatan mitra {$activity->code} berhasil diperbarui.");
    }

    public function destroyActivity($id)
    {
        $activity = PartnerActivity::findOrFail($id);
        $title = $activity->title;
        $code = $activity->code;
        $actId = $activity->id;

        $activity->delete();

        AuditLog::log(
            'HAPUS',
            'PartnerActivity',
            $actId,
            "Menghapus agenda kegiatan mitra '{$title}' ({$code})."
        );

        return back()->with('success', "Agenda kegiatan mitra {$code} berhasil dihapus dari sistem.");
    }

    public function bulkDestroyActivities(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:partner_activities,id',
        ], [
            'ids.required' => 'Pilih minimal satu agenda kegiatan untuk dihapus.',
            'ids.min' => 'Pilih minimal satu agenda kegiatan untuk dihapus.',
        ]);

        $ids = $validated['ids'];
        $count = count($ids);

        DB::transaction(function () use ($ids, $count) {
            $activities = PartnerActivity::whereIn('id', $ids)->get();
            $titles = $activities->pluck('title')->take(3)->implode(', ');
            if ($count > 3) {
                $titles .= " dan " . ($count - 3) . " lainnya";
            }

            PartnerActivity::whereIn('id', $ids)->delete();

            AuditLog::log(
                'HAPUS',
                'PartnerActivity',
                null,
                "Menghapus secara massal {$count} agenda kegiatan mitra ({$titles})."
            );
        });

        return back()->with('success', "Sebanyak {$count} agenda kegiatan mitra berhasil dihapus.");
    }

    public function bulkStatusActivities(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:partner_activities,id',
            'status' => 'required|in:rencana,menunggu_konfirmasi,terkonfirmasi,terlaksana,ditunda,dibatalkan',
        ], [
            'ids.required' => 'Pilih minimal satu agenda kegiatan.',
            'ids.min' => 'Pilih minimal satu agenda kegiatan.',
            'status.required' => 'Status baru wajib dipilih.',
        ]);

        $ids = $validated['ids'];
        $status = $validated['status'];
        $count = count($ids);

        DB::transaction(function () use ($ids, $status, $count) {
            PartnerActivity::whereIn('id', $ids)->update(['status' => $status]);

            AuditLog::log(
                'PERBARUI',
                'PartnerActivity',
                null,
                "Memperbarui status {$count} agenda kegiatan mitra menjadi '" . ucfirst(str_replace('_', ' ', $status)) . "'."
            );
        });

        $statusLabel = ucfirst(str_replace('_', ' ', $status));
        return back()->with('success', "Status {$count} agenda kegiatan mitra berhasil diperbarui menjadi {$statusLabel}.");
    }
}
