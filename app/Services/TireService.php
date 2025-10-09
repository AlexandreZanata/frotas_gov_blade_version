<?php

namespace App\Services;

use App\Models\InventoryMovement;
use App\Models\Tire;
use App\Models\TireEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TireService
{
    /**
     * Registra a entrada de um novo pneu no estoque e no histórico.
     */
    public function registerNewTire(Tire $tire, array $data): void
    {
        DB::transaction(function () use ($tire, $data) {
            // 1. Cria o evento de cadastro
            TireEvent::create([
                'tire_id' => $tire->id,
                'user_id' => Auth::id(),
                'event_type' => 'Cadastro',
                'description' => 'Pneu cadastrado no sistema.',
                'event_date' => now(),
            ]);

            // 2. Cria o movimento de entrada no estoque
            InventoryMovement::create([
                'inventory_item_id' => $tire->inventory_item_id,
                'user_id' => Auth::id(),
                'type' => 'in',
                'quantity' => 1,
                'reason' => 'Compra / Novo pneu cadastrado: ' . $tire->serial_number,
                'movement_date' => $data['purchase_date'],
            ]);

            // 3. Atualiza a quantidade no item de inventário
            $tire->inventoryItem->increment('quantity_on_hand');
        });
    }

    /**
     * Instala um pneu do estoque em um veículo.
     */
    public function installTire(Tire $tire, int $vehicleId, int $position, int $vehicleKm): void
    {
        DB::transaction(function () use ($tire, $vehicleId, $position, $vehicleKm) {
            // 1. Atualiza os dados do pneu
            $tire->update([
                'current_vehicle_id' => $vehicleId,
                'current_position' => $position,
                'status' => 'Em Uso',
            ]);

            // 2. Cria o evento de instalação
            TireEvent::create([
                'tire_id' => $tire->id,
                'user_id' => Auth::id(),
                'vehicle_id' => $vehicleId,
                'event_type' => 'Instalação',
                'description' => "Pneu instalado na posição {$position}.",
                'km_at_event' => $vehicleKm,
                'event_date' => now(),
            ]);

            // 3. Cria o movimento de SAÍDA do estoque
            InventoryMovement::create([
                'inventory_item_id' => $tire->inventory_item_id,
                'user_id' => Auth::id(),
                'type' => 'out',
                'quantity' => 1,
                'reason' => 'Instalado no veículo. Pneu S/N: ' . $tire->serial_number,
                'movement_date' => now(),
            ]);

            // 4. Atualiza a quantidade no item de inventário
            $tire->inventoryItem->decrement('quantity_on_hand');
        });
    }

    /**
     * Remove um pneu de um veículo e o retorna para o estoque.
     */
    public function moveToStock(Tire $tire, int $vehicleKm): void
    {
        DB::transaction(function () use ($tire, $vehicleKm) {
            $vehicleId = $tire->current_vehicle_id;

            // 1. Atualiza os dados do pneu
            $tire->update([
                'current_vehicle_id' => null,
                'current_position' => null,
                'status' => 'Em Estoque',
            ]);

            // 2. Cria o evento
            TireEvent::create([
                'tire_id' => $tire->id,
                'user_id' => Auth::id(),
                'vehicle_id' => $vehicleId,
                'event_type' => 'Manutenção',
                'description' => 'Pneu removido do veículo e retornado ao estoque.',
                'km_at_event' => $vehicleKm,
                'event_date' => now(),
            ]);

            // 3. Cria o movimento de ENTRADA no estoque
            InventoryMovement::create([
                'inventory_item_id' => $tire->inventory_item_id,
                'user_id' => Auth::id(),
                'type' => 'in',
                'quantity' => 1,
                'reason' => 'Retorno do veículo para estoque. Pneu S/N: ' . $tire->serial_number,
                'movement_date' => now(),
            ]);

            // 4. Atualiza a quantidade no item de inventário
            $tire->inventoryItem->increment('quantity_on_hand');
        });
    }
}
