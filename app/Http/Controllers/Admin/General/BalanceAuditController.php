<?php

namespace App\Http\Controllers\Admin\General;

use App\Http\Controllers\Controller;
use App\Models\Balance\BalanceMovement;
use Illuminate\Http\Request;

class BalanceAuditController extends Controller
{
    /**
     * Exibe uma lista paginada de todas as movimentações financeiras.
     * Permite filtrar os resultados por data, tipo (débito/crédito), e buscar por descrição.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Inicia a query base, já carregando os relacionamentos para evitar N+1 queries
        $query = BalanceMovement::with(['user', 'movable']);

        // --- Filtros ---

        // Filtro por tipo de movimentação (débito ou crédito)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filtro por período (data de início e fim)
        if ($request->filled('start_date')) {
            $query->whereDate('moved_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('moved_at', '<=', $request->end_date);
        }

        // Filtro de busca por termo na descrição
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where('description', 'like', $searchTerm);
        }

        // Ordena pelos mais recentes e pagina os resultados
        $movements = $query->latest('moved_at')->paginate(20)->withQueryString();

        // Retorna a view com os dados para a tabela de auditoria
        return view('admin.general.audit.index', compact('movements'));
    }

    /**
     * Exibe os detalhes de uma movimentação específica.
     *
     * @param  \App\Models\Balance\BalanceMovement  $audit // O Laravel fará o Rote Model Binding
     * @return \Illuminate\View\View
     */
    public function show(BalanceMovement $audit)
    {
        // Carrega os relacionamentos necessários para a view de detalhes
        $audit->load(['user', 'movable']);

        // O 'movable' pode ser uma Ordem de Fornecimento, uma Despesa, etc.
        // A view pode verificar o tipo de 'movable' para exibir informações diferentes.
        // Ex: if ($audit->movable_type === BalanceSupplyOrder::class) { ... }

        return view('admin.general.audit.show', compact('audit'));
    }
}
