<?php

namespace App\Http\Controllers\Admin\General;

use App\Http\Controllers\Controller;
use App\Models\Balance\BalanceCommitment;
use App\Models\Balance\BalanceGasStationSupplier;
use App\Models\user\Secretariat;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CommitmentController extends Controller
{
    /**
     * Exibe uma lista paginada de todos os empenhos.
     * Permite filtrar por status ou buscar pelo número do empenho.
     */
    public function index(Request $request)
    {
        $query = BalanceCommitment::with(['supplier.gasStation', 'secretariat']);

        // Filtro por status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Busca por número do empenho
        if ($request->filled('search')) {
            $query->where('commitment_number', 'like', '%' . $request->search . '%');
        }

        $commitments = $query->latest('commitment_date')->paginate(15)->withQueryString();

        return view('admin.general.commitments.index', compact('commitments'));
    }

    /**
     * Mostra o formulário para criar um novo empenho.
     * Carrega os fornecedores e secretarias para popular os selects do formulário.
     */
    public function create()
    {
        $suppliers = BalanceGasStationSupplier::with('gasStation')->get();
        $secretariats = Secretariat::orderBy('name')->get();

        return view('admin.general.commitments.create', compact('suppliers', 'secretariats'));
    }

    /**
     * Valida e armazena um novo empenho no banco de dados.
     * O saldo inicial ('balance') é definido com o mesmo valor do montante total ('total_amount').
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'commitment_number' => 'required|string|unique:balance_commitments,commitment_number|max:255',
            'year' => 'required|digits:4|integer|min:2020',
            'commitment_date' => 'required|date',
            'total_amount' => 'required|numeric|min:0.01',
            'supplier_id' => 'required|uuid|exists:balance_gas_station_suppliers,id',
            'secretariat_id' => 'required|uuid|exists:secretariats,id',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,approved,canceled',
        ]);

        // Define o saldo inicial igual ao valor total do empenho
        $validatedData['balance'] = $validatedData['total_amount'];

        BalanceCommitment::create($validatedData);

        return redirect()->route('commitments.index')->with('success', 'Empenho criado com sucesso!');
    }

    /**
     * Exibe os detalhes de um empenho específico.
     * Carrega as ordens de fornecimento relacionadas para mostrar o histórico de gastos.
     */
    public function show(BalanceCommitment $commitment)
    {
        $commitment->load(['supplier.gasStation', 'secretariat', 'supplyOrders.user']);

        return view('admin.general.commitments.show', compact('commitment'));
    }

    /**
     * Mostra o formulário para editar um empenho existente.
     */
    public function edit(BalanceCommitment $commitment)
    {
        $suppliers = BalanceGasStationSupplier::with('gasStation')->get();
        $secretariats = Secretariat::orderBy('name')->get();

        return view('admin.general.commitments.edit', compact('commitment', 'suppliers', 'secretariats'));
    }

    /**
     * Valida e atualiza um empenho no banco de dados.
     * A lógica de atualização do saldo deve ser tratada com cuidado.
     */
    public function update(Request $request, BalanceCommitment $commitment)
    {
        $validatedData = $request->validate([
            'commitment_number' => ['required', 'string', 'max:255', Rule::unique('balance_commitments')->ignore($commitment->id)],
            'year' => 'required|digits:4|integer|min:2020',
            'commitment_date' => 'required|date',
            'total_amount' => 'required|numeric|min:0.01',
            'supplier_id' => 'required|uuid|exists:balance_gas_station_suppliers,id',
            'secretariat_id' => 'required|uuid|exists:secretariats,id',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,approved,partially_used,exhausted,canceled',
        ]);

        // Recalcula o saldo se o valor total for alterado
        $spentAmount = $commitment->total_amount - $commitment->balance;
        $validatedData['balance'] = $validatedData['total_amount'] - $spentAmount;

        // Impede que o saldo se torne negativo após a atualização
        if ($validatedData['balance'] < 0) {
            return back()->withErrors(['total_amount' => 'O valor total não pode ser menor que o valor já gasto.'])->withInput();
        }

        $commitment->update($validatedData);

        return redirect()->route('commitments.index')->with('success', 'Empenho atualizado com sucesso!');
    }

    /**
     * Remove (cancela) um empenho do sistema.
     * A boa prática é alterar o status para 'canceled' em vez de deletar,
     * especialmente se houver ordens de fornecimento associadas.
     */
    public function destroy(BalanceCommitment $commitment)
    {
        // Verifica se o empenho já foi utilizado
        if ($commitment->balance < $commitment->total_amount) {
            return redirect()->route('commitments.index')->with('error', 'Não é possível excluir um empenho que já possui gastos. Cancele-o.');
        }

        // Se não houver gastos, pode ser excluído
        $commitment->delete();

        return redirect()->route('commitments.index')->with('success', 'Empenho excluído com sucesso!');
    }
}
