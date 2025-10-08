<?php

namespace App\Services;

use App\Models\Run;
use App\Models\Vehicle;
use App\Models\Checklist;
use App\Models\ChecklistAnswer;
use App\Models\ChecklistItem;
use App\Models\User;
use App\Notifications\ChecklistProblemNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class LogbookService
{
    /**
     * Salva o estado atual do fluxo do usuário para persistência de navegação
     */
    public function saveUserFlowState(string $step, ?string $runId = null): void
    {
        session([
            'logbook_flow_step' => $step,
            'logbook_flow_run_id' => $runId,
        ]);
    }

    /**
     * Restaura o estado do fluxo do usuário
     */
    public function getUserFlowState(): array
    {
        return [
            'step' => session('logbook_flow_step'),
            'run_id' => session('logbook_flow_run_id'),
        ];
    }

    /**
     * Limpa o estado do fluxo
     */
    public function clearUserFlowState(): void
    {
        session()->forget(['logbook_flow_step', 'logbook_flow_run_id']);
    }

    /**
     * Verifica se o veículo está disponível
     */
    public function checkVehicleAvailability(string $vehicleId): array
    {
        $activeRun = Run::where('vehicle_id', $vehicleId)
            ->where('status', 'in_progress')
            ->with('user')
            ->first();

        return [
            'available' => !$activeRun,
            'active_run' => $activeRun,
        ];
    }

    /**
     * Busca o último estado do checklist de um veículo
     */
    public function getLastChecklistState(string $vehicleId): ?array
    {
        $lastRun = Run::where('vehicle_id', $vehicleId)
            ->where('status', 'completed')
            ->latest('finished_at')
            ->first();

        if (!$lastRun) {
            return null;
        }

        $checklist = Checklist::where('run_id', $lastRun->id)
            ->with('answers.item')
            ->first();

        if (!$checklist) {
            return null;
        }

        $checklistData = [];
        foreach ($checklist->answers as $answer) {
            $checklistData[$answer->checklist_item_id] = [
                'status' => $answer->status,
                'notes' => $answer->notes,
            ];
        }

        return $checklistData;
    }

    /**
     * Salva o veículo selecionado na sessão (sem criar a corrida ainda)
     */
    public function saveVehicleSelection(string $vehicleId): void
    {
        session(['selected_vehicle_id' => $vehicleId]);
    }

    /**
     * Recupera o ID do veículo selecionado da sessão
     */
    public function getSelectedVehicleId(): ?string
    {
        return session('selected_vehicle_id');
    }

    /**
     * Limpa a seleção de veículo da sessão
     */
    public function clearVehicleSelection(): void
    {
        session()->forget('selected_vehicle_id');
    }

    /**
     * Cria uma corrida inicial (MÉTODO ANTIGO - mantido para compatibilidade)
     */
    public function createRun(string $vehicleId, ?string $origin = null): Run
    {
        return DB::transaction(function () use ($vehicleId, $origin) {
            $run = Run::create([
                'vehicle_id' => $vehicleId,
                'user_id' => Auth::id(),
                'status' => 'in_progress',
                'origin' => $origin ?? 'Origem Padrão',
                'started_at' => now(),
            ]);

            $this->saveUserFlowState('checklist', $run->id);

            return $run;
        });
    }

    /**
     * NOVO MÉTODO: Cria a corrida E salva o checklist juntos
     */
    public function createRunWithChecklist(string $vehicleId, array $checklistData, ?string $generalNotes = null): Run
    {
        return DB::transaction(function () use ($vehicleId, $checklistData, $generalNotes) {
            // Cria a corrida
            $run = Run::create([
                'vehicle_id' => $vehicleId,
                'user_id' => Auth::id(),
                'status' => 'in_progress',
            ]);

            // Cria o checklist
            $checklist = Checklist::create([
                'run_id' => $run->id,
                'user_id' => Auth::id(),
                'notes' => $generalNotes,
            ]);

            // Salva as respostas do checklist
            foreach ($checklistData as $itemId => $data) {
                ChecklistAnswer::create([
                    'checklist_id' => $checklist->id,
                    'checklist_item_id' => $itemId,
                    'status' => $data['status'],
                    'notes' => $data['notes'] ?? null,
                ]);

                // Notifica gestor se houver problemas
                if ($data['status'] === 'problem') {
                    $this->notifyManagerAboutProblem($run, $itemId, $data['notes'] ?? '');
                }
            }

            // Limpa a seleção de veículo da sessão
            $this->clearVehicleSelection();

            // Salva o estado do fluxo
            $this->saveUserFlowState('start_run', $run->id);

            return $run;
        });
    }

    /**
     * Salva o checklist
     */
    public function saveChecklist(Run $run, array $checklistData, ?string $generalNotes = null): Checklist
    {
        return DB::transaction(function () use ($run, $checklistData, $generalNotes) {
            $checklist = Checklist::create([
                'run_id' => $run->id,
                'user_id' => Auth::id(),
                'notes' => $generalNotes,
            ]);

            foreach ($checklistData as $itemId => $data) {
                ChecklistAnswer::create([
                    'checklist_id' => $checklist->id,
                    'checklist_item_id' => $itemId,
                    'status' => $data['status'],
                    'notes' => $data['notes'] ?? null,
                ]);

                // Notifica gestor se houver problemas
                if ($data['status'] === 'problem') {
                    $this->notifyManagerAboutProblem($run, $itemId, $data['notes'] ?? '');
                }
            }

            $this->saveUserFlowState('start_run', $run->id);

            return $checklist;
        });
    }

    /**
     * Inicia a corrida efetivamente
     */
    public function startRun(Run $run, int $startKm, string $destination): Run
    {
        $run->update([
            'start_km' => $startKm,
            'destination' => $destination,
            'started_at' => now(),
        ]);

        $this->saveUserFlowState('finish_run', $run->id);

        return $run;
    }

    /**
     * Finaliza a corrida
     */
    public function finishRun(Run $run, int $endKm, ?string $stopPoint = null): Run
    {
        $run->update([
            'end_km' => $endKm,
            'stop_point' => $stopPoint,
            'finished_at' => now(),
            'status' => 'completed',
        ]);

        $this->clearUserFlowState();

        return $run;
    }

    /**
     * Busca o último KM registrado de um veículo (end_km da última corrida finalizada)
     */
    public function getLastKm(string $vehicleId): int
    {
        $lastRun = Run::where('vehicle_id', $vehicleId)
            ->where('status', 'completed')
            ->whereNotNull('end_km')
            ->where('end_km', '>', 0)
            ->latest('finished_at')
            ->first();

        return $lastRun ? $lastRun->end_km : 0;
    }

    /**
     * Calcula a média de quilometragem das últimas 10 corridas
     */
    public function getAverageKmFromLastRuns(string $vehicleId, int $numberOfRuns = 10): ?float
    {
        $runs = Run::where('vehicle_id', $vehicleId)
            ->where('status', 'completed')
            ->whereNotNull('start_km')
            ->whereNotNull('end_km')
            ->latest('finished_at')
            ->limit($numberOfRuns)
            ->get();

        if ($runs->count() < $numberOfRuns) {
            return null; // Não há corridas suficientes
        }

        $totalKm = $runs->sum(function ($run) {
            return $run->end_km - $run->start_km;
        });

        return $totalKm / $runs->count();
    }

    /**
     * Calcula o KM máximo permitido baseado na autonomia do veículo
     * Após 10 corridas, usa a média de autonomia (km/L) x capacidade do tanque
     */
    public function getMaxAllowedKm(string $vehicleId, float $percentageAdjustment = 100): ?array
    {
        $vehicle = Vehicle::with('fuelType')->findOrFail($vehicleId);

        // Verifica se o veículo tem pelo menos 10 corridas completadas
        $completedRunsCount = Run::where('vehicle_id', $vehicleId)
            ->where('status', 'completed')
            ->whereNotNull('start_km')
            ->whereNotNull('end_km')
            ->count();

        if ($completedRunsCount < 10) {
            return [
                'has_limit' => false,
                'runs_count' => $completedRunsCount,
                'message' => "Ainda não há limite. Complete " . (10 - $completedRunsCount) . " corridas para ativar o cálculo automático.",
            ];
        }

        // Calcula a média de KM rodados nas últimas 10 corridas
        $averageKm = $this->getAverageKmFromLastRuns($vehicleId, 10);

        if (!$averageKm) {
            return [
                'has_limit' => false,
                'message' => 'Não foi possível calcular a média de quilometragem.',
            ];
        }

        // Aplica o percentual de ajuste (ex: 100%, 200%)
        $maxKm = $averageKm * ($percentageAdjustment / 100);

        return [
            'has_limit' => true,
            'max_km' => round($maxKm, 2),
            'average_km' => round($averageKm, 2),
            'percentage' => $percentageAdjustment,
            'tank_capacity' => $vehicle->fuel_tank_capacity,
            'runs_count' => $completedRunsCount,
        ];
    }

    /**
     * Valida se o KM inicial está dentro do limite permitido
     */
    public function validateStartKm(string $vehicleId, int $startKm, float $percentageAdjustment = 100): array
    {
        $lastKm = $this->getLastKm($vehicleId);
        $maxAllowedData = $this->getMaxAllowedKm($vehicleId, $percentageAdjustment);

        // Se o KM inicial é menor que o último KM, retorna erro
        if ($startKm < $lastKm) {
            return [
                'valid' => false,
                'message' => "O KM atual ($startKm km) não pode ser menor que o último KM registrado ($lastKm km).",
            ];
        }

        // Se não há limite definido ainda (menos de 10 corridas)
        if (!$maxAllowedData['has_limit']) {
            return [
                'valid' => true,
                'warning' => $maxAllowedData['message'],
            ];
        }

        // Calcula a diferença entre o KM atual e o último KM
        $kmDifference = $startKm - $lastKm;

        // Verifica se a diferença excede o limite
        if ($kmDifference > $maxAllowedData['max_km']) {
            return [
                'valid' => false,
                'message' => "A diferença de quilometragem ($kmDifference km) excede o máximo permitido ({$maxAllowedData['max_km']} km) baseado na média das últimas 10 corridas.",
                'max_allowed' => $maxAllowedData['max_km'],
                'km_difference' => $kmDifference,
            ];
        }

        return [
            'valid' => true,
            'km_difference' => $kmDifference,
            'max_allowed' => $maxAllowedData['max_km'],
        ];
    }

    /**
     * Notifica o gestor sobre problemas no checklist
     */
    protected function notifyManagerAboutProblem(Run $run, string $itemId, string $notes): void
    {
        // Busca o item do checklist
        $item = ChecklistItem::find($itemId);

        if (!$item) {
            return;
        }

        // Busca os gestores da secretaria do veículo
        // Usando whereHas com 'role' (singular) ao invés de 'roles' (plural)
        $managers = User::where('secretariat_id', $run->vehicle->secretariat_id)
            ->whereHas('role', function ($query) {
                $query->whereIn('name', ['gestor_secretaria', 'gestor_setorial', 'admin']);
            })
            ->get();

        // Se não encontrou gestores, notifica todos os admins do sistema como fallback
        if ($managers->isEmpty()) {
            $managers = User::whereHas('role', function ($query) {
                $query->where('name', 'admin');
            })->get();
        }

        // Envia notificação para cada gestor
        foreach ($managers as $manager) {
            $manager->notify(new ChecklistProblemNotification($run, $item, $notes));
        }
    }

    /**
     * Busca veículos da secretaria do usuário
     */
    public function getAvailableVehicles(): \Illuminate\Database\Eloquent\Collection
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return Vehicle::where('secretariat_id', $user->secretariat_id)
            ->with(['prefix', 'status', 'category'])
            ->get();
    }

    /**
     * Verifica se existe uma corrida em andamento para o usuário
     */
    public function getUserActiveRun(): ?Run
    {
        return Run::where('user_id', Auth::id())
            ->where('status', 'in_progress')
            ->with('vehicle.prefix')
            ->first();
    }
}
