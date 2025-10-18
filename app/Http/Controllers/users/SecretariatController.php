<?php

namespace App\Http\Controllers\users;

use App\Http\Controllers\Controller;
use App\Models\user\Secretariat;
use Illuminate\Http\Request;

class SecretariatController extends Controller
{
    /**
     * API: Pesquisar secretarias
     */
    public function search(Request $request)
    {
        $search = $request->input('q', '');

        $query = Secretariat::withCount('vehicles');

        // Filtrar por nome se houver busca
        if (strlen($search) > 0) {
            $query->where('name', 'like', "%{$search}%");
        }

        $secretariats = $query->orderBy('name')
            ->get()
            ->map(function ($secretariat) {
                return [
                    'id' => $secretariat->id,
                    'name' => $secretariat->name,
                    'vehicle_count' => $secretariat->vehicles_count,
                ];
            });

        return response()->json($secretariats);
    }
}

