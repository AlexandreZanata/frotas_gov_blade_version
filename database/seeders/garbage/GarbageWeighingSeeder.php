<?php

namespace Database\Seeders\garbage;

use App\Models\garbage\GarbageType;
use App\Models\garbage\GarbageVehicle;
use App\Models\garbage\GarbageWeighbridgeOperator;
use App\Models\garbage\GarbageWeighing;
use App\Models\user\User;
use Illuminate\Database\Seeder;

class GarbageWeighingSeeder extends Seeder
{
    public function run(): void
    {
        $requester = User::first();
        $operator = GarbageWeighbridgeOperator::first();
        $garbageVehicle = GarbageVehicle::with('currentTare')->first(); // Carrega a tara atual
        $garbageType = GarbageType::where('name', 'Lixo Úmido')->first();

        if ($requester && $operator && $garbageVehicle && $garbageType && $garbageVehicle->currentTare) {
            GarbageWeighing::updateOrCreate(
                [
                    'garbage_vehicle_id' => $garbageVehicle->id,
                    'requester_id' => $requester->id,
                ],
                [
                    'garbage_type_id' => $garbageType->id,
                    'gross_weight_kg' => 7850.75,
                    'tare_weight_kg' => $garbageVehicle->currentTare->tare_weight_kg, // Pega a tara atual
                    'weighed_at' => now(),
                    'weighbridge_operator_id' => $operator->id,
                ]
            );
        }
    }
}
