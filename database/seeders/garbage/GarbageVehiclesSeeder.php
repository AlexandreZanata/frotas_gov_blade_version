<?php

namespace Database\Seeders\garbage;

use App\Models\garbage\GarbageVehicle;
use App\Models\Vehicle\Vehicle;
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
