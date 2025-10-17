<?php

namespace Database\Seeders;

use App\Models\GarbageVehicle;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class GarbageVehiclesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicle = Vehicle::first(); // Pega o primeiro veículo para exemplo
        if ($vehicle) {
            GarbageVehicle::create([
                'vehicle_id' => $vehicle->id,
            ]);
        }
    }
}
