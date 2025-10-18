<?php

namespace Database\Seeders\user;

use App\Models\user\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Adicionamos a nova role aqui
        $roles = [
            ['name' => 'general_manager', 'description' => 'Gestor Geral', 'hierarchy_level' => 100],
            ['name' => 'sector_manager', 'description' => 'Gestor Setorial', 'hierarchy_level' => 50],
            ['name' => 'garbage_manager', 'description' => 'Gestor Resíduos', 'hierarchy_level' => 25], // <-- NOVO
            ['name' => 'driver', 'description' => 'Motorista', 'hierarchy_level' => 10],
            ['name' => 'mechanic', 'description' => 'Mecânico', 'hierarchy_level' => 10],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                $role
            );
        }
    }
}
