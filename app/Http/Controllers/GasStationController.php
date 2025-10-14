<?php

namespace App\Http\Controllers;

use App\Models\GasStation;
use App\Rules\CnpjValidation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
        // Remove formatação do CNPJ para validação
        $cnpj = $request->cnpj ? preg_replace('/[^0-9]/', '', $request->cnpj) : null;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'cnpj' => [
                'nullable',
                'string',
                'max:18',
                new CnpjValidation,
                Rule::unique('gas_stations', 'cnpj')->where(function ($query) use ($cnpj) {
                    return $query->where('cnpj', $cnpj);
                })
            ],
            'status' => 'required|in:active,inactive',
        ]);

        // Formatar CNPJ antes de salvar (remove máscara)
        $data = $validated;
        if ($request->filled('cnpj')) {
            $data['cnpj'] = $cnpj;
        }

        GasStation::create($data);

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
        // Remove formatação do CNPJ para validação
        $cnpj = $request->cnpj ? preg_replace('/[^0-9]/', '', $request->cnpj) : null;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'cnpj' => [
                'nullable',
                'string',
                'max:18',
                new CnpjValidation,
                Rule::unique('gas_stations', 'cnpj')->ignore($gasStation->id)->where(function ($query) use ($cnpj) {
                    return $query->where('cnpj', $cnpj);
                })
            ],
            'status' => 'required|in:active,inactive',
        ]);

        // Formatar CNPJ antes de salvar (remove máscara)
        $data = $validated;
        if ($request->filled('cnpj')) {
            $data['cnpj'] = $cnpj;
        } else {
            $data['cnpj'] = null;
        }

        $gasStation->update($data);

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
     * API: Verificar se CNPJ já existe
     */
    public function checkCnpj(Request $request)
    {
        $request->validate([
            'cnpj' => 'required|string',
        ]);

        $cnpj = preg_replace('/[^0-9]/', '', $request->cnpj);

        $exists = GasStation::where('cnpj', $cnpj)
            ->when($request->has('exclude_id'), function ($query) use ($request) {
                $query->where('id', '!=', $request->exclude_id);
            })
            ->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'Este CNPJ já está cadastrado no sistema.' : 'CNPJ disponível.'
        ]);
    }

    /**
     * Formatar CNPJ para exibição
     */
    public static function formatCnpj($cnpj)
    {
        if (empty($cnpj)) return null;

        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

        if (strlen($cnpj) != 14) return $cnpj;

        return substr($cnpj, 0, 2) . '.' .
            substr($cnpj, 2, 3) . '.' .
            substr($cnpj, 5, 3) . '/' .
            substr($cnpj, 8, 4) . '-' .
            substr($cnpj, 12, 2);
    }
}
