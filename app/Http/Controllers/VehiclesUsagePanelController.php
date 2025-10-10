<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\Logbook;
use Illuminate\Support\Facades\Auth;

class VehiclesUsagePanelController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Vehicle::query();

        // Filtra apenas veículos em uso (com diário de bordo em andamento)
        $query->whereHas('runs', function($q) {
            $q->whereNull('finished_at');
        });

        // Se for sector_manager, filtra pela secretaria do usuário
        if ($user->hasRole('sector_manager')) {
            $query->where('secretariat_id', $user->secretariat_id);
        }

        $vehicles = $query->with(['runs' => function($q) {
            $q->whereNull('finished_at')->latest()->limit(1);
        }, 'category', 'fuelType', 'secretariat'])
        ->paginate(15);

        // Estatísticas para cards e gráficos
        $bySecretariat = $query->clone()
            ->join('secretariats', 'vehicles.secretariat_id', '=', 'secretariats.id')
            ->selectRaw('secretariats.name as secretariat_name, count(*) as total')
            ->groupBy('secretariats.id', 'secretariats.name')
            ->get();

        $stats = [
            'bySecretariat' => $bySecretariat
        ];

        // Dados para gráfico de distribuição
        $chartData = [
            'series' => [
                [
                    'name' => 'Veículos em Uso',
                    'data' => $bySecretariat->pluck('total')->toArray()
                ]
            ],
            'categories' => $bySecretariat->pluck('secretariat_name')->toArray()
        ];

        // Gráfico de pizza para distribuição
        $pieChartData = [
            'series' => $bySecretariat->pluck('total')->toArray(),
            'labels' => $bySecretariat->pluck('secretariat_name')->toArray()
        ];

        return view('vehicles.usage-panel', compact('vehicles', 'stats', 'chartData', 'pieChartData'));
    }
}
