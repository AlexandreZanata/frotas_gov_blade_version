<?php

namespace App\Http\Controllers;

use App\Models\DefectReport;
use App\Models\DefectReportItem;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DefectReportController extends Controller
{
    /**
     * Exibe uma lista de todos os relatórios de defeito com base nas permissões do usuário.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');
        $status = $request->input('status');

        $query = DefectReport::with(['vehicle.prefix', 'vehicle.secretariat', 'user'])
            ->orderBy('created_at', 'desc');

        // Aplica filtros de hierarquia
        if ($user->isGeneralManager()) {
            // Gestor Geral vê todos os relatórios
        } elseif ($user->isManager()) {
            // Gestor Setorial vê apenas da sua secretaria
            $query->whereHas('vehicle', function ($q) use ($user) {
                $q->where('secretariat_id', $user->secretariat_id);
            });
        } else {
            // Outros usuários (motoristas, etc.) veem apenas os seus
            $query->where('user_id', $user->id);
        }

        // Filtro de busca
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('vehicle', function ($vehicleQuery) use ($search) {
                    $vehicleQuery->where('plate', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                })
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filtro de status
        if ($status) {
            $query->where('status', $status);
        }

        $defectReports = $query->paginate(15)->withQueryString();

        return view('defect_reports.index', compact('defectReports', 'search', 'status'));
    }

    /**
     * Mostra o formulário para criar uma nova ficha de comunicação de defeito.
     */
    public function create()
    {
        $user = Auth::user();

        // Carrega veículos acessíveis ao usuário
        if($user->isGeneralManager()) {
            $vehicles = Vehicle::with('prefix')->orderBy('name')->get();
        } else {
            $vehicles = Vehicle::with('prefix')->where('secretariat_id', $user->secretariat_id)->orderBy('name')->get();
        }

        $defectItems = DefectReportItem::with('category')->get()->groupBy('category.name');

        return view('defect_reports.create', compact('vehicles', 'defectItems'));
    }

    /**
     * Armazena uma nova ficha de comunicação de defeito no banco de dados.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'vehicle_id' => 'required|uuid|exists:vehicles,id',
            'notes' => 'nullable|string|max:2000',
            'answers' => 'required|array|min:1',
            'answers.*.item_id' => 'required|uuid|exists:defect_report_items,id',
            'answers.*.severity' => 'required|in:low,medium,high',
            'answers.*.notes' => 'nullable|string|max:1000',
        ], [
            'answers.required' => 'Você deve selecionar pelo menos um item defeituoso.'
        ]);

        try {
            DB::transaction(function () use ($validated, $user) {
                // 1. Cria o relatório principal
                $defectReport = DefectReport::create([
                    'vehicle_id' => $validated['vehicle_id'],
                    'user_id' => $user->id,
                    'status' => 'open', // Status inicial
                    'notes' => $validated['notes'],
                ]);

                // 2. Associa os itens defeituosos reportados
                foreach ($validated['answers'] as $answerData) {
                    $defectReport->answers()->create([
                        'defect_report_item_id' => $answerData['item_id'],
                        'severity' => $answerData['severity'],
                        'notes' => $answerData['notes'],
                    ]);
                }
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Ocorreu um erro ao salvar o relatório: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('defect-reports.index')->with('success', 'Ficha de defeito comunicada com sucesso!');
    }

    /**
     * Exibe os detalhes de uma ficha de comunicação de defeito.
     */
    public function show(DefectReport $defectReport)
    {
        $user = Auth::user();

        // Validação de permissão
        if (!$user->isGeneralManager() &&
            !($user->isManager() && $defectReport->vehicle->secretariat_id === $user->secretariat_id) &&
            $defectReport->user_id !== $user->id) {
            abort(403, 'Você não tem permissão para visualizar este relatório.');
        }

        $defectReport->load(['vehicle.prefix', 'user', 'answers.item.category']);

        return view('defect_reports.show', compact('defectReport'));
    }
}
