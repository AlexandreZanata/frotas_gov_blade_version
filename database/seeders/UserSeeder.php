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
        $this->command->info('🔄 Criando usuários...');

        // Buscar roles
        $adminRole = Role::where('name', 'general_manager')->firstOrFail();
        $driverRole = Role::where('name', 'driver')->firstOrFail();
        $sectorManagerRole = Role::where('name', 'sector_manager')->firstOrFail();
        $mechanicRole = Role::where('name', 'mechanic')->firstOrFail();

        // Buscar secretarias
        $adminSecretariat = Secretariat::where('name', 'Administração')->firstOrFail();
        $saudeSecretariat = Secretariat::where('name', 'Saúde')->firstOrFail();
        $educacaoSecretariat = Secretariat::where('name', 'Educação')->firstOrFail();

        // 1. Admin Geral
        $admin = User::create([
            'name' => 'Admin Geral',
            'email' => 'admin@frotas.gov',
            'cpf' => '00000000000',
            'phone' => '(11) 99999-0001',
            'cnh' => '12345678901',
            'cnh_expiration_date' => now()->addYears(2)->format('Y-m-d'),
            'cnh_category' => 'AB',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'secretariat_id' => $adminSecretariat->id,
            'status' => 'active',
        ]);
        $this->command->info("✓ Criado: {$admin->name} ({$admin->email})");

        // 2. Gestor Setorial - Saúde
        $gestorSaude = User::create([
            'name' => 'Carlos Silva',
            'email' => 'gestor.saude@frotas.gov',
            'cpf' => '11111111111',
            'phone' => '(11) 99999-0002',
            'cnh' => '12345678902',
            'cnh_expiration_date' => now()->addYears(2)->format('Y-m-d'),
            'cnh_category' => 'B',
            'password' => Hash::make('password'),
            'role_id' => $sectorManagerRole->id,
            'secretariat_id' => $saudeSecretariat->id,
            'status' => 'active',
        ]);
        $this->command->info("✓ Criado: {$gestorSaude->name} ({$gestorSaude->email})");

        // 3. Motorista - Saúde
        $motoristaSaude = User::create([
            'name' => 'João Motorista',
            'email' => 'motorista.saude@frotas.gov',
            'cpf' => '22222222222',
            'phone' => '(11) 99999-0003',
            'cnh' => '12345678903',
            'cnh_expiration_date' => now()->addYears(2)->format('Y-m-d'),
            'cnh_category' => 'D',
            'password' => Hash::make('password'),
            'role_id' => $driverRole->id,
            'secretariat_id' => $saudeSecretariat->id,
            'status' => 'active',
        ]);
        $this->command->info("✓ Criado: {$motoristaSaude->name} ({$motoristaSaude->email})");

        // 4. Motorista - Educação
        $motoristaEducacao = User::create([
            'name' => 'Maria Condutora',
            'email' => 'motorista.educacao@frotas.gov',
            'cpf' => '33333333333',
            'phone' => '(11) 99999-0004',
            'cnh' => '12345678904',
            'cnh_expiration_date' => now()->addYears(2)->format('Y-m-d'),
            'cnh_category' => 'D',
            'password' => Hash::make('password'),
            'role_id' => $driverRole->id,
            'secretariat_id' => $educacaoSecretariat->id,
            'status' => 'active',
        ]);
        $this->command->info("✓ Criado: {$motoristaEducacao->name} ({$motoristaEducacao->email})");

        // 5. Mecânico - Administração
        $mecanico = User::create([
            'name' => 'Pedro Mecânico',
            'email' => 'mecanico@frotas.gov',
            'cpf' => '44444444444',
            'phone' => '(11) 99999-0005',
            'cnh' => '12345678905',
            'cnh_expiration_date' => now()->addYears(2)->format('Y-m-d'),
            'cnh_category' => 'B',
            'password' => Hash::make('password'),
            'role_id' => $mechanicRole->id,
            'secretariat_id' => $adminSecretariat->id,
            'status' => 'active',
        ]);
        $this->command->info("✓ Criado: {$mecanico->name} ({$mecanico->email})");

        $this->command->info('');
        $this->command->info('✅ UserSeeder executado com sucesso!');
        $this->command->info('   - Total de usuários criados: ' . User::count());
        $this->command->info('');
        $this->command->info('📋 Credenciais de acesso:');
        $this->command->info('   Email: admin@frotas.gov | Senha: password');
        $this->command->info('   Email: motorista.saude@frotas.gov | Senha: password');
        $this->command->info('   Email: motorista.educacao@frotas.gov | Senha: password');
    }
}
