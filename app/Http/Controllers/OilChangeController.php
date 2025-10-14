<?php

namespace App\Http\Controllers;

use App\Models\OilChange;
use App\Models\Vehicle;
use App\Models\InventoryItem;
use App\Models\OilChangeSetting;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class OilChangeController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        // 1. Obter filtros da requisição
        $statusFilter = $request->get('status');
        $search = $request->get('search');

        // 2. Iniciar a consulta base com os relacionamentos necessários
        $query = Vehicle::with(['category', 'oilChanges' => function($q) {
            $q->latest('change_date')->limit(1);
        }]);

        // 3. Aplicar filtro de busca na consulta do banco de dados (mais eficiente)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('plate', 'like', "%{$search}%");
            });
        }

        // 4. Executar a consulta UMA VEZ para obter todos os veículos relevantes
        $allQueriedVehicles = $query->get();

        // 5. Processar a coleção UMA VEZ para calcular status, progresso e estatísticas
        $stats = [
            'total' => 0, 'em_dia' => 0, 'atencao' => 0,
            'critico' => 0, 'vencido' => 0, 'sem_registro' => 0
        ];

        $processedVehicles = $allQueriedVehicles->map(function($vehicle) use (&$stats) {
            $lastOilChange = $vehicle->oilChanges->first();
            $vehicle->last_oil_change = $lastOilChange; // Anexar para fácil acesso na view

            if (!$lastOilChange) {
                $vehicle->oil_status = 'sem_registro';
                $vehicle->km_progress = 0;
                $vehicle->date_progress = 0;
                $vehicle->current_km = $vehicle->current_km ?? 0;
            } else {
                // Lógica de estimativa de KM do seu controller original
                $kmInterval = $lastOilChange->next_change_km - $lastOilChange->km_at_change;
                $daysInterval = $lastOilChange->change_date->diffInDays($lastOilChange->next_change_date);
                $daysPassed = $lastOilChange->change_date->diffInDays(now());
                $estimatedKmPerDay = $daysInterval > 0 ? $kmInterval / $daysInterval : 0;
                $currentKm = $lastOilChange->km_at_change + ($estimatedKmPerDay * $daysPassed);

                $vehicle->oil_status = $lastOilChange->getStatus((int)$currentKm);
                $vehicle->km_progress = $lastOilChange->getKmProgressPercentage((int)$currentKm);
                $vehicle->date_progress = $lastOilChange->getDateProgressPercentage();
                $vehicle->current_km = (int)$currentKm;
            }

            // Adicionar cores para as barras de progresso
            $colors = [
                'vencido' => 'bg-red-500',
                'critico' => 'bg-orange-500',
                'atencao' => 'bg-yellow-500',
                'em_dia' => 'bg-green-500',
                'sem_registro' => 'bg-gray-400'
            ];
            $vehicle->km_progress_color = $colors[$vehicle->oil_status];
            $vehicle->date_progress_color = $colors[$vehicle->oil_status];

            // Atualizar estatísticas
            $stats[$vehicle->oil_status]++;
            $stats['total']++;

            return $vehicle;
        });

        // 6. Filtrar a coleção processada se um status foi selecionado
        $finalVehicles = $statusFilter
            ? $processedVehicles->filter(fn($v) => $v->oil_status === $statusFilter)
            : $processedVehicles;

        // 7. Paginar manualmente a coleção final
        $perPage = 15;
        $currentPage = Paginator::resolveCurrentPage('page');
        $currentPageItems = $finalVehicles->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $vehicles = new LengthAwarePaginator($currentPageItems, $finalVehicles->count(), $perPage, $currentPage, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        // 8. Buscar dados adicionais para a view (alertas e formulário do modal)
        $lowStockOils = InventoryItem::whereHas('category', function($q) {
            $q->where('name', 'LIKE', '%óleo%')->orWhere('name', 'LIKE', '%lubrificante%');
        })->whereRaw('quantity_on_hand <= reorder_level')->get();

        $oilItems = InventoryItem::whereHas('category', function($q) {
            $q->where('name', 'LIKE', '%óleo%')->orWhere('name', 'LIKE', '%lubrificante%');
        })->orderBy('name')->get();

        // É importante buscar uma lista limpa para o dropdown do modal
        $allVehiclesForModal = Vehicle::orderBy('name')->get(['id', 'name', 'plate']);

        return view('oil-changes.index', [
            'vehicles' => $vehicles,
            'stats' => $stats,
            'lowStockOils' => $lowStockOils,
            'oilItems' => $oilItems,
            'allVehicles' => $allVehiclesForModal, // Usar a variável correta na view
        ]);
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

            $oilChange = OilChange::create($validated);

            if ($request->filled('inventory_item_id') && $request->filled('liters_used')) {
                $inventoryItem = InventoryItem::find($validated['inventory_item_id']);

                InventoryMovement::create([
                    'inventory_item_id' => $validated['inventory_item_id'],
                    'type' => 'out',
                    'quantity' => $validated['liters_used'],
                    'reference_type' => 'App\Models\OilChange',
                    'reference_id' => $oilChange->id,
                    'user_id' => auth()->id(),
                    'notes' => 'Troca de óleo - Veículo: ' . $oilChange->vehicle->name,
                ]);

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

        $setting = OilChangeSetting::where('vehicle_category_id', $vehicle->category_id)->first();

        // Otimização: Adicionar o KM atual do veículo na resposta da API
        $data = [
            'current_km' => $vehicle->current_km ?? 0,
            'last_oil_change' => $lastOilChange,
            'suggested_km_interval' => $setting->km_interval ?? 10000,
            'suggested_days_interval' => $setting->days_interval ?? 180,
            'suggested_liters' => $setting->default_liters ?? null,
        ];

        return response()->json($data);
    }

    public function settings()
    {
        // CORREÇÃO: Verificando a permissão diretamente com o método do User model.
        if (!auth()->user()->isGeneralManager()) {
            abort(403, 'Acesso Negado. Apenas o Gestor Geral pode acessar esta página.');
        }

        $categories = \App\Models\VehicleCategory::withCount('vehicles')->orderBy('name')->get();
        $settings = OilChangeSetting::all()->keyBy('vehicle_category_id');

        return view('oil-changes.settings', compact('categories', 'settings'));
    }

    public function storeSettings(Request $request)
    {
        if (!auth()->user()->isGeneralManager()) {
            abort(403);
        }

        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*.vehicle_category_id' => 'required|exists:vehicle_categories,id',
            'settings.*.km_interval' => 'required|integer|min:1000',
            'settings.*.days_interval' => 'required|integer|min:30',
            'settings.*.default_liters' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['settings'] as $settingData) {
                OilChangeSetting::updateOrCreate(
                    ['vehicle_category_id' => $settingData['vehicle_category_id']],
                    [
                        'km_interval' => $settingData['km_interval'],
                        'days_interval' => $settingData['days_interval'],
                        'default_liters' => $settingData['default_liters'] ?? null,
                    ]
                );
            }

            DB::commit();

            return redirect()->route('oil-changes.settings')
                ->with('success', 'Configurações salvas com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao salvar configurações: ' . $e->getMessage());
        }
    }

    public function updateSettings(Request $request, $id)
    {
        $this->authorize('isGeneralManager');

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
