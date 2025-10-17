<?php

namespace Database\Seeders;

use App\Models\GarbageType;
use App\Models\GarbageVehicle;
use App\Models\GarbageWeighbridgeOperator;
use App\Models\GarbageWeighing;
use App\Models\User;
use Illuminate\Database\Seeder;

class GarbageWeighingSeeder extends Seeder
{
    public function run(): void
    {
        $requester = User::first();
        $operator = GarbageWeighbridgeOperator::first();
        $garbageVehicle = GarbageVehicle::first();
        $garbageType = GarbageType::where('name', 'Lixo Úmido')->first();

        if ($requester && $operator && $garbageVehicle && $garbageType) {
            GarbageWeighing::updateOrCreate(
                [
                    'garbage_vehicle_id' => $garbageVehicle->id,
                    'requester_id' => $requester->id,
                ],
                [
                    'garbage_type_id' => $garbageType->id,
                    'weight_kg' => 1250.75,
                    'weighed_at' => now(),
                    'weighbridge_operator_id' => $operator->id,
                ]
            );
        }
    }
}
