<?php

namespace Database\Seeders;

use App\Models\garbage\GarbageManager;
use App\Models\user\User;
use Illuminate\Database\Seeder;

class GarbageManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Encontra o usuário com o email do gestor de resíduos
        $managerUser = User::where('email', 'gestor.residuos@frotas.gov')->first();

        // Se o usuário existir, o adiciona na tabela de gestores
        if ($managerUser) {
            GarbageManager::updateOrCreate(
                ['user_id' => $managerUser->id]
            );
        }
    }
}
