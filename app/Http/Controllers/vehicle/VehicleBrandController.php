<?php

namespace App\Http\Controllers\vehicle;

use App\Http\Controllers\Controller;
use App\Models\Vehicle\VehicleBrand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class VehicleBrandController extends Controller
{
    /**
     * Procura por marcas (API).
     */
    public function apiSearch(Request $request)
    {
        $query = $request->input('q', '');

        $brands = VehicleBrand::where('name', 'LIKE', "%{$query}%")
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name']); // Retorna apenas ID e nome

        return response()->json($brands);
    }

    /**
     * Cria uma nova marca "inline" (API).
     */
    public function storeInline(Request $request)
    {
        // Validação
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:vehicle_brands,name',
        ], [
            'name.unique' => 'Esta marca já existe.',
            'name.required' => 'O nome é obrigatório.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação.',
                'errors' => $validator->errors()
            ], 422); // 422 Unprocessable Entity
        }

        try {
            $brand = VehicleBrand::create([
                'name' => $request->name,
                // Assumindo que seu ID é UUID. Se for auto-increment, remova a linha do ID.
                'id' => (string) Str::uuid()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Marca criada com sucesso!',
                'brand' => $brand // Retorna a marca criada
            ], 201); // 201 Created

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar no banco de dados: ' . $e->getMessage()
            ], 500);
        }
    }
}
