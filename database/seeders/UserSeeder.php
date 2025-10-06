<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Secretariat;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('name', 'general_manager')->firstOrFail();
        $adminSecretariat = Secretariat::where('name', 'Administração')->firstOrFail();

        User::create([
            'name' => 'Admin Geral',
            'email' => 'admin@frotas.gov',
            'cpf' => '00000000000',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'secretariat_id' => $adminSecretariat->id,
            'status' => 'active',
        ]);
    }
}
