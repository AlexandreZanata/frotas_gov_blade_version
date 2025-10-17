<?php

namespace Database\Seeders\user;

use App\Models\user\Role;
use Illuminate\Database\Seeder;

// 1. Importe o Model

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'general_manager', 'description' => 'Gestor Geral', 'hierarchy_level' => 100],
            ['name' => 'sector_manager', 'description' => 'Gestor Setorial', 'hierarchy_level' => 50],
            ['name' => 'driver', 'description' => 'Motorista', 'hierarchy_level' => 10],
            ['name' => 'mechanic', 'description' => 'Mecânico', 'hierarchy_level' => 10],
        ];

        // 2. Itere sobre o array e use o Model para criar cada registro
        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
