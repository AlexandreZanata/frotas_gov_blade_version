<?php

namespace Database\Seeders;

use App\Models\DefaultPassword;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DefaultPasswordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DefaultPassword::create([
            'name' => 'reset_password',
            'password' => Hash::make('Frotas@2025'),
            'description' => 'Senha padrão para redefinição de usuários',
            'is_active' => true,
        ]);
    }
}
