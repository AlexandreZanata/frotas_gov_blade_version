<?php

namespace App\Http\Controllers;

use App\Models\VehicleCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VehicleCategoryController extends Controller
{
    // Para a API (usado no diário de bordo)
    public function apiIndex(Request $request): JsonResponse
    {
        try {
            $categories = VehicleCategory::select('id', 'name')
                ->orderBy('name')
                ->get();

            return response()->json($categories);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao carregar categorias',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Para a interface web normal
    public function index(Request $request)
    {
        $search = $request->input('search');

        $categories = VehicleCategory::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('vehicle_categories.index', compact('categories', 'search'));
    }

    public function create()
    {
        return view('vehicle_categories.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:vehicle_categories,name']);
        VehicleCategory::create($request->only('name'));
        return redirect()->route('vehicle-categories.index')->with('success', 'Categoria criada com sucesso.');
    }

    public function edit(VehicleCategory $vehicleCategory)
    {
        return view('vehicle_categories.edit', compact('vehicleCategory'));
    }

    public function update(Request $request, VehicleCategory $vehicleCategory)
    {
        $request->validate(['name' => 'required|string|max:255|unique:vehicle_categories,name,' . $vehicleCategory->id]);
        $vehicleCategory->update($request->only('name'));
        return redirect()->route('vehicle-categories.index')->with('success', 'Categoria atualizada com sucesso.');
    }

    public function destroy(Request $request, VehicleCategory $vehicleCategory)
    {
        // Verificar se a categoria está em uso
        $vehiclesCount = $vehicleCategory->vehicles()->count();

        if ($vehiclesCount > 0) {
            return redirect()->back()
                ->with('error', "Não é possível excluir a categoria '{$vehicleCategory->name}' pois existem {$vehiclesCount} veículo(s) usando esta categoria.");
        }

        // Gerar backup se solicitado
        if ($request->has('create_backup') && $request->input('create_backup')) {
            try {
                $backupService = new \App\Services\BackupPdfService();
                $backupService->generateCategoryBackup($vehicleCategory);
            } catch (\Exception $e) {
                return redirect()->back()
                    ->with('error', 'Erro ao gerar backup: ' . $e->getMessage());
            }
        }

        $vehicleCategory->delete();

        return redirect()->route('vehicle-categories.index')
            ->with('success', 'Categoria excluída com sucesso.' . ($request->has('create_backup') ? ' Backup gerado com sucesso.' : ''));
    }
}
