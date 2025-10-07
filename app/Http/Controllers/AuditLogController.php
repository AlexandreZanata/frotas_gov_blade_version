<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Display a listing of audit logs.
     */
    public function index(Request $request)
    {
        $currentUser = auth()->user();

        // Apenas gestores gerais podem visualizar logs de auditoria
        if (!$currentUser->isGeneralManager()) {
            abort(403, 'Você não tem permissão para visualizar logs de auditoria.');
        }

        $search = $request->input('search');
        $action = $request->input('action');
        $type = $request->input('type');

        $logs = AuditLog::with(['user'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                      ->orWhere('auditable_id', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($userQuery) use ($search) {
                          $userQuery->where('name', 'like', "%{$search}%");
                      });
                });
            })
            ->when($action, function ($query, $action) {
                $query->where('action', $action);
            })
            ->when($type, function ($query, $type) {
                $query->where('auditable_type', $type);
            })
            ->latest('created_at')
            ->paginate(20)
            ->withQueryString();

        // Obter tipos únicos para filtro
        $types = AuditLog::select('auditable_type')
            ->distinct()
            ->pluck('auditable_type')
            ->map(function ($type) {
                return [
                    'value' => $type,
                    'label' => class_basename($type)
                ];
            });

        return view('audit-logs.index', compact('logs', 'search', 'action', 'type', 'types'));
    }

    /**
     * Display the specified audit log.
     */
    public function show(AuditLog $auditLog)
    {
        $currentUser = auth()->user();

        if (!$currentUser->isGeneralManager()) {
            abort(403, 'Você não tem permissão para visualizar logs de auditoria.');
        }

        $auditLog->load('user');

        return view('audit-logs.show', compact('auditLog'));
    }
}
