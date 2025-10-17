<?php

namespace App\Http\Controllers;

use App\Models\Vehicle\Prefix;
use Illuminate\Http\Request;

class PrefixController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $prefixes = Prefix::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('prefixes.index', compact('prefixes', 'search'));
    }

    public function create()
    {
        return view('prefixes.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:prefixes,name']);
        Prefix::create($request->only('name'));
        return redirect()->route('prefixes.index')->with('success', 'Prefixo criado com sucesso.');
    }

    public function edit(Prefix $prefix)
    {
        return view('prefixes.edit', compact('prefix'));
    }

    public function update(Request $request, Prefix $prefix)
    {
        $request->validate(['name' => 'required|string|max:255|unique:prefixes,name,' . $prefix->id]);
        $prefix->update($request->only('name'));
        return redirect()->route('prefixes.index')->with('success', 'Prefixo atualizado com sucesso.');
    }

    public function destroy(Request $request, Prefix $prefix)
    {
        // Verificar se o prefixo está em uso
        $vehiclesCount = $prefix->vehicles()->count();

        if ($vehiclesCount > 0) {
            return redirect()->back()
                ->with('error', "Não é possível excluir o prefixo '{$prefix->name}' pois existem {$vehiclesCount} veículo(s) usando este prefixo.");
        }

        // Gerar backup se solicitado
        if ($request->has('create_backup') && $request->input('create_backup')) {
            try {
                $backupService = new \App\Services\BackupPdfService();
                $backupService->generatePrefixBackup($prefix);
            } catch (\Exception $e) {
                return redirect()->back()
                    ->with('error', 'Erro ao gerar backup: ' . $e->getMessage());
            }
        }

        $prefix->delete();

        return redirect()->route('prefixes.index')
            ->with('success', 'Prefixo excluído com sucesso.' . ($request->has('create_backup') ? ' Backup gerado com sucesso.' : ''));
    }

    /**
     * API para buscar prefixos (autocomplete)
     */
    public function search(Request $request)
    {
        $search = $request->input('q', '');

        $prefixes = Prefix::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name']);

        return response()->json($prefixes);
    }

    /**
     * API para criar prefixo inline (usado no formulário de veículos)
     */
    public function storeInline(Request $request)
    {
        // Apenas gestores gerais e setoriais podem criar prefixos
        $user = $request->user();

        // Verificação corrigida: usar $user->role->name ao invés de $user->role
        if (!$user->role || !in_array($user->role->name, ['general_manager', 'sector_manager'])) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para criar prefixos.'
            ], 403);
        }

        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:prefixes,name'
            ]);

            $prefix = Prefix::create($request->only('name'));

            return response()->json([
                'success' => true,
                'prefix' => $prefix
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Prefixo já existe ou é inválido.',
                'errors' => $e->errors()
            ], 422);
        }
    }
}
