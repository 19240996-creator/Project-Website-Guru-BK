<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AuditLog;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();

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

        $actions = AuditLog::select('action')
            ->distinct()
            ->whereNotNull('action')
            ->where('action', '!=', '')
            ->orderBy('action')
            ->pluck('action');

        $entityTypes = AuditLog::select('entity_type')
            ->distinct()
            ->whereNotNull('entity_type')
            ->where('entity_type', '!=', '')
            ->orderBy('entity_type')
            ->pluck('entity_type');

        return view('guru.audit.index', compact('logs', 'actions', 'entityTypes'));
    }

    public function destroy($id)
    {
        $log = AuditLog::findOrFail($id);
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

        $count = AuditLog::whereIn('id', $request->ids)->delete();

        return redirect()->route('guru.audit.index')
            ->with('success', "Sebanyak {$count} catatan audit log berhasil dihapus.");
    }

    public function clear(Request $request)
    {
        $request->validate([
            'range' => 'required|string|in:all,older_30,older_90',
        ]);

        $count = 0;
        if ($request->range === 'older_30') {
            $cutoff = now()->subDays(30);
            $count = AuditLog::where('created_at', '<', $cutoff)->delete();
            $message = "Sebanyak {$count} catatan audit log yang lebih lama dari 30 hari berhasil dibersihkan.";
        } elseif ($request->range === 'older_90') {
            $cutoff = now()->subDays(90);
            $count = AuditLog::where('created_at', '<', $cutoff)->delete();
            $message = "Sebanyak {$count} catatan audit log yang lebih lama dari 90 hari berhasil dibersihkan.";
        } else {
            $count = AuditLog::count();
            AuditLog::query()->delete();
            $message = "Seluruh riwayat audit log ({$count} catatan) telah berhasil dibersihkan.";
        }

        return redirect()->route('guru.audit.index')->with('success', $message);
    }
}

