<?php

namespace App\Http\Controllers\runs;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChecklistRequest;
use App\Models\run\RunGapFind;
use App\Http\Requests\FuelingRequest;
use App\Http\Requests\RunFinishRequest;
use App\Http\Requests\RunStartRequest;
use App\Models\checklist\ChecklistItem;
use App\Models\fuel\Fueling;
use App\Models\fuel\FuelType;
use App\Models\fuel\GasStation;
use App\Models\run\Run;
use App\Models\run\RunDestination;
use App\Models\Vehicle\Vehicle;
use App\Services\LogbookService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RunController extends Controller
{
    use AuthorizesRequests;

    protected LogbookService $logbookService;

    public function __construct(LogbookService $logbookService)
    {
        $this->logbookService = $logbookService;
    }

    /**
     * Lista as corridas do usuário
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $user = Auth::user();

        $runs = Run::where('user_id', $user->id)
            ->with(['vehicle.prefix', 'vehicle.category', 'signature'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('destinations', function ($destinationQuery) use ($search) {
                        $destinationQuery->where('destination', 'like', "%{$search}%");
                    })
                        ->orWhere('stop_point', 'like', "%{$search}%")
                        ->orWhereHas('vehicle', function ($vehicleQuery) use ($search) {
                            $vehicleQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('plate', 'like', "%{$search}%")
                                ->orWhereHas('prefix', function ($prefixQuery) use ($search) {
                                    $prefixQuery->where('name', 'like', "%{$search}%");
                                });
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Obter as corridas não assinadas (sem assinatura do motorista) para o botão "Assinar Todas"
        $unsignedRuns = Run::where('user_id', $user->id)
            ->where('status', 'completed')
            ->where(function ($query) {
                $query->whereDoesntHave('signature')
                    ->orWhereHas('signature', function ($q) {
                        $q->whereNull('driver_signed_at');
                    });
            })
            ->get();

        return view('logbook.index', compact('runs', 'unsignedRuns'));
    }


    /**
     * Ponto de entrada do fluxo - verifica estado e redireciona
     */
    public function start()
    {
        // Verifica se existe uma corrida em andamento
        $activeRun = $this->logbookService->getUserActiveRun();

        if ($activeRun) {
            // Detecta automaticamente em qual etapa está baseado no estado da corrida
            if (!$activeRun->checklist) {
                // Tem corrida mas não tem checklist - vai para checklist
                return redirect()->route('logbook.checklist', $activeRun);
            } elseif ($activeRun->destinations->isEmpty()) {
                // Tem checklist mas não iniciou corrida (sem destinos)
                // Redireciona para iniciar corrida
                return redirect()->route('logbook.start-run', $activeRun);
            } else {
                // Já iniciou com km e destino - vai para finalizar
                return redirect()->route('logbook.finish', $activeRun);
            }
        }

        // Se não há corrida em andamento, começa do início
        return redirect()->route('logbook.vehicle-select');
    }


    /**
     * ETAPA 1: Seleção do Veículo
     */
    public function selectVehicle()
    {
        $vehicles = $this->logbookService->getAvailableVehicles();
        return view('logbook.select-vehicle', compact('vehicles'));
    }

    /**
     * Retorna dados do veículo via AJAX
     */
    public function getVehicleData(Vehicle $vehicle)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Verifica se o usuário tem permissão para acessar este veículo
        if (method_exists(\App\Models\logbook\LogbookPermission::class, 'canAccessVehicle') && !\App\Models\logbook\LogbookPermission::canAccessVehicle($user, $vehicle)) {
            return response()->json([
                'error' => true,
                'message' => 'Você não tem permissão para acessar este veículo.',
            ], 403);
        }

        // Verifica disponibilidade
        $availability = $this->logbookService->checkVehicleAvailability($vehicle->id);

        if (!$availability['available']) {
            return response()->json([
                'available' => false,
                'driver_name' => $availability['active_run']->user->name,
                'driver_phone' => $availability['active_run']->user->phone ?? 'N/A',
                'manager_contact' => $vehicle->secretariat->manager_contact ?? 'N/A',
            ]);
        }

        return response()->json([
            'available' => true,
            'name' => $vehicle->name,
            'plate' => $vehicle->plate,
            'prefix' => $vehicle->prefix->name ?? 'N/A',
            'secretariat' => $vehicle->secretariat->name ?? 'N/A',
        ]);
    }


    /**
     * Confirma seleção do veículo e salva na sessão (NÃO cria a corrida ainda)
     */
    public function storeVehicle(Request $request)
    {
        $request->validate(['vehicle_id' => 'required|exists:vehicles,id']);

        $vehicle = Vehicle::findOrFail($request->vehicle_id);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (method_exists(\App\Models\logbook\LogbookPermission::class, 'canAccessVehicle') && !\App\Models\logbook\LogbookPermission::canAccessVehicle($user, $vehicle)) {
            return back()->with('error', 'Você não tem permissão para acessar este veículo.');
        }

        $availability = $this->logbookService->checkVehicleAvailability($request->vehicle_id);

        if (!$availability['available']) {
            return back()->with('error', 'Veículo em uso por outro motorista.');
        }

        $this->logbookService->saveVehicleSelection($request->vehicle_id);

        return redirect()->route('logbook.checklist-form');
    }

    /**
     * ETAPA 2: Checklist do Veículo (sem corrida criada ainda)
     */
    public function checklistForm()
    {
        $vehicleId = $this->logbookService->getSelectedVehicleId();

        if (!$vehicleId) {
            return redirect()->route('logbook.vehicle-select')
                ->with('error', 'Selecione um veículo primeiro.');
        }

        $vehicle = Vehicle::with('prefix', 'category')->findOrFail($vehicleId);
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (method_exists(\App\Models\logbook\LogbookPermission::class, 'canAccessVehicle') && !\App\Models\logbook\LogbookPermission::canAccessVehicle($user, $vehicle)) {
            $this->logbookService->clearVehicleSelection();
            return redirect()->route('logbook.vehicle-select')
                ->with('error', 'Você não tem permissão para acessar este veículo.');
        }

        $checklistItems = ChecklistItem::all();
        $lastChecklistState = $this->logbookService->getLastChecklistState($vehicleId);

        return view('logbook.checklist', compact('vehicle', 'checklistItems', 'lastChecklistState'));
    }

    /**
     * Salva o checklist E CRIA A CORRIDA
     */
    public function storeChecklistAndCreateRun(ChecklistRequest $request)
    {
        $vehicleId = $this->logbookService->getSelectedVehicleId();

        if (!$vehicleId) {
            return redirect()->route('logbook.vehicle-select')
                ->with('error', 'Selecione um veículo primeiro.');
        }

        $vehicle = Vehicle::findOrFail($vehicleId);
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (method_exists(\App\Models\logbook\LogbookPermission::class, 'canAccessVehicle') && !\App\Models\logbook\LogbookPermission::canAccessVehicle($user, $vehicle)) {
            $this->logbookService->clearVehicleSelection();
            return redirect()->route('logbook.vehicle-select')
                ->with('error', 'Você não tem permissão para acessar este veículo.');
        }

        $run = $this->logbookService->createRunWithChecklist(
            $vehicleId,
            $request->validated()['checklist'],
            $request->input('general_notes')
        );

        return redirect()->route('logbook.start-run', $run)
            ->with('success', 'Checklist preenchido! Agora inicie a corrida.');
    }

    /**
     * ETAPA 2 ANTIGA: Checklist do Veículo (para corridas já criadas - manter compatibilidade)
     */
    public function checklist(Run $run)
    {
        $this->authorize('update', $run);

        $checklistItems = ChecklistItem::all();
        $lastChecklistState = $this->logbookService->getLastChecklistState($run->vehicle_id);

        return view('logbook.checklist', compact('run', 'checklistItems', 'lastChecklistState'));
    }

    /**
     * Salva o checklist (para corridas já criadas - manter compatibilidade)
     */
    public function storeChecklist(ChecklistRequest $request, Run $run)
    {
        $this->authorize('update', $run);

        $this->logbookService->saveChecklist(
            $run,
            $request->validated()['checklist'],
            $request->input('general_notes')
        );

        return redirect()->route('logbook.start-run', $run);
    }

    /**
     * ETAPA 3: Iniciar Corrida
     */
    public function startRun(Run $run)
    {
        $this->authorize('update', $run);

        $lastKm = $this->logbookService->getLastKm($run->vehicle_id);
        $maxAllowedData = $this->logbookService->getMaxAllowedKm($run->vehicle_id, 100);

        return view('logbook.start-run', compact('run', 'lastKm', 'maxAllowedData'));
    }

    /**
     * Salva dados de início da corrida
     */
    public function storeStartRun(RunStartRequest $request, Run $run)
    {
        try {
            DB::beginTransaction();

            $run->update([
                'start_km' => $request->start_km,
                'started_at' => now(),
            ]);

            // <<< --- inicio da logica de GAP de KM --- >>>

            // 1. Obter a última corrida CONCLUÍDA para o mesmo veículo
            $lastCompletedRun = Run::where('vehicle_id', $run->vehicle_id)
                ->where('id', '!=', $run->id) // Excluir a corrida atual
                ->where('status', 'completed')
                ->whereNotNull('end_km') // Garantir que end_km não seja nulo
                ->orderBy('finished_at', 'desc') // Ordenar pela data de finalização mais recente
                ->first();

            if ($lastCompletedRun) {
                $expectedKm = $lastCompletedRun->end_km;
                $recordedKm = $run->start_km; // O KM que o usuário informou e foi salvo acima

                // 2. Verificar se há diferença e se o KM registrado é maior que o esperado
                //    (Evita criar gaps negativos se o usuário digitar um valor menor por engano,
                //     embora você possa querer registrar isso também, ajustando a lógica)
                if ($recordedKm !== $expectedKm && $recordedKm > $expectedKm) {
                    $gap = $recordedKm - $expectedKm;

                    // 3. Criar o registro na tabela run_gap_finds
                    //    Certifique-se de que o Model RunGapFind foi criado e tem os fillable corretos
                    RunGapFind::create([
                        'run_id' => $run->id, // A corrida atual onde o gap foi detectado
                        'vehicle_id' => $run->vehicle_id,
                        'user_id' => $run->user_id, // Ou Auth::id() se preferir
                        'recorded_start_km' => $recordedKm,
                        'expected_start_km' => $expectedKm,
                        'gap_km' => $gap,
                    ]);
                }
            }
            // <<< --- FIM DA LÓGICA DO GAP --- >>>

            foreach ($request->destinations as $index => $destination) {
                if (!empty(trim($destination))) {
                    RunDestination::create([
                        'run_id' => $run->id,
                        'destination' => trim($destination),
                        'order' => $index,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('logbook.finish', $run)
                ->with('success', 'Corrida iniciada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao iniciar corrida: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Erro ao iniciar corrida. Tente novamente.');
        }
    }

    /**
     * ETAPA 4: Finalizar Corrida
     */
    public function finishRun(Run $run)
    {
        $this->authorize('update', $run);

        $gasStations = GasStation::where('status', 'active')->get();
        $fuelTypes = FuelType::all();

        return view('logbook.finish-run', compact('run', 'gasStations', 'fuelTypes'));
    }

    /**
     * Finaliza a corrida
     */
    public function storeFinishRun(RunFinishRequest $request, Run $run)
    {
        $this->authorize('update', $run);

        DB::transaction(function () use ($request, $run) {
            $this->logbookService->finishRun(
                $run,
                $request->validated()['end_km'],
                $request->input('stop_point')
            );

            if ($request->boolean('add_fueling')) {
                $fuelingData = [
                    'vehicle_id' => $run->vehicle_id,
                    'user_id' => Auth::id(),
                    'run_id' => $run->id,
                    'km' => $request->input('fueling_km'),
                    'liters' => $request->input('liters'),
                    'fuel_type_id' => $request->input('fuel_type_id'),
                    'fueled_at' => now(),
                    'public_code' => 'FUEL-' . strtoupper(uniqid()),
                ];

                if ($request->input('fueling_type') === 'credenciado') {
                    $gasStation = GasStation::findOrFail($request->input('gas_station_id'));
                    $fuelingData['gas_station_id'] = $gasStation->id;
                    $fuelingData['value_per_liter'] = $gasStation->price_per_liter ?? 0; // Fallback
                    $fuelingData['value'] = $request->input('liters') * ($gasStation->price_per_liter ?? 0);
                    $fuelingData['is_manual'] = false;
                } else {
                    $fuelingData['gas_station_name'] = $request->input('gas_station_name');
                    $fuelingData['value'] = $request->input('total_value');
                    $fuelingData['value_per_liter'] = $request->input('liters') > 0 ? $request->input('total_value') / $request->input('liters') : 0;
                    $fuelingData['is_manual'] = true;
                }

                if ($request->hasFile('invoice')) {
                    $fuelingData['invoice_path'] = $request->file('invoice')->store('invoices', 'public');
                }

                Fueling::create($fuelingData);
            }
        });

        return redirect()->route('logbook.index')
            ->with('success', 'Corrida finalizada com sucesso!');
    }

    /**
     * Cancelar corrida em andamento
     */
    public function cancel(Run $run)
    {
        $this->authorize('delete', $run);

        DB::transaction(function () use ($run) {
            if ($run->checklist) {
                $run->checklist->delete();
            }

            $run->delete();
            $this->logbookService->clearUserFlowState();
        });

        return redirect()->route('logbook.vehicle-select')
            ->with('success', 'Corrida cancelada.');
    }

    /**
     * Exibe detalhes de uma corrida
     */
    public function show(Run $run)
    {
        $this->authorize('view', $run);

        $run->load([
            'vehicle.prefix',
            'vehicle.category',
            'user',
            'checklist.answers.item',
            'destinations',
            'signature.driverSignature',
            'signature.adminSignature',
        ]);

        return view('logbook.show', compact('run'));
    }
}
