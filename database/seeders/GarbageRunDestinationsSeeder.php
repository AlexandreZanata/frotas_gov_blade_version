<?php

namespace Database\Seeders;

use App\Models\GarbageNeighborhood;
use App\Models\GarbageRun;
use App\Models\GarbageRunDestination;
use Illuminate\Database\Seeder;

class GarbageRunDestinationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pega a primeira corrida de coleta e o primeiro bairro como exemplo
        $garbageRun = GarbageRun::first();
        $neighborhood = GarbageNeighborhood::where('name', 'Centro')->first();

        // Garante que ambos existam antes de criar os destinos
        if ($garbageRun && $neighborhood) {

            // Destino 1: Um bairro
            GarbageRunDestination::updateOrCreate(
                [
                    'garbage_run_id' => $garbageRun->id,
                    'type' => 'neighborhood',
                    'garbage_neighborhood_id' => $neighborhood->id,
                ],
                [
                    'order' => 1,
                ]
            );

            // Destino 2: Um comentário
            GarbageRunDestination::updateOrCreate(
                [
                    'garbage_run_id' => $garbageRun->id,
                    'type' => 'comment',
                    'comment' => 'Coleta especial na praça da matriz.',
                ],
                [
                    'order' => 2,
                ]
            );
        }
    }
}
