<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\Tire;
use App\Services\TireService;
use Illuminate\Http\Request;

class TireController extends Controller
{
    protected $tireService;

    public function __construct(TireService $tireService)
    {
        $this->tireService = $tireService;
    }

    public function index(Request $request)
    {
        $query = Tire::with('inventoryItem', 'vehicle');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('serial_number', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%");
            });
        }

        $tires = $query->latest()->paginate(10);
        return view('tires.index', compact('tires'));
    }

    public function create()
    {
        // Pega apenas os "tipos" de pneu do inventário
        $inventoryItems = InventoryItem::whereHas('category', function ($q) {
            $q->where('name', 'Pneus');
        })->pluck('name', 'id');

        return view('tires.create', compact('inventoryItems'));
    }

    public function store(Request $request)
    {
        // Adicionar validação aqui
        $validated = $request->validate([
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'serial_number' => 'required|string|max:255|unique:tires,serial_number',
            'purchase_date' => 'required|date',
            // ... outras regras
        ]);

        $item = InventoryItem::find($validated['inventory_item_id']);

        $tireData = array_merge($validated, [
            'brand' => $item->getTireBrand(), // Lógica a ser criada no model InventoryItem
            'model' => $item->getTireModel(), // Lógica a ser criada no model InventoryItem
            'lifespan_km' => 60000, // Exemplo, pode vir do formulário
        ]);

        $tire = Tire::create($tireData);
        $this->tireService->registerNewTire($tire, $validated);

        return redirect()->route('tires.index')->with('success', 'Pneu cadastrado com sucesso.');
    }

    // ... métodos show, edit, update, destroy ...
}
