<?php

namespace Database\Seeders;

use App\Models\Run;
use App\Models\RunSignature;
use App\Models\User;
use Illuminate\Database\Seeder;

class RunSignatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Encontra a corrida específica criada pelo RunSeeder
        $run = Run::where('destination', 'Hospital Central')->first();

        if ($run) {
            // 2. Encontra o motorista (User) associado a essa corrida
            $driver = $run->user;

            // 3. Através do motorista, encontra sua assinatura digital
            $driverSignature = $driver->digitalSignature;

            // 4. Se a assinatura for encontrada, cria o registro na tabela de assinaturas da corrida
            if ($driverSignature) {
                RunSignature::create([
                    'run_id' => $run->id,
                    'driver_signature_id' => $driverSignature->id,
                    'driver_signed_at' => now()->addMinutes(15), // Simula que ele assinou 15 min após criar a corrida

                    // A assinatura do admin começa como nula, pois ainda não foi confirmada
                    'admin_signature_id' => null,
                    'admin_signed_at' => null,
                ]);
            }
        }
    }
}
