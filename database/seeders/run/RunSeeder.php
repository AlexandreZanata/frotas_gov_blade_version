<?php

namespace Database\Seeders\run;

use App\Models\run\Run;
use App\Models\run\RunDestination;
use App\Models\user\User;
use App\Models\Vehicle\Vehicle;
use Illuminate\Database\Seeder;

class RunSeeder extends Seeder
{
    public function run(): void
    {
        $driver = User::where('email', 'admin@frotas.gov')->first();
        $vehicle = Vehicle::where('plate', 'BRA2E19')->first();

        if ($driver && $vehicle) {
            // Criar a corrida sem o campo destination
            $run = Run::create([
                'vehicle_id' => $vehicle->id,
                'user_id' => $driver->id,
                'start_km' => 15000,
                'started_at' => now(),
                'status' => 'in_progress',
            ]);

            // Criar o destino na nova tabela
            RunDestination::create([
                'run_id' => $run->id,
                'destination' => 'Hospital Central',
                'order' => 0,
            ]);
        }

        // Adicionar mais exemplos se necessário
        $vehicle2 = Vehicle::where('plate', 'BRA0A12')->first();

        if ($driver && $vehicle2) {
            $run2 = Run::create([
                'vehicle_id' => $vehicle2->id,
                'user_id' => $driver->id,
                'start_km' => 20000,
                'started_at' => now()->subHours(2),
                'end_km' => 20045,
                'finished_at' => now()->subHours(1),
                'status' => 'completed',
            ]);

            // Múltiplos destinos para exemplo
            RunDestination::create([
                'run_id' => $run2->id,
                'destination' => 'Secretaria de Educação',
                'order' => 0,
            ]);

            RunDestination::create([
                'run_id' => $run2->id,
                'destination' => 'Escola Municipal',
                'order' => 1,
            ]);

            RunDestination::create([
                'run_id' => $run2->id,
                'destination' => 'Prefeitura',
                'order' => 2,
            ]);
        }
    }
}
