<?php

namespace App\Http\Controllers;

use App\Models\Tire;
use App\Models\Vehicle;
use App\Models\InventoryItem;
use App\Models\VehicleTireLayout;
use App\Services\TireService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TireController extends Controller
{
    protected $tireService;

    public function __construct(TireService $tireService)
    {
        $this->tireService = $tireService;
    }

    /**
     * Dashboard principal do módulo de pneus
     */
    public function index()
    {
        $stats = $this->tireService->getDashboardStats();
        $criticalTires = Tire::where('condition', 'Crítico')->with('vehicle')->get();
        $attentionTires = Tire::where('condition', 'Atenção')->with('vehicle')->get();

        return view('tires.index', compact('stats', 'criticalTires', 'attentionTires'));
    }

    /**
     * Lista de veículos para gestão de pneus
     */
    public function vehicles()
    {
        $vehicles = Vehicle::with(['tires', 'category'])->get();

        return view('tires.vehicles', compact('vehicles'));
    }

    /**
     * Detalhes do veículo com diagrama interativo
     */
    public function showVehicle($id)
    {
        $vehicle = Vehicle::with(['tires', 'category'])->findOrFail($id);
        $layout = $this->tireService->getVehicleLayout($vehicle);
        $availableTires = Tire::where('status', 'Em Estoque')->get();

        return view('tires.vehicle-detail', compact('vehicle', 'layout', 'availableTires'));
    }

    /**
     * Estoque de pneus
     */
    public function stock()
    {
        $tires = Tire::where('status', 'Em Estoque')
            ->with('inventoryItem')
            ->paginate(20);

        $inventoryItems = InventoryItem::whereHas('category', function($query) {
            $query->where('name', 'Pneus');
        })->get();

        return view('tires.stock', compact('tires', 'inventoryItems'));
    }

    /**
     * Formulário de cadastro de novo pneu
     */
    public function create()
    {
        $inventoryItems = InventoryItem::whereHas('category', function($query) {
            $query->where('name', 'Pneus');
        })->get();

        // Buscar todos os pneus para exibir na lista
        $tires = Tire::with('inventoryItem')->orderBy('created_at', 'desc')->get();

        return view('tires.create', compact('inventoryItems', 'tires'));
    }

    /**
     * Salvar novo pneu
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'serial_number' => 'required|string|unique:tires,serial_number',
            'dot_number' => 'nullable|string|max:255',
            'purchase_date' => 'required|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'lifespan_km' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $tire = Tire::create(array_merge($validated, [
                'status' => 'Em Estoque',
                'condition' => 'Novo',
                'current_km' => 0,
            ]));

            // Registrar evento
            $this->tireService->registerEvent($tire, 'Cadastro', 'Pneu cadastrado no sistema');

            DB::commit();
            return redirect()->route('tires.stock')
                ->with('success', 'Pneu cadastrado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erro ao cadastrar pneu: ' . $e->getMessage()]);
        }
    }

    /**
     * Executar rodízio de pneus no mesmo veículo
     */
    public function rotate(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'tire_1_id' => 'required|exists:tires,id',
            'tire_2_id' => 'required|exists:tires,id',
            'position_1' => 'required|integer',
            'position_2' => 'required|integer',
            'km_at_event' => 'required|integer|min:0',
        ]);

        try {
            $result = $this->tireService->rotateTires(
                $validated['vehicle_id'],
                $validated['tire_1_id'],
                $validated['tire_2_id'],
                $validated['position_1'],
                $validated['position_2'],
                $validated['km_at_event']
            );

            return response()->json([
                'success' => true,
                'message' => 'Rodízio realizado com sucesso!',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Trocar pneu (instalar novo ou do estoque)
     */
    public function replace(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'old_tire_id' => 'nullable|exists:tires,id',
            'new_tire_id' => 'required|exists:tires,id',
            'position' => 'required|integer',
            'km_at_event' => 'required|integer|min:0',
            'reason' => 'required|string',
        ]);

        try {
            $result = $this->tireService->replaceTire(
                $validated['vehicle_id'],
                $validated['old_tire_id'] ?? null,
                $validated['new_tire_id'],
                $validated['position'],
                $validated['km_at_event'],
                $validated['reason']
            );

            return response()->json([
                'success' => true,
                'message' => 'Pneu trocado com sucesso!',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Remover pneu do veículo (enviar para estoque/manutenção)
     */
    public function remove(Request $request)
    {
        $validated = $request->validate([
            'tire_id' => 'required|exists:tires,id',
            'new_status' => 'required|in:Em Estoque,Em Manutenção,Recapagem,Descartado',
            'km_at_event' => 'required|integer|min:0',
            'reason' => 'required|string',
        ]);

        try {
            $result = $this->tireService->removeTire(
                $validated['tire_id'],
                $validated['new_status'],
                $validated['km_at_event'],
                $validated['reason']
            );

            return response()->json([
                'success' => true,
                'message' => 'Pneu removido com sucesso!',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Registrar evento especial (recapagem, descarte, etc)
     */
    public function registerEvent(Request $request)
    {
        $validated = $request->validate([
            'tire_id' => 'required|exists:tires,id',
            'event_type' => 'required|in:Manutenção,Recapagem,Descarte',
            'description' => 'required|string',
            'new_status' => 'nullable|in:Em Estoque,Em Manutenção,Recapagem,Descartado',
        ]);

        try {
            $tire = Tire::findOrFail($validated['tire_id']);

            DB::beginTransaction();

            // Registrar evento
            $this->tireService->registerEvent(
                $tire,
                $validated['event_type'],
                $validated['description']
            );

            // Atualizar status se fornecido
            if (isset($validated['new_status'])) {
                $tire->update(['status' => $validated['new_status']]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Evento registrado com sucesso!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Histórico de eventos de um pneu
     */
    public function history($id)
    {
        $tire = Tire::with(['events.user', 'events.vehicle', 'inventoryItem'])->findOrFail($id);

        return view('tires.history', compact('tire'));
    }

    /**
     * Atualizar condição do pneu baseado na quilometragem
     */
    public function updateCondition(Request $request, $id)
    {
        $tire = Tire::findOrFail($id);
        $condition = $this->tireService->calculateCondition($tire);

        $tire->update(['condition' => $condition]);

        return response()->json([
            'success' => true,
            'condition' => $condition
        ]);
    }
}
