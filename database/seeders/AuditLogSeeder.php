<?php

namespace Database\Seeders;

use App\Models\auditlog\AuditLog;
use App\Models\user\User;
use App\Models\Vehicle\Vehicle;
use Illuminate\Database\Seeder;

class AuditLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Encontra o usuário 'Admin Geral' e o veículo 'FORD RANGER'
        $adminUser = User::where('email', 'admin@frotas.gov')->first();
        $vehicle = Vehicle::where('plate', 'BRA2E19')->first();

        // Só executa se ambos os registros existirem
        if ($adminUser && $vehicle) {
            // 1. Simula o LOG DE CRIAÇÃO do veículo
            AuditLog::create([
                'user_id' => $adminUser->id,
                'action' => 'created',
                'auditable_id' => $vehicle->id,
                'auditable_type' => Vehicle::class,
                'description' => 'Veículo cadastrado via seeder inicial.',
                'new_values' => $vehicle->toJson(), // Registra todos os dados do veículo no momento da criação
                'ip_address' => '127.0.0.1',
            ]);

            // 2. Simula um LOG DE ATUALIZAÇÃO de status
            AuditLog::create([
                'user_id' => $adminUser->id,
                'action' => 'updated',
                'auditable_id' => $vehicle->id,
                'auditable_type' => Vehicle::class,
                'description' => 'Status do veículo alterado manualmente.',
                'old_values' => ['status_id' => '0199ba2e...'], // ID simulado do status antigo
                'new_values' => ['status_id' => $vehicle->status_id], // ID do status atual
                'ip_address' => '127.0.0.1',
            ]);
        }
    }
}
