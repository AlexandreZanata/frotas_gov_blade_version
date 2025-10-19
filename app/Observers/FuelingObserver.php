<?php

namespace App\Observers;

use App\Models\fuel\Fueling;
use App\Models\fuel\FuelingVehicleExpense;
use App\Models\fuel\FuelingGasStationExpense; 

class FuelingObserver
{
    /**
     * Handle the Fueling "created" event.
     */
    public function created(Fueling $fueling): void
    {
        $cost = $fueling->value ?? 0.00;

        if ($cost > 0) {
            // Atualiza a despesa do veículo (lógica existente)
            $vehicleExpense = FuelingVehicleExpense::firstOrCreate(
                ['vehicle_id' => $fueling->vehicle_id]
            );
            $vehicleExpense->increment('total_fuel_cost', $cost);

            // 2. Adiciona a lógica para atualizar a despesa do posto
            $gasStationExpense = FuelingGasStationExpense::firstOrCreate(
                ['gas_station_id' => $fueling->gas_station_id]
            );
            $gasStationExpense->increment('total_fuel_cost', $cost);
        }
    }

    /**
     * Handle the Fueling "deleted" event.
     */
    public function deleted(Fueling $fueling): void
    {
        $cost = $fueling->value ?? 0.00;

        if ($cost > 0) {
            // Atualiza a despesa do veículo (lógica existente)
            $vehicleExpense = FuelingVehicleExpense::where('vehicle_id', $fueling->vehicle_id)->first();
            if ($vehicleExpense) {
                $vehicleExpense->decrement('total_fuel_cost', $cost);
            }

            // 3. Adiciona a lógica para decrementar a despesa do posto
            $gasStationExpense = FuelingGasStationExpense::where('gas_station_id', $fueling->gas_station_id)->first();
            if ($gasStationExpense) {
                $gasStationExpense->decrement('total_fuel_cost', $cost);
            }
        }
    }

    /**
     * Handle the Fueling "updating" event.
     */
    public function updating(Fueling $fueling): void
    {
        if ($fueling->isDirty('value')) {
            $originalCost = $fueling->getOriginal('value') ?? 0.00;
            $newCost = $fueling->value ?? 0.00;
            $difference = $newCost - $originalCost;

            if ($difference != 0) {
                // Atualiza a despesa do veículo (lógica existente)
                $vehicleExpense = FuelingVehicleExpense::where('vehicle_id', $fueling->vehicle_id)->first();
                if ($vehicleExpense) {
                    $vehicleExpense->increment('total_fuel_cost', $difference);
                }

                // 4. Adiciona a lógica para ajustar a despesa do posto
                $gasStationExpense = FuelingGasStationExpense::where('gas_station_id', $fueling->gas_station_id)->first();
                if ($gasStationExpense) {
                    $gasStationExpense->increment('total_fuel_cost', $difference);
                }
            }
        }
        // 5. Opcional: Adiciona lógica se o gas_station_id for alterado
        if ($fueling->isDirty('gas_station_id')) {
            $originalGasStationId = $fueling->getOriginal('gas_station_id');
            $newGasStationId = $fueling->gas_station_id;
            $cost = $fueling->value ?? 0.00; // Ou o valor original se value não mudou? Depende da regra.

            if ($cost > 0) {
                // Decrementa do posto antigo
                $oldGasStationExpense = FuelingGasStationExpense::where('gas_station_id', $originalGasStationId)->first();
                if ($oldGasStationExpense) {
                    $oldGasStationExpense->decrement('total_fuel_cost', $cost);
                }

                // Incrementa no posto novo
                $newGasStationExpense = FuelingGasStationExpense::firstOrCreate(
                    ['gas_station_id' => $newGasStationId]
                );
                $newGasStationExpense->increment('total_fuel_cost', $cost);
            }
        }

        // 6. Opcional: Adiciona lógica se o vehicle_id for alterado (para manter consistência com o exemplo)
        if ($fueling->isDirty('vehicle_id')) {
            $originalVehicleId = $fueling->getOriginal('vehicle_id');
            $newVehicleId = $fueling->vehicle_id;
            $cost = $fueling->value ?? 0.00; // Ou o valor original se value não mudou?

            if ($cost > 0) {
                // Decrementa do veículo antigo
                $oldVehicleExpense = FuelingVehicleExpense::where('vehicle_id', $originalVehicleId)->first();
                if ($oldVehicleExpense) {
                    $oldVehicleExpense->decrement('total_fuel_cost', $cost);
                }

                // Incrementa no veículo novo
                $newVehicleExpense = FuelingVehicleExpense::firstOrCreate(
                    ['vehicle_id' => $newVehicleId]
                );
                $newVehicleExpense->increment('total_fuel_cost', $cost);
            }
        }
    }
}
