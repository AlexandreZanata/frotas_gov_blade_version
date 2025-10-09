<?php

namespace App\Http\Controllers;

use App\Models\OilChange;
use App\Models\Vehicle;
use App\Models\InventoryItem;
use App\Models\OilChangeSetting;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OilChangeController extends Controller
{
    public function index(Request $request)
    {
        // Buscar todos os veículos com suas últimas trocas de óleo
        $query = Vehicle::with(['category', 'oilChanges' => function($q) {
            $q->latest('change_date')->limit(1);
        }]);

        // Filtro por status
        $statusFilter = $request->get('status');

        $vehicles = $query->get()->map(function($vehicle) {
            $lastOilChange = $vehicle->oilChanges->first();

            // Se não houver troca de óleo, considerar como vencido
            if (!$lastOilChange) {
                $vehicle->oil_status = 'sem_registro';
                $vehicle->km_progress = 0;
                $vehicle->date_progress = 0;
                return $vehicle;
            }

            // Calcular o progresso (assumindo KM atual = último KM registrado + 1000 para exemplo)
            // Em produção, você deve ter uma forma de rastrear o KM atual do veículo
            $currentKm = $lastOilChange->km_at_change + 1000; // Exemplo

            $vehicle->oil_status = $lastOilChange->getStatus($currentKm);
            $vehicle->km_progress = $lastOilChange->getKmProgressPercentage($currentKm);
            $vehicle->date_progress = $lastOilChange->getDateProgressPercentage();
            $vehicle->last_oil_change = $lastOilChange;
            $vehicle->current_km = $currentKm;

            return $vehicle;
        });

        // Aplicar filtro de status
        if ($statusFilter) {
            $vehicles = $vehicles->filter(function($vehicle) use ($statusFilter) {
                return $vehicle->oil_status === $statusFilter;
            });
        }

        // Estatísticas
        $stats = [
            'total' => $vehicles->count(),
            'em_dia' => $vehicles->where('oil_status', 'em_dia')->count(),
            'atencao' => $vehicles->where('oil_status', 'atencao')->count(),
            'critico' => $vehicles->where('oil_status', 'critico')->count(),
            'vencido' => $vehicles->where('oil_status', 'vencido')->count(),
            'sem_registro' => $vehicles->where('oil_status', 'sem_registro')->count(),
        ];

        // Verificar estoque baixo de óleo
        $lowStockOils = InventoryItem::whereHas('category', function($q) {
            $q->where('name', 'LIKE', '%óleo%')->orWhere('name', 'LIKE', '%oil%');
        })->whereRaw('quantity_on_hand <= reorder_level')->get();

        return view('oil-changes.index', compact('vehicles', 'stats', 'lowStockOils'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'km_at_change' => 'required|integer|min:0',
            'change_date' => 'required|date',
            'inventory_item_id' => 'nullable|exists:inventory_items,id',
            'liters_used' => 'nullable|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'next_change_km' => 'required|integer|min:0',
            'next_change_date' => 'required|date|after:change_date',
            'notes' => 'nullable|string',
            'service_provider' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $validated['user_id'] = auth()->id();

            // Criar registro de troca de óleo
            $oilChange = OilChange::create($validated);

            // Se um item de estoque foi usado, dar baixa
            if ($validated['inventory_item_id'] && $validated['liters_used']) {
                $inventoryItem = InventoryItem::find($validated['inventory_item_id']);

                // Criar movimento de estoque
                InventoryMovement::create([
                    'inventory_item_id' => $validated['inventory_item_id'],
                    'type' => 'out',
                    'quantity' => $validated['liters_used'],
                    'reference_type' => 'App\Models\OilChange',
                    'reference_id' => $oilChange->id,
                    'user_id' => auth()->id(),
                    'notes' => 'Troca de óleo - Veículo: ' . $oilChange->vehicle->name,
                ]);

                // Atualizar quantidade em estoque
                $inventoryItem->decrement('quantity_on_hand', $validated['liters_used']);
            }

            DB::commit();

            return redirect()->route('oil-changes.index')
                ->with('success', 'Troca de óleo registrada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao registrar troca de óleo: ' . $e->getMessage());
        }
    }

    public function history($vehicleId)
    {
        $vehicle = Vehicle::with(['oilChanges' => function($q) {
            $q->latest('change_date');
        }, 'oilChanges.user', 'oilChanges.inventoryItem'])->findOrFail($vehicleId);

        return view('oil-changes.history', compact('vehicle'));
    }

    public function getVehicleData($vehicleId)
    {
        $vehicle = Vehicle::with(['category', 'oilChanges' => function($q) {
            $q->latest('change_date')->limit(1);
        }])->findOrFail($vehicleId);

        $lastOilChange = $vehicle->oilChanges->first();

        // Buscar configuração padrão para a categoria do veículo
        $setting = OilChangeSetting::where('vehicle_category_id', $vehicle->category_id)->first();

        $data = [
            'vehicle' => $vehicle,
            'last_oil_change' => $lastOilChange,
            'suggested_km_interval' => $setting->km_interval ?? 10000,
            'suggested_days_interval' => $setting->days_interval ?? 180,
            'suggested_liters' => $setting->default_liters ?? null,
        ];

        return response()->json($data);
    }

    public function settings()
    {
        $settings = OilChangeSetting::with('vehicleCategory')->get();
        return view('oil-changes.settings', compact('settings'));
    }

    public function updateSettings(Request $request, $id)
    {
        $validated = $request->validate([
            'km_interval' => 'required|integer|min:1000',
            'days_interval' => 'required|integer|min:30',
            'default_liters' => 'nullable|numeric|min:0',
        ]);

        $setting = OilChangeSetting::findOrFail($id);
        $setting->update($validated);

        return redirect()->route('oil-changes.settings')
            ->with('success', 'Configurações atualizadas com sucesso!');
    }
}

