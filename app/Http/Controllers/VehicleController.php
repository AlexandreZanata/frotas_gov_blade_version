<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleCategory;
use App\Models\Prefix;
use App\Models\VehicleStatus;
use App\Models\FuelType; // adicionado
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::with(['category', 'prefix', 'status', 'fuelType'])->latest()->paginate(10); // incluir fuelType
        return view('vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        $categories = VehicleCategory::all();
        $prefixes = Prefix::all();
        $statuses = VehicleStatus::all();
        $fuelTypes = FuelType::all(); // lista de tipos combustivel
        return view('vehicles.create', compact('categories', 'prefixes', 'statuses', 'fuelTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'model_year' => 'required|string|max:255',
            'plate' => 'required|string|max:255|unique:vehicles,plate',
            'fuel_tank_capacity' => 'required|integer',
            'category_id' => 'required|exists:vehicle_categories,id',
            'prefix_id' => 'nullable|exists:prefixes,id',
            'status_id' => 'required|exists:vehicle_statuses,id',
            'fuel_type_id' => 'required|exists:fuel_types,id', // nova validação
        ]);

        Vehicle::create($request->all());

        return redirect()->route('vehicles.index')
            ->with('success', 'Veículo criado com sucesso.');
    }

    public function show(Vehicle $vehicle)
    {
        $vehicle->load(['category','prefix','status','fuelType']);
        return view('vehicles.show', compact('vehicle'));
    }

    public function edit(Vehicle $vehicle)
    {
        $categories = VehicleCategory::all();
        $prefixes = Prefix::all();
        $statuses = VehicleStatus::all();
        $fuelTypes = FuelType::all();
        return view('vehicles.edit', compact('vehicle', 'categories', 'prefixes', 'statuses', 'fuelTypes'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'model_year' => 'required|string|max:255',
            'plate' => 'required|string|max:255|unique:vehicles,plate,' . $vehicle->id,
            'fuel_tank_capacity' => 'required|integer',
            'category_id' => 'required|exists:vehicle_categories,id',
            'prefix_id' => 'nullable|exists:prefixes,id',
            'status_id' => 'required|exists:vehicle_statuses,id',
            'fuel_type_id' => 'required|exists:fuel_types,id',
        ]);

        $vehicle->update($request->all());

        return redirect()->route('vehicles.index')
            ->with('success', 'Veículo atualizado com sucesso.');
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();

        return redirect()->route('vehicles.index')
            ->with('success', 'Veículo excluído com sucesso.');
    }
}
