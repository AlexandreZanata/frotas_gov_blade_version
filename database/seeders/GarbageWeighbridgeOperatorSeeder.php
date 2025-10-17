<?php

namespace Database\Seeders;

use App\Models\GarbageWeighbridgeOperator;
use App\Models\User;
use Illuminate\Database\Seeder;

class GarbageWeighbridgeOperatorSeeder extends Seeder
{
    public function run(): void
    {
        // Pega o primeiro gestor geral para ser o operador da balança
        $manager = User::whereHas('role', function ($query) {
            $query->where('name', 'general_manager');
        })->first();

        if ($manager) {
            GarbageWeighbridgeOperator::updateOrCreate(
                ['user_id' => $manager->id]
            );
        }
    }
}
