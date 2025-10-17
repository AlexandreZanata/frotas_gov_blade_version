<?php

namespace Database\Seeders;

use App\Models\maintenance\InventoryItem;
use App\Models\maintenance\InventoryItemCategory;
use Illuminate\Database\Seeder;

class TireInventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Garante que a categoria "Pneus" exista
        $tireCategory = InventoryItemCategory::firstOrCreate(
            ['name' => 'Pneus'],
            ['description' => 'Pneus para veículos da frota']
        );

        // Cria alguns tipos de pneus como itens de inventário
        InventoryItem::firstOrCreate(
            ['name' => 'Pneu Aro 15 Goodyear Assurance'],
            [
                'category_id' => $tireCategory->id,
                'sku' => 'PN-GY-15-ASS',
                'quantity_on_hand' => 10,
                'unit_of_measure' => 'unidade',
                'unit_cost' => 450.00,
                'reorder_level' => 4,
            ]
        );

        InventoryItem::firstOrCreate(
            ['name' => 'Pneu Aro 16 Pirelli Scorpion'],
            [
                'category_id' => $tireCategory->id,
                'sku' => 'PN-PI-16-SCO',
                'quantity_on_hand' => 8,
                'unit_of_measure' => 'unidade',
                'unit_cost' => 620.00,
                'reorder_level' => 2,
            ]
        );
    }
}
