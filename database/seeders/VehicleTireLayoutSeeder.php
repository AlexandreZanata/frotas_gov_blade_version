<?php

namespace Database\Seeders;

use App\Models\VehicleTireLayout;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehicleTireLayoutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('vehicle_tire_layouts')->truncate();

        VehicleTireLayout::create([
            'name' => 'Carro de Passeio (4 Pneus)',
            'layout_data' => json_encode([
                'positions' => [
                    ['id' => 1, 'label' => 'D.E.', 'x' => 15, 'y' => 20], // Dianteiro Esquerdo
                    ['id' => 2, 'label' => 'D.D.', 'x' => 65, 'y' => 20], // Dianteiro Direito
                    ['id' => 3, 'label' => 'T.E.', 'x' => 15, 'y' => 60], // Traseiro Esquerdo
                    ['id' => 4, 'label' => 'T.D.', 'x' => 65, 'y' => 60], // Traseiro Direito
                ]
            ])
        ]);

        VehicleTireLayout::create([
            'name' => 'Caminhão Toco (6 Pneus)',
            'layout_data' => json_encode([
                'positions' => [
                    ['id' => 1, 'label' => 'D.E.', 'x' => 15, 'y' => 15],
                    ['id' => 2, 'label' => 'D.D.', 'x' => 65, 'y' => 15],
                    ['id' => 3, 'label' => 'T.E.I', 'x' => 5, 'y' => 60],  // Traseiro Esquerdo Interno
                    ['id' => 4, 'label' => 'T.E.E', 'x' => 20, 'y' => 60], // Traseiro Esquerdo Externo
                    ['id' => 5, 'label' => 'T.D.I', 'x' => 55, 'y' => 60], // Traseiro Direito Interno
                    ['id' => 6, 'label' => 'T.D.E', 'x' => 70, 'y' => 60], // Traseiro Direito Externo
                ]
            ])
        ]);
    }
}
