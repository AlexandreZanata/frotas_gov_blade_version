<?php

namespace Database\Seeders\vehicle;

use App\Models\Vehicle\VehicleStatus;
use Illuminate\Database\Seeder;

class VehicleStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['name' => 'Disponível'],
            ['name' => 'Em Uso'],
            ['name' => 'Em Manutenção'],
            ['name' => 'Bloqueado']
        ];

        foreach ($statuses as $status) {
            VehicleStatus::create($status);
        }
    }
}
