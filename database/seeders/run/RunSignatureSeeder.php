<?php

namespace Database\Seeders\run;

use App\Models\run\Run;
use App\Models\run\RunDestination;
use App\Models\run\RunSignature;
use Illuminate\Database\Seeder;

class RunSignatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Encontra a corrida através do primeiro destino
        $runDestination = RunDestination::where('destination', 'Hospital Central')->first();

        if ($runDestination) {
            // 2. Obtém a corrida através do relacionamento
            $run = $runDestination->run;

            // 3. Encontra o motorista (User) associado a essa corrida
            $driver = $run->user;

            // 4. Através do motorista, encontra sua assinatura digital
            $driverSignature = $driver->digitalSignature;

            // 5. Se a assinatura for encontrada, cria o registro na tabela de assinaturas da corrida
            if ($driverSignature) {
                RunSignature::create([
                    'run_id' => $run->id,
                    'driver_signature_id' => $driverSignature->id,
                    'driver_signed_at' => now()->addMinutes(15), // Simula que ele assinou 15 min após criar a corrida

                    // A assinatura do admin começa como nula, pois ainda não foi confirmada
                    'admin_signature_id' => null,
                    'admin_signed_at' => null,
                ]);

                $this->command->info("✅ Assinatura da corrida criada para: Hospital Central");
            } else {
                $this->command->warn("⚠️  Assinatura digital do motorista não encontrada para a corrida");
            }
        } else {
            $this->command->error("❌ Corrida com destino 'Hospital Central' não encontrada");

            // Alternativa: buscar qualquer corrida existente
            $this->createSignatureForAnyRun();
        }
    }

    /**
     * Cria assinatura para qualquer corrida existente (fallback)
     */
    private function createSignatureForAnyRun(): void
    {
        $run = Run::first();

        if (!$run) {
            $this->command->error("❌ Nenhuma corrida encontrada no sistema");
            return;
        }

        $driver = $run->user;
        $driverSignature = $driver->digitalSignature;

        if ($driverSignature) {
            RunSignature::create([
                'run_id' => $run->id,
                'driver_signature_id' => $driverSignature->id,
                'driver_signed_at' => now()->addMinutes(15),
                'admin_signature_id' => null,
                'admin_signed_at' => null,
            ]);

            $destinations = $run->destinations->pluck('destination')->implode(', ');
            $this->command->info("✅ Assinatura criada para corrida com destinos: {$destinations}");
        } else {
            $this->command->warn("⚠️  Nenhuma assinatura digital encontrada para o motorista");
        }
    }
}
