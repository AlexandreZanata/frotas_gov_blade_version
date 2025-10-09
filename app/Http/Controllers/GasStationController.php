<?php

namespace App\Http\Controllers;

use App\Models\GasStation;
use Illuminate\Http\Request;

class GasStationController extends Controller
{
    /**
     * Listar todos os postos
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');

        $gasStations = GasStation::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('cnpj', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(15);

        return view('gas-stations.index', compact('gasStations', 'search'));
    }

    /**
     * Mostrar formulário de criação
     */
    public function create()
    {
        return view('gas-stations.create');
    }

    /**
     * Salvar novo posto
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'cnpj' => 'nullable|string|max:18|unique:gas_stations,cnpj',
            'status' => 'required|in:active,inactive',
        ]);

        GasStation::create($request->all());

        return redirect()->route('gas-stations.index')
            ->with('success', 'Posto cadastrado com sucesso!');
    }

    /**
     * Exibir posto específico
     */
    public function show(GasStation $gasStation)
    {
        // Buscar cotações relacionadas
        $recentQuotations = $gasStation->quotationPrices()
            ->with(['quotation', 'fuelType'])
            ->latest()
            ->take(10)
            ->get();

        return view('gas-stations.show', compact('gasStation', 'recentQuotations'));
    }

    /**
     * Mostrar formulário de edição
     */
    public function edit(GasStation $gasStation)
    {
        return view('gas-stations.edit', compact('gasStation'));
    }

    /**
     * Atualizar posto
     */
    public function update(Request $request, GasStation $gasStation)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'cnpj' => 'nullable|string|max:18|unique:gas_stations,cnpj,' . $gasStation->id,
            'status' => 'required|in:active,inactive',
        ]);

        $gasStation->update($request->all());

        return redirect()->route('gas-stations.index')
            ->with('success', 'Posto atualizado com sucesso!');
    }

    /**
     * Excluir posto
     */
    public function destroy(GasStation $gasStation)
    {
        try {
            $gasStation->delete();

            return redirect()->route('gas-stations.index')
                ->with('success', 'Posto excluído com sucesso!');

        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao excluir posto: ' . $e->getMessage());
        }
    }

    /**
     * API: Buscar postos
     */
    public function search(Request $request)
    {
        $search = $request->get('q', '');

        $gasStations = GasStation::where('status', 'active')
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'address', 'cnpj']);

        return response()->json($gasStations);
    }
}

