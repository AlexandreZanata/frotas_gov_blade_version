<?php

namespace Database\Seeders\user;

use App\Models\Manager\GeneralManager;
use App\Models\Manager\ManagerStatus;
use App\Models\user\Role;
use App\Models\user\User;
use Illuminate\Database\Seeder;

class GeneralManagerSeeder extends Seeder
{
    public function run(): void
    {
        $generalManagerRole = Role::where('name', 'general_manager')->first();

        $managerUser = User::where('role_id', $generalManagerRole->id)->first();

        // Pega o status "Ativo"
        $activeStatus = ManagerStatus::where('name', 'active')->first();

        if ($managerUser && $activeStatus) {
            GeneralManager::firstOrCreate(
                ['user_id' => $managerUser->id], // Evita duplicatas
                [
                    'manager_status_id' => $activeStatus->id,
                ]
            );
        }
    }
}
