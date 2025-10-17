<?php

namespace App\Services;

use App\Models\maintenance\Tire;
use App\Models\maintenance\TireEvent;
use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\VehicleTireLayout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TireService
{
    /**
     * Obter estatísticas para o dashboard
     */
    public function getDashboardStats()
    {
        $totalTires = Tire::count();
        $criticalCount = Tire::where('condition', 'Crítico')->count();
        $attentionCount = Tire::where('condition', 'Atenção')->count();
        $inUseCount = Tire::where('status', 'Em Uso')->count();
        $inStockCount = Tire::where('status', 'Em Estoque')->count();
        $inMaintenanceCount = Tire::whereIn('status', ['Em Manutenção', 'Recapagem'])->count();

        // Calcular vida útil média da frota
        $averageLifespan = Tire::where('status', 'Em Uso')
            ->selectRaw('AVG((lifespan_km - current_km) / lifespan_km * 100) as avg_life')
            ->first()
            ->avg_life ?? 0;

        $totalVehicles = Vehicle::whereHas('tires')->count();

        return [
            'total' => $totalTires,
            'critical_count' => $criticalCount,
            'attention_count' => $attentionCount,
            'in_use' => $inUseCount,
            'in_stock' => $inStockCount,
            'maintenance' => $inMaintenanceCount,
            'average_lifespan' => round($averageLifespan, 2),
            'total_vehicles' => $totalVehicles,
        ];
    }

    /**
     * Obter layout de pneus para um veículo
     */
    public function getVehicleLayout(Vehicle $vehicle)
    {
        // Mapear categoria de veículo para layout
        $layoutMap = [
            'Carro' => 1,
            'Caminhonete' => 1,
            'Van' => 2,
            'Caminhão' => 3,
            'Ônibus' => 4,
            'Motocicleta' => 5,
        ];

        $categoryName = $vehicle->category->name ?? 'Carro';
        $layoutId = $layoutMap[$categoryName] ?? 1;

        $layout = VehicleTireLayout::find($layoutId);

        if (!$layout) {
            // Layout padrão (4 pneus)
            return $this->getDefaultLayout();
        }

        return $layout;
    }

    /**
     * Layout padrão para veículos sem layout específico
     */
    private function getDefaultLayout()
    {
        return (object)[
            'id' => 0,
            'name' => 'Padrão (4 Pneus)',
            'layout_data' => [
                'positions' => [
                    ['id' => 1, 'name' => 'Dianteiro Esquerdo', 'x' => 20, 'y' => 10],
                    ['id' => 2, 'name' => 'Dianteiro Direito', 'x' => 80, 'y' => 10],
                    ['id' => 3, 'name' => 'Traseiro Esquerdo', 'x' => 20, 'y' => 80],
                    ['id' => 4, 'name' => 'Traseiro Direito', 'x' => 80, 'y' => 80],
                ]
            ]
        ];
    }

    /**
     * Executar rodízio de pneus
     */
    public function rotateTires($vehicleId, $tire1Id, $tire2Id, $position1, $position2, $kmAtEvent)
    {
        return DB::transaction(function() use ($vehicleId, $tire1Id, $tire2Id, $position1, $position2, $kmAtEvent) {
            $tire1 = Tire::findOrFail($tire1Id);
            $tire2 = Tire::findOrFail($tire2Id);
            $vehicle = Vehicle::findOrFail($vehicleId);

            // Validações
            if ($tire1->current_vehicle_id != $vehicleId || $tire2->current_vehicle_id != $vehicleId) {
                throw new \Exception('Ambos os pneus devem estar no mesmo veículo');
            }

            // Trocar posições
            $tire1->update(['current_position' => $position2]);
            $tire2->update(['current_position' => $position1]);

            // Registrar eventos
            $this->registerEvent(
                $tire1,
                'Rodízio',
                "Rodízio: Posição {$position1} → {$position2}",
                $vehicleId,
                $kmAtEvent
            );

            $this->registerEvent(
                $tire2,
                'Rodízio',
                "Rodízio: Posição {$position2} → {$position1}",
                $vehicleId,
                $kmAtEvent
            );

            return [
                'tire1' => $tire1->fresh(),
                'tire2' => $tire2->fresh(),
            ];
        });
    }

    /**
     * Substituir pneu
     */
    public function replaceTire($vehicleId, $oldTireId, $newTireId, $position, $kmAtEvent, $reason)
    {
        return DB::transaction(function() use ($vehicleId, $oldTireId, $newTireId, $position, $kmAtEvent, $reason) {
            $vehicle = Vehicle::findOrFail($vehicleId);
            $newTire = Tire::findOrFail($newTireId);

            // Validar que o novo pneu está disponível
            if ($newTire->status != 'Em Estoque') {
                throw new \Exception('O pneu selecionado não está disponível no estoque');
            }

            // Remover pneu antigo se existir
            if ($oldTireId) {
                $oldTire = Tire::findOrFail($oldTireId);
                $oldTire->update([
                    'status' => 'Em Estoque',
                    'current_vehicle_id' => null,
                    'current_position' => null,
                ]);

                $this->registerEvent(
                    $oldTire,
                    'Troca',
                    "Pneu removido do veículo. Motivo: {$reason}",
                    $vehicleId,
                    $kmAtEvent
                );
            }

            // Instalar novo pneu
            $newTire->update([
                'status' => 'Em Uso',
                'current_vehicle_id' => $vehicleId,
                'current_position' => $position,
            ]);

            $this->registerEvent(
                $newTire,
                'Instalação',
                "Pneu instalado na posição {$position}. Motivo: {$reason}",
                $vehicleId,
                $kmAtEvent
            );

            return [
                'old_tire' => $oldTireId ? Tire::find($oldTireId) : null,
                'new_tire' => $newTire->fresh(),
            ];
        });
    }

    /**
     * Remover pneu do veículo
     */
    public function removeTire($tireId, $newStatus, $kmAtEvent, $reason)
    {
        return DB::transaction(function() use ($tireId, $newStatus, $kmAtEvent, $reason) {
            $tire = Tire::findOrFail($tireId);

            if ($tire->status != 'Em Uso') {
                throw new \Exception('Este pneu não está em uso');
            }

            $vehicleId = $tire->current_vehicle_id;

            $tire->update([
                'status' => $newStatus,
                'current_vehicle_id' => null,
                'current_position' => null,
            ]);

            $eventType = match($newStatus) {
                'Recapagem' => 'Recapagem',
                'Em Manutenção' => 'Manutenção',
                'Descartado' => 'Descarte',
                default => 'Troca',
            };

            $this->registerEvent(
                $tire,
                $eventType,
                "Pneu removido. Motivo: {$reason}. Novo status: {$newStatus}",
                $vehicleId,
                $kmAtEvent
            );

            return $tire->fresh();
        });
    }

    /**
     * Registrar evento de pneu
     */
    public function registerEvent(Tire $tire, $eventType, $description, $vehicleId = null, $kmAtEvent = null)
    {
        return TireEvent::create([
            'tire_id' => $tire->id,
            'user_id' => Auth::id(),
            'vehicle_id' => $vehicleId ?? $tire->current_vehicle_id,
            'event_type' => $eventType,
            'description' => $description,
            'km_at_event' => $kmAtEvent,
            'event_date' => now(),
        ]);
    }

    /**
     * Calcular condição do pneu baseado na quilometragem
     */
    public function calculateCondition(Tire $tire)
    {
        $percentageUsed = ($tire->current_km / $tire->lifespan_km) * 100;

        if ($percentageUsed >= 90) {
            return 'Crítico';
        } elseif ($percentageUsed >= 70) {
            return 'Atenção';
        } elseif ($percentageUsed >= 30) {
            return 'Bom';
        } else {
            return 'Novo';
        }
    }

    /**
     * Atualizar quilometragem de todos os pneus de um veículo
     */
    public function updateVehicleTiresKm($vehicleId, $currentKm, $previousKm)
    {
        $kmDiff = $currentKm - $previousKm;

        if ($kmDiff <= 0) {
            return;
        }

        $tires = Tire::where('current_vehicle_id', $vehicleId)
            ->where('status', 'Em Uso')
            ->get();

        foreach ($tires as $tire) {
            $newKm = $tire->current_km + $kmDiff;
            $tire->update([
                'current_km' => $newKm,
                'condition' => $this->calculateCondition($tire),
            ]);
        }
    }
}
