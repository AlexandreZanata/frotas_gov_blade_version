<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleCategory;
use App\Models\Prefix;
use App\Models\VehicleStatus;
use App\Models\FuelType;
use App\Models\Secretariat;
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
        $fuelTypes = FuelType::all();
        $secretariats = Secretariat::all();
        return view('vehicles.create', compact('categories', 'prefixes', 'statuses', 'fuelTypes', 'secretariats'));
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
            'prefix_id' => 'required|exists:prefixes,id',
            'status_id' => 'required|exists:vehicle_statuses,id',
            'fuel_type_id' => 'required|exists:fuel_types,id',
            'secretariat_id' => 'required|exists:secretariats,id',
        ]);

        // Criar veículo associando a secretaria do usuário logado
        $vehicleData = $request->all();
        $vehicleData['secretariat_id'] = auth()->user()->secretariat_id;

        Vehicle::create($vehicleData);

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
        $secretariats = Secretariat::all();
        return view('vehicles.edit', compact('vehicle', 'categories', 'prefixes', 'statuses', 'fuelTypes', 'secretariats'));
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
            'prefix_id' => 'required|exists:prefixes,id',
            'status_id' => 'required|exists:vehicle_statuses,id',
            'fuel_type_id' => 'required|exists:fuel_types,id',
            'secretariat_id' => 'required|exists:secretariats,id',
        ]);

        // Atualizar com os dados do request (incluindo secretariat_id)
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

    /**
     * API para buscar veículos (autocomplete) com verificação de disponibilidade
     */
    public function search(Request $request)
    {
        $search = $request->input('q', '');
        $secretariatId = auth()->user()->secretariat_id;

        $vehicles = Vehicle::with(['prefix', 'secretariat'])
            ->where('secretariat_id', $secretariatId)
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('plate', 'like', "%{$search}%")
                      ->orWhereHas('prefix', function ($prefixQuery) use ($search) {
                          $prefixQuery->where('name', 'like', "%{$search}%");
                      });
                });
            })
            ->orderBy('name')
            ->limit(20)
            ->get()
            ->map(function ($vehicle) {
                // Verifica se o veículo está em uso
                $activeRun = $vehicle->runs()
                    ->where('status', 'in_progress')
                    ->first();

                return [
                    'id' => $vehicle->id,
                    'name' => $vehicle->name,
                    'plate' => $vehicle->plate,
                    'prefix' => $vehicle->prefix->name ?? 'N/A',
                    'secretariat' => $vehicle->secretariat->name ?? 'N/A',
                    'full_name' => ($vehicle->prefix->name ?? '') . ' - ' . $vehicle->name,
                    'available' => !$activeRun,
                ];
            });

        return response()->json($vehicles);
    }
}
