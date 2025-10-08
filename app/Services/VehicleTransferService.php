<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Models\VehicleTransfer;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VehicleTransferService
{
    /**
     * Criar uma nova solicitação de transferência
     */
    public function createTransferRequest(array $data, User $requester): VehicleTransfer
    {
        return DB::transaction(function () use ($data, $requester) {
            $vehicle = Vehicle::findOrFail($data['vehicle_id']);

            // Se o requisitante é Gestor Geral, aprova automaticamente
            $isGeneralManager = $requester->role->name === 'general_manager';

            $transfer = VehicleTransfer::create([
                'vehicle_id' => $vehicle->id,
                'origin_secretariat_id' => $vehicle->secretariat_id,
                'destination_secretariat_id' => $data['destination_secretariat_id'],
                'requester_id' => $requester->id,
                'approver_id' => $isGeneralManager ? $requester->id : null,
                'type' => $data['type'],
                'status' => $isGeneralManager ? 'approved' : 'pending',
                'start_date' => $data['start_date'] ?? null,
                'end_date' => $data['end_date'] ?? null,
                'request_notes' => $data['request_notes'] ?? null,
                'processed_at' => $isGeneralManager ? now() : null,
            ]);

            // Se aprovado automaticamente (Gestor Geral), já transfere o veículo
            if ($isGeneralManager) {
                $this->executeTransfer($transfer);
            }

            Log::info('Transferência de veículo criada', [
                'transfer_id' => $transfer->id,
                'vehicle_id' => $vehicle->id,
                'requester_id' => $requester->id,
                'auto_approved' => $isGeneralManager,
            ]);

            return $transfer->fresh(['vehicle', 'originSecretariat', 'destinationSecretariat']);
        });
    }

    /**
     * Aprovar uma transferência
     */
    public function approveTransfer(VehicleTransfer $transfer, User $approver, ?string $notes = null): VehicleTransfer
    {
        if (!$transfer->isPending()) {
            throw new \Exception('Esta transferência não está pendente.');
        }

        if (!$transfer->canBeApprovedBy($approver)) {
            throw new \Exception('Você não tem permissão para aprovar esta transferência.');
        }

        return DB::transaction(function () use ($transfer, $approver, $notes) {
            $transfer->update([
                'status' => 'approved',
                'approver_id' => $approver->id,
                'approver_notes' => $notes,
                'processed_at' => now(),
            ]);

            // Executar a transferência do veículo
            $this->executeTransfer($transfer);

            Log::info('Transferência aprovada', [
                'transfer_id' => $transfer->id,
                'approver_id' => $approver->id,
            ]);

            return $transfer->fresh(['vehicle', 'originSecretariat', 'destinationSecretariat']);
        });
    }

    /**
     * Rejeitar uma transferência
     */
    public function rejectTransfer(VehicleTransfer $transfer, User $approver, string $notes): VehicleTransfer
    {
        if (!$transfer->isPending()) {
            throw new \Exception('Esta transferência não está pendente.');
        }

        if (!$transfer->canBeApprovedBy($approver)) {
            throw new \Exception('Você não tem permissão para rejeitar esta transferência.');
        }

        $transfer->update([
            'status' => 'rejected',
            'approver_id' => $approver->id,
            'approver_notes' => $notes,
            'processed_at' => now(),
        ]);

        Log::info('Transferência rejeitada', [
            'transfer_id' => $transfer->id,
            'approver_id' => $approver->id,
        ]);

        return $transfer->fresh(['vehicle', 'originSecretariat', 'destinationSecretariat']);
    }

    /**
     * Devolver um veículo emprestado temporariamente
     */
    public function returnVehicle(VehicleTransfer $transfer, User $user, ?string $notes = null): VehicleTransfer
    {
        if (!$transfer->canBeReturnedBy($user)) {
            throw new \Exception('Você não tem permissão para devolver este veículo.');
        }

        return DB::transaction(function () use ($transfer, $user, $notes) {
            // Retornar o veículo para a secretaria de origem
            $vehicle = $transfer->vehicle;
            $vehicle->update([
                'secretariat_id' => $transfer->origin_secretariat_id,
            ]);

            $transfer->update([
                'status' => 'returned',
                'returned_at' => now(),
                'approver_notes' => $notes ? ($transfer->approver_notes . "\n\nNota de devolução: " . $notes) : $transfer->approver_notes,
            ]);

            Log::info('Veículo devolvido', [
                'transfer_id' => $transfer->id,
                'vehicle_id' => $vehicle->id,
                'user_id' => $user->id,
            ]);

            return $transfer->fresh(['vehicle', 'originSecretariat', 'destinationSecretariat']);
        });
    }

    /**
     * Executar a transferência física do veículo
     */
    protected function executeTransfer(VehicleTransfer $transfer): void
    {
        $vehicle = $transfer->vehicle;

        // Atualizar a secretaria do veículo
        $vehicle->update([
            'secretariat_id' => $transfer->destination_secretariat_id,
        ]);

        Log::info('Veículo transferido', [
            'vehicle_id' => $vehicle->id,
            'from' => $transfer->origin_secretariat_id,
            'to' => $transfer->destination_secretariat_id,
        ]);
    }

    /**
     * Obter transferências que o usuário pode aprovar
     */
    public function getPendingTransfersForApproval(User $user)
    {
        $query = VehicleTransfer::with(['vehicle.prefix', 'originSecretariat', 'destinationSecretariat', 'requester'])
            ->pending()
            ->orderBy('created_at', 'desc');

        // Gestor Geral vê tudo
        if ($user->role->name === 'general_manager') {
            return $query->get();
        }

        // Gestor Setorial vê apenas da sua secretaria
        if ($user->role->name === 'sector_manager') {
            return $query->where('origin_secretariat_id', $user->secretariat_id)->get();
        }

        // Outros usuários não veem nada para aprovar
        return collect();
    }

    /**
     * Obter transferências ativas (aprovadas e temporárias que podem ser devolvidas)
     */
    public function getActiveTransfersForReturn(User $user)
    {
        $query = VehicleTransfer::with(['vehicle.prefix', 'originSecretariat', 'destinationSecretariat', 'requester'])
            ->approved()
            ->temporary()
            ->whereNull('returned_at')
            ->orderBy('end_date', 'asc');

        // Gestor Geral vê tudo
        if ($user->role->name === 'general_manager') {
            return $query->get();
        }

        // Gestor Setorial vê apenas da sua secretaria de origem
        if ($user->role->name === 'sector_manager') {
            return $query->where('origin_secretariat_id', $user->secretariat_id)->get();
        }

        // Usuário comum vê apenas os que ele solicitou
        return $query->where('requester_id', $user->id)->get();
    }

    /**
     * Obter histórico de transferências
     */
    public function getTransferHistory(User $user)
    {
        $query = VehicleTransfer::with(['vehicle.prefix', 'originSecretariat', 'destinationSecretariat', 'requester', 'approver'])
            ->orderBy('created_at', 'desc');

        // Gestor Geral vê tudo
        if ($user->role->name === 'general_manager') {
            return $query->paginate(20);
        }

        // Gestor Setorial vê da sua secretaria
        if ($user->role->name === 'sector_manager') {
            return $query->where(function ($q) use ($user) {
                $q->where('origin_secretariat_id', $user->secretariat_id)
                  ->orWhere('destination_secretariat_id', $user->secretariat_id);
            })->paginate(20);
        }

        // Usuário comum vê apenas os seus
        return $query->where('requester_id', $user->id)->paginate(20);
    }
}

