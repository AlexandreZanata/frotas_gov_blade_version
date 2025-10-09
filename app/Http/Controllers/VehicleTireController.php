<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleTireController extends Controller
{
    public function show(Vehicle $vehicle)
    {
        // Carrega o layout (simplificado, idealmente viria de uma relação no model Vehicle)
        $layout = \App\Models\VehicleTireLayout::first(); // Lógica para pegar o layout correto

        $vehicle->load('tires');

        return view('vehicles.tires.show', compact('vehicle', 'layout'));
    }

    // Adicionar aqui os métodos para rotate, moveToStock, replace, etc.
    // que chamarão o TireService
}
