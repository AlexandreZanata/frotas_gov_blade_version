<?php

namespace Database\Seeders\garbage;

use App\Models\garbage\GarbageRun;
use App\Models\garbage\GarbageUser;
use App\Models\garbage\GarbageVehicle;
use App\Models\garbage\GarbageWeighing;
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
