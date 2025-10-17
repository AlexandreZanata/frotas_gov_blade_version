<?php

namespace App\Http\Controllers\Admin\Sector;

use App\Http\Controllers\Controller;
use App\Models\Balance\BalanceCommitment;
use App\Models\Balance\BalanceSupplyOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SupplyOrderController extends Controller
{
    /**
     * Exibe as ordens de fornecimento da secretaria do usuário logado.
     * Garante que apenas dados pertinentes à secretaria sejam mostrados.
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user->secretariat_id) {
            abort(403, 'Acesso negado. Usuário não pertence a uma secretaria.');
        }

        $supplyOrders = BalanceSupplyOrder::whereHas('commitment', function ($query) use ($user) {
            $query->where('secretariat_id', $user->secretariat_id);
        })->with('commitment.supplier.gasStation', 'user')->latest()->paginate(15);

        return view('admin.sector.supply-orders.index', compact('supplyOrders'));
    }

    /**
     * Mostra o formulário para criar uma nova ordem de fornecimento.
     * Lista apenas empenhos aprovados e com saldo da secretaria do usuário.
     */
    public function create()
    {
        $user = Auth::user();
        if (!$user->secretariat_id) {
            abort(403, 'Acesso negado. Usuário não pertence a uma secretaria.');
        }

        $commitments = BalanceCommitment::where('secretariat_id', $user->secretariat_id)
            ->where('status', 'approved')
            ->where('balance', '>', 0)
            ->with('supplier.gasStation')
            ->get();

        return view('admin.sector.supply-orders.create', compact('commitments'));
    }

    /**
     * Armazena uma nova ordem de fornecimento.
     * Utiliza uma transação para garantir a atomicidade da operação:
     * 1. Valida o saldo.
     * 2. Cria a ordem.
     * 3. Debita o valor do empenho.
     * 4. Registra a movimentação para auditoria.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'commitment_id' => 'required|uuid|exists:balance_commitments,id',
            'value' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'number' => 'required|string|max:255|unique:balance_supply_orders,number',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($request, $user) {
                // Bloqueia o empenho para evitar condições de corrida
                $commitment = BalanceCommitment::where('id', $request->commitment_id)
                    ->where('secretariat_id', $user->secretariat_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                // Valida se o empenho tem saldo suficiente
                if ($commitment->balance < $request->value) {
                    throw ValidationException::withMessages([
                        'value' => 'O valor da ordem de fornecimento excede o saldo disponível no empenho (Saldo: R$ ' . number_format($commitment->balance, 2, ',', '.') . ').',
                    ]);
                }

                // 1. Cria a Ordem de Fornecimento
                $supplyOrder = $commitment->supplyOrders()->create([
                    'user_id' => $user->id,
                    'number' => $request->number,
                    'date' => $request->date,
                    'value' => $request->value,
                    'status' => 'authorized',
                    'notes' => $request->notes,
                ]);

                // 2. Debita o valor do saldo do empenho
                $commitment->decrement('balance', $request->value);

                // 3. Atualiza o status do empenho se o saldo zerar
                if ($commitment->balance <= 0) {
                    $commitment->status = 'exhausted';
                } else {
                    $commitment->status = 'partially_used';
                }
                $commitment->save();

                // 4. Registra a movimentação (auditoria)
                $supplyOrder->movements()->create([
                    'user_id' => $user->id,
                    'type' => 'debit',
                    'amount' => $request->value,
                    'description' => "Débito referente à Ordem de Fornecimento nº {$request->number}",
                    'moved_at' => now(),
                ]);
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Ocorreu um erro: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('supply-orders.index')->with('success', 'Ordem de Fornecimento criada com sucesso!');
    }

    /**
     * Exibe os detalhes de uma ordem de fornecimento específica.
     */
    public function show(string $id)
    {
        $user = Auth::user();
        $supplyOrder = BalanceSupplyOrder::where('id', $id)
            ->whereHas('commitment', fn($q) => $q->where('secretariat_id', $user->secretariat_id))
            ->with(['commitment.supplier.gasStation', 'user', 'movements'])
            ->firstOrFail();

        return view('admin.sector.supply-orders.show', compact('supplyOrder'));
    }

    /**
     * Ação não permitida para este perfil. Ordens não devem ser editadas.
     */
    public function edit(string $id)
    {
        abort(403, 'Ação não permitida. Ordens de Fornecimento não podem ser editadas.');
    }

    /**
     * Ação não permitida para este perfil.
     */
    public function update(Request $request, string $id)
    {
        abort(403, 'Ação não permitida.');
    }

    /**
     * Cancela uma ordem de fornecimento e estorna o valor ao empenho.
     * Utiliza uma transação para garantir a consistência dos dados.
     */
    public function destroy(string $id)
    {
        $user = Auth::user();
        $supplyOrder = BalanceSupplyOrder::where('id', $id)
            ->whereHas('commitment', fn($q) => $q->where('secretariat_id', $user->secretariat_id))
            ->where('status', '!=', 'canceled')
            ->firstOrFail();

        try {
            DB::transaction(function () use ($supplyOrder, $user) {
                // 1. Bloqueia o empenho
                $commitment = BalanceCommitment::lockForUpdate()->find($supplyOrder->commitment_id);

                // 2. Estorna o valor para o saldo do empenho
                $commitment->increment('balance', $supplyOrder->value);

                // 3. Atualiza o status do empenho
                $commitment->status = 'partially_used';
                if ($commitment->balance >= $commitment->total_amount) {
                    $commitment->status = 'approved'; // Volta a aprovado se o valor for totalmente estornado
                }
                $commitment->save();

                // 4. Atualiza o status da ordem
                $supplyOrder->update(['status' => 'canceled']);

                // 5. Registra a movimentação de crédito (estorno)
                $supplyOrder->movements()->create([
                    'user_id' => $user->id,
                    'type' => 'credit',
                    'amount' => $supplyOrder->value,
                    'description' => "Estorno referente ao cancelamento da Ordem de Fornecimento nº {$supplyOrder->number}",
                    'moved_at' => now(),
                ]);
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Ocorreu um erro ao cancelar a ordem: ' . $e->getMessage());
        }

        return redirect()->route('supply-orders.index')->with('success', 'Ordem de Fornecimento cancelada e valor estornado!');
    }
}
