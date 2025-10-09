<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VehicleTireLayout;

class VehicleTireLayoutSeeder extends Seeder
{
    public function run(): void
    {
        $layouts = [
            [
                'name' => 'Carro/Caminhonete (4 Pneus)',
                'layout_data' => [
                    'positions' => [
                        ['id' => 1, 'name' => 'Dianteiro Esquerdo', 'x' => 25, 'y' => 15, 'label' => 'DE'],
                        ['id' => 2, 'name' => 'Dianteiro Direito', 'x' => 75, 'y' => 15, 'label' => 'DD'],
                        ['id' => 3, 'name' => 'Traseiro Esquerdo', 'x' => 25, 'y' => 75, 'label' => 'TE'],
                        ['id' => 4, 'name' => 'Traseiro Direito', 'x' => 75, 'y' => 75, 'label' => 'TD'],
                    ]
                ]
            ],
            [
                'name' => 'Van (6 Pneus)',
                'layout_data' => [
                    'positions' => [
                        ['id' => 1, 'name' => 'Dianteiro Esquerdo', 'x' => 25, 'y' => 10, 'label' => 'DE'],
                        ['id' => 2, 'name' => 'Dianteiro Direito', 'x' => 75, 'y' => 10, 'label' => 'DD'],
                        ['id' => 3, 'name' => 'Traseiro Esquerdo Ext', 'x' => 20, 'y' => 75, 'label' => 'TEE'],
                        ['id' => 4, 'name' => 'Traseiro Esquerdo Int', 'x' => 30, 'y' => 75, 'label' => 'TEI'],
                        ['id' => 5, 'name' => 'Traseiro Direito Int', 'x' => 70, 'y' => 75, 'label' => 'TDI'],
                        ['id' => 6, 'name' => 'Traseiro Direito Ext', 'x' => 80, 'y' => 75, 'label' => 'TDE'],
                    ]
                ]
            ],
            [
                'name' => 'Caminhão Truck (10 Pneus)',
                'layout_data' => [
                    'positions' => [
                        ['id' => 1, 'name' => 'Dianteiro Esquerdo', 'x' => 25, 'y' => 10, 'label' => 'DE'],
                        ['id' => 2, 'name' => 'Dianteiro Direito', 'x' => 75, 'y' => 10, 'label' => 'DD'],
                        ['id' => 3, 'name' => 'Traseiro Esq Ext 1', 'x' => 18, 'y' => 60, 'label' => 'TE1E'],
                        ['id' => 4, 'name' => 'Traseiro Esq Int 1', 'x' => 28, 'y' => 60, 'label' => 'TE1I'],
                        ['id' => 5, 'name' => 'Traseiro Dir Int 1', 'x' => 72, 'y' => 60, 'label' => 'TD1I'],
                        ['id' => 6, 'name' => 'Traseiro Dir Ext 1', 'x' => 82, 'y' => 60, 'label' => 'TD1E'],
                        ['id' => 7, 'name' => 'Traseiro Esq Ext 2', 'x' => 18, 'y' => 80, 'label' => 'TE2E'],
                        ['id' => 8, 'name' => 'Traseiro Esq Int 2', 'x' => 28, 'y' => 80, 'label' => 'TE2I'],
                        ['id' => 9, 'name' => 'Traseiro Dir Int 2', 'x' => 72, 'y' => 80, 'label' => 'TD2I'],
                        ['id' => 10, 'name' => 'Traseiro Dir Ext 2', 'x' => 82, 'y' => 80, 'label' => 'TD2E'],
                    ]
                ]
            ],
            [
                'name' => 'Ônibus (6 Pneus)',
                'layout_data' => [
                    'positions' => [
                        ['id' => 1, 'name' => 'Dianteiro Esquerdo', 'x' => 25, 'y' => 10, 'label' => 'DE'],
                        ['id' => 2, 'name' => 'Dianteiro Direito', 'x' => 75, 'y' => 10, 'label' => 'DD'],
                        ['id' => 3, 'name' => 'Traseiro Esquerdo Ext', 'x' => 20, 'y' => 80, 'label' => 'TEE'],
                        ['id' => 4, 'name' => 'Traseiro Esquerdo Int', 'x' => 30, 'y' => 80, 'label' => 'TEI'],
                        ['id' => 5, 'name' => 'Traseiro Direito Int', 'x' => 70, 'y' => 80, 'label' => 'TDI'],
                        ['id' => 6, 'name' => 'Traseiro Direito Ext', 'x' => 80, 'y' => 80, 'label' => 'TDE'],
                    ]
                ]
            ],
            [
                'name' => 'Motocicleta (2 Pneus)',
                'layout_data' => [
                    'positions' => [
                        ['id' => 1, 'name' => 'Dianteiro', 'x' => 50, 'y' => 20, 'label' => 'D'],
                        ['id' => 2, 'name' => 'Traseiro', 'x' => 50, 'y' => 80, 'label' => 'T'],
                    ]
                ]
            ],
        ];

        foreach ($layouts as $layout) {
            VehicleTireLayout::create($layout);
        }
    }
}

