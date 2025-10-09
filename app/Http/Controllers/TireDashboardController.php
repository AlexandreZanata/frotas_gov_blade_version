<?php

namespace App\Http\Controllers;

use App\Models\Tire;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class TireDashboardController extends Controller
{
    public function index()
    {
        $criticalCount = Tire::where('condition', 'Crítico')->count();
        $attentionCount = Tire::where('condition', 'Atenção')->count();
        $avgLifespan = Tire::avg('current_km'); // Simplificado, pode ser melhorado
        $monitoredVehicles = Vehicle::has('tires')->count();

        $tiresNeedingAttention = Tire::whereIn('condition', ['Crítico', 'Atenção'])
            ->with('vehicle')
            ->orderBy('condition', 'desc')
            ->get();

        return view('tires.dashboard', compact(
            'criticalCount',
            'attentionCount',
            'avgLifespan',
            'monitoredVehicles',
            'tiresNeedingAttention'
        ));
    }
}
