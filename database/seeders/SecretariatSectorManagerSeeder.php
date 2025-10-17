<?php

namespace Database\Seeders;

use App\Models\Manager\ManagerStatus;
use App\Models\Manager\SecretariatSectorManager;
use App\Models\Role;
use App\Models\Secretariat;
use App\Models\User;
use Illuminate\Database\Seeder;

class SecretariatSectorManagerSeeder extends Seeder
{
    public function run(): void
    {
        // Pega a role 'sector_manager'
        $sectorManagerRole = Role::where('name', 'sector_manager')->first();

        // Pega o primeiro usuário com essa role que ainda não foi designado
        $managerUser = User::where('role_id', $sectorManagerRole->id)
            ->whereDoesntHave('sectorManagerDetails') // 'sectorManagerDetails' precisa ser criado na Model User
            ->first();

        $secretariat = Secretariat::first();
        $activeStatus = ManagerStatus::where('name', 'active')->first();

        // Só cria o registro se todos os dados existirem
        if ($managerUser && $secretariat && $activeStatus) {
            SecretariatSectorManager::create([
                'user_id' => $managerUser->id,
                'secretariat_id' => $secretariat->id,
                'manager_status_id' => $activeStatus->id,
            ]);
        }
    }
}
