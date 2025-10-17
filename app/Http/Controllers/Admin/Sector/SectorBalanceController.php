<?php

namespace App\Http\Controllers\Admin\Sector;

use App\Http\Controllers\Controller;
use App\Models\Balance\BalanceCommitment;
use App\Models\Balance\BalanceSupplyOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SectorBalanceController extends Controller
{
    /**
     * Exibe um dashboard com os dados financeiros agregados da secretaria do usuário logado.
     */
    public function index()
    {
        // Pega o ID da secretaria do usuário autenticado
        $secretariatId = Auth::user()->secretariat_id;

        // Se o usuário não tiver uma secretaria, nega o acesso
        if (!$secretariatId) {
            abort(403, 'Usuário não vinculado a uma secretaria.');
        }

        // Busca todos os empenhos pertencentes a esta secretaria
        $commitments = BalanceCommitment::where('secretariat_id', $secretariatId)->get();

        // Calcula os totais
        $totalAllocated = $commitments->sum('total_amount'); // Valor total empenhado
        $currentBalance = $commitments->sum('balance');      // Saldo somado de todos os empenhos
        $totalSpent = $totalAllocated - $currentBalance;     // Total já gasto

        // Busca as últimas 10 ordens de fornecimento para um feed de atividades recentes
        $recentSupplyOrders = BalanceSupplyOrder::whereHas('commitment', function ($query) use ($secretariatId) {
            $query->where('secretariat_id', $secretariatId);
        })->with('user', 'commitment')->latest()->take(10)->get();


        return view('admin.sector.dashboard.index', compact(
            'totalAllocated',
            'currentBalance',
            'totalSpent',
            'commitments',
            'recentSupplyOrders'
        ));
    }

    /**
     * Exibe os detalhes de um empenho específico, se ele pertencer à secretaria do usuário.
     */
    public function show(string $id)
    {
        $secretariatId = Auth::user()->secretariat_id;

        // Busca o empenho e verifica se ele pertence à secretaria do usuário
        $commitment = BalanceCommitment::where('id', $id)
            ->where('secretariat_id', $secretariatId)
            ->with(['supplier.gasStation', 'supplyOrders.user'])
            ->firstOrFail(); // Retorna 404 se não encontrar

        return view('admin.sector.commitments.show', compact('commitment'));
    }


    // --- Métodos Não Aplicáveis para este Controller ---

    /**
     * Ação não permitida para este perfil.
     */
    public function create()
    {
        abort(403, 'Ação não permitida.');
    }

    /**
     * Ação não permitida para este perfil.
     */
    public function store(Request $request)
    {
        abort(403, 'Ação não permitida.');
    }

    /**
     * Ação não permitida para este perfil.
     */
    public function edit(string $id)
    {
        abort(403, 'Ação não permitida.');
    }

    /**
     * Ação não permitida para este perfil.
     */
    public function update(Request $request, string $id)
    {
        abort(403, 'Ação não permitida.');
    }

    /**
     * Ação não permitida para este perfil.
     */
    public function destroy(string $id)
    {
        abort(403, 'Ação não permitida.');
    }
}
