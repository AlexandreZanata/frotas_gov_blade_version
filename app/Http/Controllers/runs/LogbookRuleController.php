<?php

namespace App\Http\Controllers\runs;

use App\Http\Controllers\Controller;
use App\Models\logbook\LogbookRule;
use App\Models\user\User;
use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\VehicleCategory;
use App\Rules\UniqueLogbookRule;
use Illuminate\Http\Request;

class LogbookRuleController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->isGeneralManager()) {
            abort(403, 'Acesso não autorizado');
        }

        $search = $request->get('search');

        $rules = LogbookRule::with(['vehicleCategory', 'user', 'vehicle'])
            ->when($search, function($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('target_type', 'like', "%{$search}%")
                    ->orWhere('rule_type', 'like', "%{$search}%");
            })
            ->orderBy('target_type')
            ->orderBy('name')
            ->paginate(10);

        return view('logbook-rules.index', compact('rules', 'search'));
    }

    public function create()
    {
        $vehicleCategories = VehicleCategory::all();
        $users = User::where('status', 'active')->get();
        $vehicles = Vehicle::all();

        return view('logbook-rules.create', compact('vehicleCategories', 'users', 'vehicles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'rule_type' => 'required|in:fixed,formula',
            'fixed_value' => 'required_if:rule_type,fixed|nullable|integer|min:0',
            'formula_type' => 'required_if:rule_type,formula|nullable|in:daily_average_plus_fixed,daily_average_plus_percentage',
            'formula_value' => 'required_if:rule_type,formula|nullable|integer|min:0',
            'target_type' => 'required|in:global,vehicle_category,user,vehicle',
            'target_id' => [
                'required_if:target_type,vehicle_category,user,vehicle',
                'nullable',
                new UniqueLogbookRule(null, $request->target_type)
            ],
            'is_active' => 'boolean'
        ]);

        if ($request->target_type === 'global') {
            $existingGlobalRule = LogbookRule::where('target_type', 'global')
                ->whereNull('target_id')
                ->where('is_active', true)
                ->exists();

            if ($existingGlobalRule) {
                return redirect()->back()
                    ->withErrors(['target_type' => 'Já existe uma regra global ativa no sistema. Só é permitida uma regra global por vez.'])
                    ->withInput();
            }
        }

        // Prepara os dados para criação
        $ruleData = [
            'name' => $request->name,
            'description' => $request->description,
            'rule_type' => $request->rule_type,
            'target_type' => $request->target_type,
            'target_id' => $request->target_type === 'global' ? null : $request->target_id,
            'is_active' => $request->is_active ?? true,
        ];

        // Adiciona campos específicos baseados no tipo de regra
        if ($request->rule_type === 'fixed') {
            $ruleData['fixed_value'] = $request->fixed_value;
            $ruleData['formula_type'] = null;
            $ruleData['formula_value'] = null;
        } else {
            $ruleData['fixed_value'] = null;
            $ruleData['formula_type'] = $request->formula_type;
            $ruleData['formula_value'] = $request->formula_value;
        }

        $logbookRule = LogbookRule::create($ruleData);

        // AUDIT LOG - CRIAÇÃO
        $this->createAuditLog(
            $logbookRule,
            'created',
            null,
            $ruleData,
            "Regra de quilometragem '{$logbookRule->name}' criada"
        );

        return redirect()->route('logbook-rules.index')
            ->with('success', 'Regra criada com sucesso.');
    }

    public function update(Request $request, LogbookRule $logbookRule)
    {
        // Salvar valores antigos para o audit log
        $oldValues = $logbookRule->toArray();

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'rule_type' => 'required|in:fixed,formula',
            'fixed_value' => 'required_if:rule_type,fixed|nullable|integer|min:0',
            'formula_type' => 'required_if:rule_type,formula|nullable|in:daily_average_plus_fixed,daily_average_plus_percentage',
            'formula_value' => 'required_if:rule_type,formula|nullable|integer|min:0',
            'target_type' => 'required|in:global,vehicle_category,user,vehicle',
            'target_id' => [
                'required_if:target_type,vehicle_category,user,vehicle',
                'nullable',
                new UniqueLogbookRule($logbookRule->id, $request->target_type)
            ],
            'is_active' => 'boolean'
        ]);

        // Prepara os dados para atualização
        $ruleData = [
            'name' => $request->name,
            'description' => $request->description,
            'rule_type' => $request->rule_type,
            'target_type' => $request->target_type,
            'target_id' => $request->target_type === 'global' ? null : $request->target_id,
            'is_active' => $request->is_active ?? $logbookRule->is_active,
        ];

        // Atualiza campos específicos baseados no tipo de regra
        if ($request->rule_type === 'fixed') {
            $ruleData['fixed_value'] = $request->fixed_value;
            $ruleData['formula_type'] = null;
            $ruleData['formula_value'] = null;
        } else {
            $ruleData['fixed_value'] = null;
            $ruleData['formula_type'] = $request->formula_type;
            $ruleData['formula_value'] = $request->formula_value;
        }

        $logbookRule->update($ruleData);

        // AUDIT LOG - EDIÇÃO
        $this->createAuditLog(
            $logbookRule,
            'updated',
            $oldValues,
            $logbookRule->fresh()->toArray(),
            "Regra de quilometragem '{$logbookRule->name}' atualizada"
        );

        return redirect()->route('logbook-rules.index')
            ->with('success', 'Regra atualizada com sucesso.');
    }

    public function edit(LogbookRule $logbookRule)
    {
        $vehicleCategories = VehicleCategory::all();
        $users = User::where('status', 'active')->get();
        $vehicles = Vehicle::all();

        return view('logbook-rules.edit', compact('logbookRule', 'vehicleCategories', 'users', 'vehicles'));
    }

    public function destroy(LogbookRule $logbookRule)
    {
        // Salvar dados para o audit log antes de excluir
        $oldValues = $logbookRule->toArray();
        $ruleName = $logbookRule->name;

        $logbookRule->delete();


        $this->createAuditLog(
            $logbookRule,
            'deleted',
            $oldValues,
            null,
            "Regra de quilometragem '{$ruleName}' excluída"
        );

        return redirect()->route('logbook-rules.index')
            ->with('success', 'Regra excluída com sucesso!');
    }

    public function toggleStatus(LogbookRule $logbookRule)
    {
        $oldStatus = $logbookRule->is_active;
        $newStatus = !$logbookRule->is_active;

        $logbookRule->update(['is_active' => $newStatus]);

        $status = $logbookRule->is_active ? 'ativada' : 'desativada';

        // AUDIT LOG - ALTERAÇÃO DE STATUS
        $this->createAuditLog(
            $logbookRule,
            'update',
            ['is_active' => $oldStatus],
            ['is_active' => $newStatus],
            "Regra de quilometragem '{$logbookRule->name}' {$status}"
        );

        return redirect()->route('logbook-rules.index')
            ->with('success', "Regra {$status} com sucesso!");
    }

    /**
     * Cria um registro de audit log
     */
    private function createAuditLog($model, $action, $oldValues = null, $newValues = null, $description = null)
    {
        try {
            \App\Models\auditlog\AuditLog::create([
                'user_id' => auth()->id(),
                'action' => $action,
                'auditable_type' => get_class($model),
                'auditable_id' => $model->id,
                'old_values' => $oldValues ? json_encode($oldValues) : null,
                'new_values' => $newValues ? json_encode($newValues) : null,
                'description' => $description,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao criar audit log: ' . $e->getMessage());
        }
    }
}
