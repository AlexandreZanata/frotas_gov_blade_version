<?php

namespace Database\Seeders\user;

use App\Models\user\User;
use App\Models\user\UserPhoto;
use Illuminate\Database\Seeder;

class UserPhotoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Encontra o usuário 'Admin Geral' que já foi criado
        $adminUser = User::where('email', 'admin@frotas.gov')->first();

        // Só executa se o usuário Admin existir
        if ($adminUser) {
            // Adiciona uma foto de perfil
            UserPhoto::create([
                'user_id' => $adminUser->id,
                'photo_type' => 'profile',
                'path' => 'user_photos/profile_admin_geral.jpg', // Caminho simulado
            ]);

            // Adiciona uma foto de CNH (frente)
            UserPhoto::create([
                'user_id' => $adminUser->id,
                'photo_type' => 'cnh_front',
                'path' => 'user_photos/cnh_admin_geral.jpg', // Caminho simulado
            ]);
        }
    }
}
