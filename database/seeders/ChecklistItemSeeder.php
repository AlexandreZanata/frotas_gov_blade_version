<?php

namespace Database\Seeders;

use App\Models\ChecklistItem;
use Illuminate\Database\Seeder;

class ChecklistItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'name' => 'Combustível',
                'description' => 'Verifique o nível de combustível do veículo'
            ],
            [
                'name' => 'Água',
                'description' => 'Verifique o nível de água do radiador'
            ],
            [
                'name' => 'Óleo',
                'description' => 'Verifique o nível de óleo do motor'
            ],
            [
                'name' => 'Bateria',
                'description' => 'Verifique o estado da bateria e terminais'
            ],
            [
                'name' => 'Pneus',
                'description' => 'Verifique o estado e calibragem dos pneus'
            ],
            [
                'name' => 'Filtro de Ar',
                'description' => 'Verifique o estado do filtro de ar'
            ],
            [
                'name' => 'Lâmpadas',
                'description' => 'Verifique o funcionamento de todas as lâmpadas (faróis, setas, freio)'
            ],
            [
                'name' => 'Sistema Elétrico',
                'description' => 'Verifique o funcionamento do sistema elétrico geral'
            ],
        ];

        foreach ($items as $item) {
            ChecklistItem::firstOrCreate(
                ['name' => $item['name']],
                ['description' => $item['description']]
            );
        }
    }
}

