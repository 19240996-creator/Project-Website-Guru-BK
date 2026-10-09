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
}
