<?php

namespace App\Observers;

use App\Models\fuel\Fueling;
use App\Models\fuel\FuelingVehicleExpense;

class FuelingObserver
{
    /**
     * Handle the Fueling "created" event.
     */
    public function created(Fueling $fueling): void
    {
        // Usa o valor total diretamente
        $cost = $fueling->value ?? 0.00; // Pega o valor total do abastecimento

        if ($cost > 0) {
            $expense = FuelingVehicleExpense::firstOrCreate(
                ['vehicle_id' => $fueling->vehicle_id]
            );

            // Adiciona o custo ao total
            $expense->increment('total_fuel_cost', $cost);
        }
    }

    /**
     * Handle the Fueling "deleted" event.
     */
    public function deleted(Fueling $fueling): void
    {
        // Usa o valor total diretamente
        $cost = $fueling->value ?? 0.00;

        if ($cost > 0) {
            $expense = FuelingVehicleExpense::where('vehicle_id', $fueling->vehicle_id)->first();

            if ($expense) {
                // Remove o custo do total
                $expense->decrement('total_fuel_cost', $cost);
            }
        }
    }

    /**
     * Handle the Fueling "updating" event. (Opcional, mas recomendado)
     * Se o valor do abastecimento for editado, ajusta a despesa total.
     */
    public function updating(Fueling $fueling): void
    {
        // Verifica se o valor total foi modificado
        if ($fueling->isDirty('value')) {
            $originalCost = $fueling->getOriginal('value') ?? 0.00;
            $newCost = $fueling->value ?? 0.00;
            $difference = $newCost - $originalCost;

            if ($difference != 0) {
                $expense = FuelingVehicleExpense::where('vehicle_id', $fueling->vehicle_id)->first();
                if ($expense) {
                    $expense->increment('total_fuel_cost', $difference);
                }
            }
        }
    }
}
