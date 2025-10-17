<?php

namespace Database\Seeders;

use App\Models\GarbageRun;
use App\Models\GarbageUser;
use App\Models\GarbageVehicle;
use App\Models\GarbageWeighing;
use Illuminate\Database\Seeder;

class GarbageRunsSeeder extends Seeder
{
    public function run(): void
    {
        $gVehicle = GarbageVehicle::first();
        $gUser = GarbageUser::first();
        $weighing = GarbageWeighing::first();

        if ($gVehicle && $gUser && $weighing) {
            // Cria a corrida de coleta
            GarbageRun::updateOrCreate(
                [
                    'vehicle_id' => $gVehicle->id,
                    'user_id' => $gUser->id,
                ],
                [
                    'weighing_id' => $weighing->id,
                    'start_km' => 20000,
                    'started_at' => now()->subHour(),
                    'status' => 'in_progress',
                ]
            );
        }
    }
}
