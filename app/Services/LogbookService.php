<?php

namespace App\Services;

use App\Models\Run;
use App\Models\Vehicle;
use App\Models\Checklist;
use App\Models\ChecklistAnswer;
use App\Models\User;
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
                'origin' => 'A definir',
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
            'finished_at' => now(),
            'status' => 'completed',
        ]);

        $this->clearUserFlowState();

        return $run;
    }

    /**
     * Busca o último KM registrado de um veículo
     */
    public function getLastKm(string $vehicleId): int
    {
        $lastRun = Run::where('vehicle_id', $vehicleId)
            ->where('status', 'completed')
            ->whereNotNull('end_km')
            ->latest('finished_at')
            ->first();

        return $lastRun ? $lastRun->end_km : 0;
    }

    /**
     * Notifica o gestor sobre problemas no checklist
     */
    protected function notifyManagerAboutProblem(Run $run, string $itemId, string $notes): void
    {
        // Implementar lógica de notificação
        // Exemplo: enviar email, notificação no sistema, etc.
    }

    /**
     * Busca veículos da secretaria do usuário
     */
    public function getAvailableVehicles(): \Illuminate\Database\Eloquent\Collection
    {
        return Vehicle::where('secretariat_id', Auth::user()->secretariat_id)
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

