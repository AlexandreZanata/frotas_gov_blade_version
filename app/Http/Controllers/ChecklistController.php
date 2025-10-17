<?php

namespace App\Http\Controllers;

use App\Models\checklist\Checklist;
use App\Models\defect\DefectReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChecklistController extends Controller
{
    /**
     * Exibir todos os checklists (com hierarquia de permissões)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');
        $status = $request->input('status');
        $hasDefects = $request->input('has_defects');

        // Query base com relacionamentos
        $query = Checklist::with(['run.vehicle.prefix', 'run.vehicle.secretariat', 'user', 'approver'])
            ->orderBy('created_at', 'desc');

        // Aplicar filtros de hierarquia
        if ($user->isGeneralManager()) {
            // Gestor Geral vê todos os checklists
        } elseif ($user->isManager()) {
            // Gestor Setorial vê apenas da sua secretaria
            $query->whereHas('run.vehicle', function ($q) use ($user) {
                $q->where('secretariat_id', $user->secretariat_id);
            });
        } else {
            // Outros usuários veem apenas os seus
            $query->where('user_id', $user->id);
        }

        // Filtro de busca
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('run.vehicle', function ($vehicleQuery) use ($search) {
                    $vehicleQuery->where('plate', 'like', "%{$search}%")
                        ->orWhereHas('prefix', function ($prefixQuery) use ($search) {
                            $prefixQuery->where('abbreviation', 'like', "%{$search}%");
                        });
                })
                ->orWhereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%");
                });
            });
        }

        // Filtro de status
        if ($status) {
            $query->where('approval_status', $status);
        }

        // Filtro de defeitos
        if ($hasDefects !== null) {
            $query->where('has_defects', $hasDefects === '1');
        }

        $checklists = $query->paginate(10);

        return view('checklists.index', compact('checklists', 'search', 'status', 'hasDefects'));
    }

    /**
     * Exibir checklists pendentes (notificações)
     */
    public function pending(Request $request)
    {
        $user = Auth::user();

        // Apenas gestores podem aprovar
        if (!$user->isManager() && !$user->isGeneralManager()) {
            abort(403, 'Você não tem permissão para acessar esta página.');
        }

        // Query base para checklists com defeitos pendentes
        $query = Checklist::with(['run.vehicle.prefix', 'run.vehicle.secretariat', 'user'])
            ->where('has_defects', true)
            ->where('approval_status', 'pending')
            ->orderBy('created_at', 'asc');

        // Aplicar filtro de secretaria para gestor setorial
        if ($user->isManager() && !$user->isGeneralManager()) {
            $query->whereHas('run.vehicle', function ($q) use ($user) {
                $q->where('secretariat_id', $user->secretariat_id);
            });
        }

        // Também buscar relatórios de defeitos pendentes
        $defectReportsQuery = DefectReport::with(['vehicle.prefix', 'vehicle.secretariat', 'user'])
            ->where('status', 'open')
            ->orderBy('created_at', 'asc');

        if ($user->isManager() && !$user->isGeneralManager()) {
            $defectReportsQuery->whereHas('vehicle', function ($q) use ($user) {
                $q->where('secretariat_id', $user->secretariat_id);
            });
        }

        $pendingChecklists = $query->paginate(10, ['*'], 'checklists_page');
        $pendingDefectReports = $defectReportsQuery->paginate(10, ['*'], 'defects_page');

        return view('checklists.pending', compact('pendingChecklists', 'pendingDefectReports'));
    }

    /**
     * Exibir detalhes de um checklist
     */
    public function show(Checklist $checklist)
    {
        $user = Auth::user();

        // Verificar permissões
        if (!$user->isGeneralManager() &&
            !($user->isManager() && $checklist->run->vehicle->secretariat_id === $user->secretariat_id) &&
            $checklist->user_id !== $user->id) {
            abort(403, 'Você não tem permissão para visualizar este checklist.');
        }

        $checklist->load([
            'run.vehicle.prefix',
            'run.vehicle.secretariat',
            'user',
            'approver',
            'answers.checklistItem'
        ]);

        return view('checklists.show', compact('checklist'));
    }

    /**
     * Aprovar checklist
     */
    public function approve(Request $request, Checklist $checklist)
    {
        $user = Auth::user();

        // Verificar permissões
        if (!$user->isManager() && !$user->isGeneralManager()) {
            abort(403, 'Você não tem permissão para aprovar checklists.');
        }

        if (!$user->isGeneralManager() && $checklist->run->vehicle->secretariat_id !== $user->secretariat_id) {
            abort(403, 'Você não tem permissão para aprovar este checklist.');
        }

        $validated = $request->validate([
            'comment' => 'required|string|max:1000',
        ], [
            'comment.required' => 'O comentário é obrigatório.',
        ]);

        DB::transaction(function () use ($checklist, $user, $validated) {
            $checklist->update([
                'approval_status' => 'approved',
                'approver_id' => $user->id,
                'approver_comment' => $validated['comment'],
                'approved_at' => now(),
            ]);

            // TODO: Enviar notificação para o usuário que criou o checklist
            // TODO: Se aprovado, encaminhar para módulo do mecânico
        });

        return redirect()->route('checklists.show', $checklist)
            ->with('success', 'Checklist aprovado com sucesso! Solicitação encaminhada ao mecânico.');
    }

    /**
     * Rejeitar checklist
     */
    public function reject(Request $request, Checklist $checklist)
    {
        $user = Auth::user();

        // Verificar permissões
        if (!$user->isManager() && !$user->isGeneralManager()) {
            abort(403, 'Você não tem permissão para rejeitar checklists.');
        }

        if (!$user->isGeneralManager() && $checklist->run->vehicle->secretariat_id !== $user->secretariat_id) {
            abort(403, 'Você não tem permissão para rejeitar este checklist.');
        }

        $validated = $request->validate([
            'comment' => 'required|string|max:1000',
        ], [
            'comment.required' => 'O comentário é obrigatório para rejeição.',
        ]);

        DB::transaction(function () use ($checklist, $user, $validated) {
            $checklist->update([
                'approval_status' => 'rejected',
                'approver_id' => $user->id,
                'approver_comment' => $validated['comment'],
                'approved_at' => now(),
            ]);

            // TODO: Enviar notificação para o usuário que criou o checklist
        });

        return redirect()->route('checklists.show', $checklist)
            ->with('success', 'Checklist rejeitado. O usuário será notificado.');
    }
}

