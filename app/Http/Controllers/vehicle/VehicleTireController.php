<?php

namespace App\Http\Controllers\vehicle;

use App\Http\Controllers\Controller;
use App\Models\Vehicle\Vehicle;

class VehicleTireController extends Controller
{
    public function show(Vehicle $vehicle)
    {
        // Carrega o layout (simplificado, idealmente viria de uma relação no model Vehicle)
        $layout = \App\Models\Vehicle\VehicleTireLayout::first(); // Lógica para pegar o layout correto

        $vehicle->load('tires');

        return view('vehicles.tires.show', compact('vehicle', 'layout'));
    }

    // Adicionar aqui os métodos para rotate, moveToStock, replace, etc.
    // que chamarão o TireService
}
