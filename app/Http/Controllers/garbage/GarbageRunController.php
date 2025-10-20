<?php

namespace App\Http\Controllers\garbage;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChecklistRequest;
use App\Models\garbage\GarbageNeighborhood;
use App\Models\garbage\GarbageRun;
use App\Models\garbage\GarbageRunDestination;
use App\Models\garbage\GarbageVehicle;
use App\Models\checklist\ChecklistItem;
use App\Models\run\RunGapFind;
use App\Services\GarbageLogbookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        $user = Auth::user();

        $runs = GarbageRun::where('user_id', $user->id)
            ->with(['vehicle.prefix', 'vehicle.category', 'signature', 'destinations.neighborhood'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('destinations.neighborhood', function ($neighborhoodQuery) use ($search) {
                        $neighborhoodQuery->where('name', 'like', "%{$search}%");
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

        $unsignedRuns = GarbageRun::where('user_id', $user->id)
            ->where('status', 'completed')
            ->where(function ($query) {
                $query->whereDoesntHave('signature')
                    ->orWhereHas('signature', function ($q) {
                        $q->whereNull('driver_signed_at');
                    });
            })
            ->get();

        return view('garbage-logbook.index', compact('runs', 'unsignedRuns'));
    }

    public function start()
    {
        $activeRun = $this->logbookService->getUserActiveRun();

        if ($activeRun) {
            if (!$activeRun->checklist) {
                return redirect()->route('garbage-logbook.checklist', $activeRun);
            } elseif ($activeRun->destinations->isEmpty()) {
                return redirect()->route('garbage-logbook.start-run', $activeRun);
            } else {
                return redirect()->route('garbage-logbook.finish', $activeRun);
            }
        }

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

        $garbageVehicle = GarbageVehicle::with('vehicle')->findOrFail($request->vehicle_id);
        $vehicle = $garbageVehicle->vehicle;

        $availability = $this->logbookService->checkVehicleAvailability($request->vehicle_id);

        if (!$availability['available']) {
            return back()->with('error', 'Veículo em uso por outro motorista.');
        }

        $this->logbookService->saveVehicleSelection($request->vehicle_id);

        return redirect()->route('garbage-logbook.checklist-form');
    }

    public function checklistForm()
    {
        $vehicleId = $this->logbookService->getSelectedVehicleId();

        if (!$vehicleId) {
            return redirect()->route('garbage-logbook.vehicle-select')
                ->with('error', 'Selecione um veículo primeiro.');
        }

        $garbageVehicle = GarbageVehicle::with('vehicle.prefix', 'vehicle.category')->findOrFail($vehicleId);
        $vehicle = $garbageVehicle->vehicle;

        $checklistItems = ChecklistItem::all();
        $lastChecklistState = $this->logbookService->getLastChecklistState($vehicleId);

        return view('garbage-logbook.checklist', compact('vehicle', 'checklistItems', 'lastChecklistState'));
    }

    public function storeChecklistAndCreateRun(ChecklistRequest $request)
    {
        $vehicleId = $this->logbookService->getSelectedVehicleId();

        if (!$vehicleId) {
            return redirect()->route('garbage-logbook.vehicle-select')
                ->with('error', 'Selecione um veículo primeiro.');
        }

        $garbageVehicle = GarbageVehicle::with('vehicle')->findOrFail($vehicleId);
        $vehicle = $garbageVehicle->vehicle;

        $run = $this->logbookService->createRunWithChecklist(
            $vehicleId,
            $request->validated()['checklist'],
            $request->input('general_notes')
        );

        return redirect()->route('garbage-logbook.start-run', $run)
            ->with('success', 'Checklist preenchido! Agora inicie a coleta.');
    }

    public function startRun(GarbageRun $run)
    {
        $this->authorize('update', $run);

        $lastKm = $this->logbookService->getLastKm($run->vehicle_id);
        $neighborhoods = GarbageNeighborhood::all();

        return view('garbage-logbook.start-run', compact('run', 'lastKm', 'neighborhoods'));
    }

    public function storeStartRun(Request $request, GarbageRun $run)
    {
        $this->authorize('update', $run);

        $request->validate([
            'start_km' => 'required|integer|min:' . $run->vehicle->last_km,
            'neighborhoods' => 'required|array|min:1',
            'neighborhoods.*' => 'exists:garbage_neighborhoods,id',
        ]);

        try {
            DB::beginTransaction();

            $run->update([
                'start_km' => $request->start_km,
                'started_at' => now(),
            ]);

            // Salvar bairros selecionados
            foreach ($request->neighborhoods as $index => $neighborhoodId) {
                GarbageRunDestination::create([
                    'garbage_run_id' => $run->id,
                    'garbage_neighborhood_id' => $neighborhoodId,
                    'order' => $index,
                ]);
            }

            // Lógica de GAP de KM (similar ao RunController)
            $lastCompletedRun = GarbageRun::where('vehicle_id', $run->vehicle_id)
                ->where('id', '!=', $run->id)
                ->where('status', 'completed')
                ->whereNotNull('end_km')
                ->orderBy('finished_at', 'desc')
                ->first();

            if ($lastCompletedRun) {
                $expectedKm = $lastCompletedRun->end_km;
                $recordedKm = $run->start_km;

                if ($recordedKm !== $expectedKm && $recordedKm > $expectedKm) {
                    $gap = $recordedKm - $expectedKm;

                    RunGapFind::create([
                        'run_id' => $run->id,
                        'vehicle_id' => $run->vehicle_id,
                        'user_id' => $run->user_id,
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
        $this->authorize('update', $run);

        return view('garbage-logbook.finish-run', compact('run'));
    }

    public function storeFinishRun(Request $request, GarbageRun $run)
    {
        $this->authorize('update', $run);

        $request->validate([
            'end_km' => 'required|integer|min:' . $run->start_km,
            'stop_point' => 'nullable|string|max:255',
            'pesagem' => 'nullable|numeric|min:0', // Campo para pesagem
        ]);

        DB::transaction(function () use ($request, $run) {
            $this->logbookService->finishRun(
                $run,
                $request->end_km,
                $request->stop_point,
                $request->pesagem // Incluir pesagem
            );
        });

        return redirect()->route('garbage-logbook.index')
            ->with('success', 'Coleta finalizada com sucesso!');
    }

    public function show(GarbageRun $run)
    {
        $this->authorize('view', $run);

        $run->load([
            'vehicle.prefix',
            'vehicle.category',
            'user',
            'checklist.answers.item',
            'destinations.neighborhood',
            'signature.driverSignature',
            'signature.adminSignature',
        ]);

        return view('garbage-logbook.show', compact('run'));
    }

    public function cancel(GarbageRun $run)
    {
        $this->authorize('delete', $run);

        DB::transaction(function () use ($run) {
            if ($run->checklist) {
                $run->checklist->delete();
            }

            $run->delete();
            $this->logbookService->clearUserFlowState();
        });

        return redirect()->route('garbage-logbook.vehicle-select')
            ->with('success', 'Coleta cancelada.');
    }
}
