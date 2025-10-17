<?php

namespace Database\Seeders\garbage;

use App\Models\garbage\GarbageMaintenanceTareVehicle;
use App\Models\garbage\GarbageVehicle;
use App\Models\user\User;
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
