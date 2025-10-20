<?php

namespace App\Http\Controllers\garbage;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChecklistRequest;
use App\Models\garbage\GarbageNeighborhood;
use App\Models\garbage\GarbageRun;
use App\Models\garbage\GarbageRunDestination;
use App\Models\garbage\GarbageVehicle;
use App\Models\garbage\GarbageWeighing;
use App\Models\checklist\ChecklistItem;
use App\Models\run\RunGapFind;
use App\Services\GarbageLogbookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\garbage\GarbageUser;

class GarbageRunController extends Controller
{
    protected GarbageLogbookService $logbookService;

    public function __construct(GarbageLogbookService $logbookService)
    {
        $this->logbookService = $logbookService;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        // A busca pelo garbageUser agora deve usar o user_id do usuário autenticado
        $garbageUser = GarbageUser::where('user_id', Auth::id())->first();

        // Se não for um usuário de coleta, redireciona ou mostra erro
        if (!$garbageUser) {
            return redirect()->route('dashboard')->with('error', 'Acesso não permitido.'); // Ou outra rota
        }


        $runs = GarbageRun::where('user_id', $garbageUser->id) // Filtra pelo ID do GarbageUser
        ->with([
            'vehicle.vehicle.prefix', // Carrega GarbageVehicle > Vehicle > Prefix
            'vehicle.vehicle.category', // Carrega GarbageVehicle > Vehicle > Category
            'signature',
            'destinations.neighborhood',
            'weighing'
        ])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('destinations.neighborhood', function ($neighborhoodQuery) use ($search) {
                        $neighborhoodQuery->where('name', 'like', "%{$search}%");
                    })
                        ->orWhere('stop_point', 'like', "%{$search}%")
                        ->orWhereHas('vehicle.vehicle', function ($vehicleQuery) use ($search) { // Ajuste para buscar dentro de vehicle.vehicle
                            $vehicleQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('plate', 'like', "%{$search}%")
                                ->orWhereHas('prefix', function ($prefixQuery) use ($search) {
                                    $prefixQuery->where('name', 'like', "%{$search}%");
                                });
                        });
                });
            })
            ->latest('started_at') // Ordenar por início da corrida
            ->paginate(10)
            ->withQueryString();

        $unsignedRuns = GarbageRun::where('user_id', $garbageUser->id) // Filtra pelo ID do GarbageUser
        ->where('status', 'completed')
            ->where(function ($query) {
                $query->whereDoesntHave('signature')
                    ->orWhereHas('signature', function ($q) {
                        $q->whereNull('driver_signed_at');
                    });
            })
            ->get();

        return view('garbage-logbook.index', compact('runs', 'unsignedRuns', 'search'));
    }


    // Retorna o usuário logado para a corrida ativa (deve ser GarbageUser)
    public function getUserActiveRun()
    {
        $garbageUser = GarbageUser::where('user_id', Auth::id())->first();
        if (!$garbageUser) {
            return null;
        }
        return GarbageRun::where('user_id', $garbageUser->id) // Busca pelo ID do GarbageUser
        ->where('status', 'in_progress')
            ->with(['vehicle.vehicle.prefix', 'destinations.neighborhood']) // Ajuste para carregar vehicle.vehicle.prefix
            ->first();
    }


    public function start()
    {
        $activeRun = $this->getUserActiveRun(); // Usa o método ajustado

        if ($activeRun) {
            // Verifica se a corrida já tem KM inicial e bairros definidos
            if ($activeRun->start_km === null || $activeRun->destinations->isEmpty()) {
                // Se não tiver KM inicial ou bairros, vai para a tela de iniciar corrida
                return redirect()->route('garbage-logbook.start-run', $activeRun);
            } else {
                // Se já tiver KM e bairros, vai direto para a tela de finalizar
                return redirect()->route('garbage-logbook.finish', $activeRun);
            }
        }


        // Se não há corrida ativa, verifica se há veículo selecionado na sessão
        $selectedVehicleId = $this->logbookService->getSelectedVehicleId();
        if ($selectedVehicleId) {
            // Se houver veículo selecionado, vai para o formulário de checklist
            return redirect()->route('garbage-logbook.checklist-form');
        }


        // Caso contrário, vai para a seleção de veículo
        return redirect()->route('garbage-logbook.vehicle-select');
    }


    public function selectVehicle()
    {
        $vehicles = $this->logbookService->getAvailableVehicles();
        return view('garbage-logbook.select-vehicle', compact('vehicles'));
    }


    public function storeVehicle(Request $request)
    {
        $request->validate(['vehicle_id' => 'required|exists:garbage_vehicles,id']);

        $garbageVehicle = GarbageVehicle::findOrFail($request->vehicle_id);

        // Verifica se o usuário logado tem permissão para usar este veículo
        $garbageUser = GarbageUser::where('user_id', Auth::id())->first();
        if (!$garbageUser || !$garbageUser->vehicles->contains($garbageVehicle->id)) {
            return back()->with('error', 'Você não tem permissão para usar este veículo.');
        }

        // Verifica a disponibilidade usando o serviço
        $availability = $this->logbookService->checkVehicleAvailability($request->vehicle_id);
        if (!$availability['available']) {
            return back()->with('error', 'Veículo em uso por outro motorista: ' . $availability['currentUser']);
        }


        $this->logbookService->saveVehicleSelection($request->vehicle_id);
        return redirect()->route('garbage-logbook.checklist-form');
    }


    public function checklistForm()
    {
        $vehicleId = $this->logbookService->getSelectedVehicleId();
        if (!$vehicleId) {
            return redirect()->route('garbage-logbook.vehicle-select')->with('error', 'Selecione um veículo primeiro.');
        }

        // Carrega GarbageVehicle e o Vehicle relacionado com prefix e category
        $garbageVehicle = GarbageVehicle::with('vehicle.prefix', 'vehicle.category')->findOrFail($vehicleId);
        // A variável $vehicle agora acessa o model Vehicle dentro de GarbageVehicle
        $vehicle = $garbageVehicle->vehicle;

        $checklistItems = ChecklistItem::orderBy('name')->get(); // Ordena os itens
        $lastChecklistState = $this->logbookService->getLastChecklistState($vehicleId);

        // Passa o model Vehicle (não GarbageVehicle) para a view
        return view('garbage-logbook.checklist', compact('vehicle', 'checklistItems', 'lastChecklistState'));
    }


    // Usa o ChecklistRequest para validação
    public function storeChecklistAndCreateRun(ChecklistRequest $request)
    {
        $vehicleId = $this->logbookService->getSelectedVehicleId();
        if (!$vehicleId) {
            return redirect()->route('garbage-logbook.vehicle-select')->with('error', 'Sessão expirada ou veículo não selecionado.');
        }

        try {
            DB::beginTransaction();
            $run = $this->logbookService->createRunWithChecklist(
                $vehicleId,
                $request->validated()['checklist'],
                $request->input('general_notes')
            );
            DB::commit();

            return redirect()->route('garbage-logbook.start-run', $run)->with('success', 'Checklist salvo! Agora informe os dados da coleta.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao salvar checklist e criar run: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Erro ao salvar o checklist. Tente novamente.');
        }
    }


    public function startRun(GarbageRun $run)
    {
        // Garante que a view só pode ser acessada pelo usuário dono da corrida
        // $this->authorize('update', $run); // Política de autorização

        // Verifica se a corrida pertence ao GarbageUser logado
        $garbageUser = GarbageUser::where('user_id', Auth::id())->first();
        if (!$garbageUser || $run->user_id !== $garbageUser->id) {
            abort(403, 'Acesso não autorizado a esta coleta.');
        }

        $lastKm = $this->logbookService->getLastKm($run->vehicle_id);
        $neighborhoods = $this->logbookService->getAvailableNeighborhoods();

        // Passa a variável $run para a view
        return view('garbage-logbook.start-run', compact('run', 'lastKm', 'neighborhoods'));
    }


    public function storeStartRun(Request $request, GarbageRun $run)
    {
        // $this->authorize('update', $run); // Política de autorização
        // Verifica se a corrida pertence ao GarbageUser logado
        $garbageUser = GarbageUser::where('user_id', Auth::id())->first();
        if (!$garbageUser || $run->user_id !== $garbageUser->id) {
            abort(403, 'Acesso não autorizado a esta coleta.');
        }


        $lastKm = $this->logbookService->getLastKm($run->vehicle_id);

        $validated = $request->validate([
            'start_km' => 'required|integer|min:' . $lastKm,
            'neighborhoods' => 'required|array|min:1',
            'neighborhoods.*' => 'exists:garbage_neighborhoods,id',
        ]);

        try {
            DB::beginTransaction();

            $run->update([
                'start_km' => $validated['start_km'],
                'started_at' => now(), // Define a hora de início aqui
            ]);

            // Remover destinos antigos se houver, antes de adicionar novos
            $run->destinations()->delete();

            // Salvar bairros selecionados
            foreach ($validated['neighborhoods'] as $index => $neighborhoodId) {
                GarbageRunDestination::create([
                    'garbage_run_id' => $run->id,
                    'garbage_neighborhood_id' => $neighborhoodId,
                    'order' => $index, // Ordem de seleção/visita
                ]);
            }

            // Lógica de GAP de KM
            $lastCompletedRun = GarbageRun::where('vehicle_id', $run->vehicle_id)
                ->where('id', '!=', $run->id)
                ->where('status', 'completed')
                ->whereNotNull('end_km')
                ->orderBy('finished_at', 'desc')
                ->first();

            if ($lastCompletedRun) {
                $expectedKm = $lastCompletedRun->end_km;
                $recordedKm = $run->start_km;

                // Registra GAP apenas se o KM registrado for maior que o esperado
                if ($recordedKm > $expectedKm) {
                    $gap = $recordedKm - $expectedKm;

                    // Assume que RunGapFind está no namespace App\Models\run
                    RunGapFind::create([
                        'run_id' => $run->id,
                        'vehicle_id' => $run->vehicle_id, // Usar vehicle_id da run
                        'user_id' => $run->user_id, // Usar user_id da run (que é o garbage_user_id)
                        'recorded_start_km' => $recordedKm,
                        'expected_start_km' => $expectedKm,
                        'gap_km' => $gap,
                    ]);
                }
            }


            DB::commit();

            return redirect()->route('garbage-logbook.finish', $run)
                ->with('success', 'Coleta iniciada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao iniciar coleta: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Erro ao iniciar coleta. Tente novamente.');
        }
    }


    public function finishRun(GarbageRun $run)
    {
        // $this->authorize('update', $run);
        // Verifica se a corrida pertence ao GarbageUser logado
        $garbageUser = GarbageUser::where('user_id', Auth::id())->first();
        if (!$garbageUser || $run->user_id !== $garbageUser->id) {
            abort(403, 'Acesso não autorizado a esta coleta.');
        }

        // Carrega os relacionamentos necessários para a view
        $run->load(['vehicle.vehicle.prefix', 'destinations.neighborhood']);


        return view('garbage-logbook.finish-run', compact('run'));
    }


    public function storeFinishRun(Request $request, GarbageRun $run)
    {
        // $this->authorize('update', $run);
        // Verifica se a corrida pertence ao GarbageUser logado
        $garbageUser = GarbageUser::where('user_id', Auth::id())->first();
        if (!$garbageUser || $run->user_id !== $garbageUser->id) {
            abort(403, 'Acesso não autorizado a esta coleta.');
        }


        $validated = $request->validate([
            // Garante que o KM final não seja menor que o inicial
            'end_km' => 'required|integer|min:' . $run->start_km,
            'stop_point' => 'nullable|string|max:255',
            // Validação para pesagem
            'pesagem' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $weighingId = null;
            // Criar registro de pesagem se informado e maior que zero
            if (!empty($validated['pesagem']) && $validated['pesagem'] > 0) {
                // Busca a tara atual do veículo (ou usa 0 se não encontrar)
                // Assumindo que a relação 'currentTare' existe no model GarbageVehicle
                // $tare = $run->vehicle->currentTare->tare_weight_kg ?? 0;
                $tare = 0; // Defina a tara ou busque-a corretamente


                $weighing = GarbageWeighing::create([
                    // Gerar código único para pesagem
                    'weighing_code' => 'WG-' . strtoupper(uniqid()),
                    'gross_weight_kg' => $validated['pesagem'],
                    'tare_weight_kg' => $tare,
                    // Calcula peso líquido
                    'net_weight_kg' => max(0, $validated['pesagem'] - $tare),
                    'weighed_at' => now(),
                    // Usa o ID do GarbageUser
                    'requester_id' => $garbageUser->id,
                    'garbage_vehicle_id' => $run->vehicle_id,
                ]);
                $weighingId = $weighing->id; // Guarda o ID para associar à corrida
            }


            // Chama o serviço para finalizar a corrida, passando o weighingId
            $this->logbookService->finishRun(
                $run,
                $validated['end_km'],
                $validated['stop_point'] ?? null,
                $weighingId // Passa o ID da pesagem ou null
            );

            DB::commit();

            return redirect()->route('garbage-logbook.index')
                ->with('success', 'Coleta finalizada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao finalizar coleta {$run->id}: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Erro ao finalizar a coleta. Tente novamente.');
        }
    }


    public function show(GarbageRun $run)
    {
        // $this->authorize('view', $run);
        // Verifica se a corrida pertence ao GarbageUser logado
        $garbageUser = GarbageUser::where('user_id', Auth::id())->first();
        if (!$garbageUser || $run->user_id !== $garbageUser->id) {
            abort(403, 'Acesso não autorizado a esta coleta.');
        }


        $run->load([
            // Ajuste para carregar os relacionamentos corretamente
            'vehicle.vehicle.prefix', // GarbageRun -> GarbageVehicle -> Vehicle -> Prefix
            'vehicle.vehicle.category', // GarbageRun -> GarbageVehicle -> Vehicle -> Category
            'user.user', // GarbageRun -> GarbageUser -> User (o usuário principal)
            'checklist.answers.item', // Carrega o checklist, suas respostas e o item de cada resposta
            'destinations.neighborhood', // Bairros visitados
            'signature.driverSignature.user', // Assinatura -> AssinaturaDigital -> User (Motorista)
            'signature.adminSignature.user', // Assinatura -> AssinaturaDigital -> User (Admin)
            'weighing', // Carrega a pesagem associada
        ]);

        return view('garbage-logbook.show', compact('run'));
    }


    public function cancel(GarbageRun $run)
    {
        // $this->authorize('delete', $run);
        // Verifica se a corrida pertence ao GarbageUser logado
        $garbageUser = GarbageUser::where('user_id', Auth::id())->first();
        if (!$garbageUser || $run->user_id !== $garbageUser->id) {
            abort(403, 'Acesso não autorizado a esta coleta.');
        }

        // Só permite cancelar se estiver em andamento
        if ($run->status !== 'in_progress') {
            return redirect()->route('garbage-logbook.index')->with('error', 'Não é possível cancelar uma coleta já finalizada.');
        }


        try {
            DB::beginTransaction();

            // Remove destinos e respostas do checklist antes de remover o checklist e a run
            $run->destinations()->delete();
            if ($run->checklist) {
                $run->checklist->answers()->delete();
                $run->checklist->delete();
            }
            // Remove possíveis GAPs registrados
            RunGapFind::where('run_id', $run->id)->delete();


            $run->delete(); // Exclui a corrida

            // Limpa o estado da sessão (veículo selecionado)
            $this->logbookService->clearUserFlowState();

            DB::commit();

            return redirect()->route('garbage-logbook.index') // Redireciona para o índice após cancelar
            ->with('success', 'Coleta cancelada com sucesso.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao cancelar coleta {$run->id}: " . $e->getMessage());
            return redirect()->route('garbage-logbook.index')->with('error', 'Erro ao cancelar a coleta.');
        }
    }
}
