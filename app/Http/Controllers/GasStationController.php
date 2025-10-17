<?php

namespace App\Http\Controllers;

use App\Models\fuel\GasStation;
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

        \Log::info('STORE - CNPJ Recebido:', [
            'request_cnpj' => $request->cnpj,
            'cleaned_cnpj' => $cnpj
        ]);

        // Validação MANUAL para CNPJ duplicado ANTES da validação do Laravel
        if ($cnpj) {
            $existing = GasStation::where('cnpj', $cnpj)->first();

            if ($existing) {
                \Log::warning('CNPJ duplicado detectado manualmente no store:', [
                    'existing_id' => $existing->id,
                    'existing_name' => $existing->name,
                    'cnpj' => $cnpj
                ]);

                return back()
                    ->withInput()
                    ->withErrors(['cnpj' => 'Este CNPJ já está cadastrado no sistema.']);
            }
        }

        // Validação do Laravel
        $validationRules = [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive',
        ];

        // Adicionar regra de CNPJ apenas se foi fornecido
        if ($request->filled('cnpj')) {
            $validationRules['cnpj'] = [
                'nullable',
                'string',
                'max:18',
                new CnpjValidation,
                Rule::unique('gas_stations', 'cnpj')
            ];
        } else {
            $validationRules['cnpj'] = 'nullable';
        }

        $validated = $request->validate($validationRules, [
            'cnpj.unique' => 'Este CNPJ já está cadastrado no sistema.'
        ]);

        // Formatar CNPJ antes de salvar (remove máscara)
        $data = $validated;
        if ($request->filled('cnpj')) {
            $data['cnpj'] = $cnpj;
        }

        \Log::info('Criando novo posto:', [
            'name' => $data['name'],
            'cnpj' => $data['cnpj'] ?? null
        ]);

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
     * Atualizar posto - MÉTODO COMPLETAMENTE CORRIGIDO
     */
    public function update(Request $request, GasStation $gasStation)
    {
        // Remove formatação do CNPJ para validação
        $cnpj = $request->cnpj ? preg_replace('/[^0-9]/', '', $request->cnpj) : null;

        \Log::info('UPDATE - CNPJ Recebido:', [
            'request_cnpj' => $request->cnpj,
            'cleaned_cnpj' => $cnpj,
            'current_cnpj' => $gasStation->cnpj
        ]);

        // Validação MANUAL para CNPJ duplicado ANTES da validação do Laravel
        if ($cnpj && $cnpj !== $gasStation->cnpj) {
            $existing = GasStation::where('cnpj', $cnpj)
                ->where('id', '!=', $gasStation->id)
                ->first();

            if ($existing) {
                \Log::warning('CNPJ duplicado detectado manualmente:', [
                    'existing_id' => $existing->id,
                    'existing_name' => $existing->name,
                    'cnpj' => $cnpj
                ]);

                return back()
                    ->withInput()
                    ->withErrors(['cnpj' => 'Este CNPJ já está cadastrado no sistema.']);
            }
        }

        // Validação do Laravel
        $validationRules = [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive',
        ];

        // Adicionar regra de CNPJ apenas se foi fornecido
        if ($request->filled('cnpj')) {
            $validationRules['cnpj'] = [
                'nullable',
                'string',
                'max:18',
                new CnpjValidation,
                Rule::unique('gas_stations', 'cnpj')->ignore($gasStation->id)
            ];
        } else {
            $validationRules['cnpj'] = 'nullable';
        }

        $validated = $request->validate($validationRules, [
            'cnpj.unique' => 'Este CNPJ já está cadastrado no sistema.'
        ]);

        // Formatar CNPJ antes de salvar (remove máscara)
        $data = $validated;
        if ($request->filled('cnpj')) {
            $data['cnpj'] = $cnpj;
        } else {
            $data['cnpj'] = null;
        }

        \Log::info('Atualizando posto:', [
            'id' => $gasStation->id,
            'cnpj_antigo' => $gasStation->cnpj,
            'cnpj_novo' => $data['cnpj']
        ]);

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
     * API: Verificar se CNPJ já existe - MÉTODO SIMPLIFICADO
     */
    public function checkCnpj(Request $request)
    {
        // Log simples para debug
        \Log::info('API Check CNPJ chamada');

        try {
            $cnpj = $request->cnpj ? preg_replace('/[^0-9]/', '', $request->cnpj) : null;

            if (!$cnpj || strlen($cnpj) !== 14) {
                return response()->json([
                    'exists' => false,
                    'message' => 'CNPJ deve conter 14 dígitos.'
                ]);
            }

            $query = GasStation::where('cnpj', $cnpj);

            if ($request->exclude_id) {
                $query->where('id', '!=', $request->exclude_id);
            }

            $exists = $query->exists();

            return response()->json([
                'exists' => $exists,
                'message' => $exists ? 'Este CNPJ já está cadastrado no sistema.' : 'CNPJ disponível.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro no checkCnpj:', ['error' => $e->getMessage()]);

            return response()->json([
                'error' => 'Erro interno do servidor'
            ], 500);
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
