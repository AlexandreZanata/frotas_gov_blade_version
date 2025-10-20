<?php

namespace App\Services;

use App\Models\garbage\GarbageRun;
use App\Models\garbage\GarbageVehicle;
use App\Models\garbage\GarbageUser;
use App\Models\checklist\Checklist;
use App\Models\checklist\ChecklistAnswer;
use App\Models\checklist\ChecklistItem; // Importar ChecklistItem
use App\Models\user\User;
use App\Notifications\ChecklistProblemNotification; // Supondo que você tenha esta notificação
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class GarbageLogbookService
{
    // --- Funções para salvar/restaurar estado ---
    public function saveUserFlowState(string $step, ?string $runId = null): void
    {
        session([
            'garbage_logbook_flow_step' => $step,
            'garbage_logbook_flow_run_id' => $runId,
        ]);
    }

    public function getUserFlowState(): array
    {
        return [
            'step' => session('garbage_logbook_flow_step'),
            'run_id' => session('garbage_logbook_flow_run_id'),
        ];
    }
    // --- Fim Funções Estado ---

    public function getUserActiveRun()
    {
        // Garante que busca pelo GarbageUser associado ao usuário autenticado
        $garbageUser = GarbageUser::where('user_id', Auth::id())->first();
        if (!$garbageUser) {
            return null; // Se não for um usuário de coleta, não há corrida ativa para ele
        }

        return GarbageRun::where('user_id', $garbageUser->id) // Usa o ID do GarbageUser
        ->where('status', 'in_progress')
            ->with(['vehicle.vehicle.prefix', 'destinations.neighborhood']) // Corrigido para carregar vehicle.vehicle.prefix
            ->first();
    }

    public function getAvailableVehicles()
    {
        $user = Auth::user();
        $garbageUser = GarbageUser::where('user_id', $user->id)->first();

        if (!$garbageUser) {
            return collect(); // Retorna coleção vazia se não for usuário de coleta
        }

        // Carrega veículos associados a este GarbageUser
        $userVehicles = $garbageUser->vehicles()
            ->with(['vehicle.prefix', 'vehicle.category', 'vehicle.secretariat']) // Adicionado secretariat
            ->get();

        $vehicles = [];
        foreach ($userVehicles as $garbageVehicle) {
            // Verifica se $garbageVehicle->vehicle existe antes de acessar propriedades
            if (!$garbageVehicle->vehicle) {
                Log::warning("GarbageVehicle ID {$garbageVehicle->id} não tem um Vehicle relacionado.");
                continue; // Pula este veículo se a relação estiver quebrada
            }
            $vehicle = $garbageVehicle->vehicle; // Agora é seguro acessar

            $availability = $this->checkVehicleAvailability($garbageVehicle->id);

            $vehicles[] = [
                'id' => $garbageVehicle->id,
                'prefix' => $vehicle->prefix->name ?? 'N/A',
                'name' => $vehicle->name ?? 'Nome Indefinido',
                'plate' => $vehicle->plate ?? 'Placa Indefinida',
                'secretariat' => $vehicle->secretariat->name ?? 'N/A',
                'available' => $availability['available'],
                // Adiciona informações do usuário atual se não estiver disponível
                'currentUser' => $availability['currentUser'] ?? null,
                'started_at' => $availability['started_at'] ?? null,
            ];
        }

        return $vehicles;
    }

    // Método para verificar disponibilidade
    public function checkVehicleAvailability(string $garbageVehicleId): array
    {
        // Busca a corrida ativa pelo vehicle_id (que é o ID do GarbageVehicle)
        $activeRun = GarbageRun::where('vehicle_id', $garbageVehicleId)
            ->where('status', 'in_progress')
            ->with('user.user') // Carrega GarbageUser e o User principal associado
            ->first();

        if ($activeRun) {
            // Tenta acessar o nome do usuário principal através da relação aninhada
            $userName = $activeRun->user->user->name ?? 'Desconhecido';

            return [
                'available' => false,
                'currentUser' => $userName, // Nome do usuário principal
                'started_at' => $activeRun->started_at,
            ];
        }

        return ['available' => true];
    }


    /**
     * Busca o estado do último checklist preenchido para um veículo específico.
     * Ajustado para buscar checklist pela run_id na tabela checklists.
     */
    public function getLastChecklistState(string $garbageVehicleId): array
    {
        $lastRun = GarbageRun::where('vehicle_id', $garbageVehicleId)
            ->where('status', 'completed')
            ->latest('finished_at')
            ->first();

        if (!$lastRun) {
            return [];
        }

        // Busca o checklist associado à ÚLTIMA corrida
        $lastChecklist = Checklist::where('run_id', $lastRun->id)->first();

        if (!$lastChecklist) {
            return []; // Retorna vazio se a última corrida não teve checklist
        }

        // Buscar as respostas do checklist dessa corrida anterior
        $answers = ChecklistAnswer::where('checklist_id', $lastChecklist->id)
            ->get();

        if ($answers->isEmpty()) {
            return [];
        }

        // Formatar os dados para a view
        return $answers->keyBy('checklist_item_id')
            ->map(function ($answer) {
                return [
                    'status' => $answer->status,
                    'notes' => $answer->notes,
                ];
            })->toArray();
    }


    public function getAvailableNeighborhoods()
    {
        $user = Auth::user();
        $garbageUser = GarbageUser::where('user_id', $user->id)->first();

        if (!$garbageUser) {
            return collect();
        }
        // Retorna a coleção de bairros associados ao GarbageUser
        return $garbageUser->neighborhoods()->orderBy('name')->get();
    }

    public function saveVehicleSelection($vehicleId)
    {
        Session::put('garbage_selected_vehicle_id', $vehicleId);
        // Salva o estado do fluxo indicando que o próximo passo é o checklist
        $this->saveUserFlowState('checklist');
    }

    public function getSelectedVehicleId()
    {
        return Session::get('garbage_selected_vehicle_id');
    }

    public function clearVehicleSelection()
    {
        Session::forget('garbage_selected_vehicle_id');
    }

    /**
     * Cria a corrida E salva o checklist juntos (Módulo Coleta de Lixo)
     */
    public function createRunWithChecklist($vehicleId, $checklistData, $generalNotes = null): GarbageRun
    {
        // Usando DB::transaction para garantir atomicidade
        return DB::transaction(function () use ($vehicleId, $checklistData, $generalNotes) {
            // 1. Obter o ID do usuário autenticado (tabela 'users')
            $authUserId = Auth::id();

            // 2. Encontrar o registro 'GarbageUser' correspondente
            $garbageUser = GarbageUser::where('user_id', $authUserId)->firstOrFail();

            // Verifica se há problemas no checklist
            $hasDefects = false;
            foreach ($checklistData as $itemId => $data) {
                if ($data['status'] === 'problem') {
                    $hasDefects = true;
                    break;
                }
            }

            // 3. Cria a GarbageRun usando o ID do GarbageUser
            $run = GarbageRun::create([
                'vehicle_id' => $vehicleId,
                'user_id' => $garbageUser->id, // ID correto da tabela garbage_users
                'status' => 'in_progress',
                // started_at e start_km serão preenchidos na próxima etapa (startRun)
            ]);

            // 4. Cria o Checklist associado à GarbageRun, usando o ID do GarbageUser
            $checklist = Checklist::create([
                'run_id' => $run->id, // Associa à GarbageRun recém-criada
                'user_id' => $garbageUser->id, // ID correto da tabela garbage_users
                'notes' => $generalNotes,
                'has_defects' => $hasDefects, // Adicionado para indicar se há defeitos
            ]);

            // 5. Salva as respostas do checklist
            foreach ($checklistData as $itemId => $data) {
                // Validação básica do status
                if (!isset($data['status']) || !in_array($data['status'], ['ok', 'attention', 'problem'])) {
                    Log::error("Status inválido ou ausente para o item {$itemId} no checklist {$checklist->id}");
                    // Você pode lançar uma exceção aqui se preferir interromper o processo
                    throw new \InvalidArgumentException("Status inválido ou ausente para o item {$itemId}.");
                }

                ChecklistAnswer::create([
                    'checklist_id' => $checklist->id,
                    'checklist_item_id' => $itemId,
                    'status' => $data['status'],
                    // Salva notas apenas se o status for 'problem' e a nota existir/não for vazia
                    'notes' => ($data['status'] === 'problem' && !empty($data['notes'])) ? $data['notes'] : null,
                ]);

                // *** ADICIONADO: Notifica gestor se houver problemas ***
                if ($data['status'] === 'problem') {
                    $this->notifyManagerAboutProblem($run, $itemId, $data['notes'] ?? '');
                }
            }

            // 6. Limpa o ID do veículo da sessão
            $this->clearVehicleSelection();

            // *** ADICIONADO: Salva o estado do fluxo para a próxima etapa ***
            $this->saveUserFlowState('start_run', $run->id);

            return $run; // Retorna a GarbageRun criada
        });
    }

    // *** ADICIONADO: Método para notificar gestores (similar ao LogbookService) ***
    protected function notifyManagerAboutProblem(GarbageRun $run, string $itemId, string $notes): void
    {
        $item = ChecklistItem::find($itemId);
        if (!$item) return;

        // Tenta carregar a secretaria através do GarbageVehicle -> Vehicle -> Secretariat
        $secretariatId = $run->loadMissing('vehicle.vehicle.secretariat')->vehicle?->vehicle?->secretariat_id;

        if (!$secretariatId) {
            Log::error("Não foi possível encontrar a secretaria para o veículo da corrida {$run->id}");
            // Como fallback, notificar Admins Gerais se não achar a secretaria
            $managers = User::whereHas('role', fn ($q) => $q->where('name', 'general_manager'))->get();
        } else {
            // Busca gestores da secretaria específica E Admins Gerais
            $managers = User::where('secretariat_id', $secretariatId)
                ->whereHas('role', fn ($q) => $q->whereIn('name', ['sector_manager', 'general_manager']))
                ->get();

            // Se não encontrou gestores setoriais, busca apenas os Admins Gerais
            if ($managers->isEmpty()) {
                $managers = User::whereHas('role', fn ($q) => $q->where('name', 'general_manager'))->get();
            }
        }


        // Envia notificação para cada gestor encontrado
        foreach ($managers as $manager) {
            // Use a notificação apropriada, assumindo que ChecklistProblemNotification existe
            try {
                // $manager->notify(new ChecklistProblemNotification($run, $item, $notes));
                Log::info("Notificaria o gestor {$manager->name} sobre problema no item '{$item->name}' da coleta {$run->id}");
            } catch (\Exception $e) {
                Log::error("Falha ao enviar notificação para {$manager->email}: " . $e->getMessage());
            }
        }
    }


    public function getLastKm($vehicleId)
    {
        // Busca pela coluna 'vehicle_id' na tabela 'garbage_runs'
        $lastRun = GarbageRun::where('vehicle_id', $vehicleId)
            ->where('status', 'completed') // Apenas corridas completadas
            ->whereNotNull('end_km')
            ->where('end_km', '>', 0) // Garante que end_km é válido
            ->latest('finished_at') // Pega a mais recente baseada na finalização
            ->first();

        // Se não encontrar, tenta buscar KM inicial (se existir no seu modelo Vehicle)
        if (!$lastRun) {
            // $garbageVehicle = GarbageVehicle::with('vehicle')->find($vehicleId);
            // return $garbageVehicle?->vehicle?->initial_km ?? 0; // Exemplo, ajuste conforme seu model
            return 0; // Retorna 0 se não houver corridas anteriores
        }

        return $lastRun->end_km;
    }

    // Ajustado para receber $weighingId opcionalmente e usar GarbageRun
    public function finishRun(GarbageRun $run, int $endKm, ?string $stopPoint = null, ?string $weighingId = null)
    {
        $run->update([
            'end_km' => $endKm,
            'stop_point' => $stopPoint,
            'weighing_id' => $weighingId, // Atualiza com o ID da pesagem, se houver
            'finished_at' => now(),
            'status' => 'completed',
        ]);
        // Limpar estado do fluxo após finalizar
        $this->clearUserFlowState();
    }


    public function clearUserFlowState()
    {
        $this->clearVehicleSelection();
        // Adicione aqui a limpeza de outros dados de sessão, se houver
        session()->forget(['garbage_logbook_flow_step', 'garbage_logbook_flow_run_id']);
    }
}
