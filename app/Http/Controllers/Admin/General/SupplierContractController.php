<?php

namespace App\Http\Controllers\Admin\General;

use App\Http\Controllers\Controller;
use App\Models\Balance\BalanceGasStationSupplier;
use App\Models\GasStation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SupplierContractController extends Controller
{
    /**
     * Exibe uma lista paginada de contratos de fornecedores.
     * Permite a busca por nome do posto, CNPJ ou número do contrato.
     */
    public function index(Request $request)
    {
        $query = BalanceGasStationSupplier::with('gasStation');

        // Filtro de busca
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('contract_number', 'like', "%{$searchTerm}%")
                    ->orWhere('supplier_document', 'like', "%{$searchTerm}%")
                    ->orWhereHas('gasStation', function ($subq) use ($searchTerm) {
                        $subq->where('name', 'like', "%{$searchTerm}%");
                    });
            });
        }

        $contracts = $query->latest()->paginate(15)->withQueryString();

        return view('admin.general.contracts.index', compact('contracts'));
    }

    /**
     * Mostra o formulário para criar um novo contrato.
     * Apenas postos de gasolina que ainda não possuem um contrato são listados.
     */
    public function create()
    {
        // Pega apenas os IDs dos postos que já têm contrato
        $existingGasStationIds = BalanceGasStationSupplier::pluck('gas_station_id');

        // Carrega os postos que não estão na lista de existentes
        $gasStations = GasStation::whereNotIn('id', $existingGasStationIds)->orderBy('name')->get();

        return view('admin.general.contracts.create', compact('gasStations'));
    }

    /**
     * Valida e armazena um novo contrato, incluindo o upload de documentos.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'gas_station_id' => 'required|uuid|exists:gas_stations,id|unique:balance_gas_station_suppliers,gas_station_id',
            'procurement_type' => 'required|in:bidding,direct_contract',
            'bidding_modality' => 'nullable|string|max:255',
            'process_number' => 'nullable|string|max:255|unique:balance_gas_station_suppliers,process_number',
            'contract_number' => 'nullable|string|max:255|unique:balance_gas_station_suppliers,contract_number',
            'contract_start_date' => 'required|date',
            'contract_end_date' => 'required|date|after_or_equal:contract_start_date',
            'supplier_document' => 'required|string|max:255|unique:balance_gas_station_suppliers,supplier_document',
            'total_contract_value' => 'nullable|numeric|min:0',
            'legal_notes' => 'nullable|string',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,png|max:5120', // Aceita múltiplos arquivos de até 5MB
        ]);

        // Gera a chave criptografada única
        $keySource = $validatedData['contract_number'] ?? $validatedData['process_number'];
        $validatedData['encrypted_contract_key'] = Hash::make($keySource . Str::random(10));

        // Lida com o upload de arquivos
        if ($request->hasFile('documents')) {
            $paths = [];
            foreach ($request->file('documents') as $file) {
                // Armazena o arquivo em 'storage/app/private/contracts' e obtém o caminho
                $paths[] = $file->store('private/contracts');
            }
            $validatedData['document_paths'] = $paths;
        }

        BalanceGasStationSupplier::create($validatedData);

        return redirect()->route('contracts.index')->with('success', 'Contrato de fornecedor criado com sucesso!');
    }

    /**
     * Exibe os detalhes de um contrato específico.
     */
    public function show(BalanceGasStationSupplier $contract)
    {
        // Opcional: Carregar relacionamentos se necessário na view de detalhes
        $contract->load('gasStation', 'commitments');

        return view('admin.general.contracts.show', compact('contract'));
    }

    /**
     * Mostra o formulário para editar um contrato existente.
     */
    public function edit(BalanceGasStationSupplier $contract)
    {
        return view('admin.general.contracts.edit', compact('contract'));
    }

    /**
     * Valida e atualiza um contrato existente no banco de dados.
     */
    public function update(Request $request, BalanceGasStationSupplier $contract)
    {
        $validatedData = $request->validate([
            'procurement_type' => 'required|in:bidding,direct_contract',
            'bidding_modality' => 'nullable|string|max:255',
            'process_number' => ['nullable', 'string', 'max:255', Rule::unique('balance_gas_station_suppliers')->ignore($contract->id)],
            'contract_number' => ['nullable', 'string', 'max:255', Rule::unique('balance_gas_station_suppliers')->ignore($contract->id)],
            'contract_start_date' => 'required|date',
            'contract_end_date' => 'required|date|after_or_equal:contract_start_date',
            'supplier_document' => ['required', 'string', 'max:255', Rule::unique('balance_gas_station_suppliers')->ignore($contract->id)],
            'total_contract_value' => 'nullable|numeric|min:0',
            'legal_notes' => 'nullable|string',
        ]);

        $contract->update($validatedData);

        return redirect()->route('contracts.show', $contract->id)->with('success', 'Contrato atualizado com sucesso!');
    }

    /**
     * Remove um contrato do banco de dados.
     * A exclusão é impedida se o contrato já tiver empenhos associados.
     */
    public function destroy(BalanceGasStationSupplier $contract)
    {
        // Verifica se existem empenhos vinculados a este contrato
        if ($contract->commitments()->exists()) {
            return redirect()->route('contracts.index')
                ->with('error', 'Não é possível excluir um contrato que possui empenhos vinculados. Considere arquivá-lo.');
        }

        // Deleta os arquivos associados do storage para não deixar lixo
        if (is_array($contract->document_paths)) {
            foreach ($contract->document_paths as $path) {
                Storage::delete($path);
            }
        }

        $contract->delete();

        return redirect()->route('contracts.index')->with('success', 'Contrato excluído com sucesso!');
    }
}
