<?php

namespace App\Http\Controllers\vehicle;

use App\Http\Controllers\Controller;
use App\Models\Vehicle\AcquisitionType;
use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\VehiclePriceOrigin;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VehiclePriceOriginController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $priceOrigins = VehiclePriceOrigin::with(['vehicle', 'acquisitionType', 'vehicle.prefix'])
            ->when($search, function ($query, $search) {
                $query->whereHas('vehicle', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('plate', 'like', "%{$search}%")
                        ->orWhereHas('prefix', function ($prefixQuery) use ($search) {
                            $prefixQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('vehicle-price-origins.index', compact('priceOrigins', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vehicles = Vehicle::whereDoesntHave('priceOrigin')
            ->with(['prefix', 'brand'])
            ->orderBy('name')
            ->get();
        $acquisitionTypes = AcquisitionType::orderBy('name')->get();

        return view('vehicle-price-origins.create', compact('vehicles', 'acquisitionTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id|unique:vehicle_price_origins,vehicle_id',
            'amount' => 'required|numeric|min:0',
            'acquisition_date' => 'required|date',
            'acquisition_type_id' => 'required|exists:acquisition_types,id',
        ]);

        // Garantir que o ID seja UUID
        $data = $request->all();
        $data['id'] = (string) Str::uuid();

        VehiclePriceOrigin::create($data);

        return redirect()->route('vehicle-price-origins.index')
            ->with('success', 'Patrimônio do veículo cadastrado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Buscar pelo UUID
        $vehiclePriceOrigin = VehiclePriceOrigin::with([
            'vehicle',
            'acquisitionType',
            'vehicle.prefix',
            'vehicle.brand',
            'vehicle.category',
            'vehicle.secretariat'
        ])->findOrFail($id);

        return view('vehicle-price-origins.show', compact('vehiclePriceOrigin'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Buscar pelo UUID
        $vehiclePriceOrigin = VehiclePriceOrigin::with(['vehicle', 'acquisitionType', 'vehicle.prefix'])
            ->findOrFail($id);
        $acquisitionTypes = AcquisitionType::orderBy('name')->get();

        return view('vehicle-price-origins.edit', compact('vehiclePriceOrigin', 'acquisitionTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Buscar pelo UUID
        $vehiclePriceOrigin = VehiclePriceOrigin::findOrFail($id);

        $request->validate([
            'amount' => 'required|numeric|min:0',
            'acquisition_date' => 'required|date',
            'acquisition_type_id' => 'required|exists:acquisition_types,id',
        ]);

        $vehiclePriceOrigin->update($request->all());

        return redirect()->route('vehicle-price-origins.index')
            ->with('success', 'Patrimônio do veículo atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     * Não permitido - apenas edição
     */
    public function destroy($id)
    {
        // Buscar pelo UUID
        $vehiclePriceOrigin = VehiclePriceOrigin::findOrFail($id);

        return redirect()->route('vehicle-price-origins.index')
            ->with('error', 'Exclusão de patrimônio não é permitida. Apenas edição.');
    }

    /**
     * API para buscar veículos disponíveis para patrimônio
     */
    public function searchAvailableVehicles(Request $request)
    {
        try {
            $search = $request->input('q', '');

            \Log::info('Buscando veículos disponíveis para patrimônio', ['search' => $search]);

            $vehicles = Vehicle::whereDoesntHave('priceOrigin')
                ->with(['prefix', 'brand'])
                ->when($search, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('vehicles.name', 'like', "%{$search}%")
                            ->orWhere('vehicles.plate', 'like', "%{$search}%")
                            ->orWhereHas('prefix', function ($prefixQuery) use ($search) {
                                $prefixQuery->where('name', 'like', "%{$search}%");
                            })
                            ->orWhereHas('brand', function ($brandQuery) use ($search) {
                                $brandQuery->where('name', 'like', "%{$search}%");
                            });
                    });
                })
                ->orderBy('vehicles.name')
                ->limit(20)
                ->get()
                ->map(function ($vehicle) {
                    return [
                        'id' => $vehicle->id,
                        'name' => $vehicle->name,
                        'plate' => $vehicle->plate,
                        'brand' => $vehicle->brand->name ?? 'N/A',
                        'model_year' => $vehicle->model_year,
                        'prefix_id' => $vehicle->prefix->name ?? 'N/A',
                        'display_name' => ($vehicle->prefix->name ?? '') . ' - ' . $vehicle->name . ' (' . $vehicle->plate . ')',
                    ];
                });

            \Log::info('Veículos encontrados', ['count' => count($vehicles)]);

            return response()->json($vehicles);

        } catch (\Exception $e) {
            \Log::error('Erro na busca de veículos disponíveis', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Erro interno do servidor',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
