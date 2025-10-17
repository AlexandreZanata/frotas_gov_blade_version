<?php

namespace Database\Seeders;

use App\Models\GarbageMaintenanceTareVehicle;
use App\Models\GarbageVehicle;
use App\Models\User;
use Illuminate\Database\Seeder;

class GarbageMaintenanceTareVehicleSeeder extends Seeder
{
    public function run(): void
    {
        $garbageVehicle = GarbageVehicle::first();
        $user = User::first();

        if ($garbageVehicle && $user) {
            GarbageMaintenanceTareVehicle::create([
                'garbage_vehicle_id' => $garbageVehicle->id,
                'user_id' => $user->id,
                'tare_weight_kg' => 4500.50,
                'calibrated_at' => now()->subDay(),
                'notes' => 'Calibração inicial do veículo.',
            ]);
        }
    }
}
