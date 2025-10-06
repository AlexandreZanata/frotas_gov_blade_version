<?php

namespace Database\Seeders;

use App\Models\VehicleStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
