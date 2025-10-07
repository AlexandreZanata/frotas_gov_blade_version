<?php

namespace App\Http\Controllers;

use App\Models\Run;
use App\Models\Vehicle;
use App\Models\ChecklistItem; // Certifique-se de que este model exista
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RunController extends Controller
{
    /**
     * Lista as corridas do usuário ou todas, se for admin.
     */
    public function index()
    {
        // Lógica para listar corridas (ex: apenas as do usuário logado)
        $runs = Run::where('user_id', Auth::id())
            ->with('vehicle')
            ->latest()
            ->paginate(15);

        return view('runs.index', compact('runs'));
    }

    // ETAPA 1: ESCOLHER VEÍCULO
    public function createStep1()
    {
        // Filtra veículos da secretaria do usuário
        $vehicles = Vehicle::where('secretariat_id', Auth::user()->secretariat_id)->get();
        return view('runs.create-step-1', compact('vehicles'));
    }

    public function storeStep1(Request $request)
    {
        $request->validate(['vehicle_id' => 'required|exists:vehicles,id']);

        // Verifica se o veículo já tem uma corrida em andamento
        $existingRun = Run::where('vehicle_id', $request->vehicle_id)
            ->where('status', 'in_progress')
            ->first();

        if ($existingRun) {
            return back()->with('error', 'Este veículo já está em uso pelo motorista ' . $existingRun->user->name);
        }

        // Cria a corrida com o status inicial
        $run = Run::create([
            'vehicle_id' => $request->vehicle_id,
            'user_id' => Auth::id(),
            'status' => 'in_progress', // Status inicial
            'origin' => 'Pátio da Prefeitura', // Origem padrão ou buscar de algum lugar
        ]);

        return redirect()->route('runs.create-step-2', $run);
    }

    // ETAPA 2: CHECKLIST
    public function createStep2(Run $run)
    {
        $checklistItems = ChecklistItem::all(); // Assumindo que você tem um model para os itens do checklist
        return view('runs.create-step-2', compact('run', 'checklistItems'));
    }

    public function storeStep2(Request $request, Run $run)
    {
        // Aqui você salvaria as respostas do checklist.
        // A lógica exata dependerá de como você estruturou a tabela `checklist_answers`.
        // Exemplo:
        // foreach ($request->checklist as $itemId => $answer) {
        //     ChecklistAnswer::create([
        //         'run_id' => $run->id,
        //         'checklist_item_id' => $itemId,
        //         'status' => $answer['status'],
        //         'notes' => $answer['notes'] ?? null,
        //     ]);
        // }

        return redirect()->route('runs.create-step-3', $run);
    }

    // ETAPA 3: INICIAR CORRIDA
    public function createStep3(Run $run)
    {
        // Pega o KM final da última corrida completada com este veículo
        $lastRunKm = Run::where('vehicle_id', $run->vehicle_id)
            ->where('status', 'completed')
            ->latest('finished_at')
            ->first()->end_km ?? 0;

        return view('runs.create-step-3', compact('run', 'lastRunKm'));
    }

    public function storeStep3(Request $request, Run $run)
    {
        $request->validate([
            'start_km' => 'required|numeric|min:0',
            'destination' => 'required|string|max:255',
        ]);

        $run->update([
            'start_km' => $request->start_km,
            'destination' => $request->destination,
            'started_at' => now(),
        ]);

        // Corrida iniciada, redireciona para o painel principal ou para uma página de "corrida em progresso"
        return redirect()->route('runs.index')->with('success', 'Diário de Bordo iniciado com sucesso!');
    }

    // ETAPA 4: FINALIZAR CORRIDA
    public function finish(Run $run)
    {
        // Verifica se o usuário logado é o dono da corrida
        if ($run->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        return view('runs.finish', compact('run'));
    }

    public function complete(Request $request, Run $run)
    {
        $request->validate([
            'end_km' => 'required|numeric|gte:' . $run->start_km,
            'stoppage_point' => 'required|string|max:255',
            // Adicione aqui a validação para os campos de abastecimento, se preenchidos
        ]);

        $run->update([
            'end_km' => $request->end_km,
            // 'stoppage_point' => $request->stoppage_point, // Adicione este campo na migration se necessário
            'finished_at' => now(),
            'status' => 'completed',
        ]);

        // Lógica para salvar o abastecimento (se houver)
        if ($request->filled('fueling_liters')) {
            // Fueling::create([...]);
        }

        return redirect()->route('runs.index')->with('success', 'Corrida finalizada com sucesso!');
    }
}
