<?php

namespace Database\Seeders;

use App\Models\fuel\FuelingVehicleExpense;
use App\Models\Vehicle\Vehicle;
use Illuminate\Database\Seeder;

class FuelingVehicleExpenseSeeder extends Seeder
{
    public function run(): void
    {
        $vehicles = Vehicle::all();

        foreach ($vehicles as $vehicle) {
            FuelingVehicleExpense::updateOrCreate(
                ['vehicle_id' => $vehicle->id],
                ['total_fuel_cost' => 0.00] // Garante que comece zerado
            );
        }
    }
}
