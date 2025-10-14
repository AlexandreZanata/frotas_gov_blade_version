<?php
// app/Http/Controllers/LogbookRuleController.php

namespace App\Http\Controllers;

use App\Models\LogbookRule;
use App\Models\VehicleCategory;
use App\Models\User;
use App\Models\Vehicle;
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
            ->paginate(10); // Mude de get() para paginate()

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
            'description' => 'nullable|string', // Adicione esta validação
            'rule_type' => 'required|in:fixed,formula',
            'fixed_value' => 'required_if:rule_type,fixed|nullable|integer|min:0',
            'formula_type' => 'required_if:rule_type,formula|nullable|in:daily_average_plus_fixed,daily_average_plus_percentage',
            'formula_value' => 'required_if:rule_type,formula|nullable|integer|min:0',
            'target_type' => 'required|in:global,vehicle_category,user,vehicle',
            'target_id' => 'required_if:target_type,vehicle_category,user,vehicle|nullable',
            'is_active' => 'boolean'
        ]);

        // Prepara os dados para criação
        $ruleData = [
            'name' => $request->name,
            'description' => $request->description, // Inclui a descrição
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

        LogbookRule::create($ruleData);

        return redirect()->route('logbook-rules.index')
            ->with('success', 'Regra criada com sucesso.');
    }

    public function edit(LogbookRule $logbookRule)
    {
        $vehicleCategories = VehicleCategory::all();
        $users = User::where('status', 'active')->get();
        $vehicles = Vehicle::all();

        return view('logbook-rules.edit', compact('logbookRule', 'vehicleCategories', 'users', 'vehicles'));
    }

    public function update(Request $request, LogbookRule $logbookRule)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string', // Adicione esta validação
            'rule_type' => 'required|in:fixed,formula',
            'fixed_value' => 'required_if:rule_type,fixed|nullable|integer|min:0',
            'formula_type' => 'required_if:rule_type,formula|nullable|in:daily_average_plus_fixed,daily_average_plus_percentage',
            'formula_value' => 'required_if:rule_type,formula|nullable|integer|min:0',
            'target_type' => 'required|in:global,vehicle_category,user,vehicle',
            'target_id' => 'required_if:target_type,vehicle_category,user,vehicle|nullable',
            'is_active' => 'boolean'
        ]);

        // Prepara os dados para atualização
        $ruleData = [
            'name' => $request->name,
            'description' => $request->description, // Inclui a descrição
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

        return redirect()->route('logbook-rules.index')
            ->with('success', 'Regra atualizada com sucesso.');
    }

    public function destroy(LogbookRule $logbookRule)
    {
        $logbookRule->delete();

        return redirect()->route('logbook-rules.index')
            ->with('success', 'Regra excluída com sucesso!');
    }

    public function toggleStatus(LogbookRule $logbookRule)
    {
        $logbookRule->update(['is_active' => !$logbookRule->is_active]);

        $status = $logbookRule->is_active ? 'ativada' : 'desativada';

        return redirect()->route('logbook-rules.index')
            ->with('success', "Regra {$status} com sucesso!");
    }
}
