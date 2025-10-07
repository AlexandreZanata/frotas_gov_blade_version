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
    public function index(Request $request)
    {
        $search = $request->input('search');

        $vehicles = Vehicle::with(['category', 'prefix', 'status', 'fuelType'])
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('plate', 'like', "%{$search}%")
                      ->orWhere('brand', 'like', "%{$search}%")
                      ->orWhereHas('category', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('vehicles.index', compact('vehicles', 'search'));
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

    public function destroy(Request $request, Vehicle $vehicle)
    {
        // Gerar backup se solicitado
        if ($request->has('create_backup') && $request->input('create_backup')) {
            try {
                $backupService = new \App\Services\BackupPdfService();
                $backupService->generateVehicleBackup($vehicle);
            } catch (\Exception $e) {
                return redirect()->back()
                    ->with('error', 'Erro ao gerar backup: ' . $e->getMessage());
            }
        }

        $vehicle->delete();

        return redirect()->route('vehicles.index')
            ->with('success', 'Veículo excluído com sucesso.' . ($request->has('create_backup') ? ' Backup gerado com sucesso.' : ''));
    }
}
