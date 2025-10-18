<?php

namespace Database\Seeders\user;

use App\Models\user\CnhCategory;
use App\Models\user\Role;
use App\Models\user\Secretariat;
use App\Models\user\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔄 Criando/Atualizando usuários...');

        // Buscar roles
        $adminRole = Role::where('name', 'general_manager')->firstOrFail();
        $garbageManagerRole = Role::where('name', 'garbage_manager')->firstOrFail(); // <-- NOVO
        $sectorManagerRole = Role::where('name', 'sector_manager')->firstOrFail();
        $driverRole = Role::where('name', 'driver')->firstOrFail();
        $mechanicRole = Role::where('name', 'mechanic')->firstOrFail();

        // Buscar secretarias
        $adminSecretariat = Secretariat::where('name', 'Administração')->firstOrFail();
        $saudeSecretariat = Secretariat::where('name', 'Saúde')->firstOrFail();
        $educacaoSecretariat = Secretariat::where('name', 'Educação')->firstOrFail();

        // Buscar categorias CNH
        $categoriaAB = CnhCategory::where('code', 'AB')->firstOrFail();
        $categoriaB = CnhCategory::where('code', 'B')->firstOrFail();
        $categoriaD = CnhCategory::where('code', 'D')->firstOrFail();

        // 1. Admin Geral
        $admin = User::updateOrCreate(
            ['email' => 'admin@frotas.gov'],
            [
                'name' => 'Admin Geral',
                'cpf' => '00000000000',
                'phone' => '(11) 99999-0001',
                'cnh' => '12345678901',
                'cnh_expiration_date' => now()->addYears(2)->format('Y-m-d'),
                'cnh_category_id' => $categoriaAB->id,
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
                'secretariat_id' => $adminSecretariat->id,
                'status' => 'active',
            ]
        );
        $this->command->info("✓ Criado/Atualizado: {$admin->name} ({$admin->email})");

        // 2. Gestor de Resíduos (NOVO)
        $garbageManager = User::updateOrCreate(
            ['email' => 'gestor.residuos@frotas.gov'],
            [
                'name' => 'Ana Resíduos',
                'cpf' => '55555555555',
                'phone' => '(11) 99999-0006',
                'cnh' => '12345678906',
                'cnh_expiration_date' => now()->addYears(3)->format('Y-m-d'),
                'cnh_category_id' => $categoriaB->id,
                'password' => Hash::make('password'),
                'role_id' => $garbageManagerRole->id,
                'secretariat_id' => $adminSecretariat->id,
                'status' => 'active',
            ]
        );
        $this->command->info("✓ Criado/Atualizado: {$garbageManager->name} ({$garbageManager->email})");

        // 3. Gestor Setorial - Saúde
        $gestorSaude = User::updateOrCreate(
            ['email' => 'gestor.saude@frotas.gov'],
            [
                'name' => 'Carlos Silva',
                'cpf' => '11111111111',
                'phone' => '(11) 99999-0002',
                'cnh' => '12345678902',
                'cnh_expiration_date' => now()->addYears(2)->format('Y-m-d'),
                'cnh_category_id' => $categoriaB->id,
                'password' => Hash::make('password'),
                'role_id' => $sectorManagerRole->id,
                'secretariat_id' => $saudeSecretariat->id,
                'status' => 'active',
            ]
        );
        $this->command->info("✓ Criado/Atualizado: {$gestorSaude->name} ({$gestorSaude->email})");

        // 4. Motorista - Saúde
        $motoristaSaude = User::updateOrCreate(
            ['email' => 'motorista.saude@frotas.gov'],
            [
                'name' => 'João Motorista',
                'cpf' => '22222222222',
                'phone' => '(11) 99999-0003',
                'cnh' => '12345678903',
                'cnh_expiration_date' => now()->addYears(2)->format('Y-m-d'),
                'cnh_category_id' => $categoriaD->id,
                'password' => Hash::make('password'),
                'role_id' => $driverRole->id,
                'secretariat_id' => $saudeSecretariat->id,
                'status' => 'active',
            ]
        );
        $this->command->info("✓ Criado/Atualizado: {$motoristaSaude->name} ({$motoristaSaude->email})");

        // 5. Motorista - Educação
        $motoristaEducacao = User::updateOrCreate(
            ['email' => 'motorista.educacao@frotas.gov'],
            [
                'name' => 'Maria Condutora',
                'cpf' => '33333333333',
                'phone' => '(11) 99999-0004',
                'cnh' => '12345678904',
                'cnh_expiration_date' => now()->addYears(2)->format('Y-m-d'),
                'cnh_category_id' => $categoriaD->id,
                'password' => Hash::make('password'),
                'role_id' => $driverRole->id,
                'secretariat_id' => $educacaoSecretariat->id,
                'status' => 'active',
            ]
        );
        $this->command->info("✓ Criado/Atualizado: {$motoristaEducacao->name} ({$motoristaEducacao->email})");

        // 6. Mecânico - Administração
        $mecanico = User::updateOrCreate(
            ['email' => 'mecanico@frotas.gov'],
            [
                'name' => 'Pedro Mecânico',
                'cpf' => '44444444444',
                'phone' => '(11) 99999-0005',
                'cnh' => '12345678905',
                'cnh_expiration_date' => now()->addYears(2)->format('Y-m-d'),
                'cnh_category_id' => $categoriaB->id,
                'password' => Hash::make('password'),
                'role_id' => $mechanicRole->id,
                'secretariat_id' => $adminSecretariat->id,
                'status' => 'active',
            ]
        );
        $this->command->info("✓ Criado/Atualizado: {$mecanico->name} ({$mecanico->email})");

        $this->command->info('');
        $this->command->info('✅ UserSeeder executado com sucesso!');
        $this->command->info('   - Total de usuários no sistema: ' . User::count());
        $this->command->info('');
        $this->command->info('📋 Credenciais de acesso (senha padrão: "password"):');
        $this->command->info('   - admin@frotas.gov');
        $this->command->info('   - gestor.residuos@frotas.gov'); // <-- NOVO
        $this->command->info('   - motorista.saude@frotas.gov');
        $this->command->info('   - motorista.educacao@frotas.gov');
    }
}
