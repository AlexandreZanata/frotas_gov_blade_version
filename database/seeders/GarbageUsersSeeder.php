<?php

namespace Database\Seeders;

use App\Models\GarbageUser;
use App\Models\User;
use Illuminate\Database\Seeder;

class GarbageUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Encontra o primeiro usuário que tem a role 'driver'
        $driver = User::whereHas('role', function ($query) {
            $query->where('name', 'driver');
        })->first();

        // Se encontrar um motorista, cria o registro em garbage_users
        if ($driver) {
            GarbageUser::updateOrCreate(
                ['user_id' => $driver->id]
            );
        }
    }
}
