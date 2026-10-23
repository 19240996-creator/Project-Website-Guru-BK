<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AuditLog;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = AuditLog::with('user')->latest();

        // Pemisahan Log Antar-Role (PRD Bagian 7):
        // Guru Koordinator BK memantau aktivitas operasional bimbingan konseling (Guru BK dan Koordinator BK),
        // namun secara ketat terisolasi dari log aktivitas Kepala Kampus dan Admin DICO.
        if ($user->role === 'koordinator_bk') {
            $query->whereHas('user', function ($uq) {
                $uq->whereIn('role', ['guru_bk', 'koordinator_bk']);
            });
        } elseif ($user->role === 'guru_bk') {
            $query->where('user_id', $user->id);
        } else {
            $query->whereRaw('1 = 0');
        }

        if ($request->filled('q')) {
            $keyword = trim($request->q);
            $query->where(function ($sub) use ($keyword) {
                $sub->where('description', 'like', "%{$keyword}%")
                    ->orWhere('entity_type', 'like', "%{$keyword}%")
                    ->orWhere('action', 'like', "%{$keyword}%")
                    ->orWhere('ip_address', 'like', "%{$keyword}%")
                    ->orWhereHas('user', function ($uq) use ($keyword) {
                        $uq->where('name', 'like', "%{$keyword}%");
                    });
            });
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('entity_type')) {
            $query->where('entity_type', $request->entity_type);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $logs = $query->paginate(25)->withQueryString();

        $actionQuery = AuditLog::select('action')->distinct()->whereNotNull('action')->where('action', '!=', '');
        $entityTypeQuery = AuditLog::select('entity_type')->distinct()->whereNotNull('entity_type')->where('entity_type', '!=', '');

        if ($user->role === 'koordinator_bk') {
            $actionQuery->whereHas('user', fn($uq) => $uq->whereIn('role', ['guru_bk', 'koordinator_bk']));
            $entityTypeQuery->whereHas('user', fn($uq) => $uq->whereIn('role', ['guru_bk', 'koordinator_bk']));
        } elseif ($user->role === 'guru_bk') {
            $actionQuery->where('user_id', $user->id);
            $entityTypeQuery->where('user_id', $user->id);
        }

        $actions = $actionQuery->orderBy('action')->pluck('action');
        $entityTypes = $entityTypeQuery->orderBy('entity_type')->pluck('entity_type');

        return view('guru.audit.index', compact('logs', 'actions', 'entityTypes'));
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $logQuery = AuditLog::where('id', $id);

        if ($user->role === 'koordinator_bk') {
            $logQuery->whereHas('user', fn($uq) => $uq->whereIn('role', ['guru_bk', 'koordinator_bk']));
        } else {
            $logQuery->where('user_id', $user->id);
        }

        $log = $logQuery->firstOrFail();
        $desc = \Illuminate\Support\Str::limit($log->description, 50);
        $log->delete();

        return redirect()->route('guru.audit.index')
            ->with('success', "Catatan log \"{$desc}\" berhasil dihapus.");
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:audit_logs,id',
        ]);

        $user = auth()->user();
        $deleteQuery = AuditLog::whereIn('id', $request->ids);

        if ($user->role === 'koordinator_bk') {
            $deleteQuery->whereHas('user', fn($uq) => $uq->whereIn('role', ['guru_bk', 'koordinator_bk']));
        } else {
            $deleteQuery->where('user_id', $user->id);
        }

        $count = $deleteQuery->delete();

        return redirect()->route('guru.audit.index')
            ->with('success', "Sebanyak {$count} catatan audit log berhasil dihapus.");
    }

    public function clear(Request $request)
    {
        $request->validate([
            'range' => 'required|string|in:all,older_30,older_90',
        ]);

        $user = auth()->user();
        $baseQuery = AuditLog::query();

        if ($user->role === 'koordinator_bk') {
            $baseQuery->whereHas('user', fn($uq) => $uq->whereIn('role', ['guru_bk', 'koordinator_bk']));
        } else {
            $baseQuery->where('user_id', $user->id);
        }

        $count = 0;
        if ($request->range === 'older_30') {
            $cutoff = now()->subDays(30);
            $count = (clone $baseQuery)->where('created_at', '<', $cutoff)->delete();
            $message = "Sebanyak {$count} catatan audit log yang lebih lama dari 30 hari berhasil dibersihkan.";
        } elseif ($request->range === 'older_90') {
            $cutoff = now()->subDays(90);
            $count = (clone $baseQuery)->where('created_at', '<', $cutoff)->delete();
            $message = "Sebanyak {$count} catatan audit log yang lebih lama dari 90 hari berhasil dibersihkan.";
        } else {
            $count = (clone $baseQuery)->count();
            $baseQuery->delete();
            $message = "Seluruh riwayat audit log ({$count} catatan) telah berhasil dibersihkan.";
        }

        return redirect()->route('guru.audit.index')->with('success', $message);
    }
}

