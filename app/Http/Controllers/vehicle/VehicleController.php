<?php

namespace App\Http\Controllers\vehicle;

use App\Http\Controllers\Controller;
use App\Models\fuel\FuelType;
use App\Models\logbook\LogbookPermission;
use App\Models\user\Secretariat;
use App\Models\Vehicle\Prefix;
use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\VehicleBrand;
use App\Models\Vehicle\VehicleCategory;
use App\Models\Vehicle\VehicleHeritage;
use App\Models\Vehicle\VehicleStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Carrega os novos relacionamentos 'brand' e 'heritage'
        $vehicles = Vehicle::with(['category', 'prefix', 'status', 'fuelType', 'brand', 'heritage'])
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('plate', 'like', "%{$search}%")
                    // Busca no relacionamento de marcas
                    ->orWhereHas('brand', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('category', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('vehicles.index', compact('vehicles', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Carrega todos os dados necessários para os dropdowns
        $categories = VehicleCategory::orderBy('name')->get();
        $prefixes = Prefix::orderBy('name')->get();
        $statuses = VehicleStatus::orderBy('name')->get();
        $fuelTypes = FuelType::orderBy('name')->get();
        $secretariats = Secretariat::orderBy('name')->get();
        $brands = VehicleBrand::orderBy('name')->get();
        $heritages = VehicleHeritage::orderBy('name')->get();

        return view('vehicles.create', compact('categories', 'prefixes', 'statuses', 'fuelTypes', 'secretariats', 'brands', 'heritages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validações ajustadas para os novos campos
        $request->validate([
            'name' => 'required|string|max:255',
            'brand_id' => 'required|exists:vehicle_brands,id',
            'heritage_id' => 'required|exists:vehicle_heritages,id',
            'model_year' => 'required|string|max:255',
            'plate' => 'required|string|max:255|unique:vehicles,plate',
            'fuel_tank_capacity' => 'required|integer',
            'category_id' => 'required|exists:vehicle_categories,id',
            'prefix_id' => 'required|exists:prefixes,id',
            'status_id' => 'required|exists:vehicle_statuses,id',
            'fuel_type_id' => 'required|exists:fuel_types,id',
            'secretariat_id' => 'required|exists:secretariats,id',
        ]);

        Vehicle::create($request->all());

        return redirect()->route('vehicles.index')
            ->with('success', 'Veículo criado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Vehicle $vehicle)
    {
        // Carrega os novos relacionamentos para a view de detalhes
        $vehicle->load(['category', 'prefix', 'status', 'fuelType', 'brand', 'heritage', 'secretariat']);
        return view('vehicles.show', compact('vehicle'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vehicle $vehicle)
    {
        $categories = VehicleCategory::orderBy('name')->get();
        $prefixes = Prefix::orderBy('name')->get();
        $statuses = VehicleStatus::orderBy('name')->get();
        $fuelTypes = FuelType::orderBy('name')->get();
        $secretariats = Secretariat::orderBy('name')->get();
        $brands = VehicleBrand::orderBy('name')->get();
        $heritages = VehicleHeritage::orderBy('name')->get();

        return view('vehicles.edit', compact('vehicle', 'categories', 'prefixes', 'statuses', 'fuelTypes', 'secretariats', 'brands', 'heritages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'brand_id' => 'required|exists:vehicle_brands,id',
            'heritage_id' => 'required|exists:vehicle_heritages,id',
            'model_year' => 'required|string|max:255',
            'plate' => 'required|string|max:255|unique:vehicles,plate,' . $vehicle->id,
            'fuel_tank_capacity' => 'required|integer',
            'category_id' => 'required|exists:vehicle_categories,id',
            'prefix_id' => 'required|exists:prefixes,id',
            'status_id' => 'required|exists:vehicle_statuses,id',
            'fuel_type_id' => 'required|exists:fuel_types,id',
            'secretariat_id' => 'required|exists:secretariats,id',
        ]);

        $vehicle->update($request->all());

        return redirect()->route('vehicles.index')
            ->with('success', 'Veículo atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Vehicle $vehicle)
    {
        // Gerar backup se solicitado (lógica mantida)
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
     * API para buscar veículos (autocomplete) com verificação de disponibilidade e privilégios
     */
    public function search(Request $request)
    {
        $search = $request->input('q', '');
        $user = auth()->user();

        // Obter IDs dos veículos que o usuário tem permissão para acessar
        $accessibleVehicleIds = LogbookPermission::getUserAccessibleVehicleIds($user);

        // Se não há veículos acessíveis, retornar array vazio
        if (empty($accessibleVehicleIds)) {
            return response()->json([]);
        }

        $vehicles = Vehicle::with(['prefix', 'secretariat'])
            ->whereIn('id', $accessibleVehicleIds)
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

    /**
     * API de busca simplificada.
     */
    public function apiSearch(Request $request): JsonResponse
    {
        try {
            \Log::info('VehicleController apiSearch method called', ['request' => $request->all()]);

            $query = Vehicle::with(['category', 'prefix'])
                ->select('id', 'prefix_id', 'name', 'category_id', 'plate')
                ->whereNotNull('prefix_id')
                ->orderBy('prefix_id');

            $vehicles = $query->get()->map(function($vehicle) {
                return [
                    'id' => $vehicle->id,
                    'name' => $vehicle->name,
                    'prefix_id' => $vehicle->prefix->name ?? '', // Pega o nome do prefixo
                    'plate' => $vehicle->plate,
                    // Garante que o prefixo apareça PRIMEIRO no display
                    'display_name' => ($vehicle->prefix->name ?? '') . ' - ' . $vehicle->name
                ];
            });

            \Log::info('Vehicles found for API: ' . $vehicles->count());

            return response()->json($vehicles);
        } catch (\Exception $e) {
            \Log::error('Erro ao carregar veículos para API: ' . $e->getMessage());
            return response()->json([
                'error' => 'Erro ao carregar veículos',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
