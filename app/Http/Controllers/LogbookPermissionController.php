<?php

namespace App\Http\Controllers;

use App\Models\LogbookPermission;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Secretariat;
use Illuminate\Http\Request;

class LogbookPermissionController extends Controller
{
    public function index()
    {
        // Apenas gestores gerais podem acessar
        if (!auth()->user()->isGeneralManager()) {
            abort(403, 'Acesso negado.');
        }

        $permissions = LogbookPermission::with(['user', 'secretariat', 'vehicles.prefix'])
            ->latest()
            ->paginate(10);

        return view('logbook-permissions.index', compact('permissions'));
    }

    public function create()
    {
        if (!auth()->user()->isGeneralManager()) {
            abort(403, 'Acesso negado.');
        }

        // Admin Geral pode atribuir privilégios para qualquer usuário, incluindo ele mesmo
        $users = User::whereHas('role', function($q) {
            $q->whereIn('name', ['driver', 'sector_manager', 'general_manager']);
        })->with(['role', 'secretariat'])->get();

        $secretariats = Secretariat::all();
        $vehicles = Vehicle::with('prefix')->get();

        return view('logbook-permissions.create', compact('users', 'secretariats', 'vehicles'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isGeneralManager()) {
            abort(403, 'Acesso negado.');
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'scope' => 'required|in:all,secretariat,vehicles',
            'secretariat_ids' => 'required_if:scope,secretariat|array|min:1',
            'secretariat_ids.*' => 'exists:secretariats,id',
            'vehicle_ids' => 'required_if:scope,vehicles|array|min:1',
            'vehicle_ids.*' => 'exists:vehicles,id',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $permission = LogbookPermission::create([
            'user_id' => $validated['user_id'],
            'scope' => $validated['scope'],
            'secretariat_id' => null,
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Se o escopo for 'secretariat', associar as secretarias
        if ($validated['scope'] === 'secretariat' && !empty($validated['secretariat_ids'])) {
            $permission->secretariats()->attach($validated['secretariat_ids']);
        }

        // Se o escopo for 'vehicles', associar os veículos
        if ($validated['scope'] === 'vehicles' && !empty($validated['vehicle_ids'])) {
            $permission->vehicles()->attach($validated['vehicle_ids']);
        }

        return redirect()->route('logbook-permissions.index')
            ->with('success', 'Permissão criada com sucesso.');
    }

    public function edit(LogbookPermission $logbookPermission)
    {
        if (!auth()->user()->isGeneralManager()) {
            abort(403, 'Acesso negado.');
        }

        $logbookPermission->load(['vehicles', 'user', 'secretariat']);

        // Admin Geral pode editar privilégios de qualquer usuário, incluindo ele mesmo
        $users = User::whereHas('role', function($q) {
            $q->whereIn('name', ['driver', 'sector_manager', 'general_manager']);
        })->with(['role', 'secretariat'])->get();

        $secretariats = Secretariat::all();
        $vehicles = Vehicle::with('prefix')->get();

        return view('logbook-permissions.edit', compact('logbookPermission', 'users', 'secretariats', 'vehicles'));
    }

    public function update(Request $request, LogbookPermission $logbookPermission)
    {
        if (!auth()->user()->isGeneralManager()) {
            abort(403, 'Acesso negado.');
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'scope' => 'required|in:all,secretariat,vehicles',
            'secretariat_ids' => 'required_if:scope,secretariat|nullable|array',
            'secretariat_ids.*' => 'exists:secretariats,id',
            'vehicle_ids' => 'required_if:scope,vehicles|nullable|array',
            'vehicle_ids.*' => 'exists:vehicles,id',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $logbookPermission->update([
            'user_id' => $validated['user_id'],
            'scope' => $validated['scope'],
            'secretariat_id' => null, // Campo legado, não mais usado
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Atualizar secretarias associadas
        if ($validated['scope'] === 'secretariat' && isset($validated['secretariat_ids'])) {
            $logbookPermission->secretariats()->sync($validated['secretariat_ids']);
        } else {
            $logbookPermission->secretariats()->detach();
        }

        // Atualizar veículos associados
        if ($validated['scope'] === 'vehicles' && isset($validated['vehicle_ids'])) {
            $logbookPermission->vehicles()->sync($validated['vehicle_ids']);
        } else {
            $logbookPermission->vehicles()->detach();
        }

        return redirect()->route('logbook-permissions.index')
            ->with('success', 'Permissão atualizada com sucesso.');
    }

    public function destroy(LogbookPermission $logbookPermission)
    {
        if (!auth()->user()->isGeneralManager()) {
            abort(403, 'Acesso negado.');
        }

        $logbookPermission->delete();

        return redirect()->route('logbook-permissions.index')
            ->with('success', 'Permissão excluída com sucesso.');
    }
}
