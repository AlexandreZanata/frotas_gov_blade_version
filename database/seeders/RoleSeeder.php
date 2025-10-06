<?php

namespace Database\Seeders;

use App\Models\Role; // 1. Importe o Model
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'general_manager', 'description' => 'Gestor Geral'],
            ['name' => 'sector_manager', 'description' => 'Gestor Setorial'],
            ['name' => 'driver', 'description' => 'Motorista'],
            ['name' => 'mechanic', 'description' => 'Mecânico'],
        ];

        // 2. Itere sobre o array e use o Model para criar cada registro
        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
