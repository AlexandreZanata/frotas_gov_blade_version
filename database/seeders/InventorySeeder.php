<?php
namespace Database\Seeders;
use App\Models\InventoryItem;
use App\Models\InventoryItemCategory;
use App\Models\InventoryMovement;
use App\Models\User;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder {
    public function run(): void {
        $categoryFiltros = InventoryItemCategory::where('name', 'Filtros')->first();
        $categoryLubrificantes = InventoryItemCategory::where('name', 'Lubrificantes')->first();
        $adminUser = User::where('email', 'admin@frotas.gov')->first();

        if ($categoryFiltros && $adminUser) {
            // 1. Cria o item com saldo 20
            $filtroOleo = InventoryItem::create([
                'category_id' => $categoryFiltros->id,
                'name' => 'Filtro de Óleo Mann-Filter W712/8',
                'sku' => 'MFW7128',
                'quantity_on_hand' => 20,
                'unit_of_measure' => 'unidade',
                'reorder_level' => 5,
            ]);

            // 2. Registra a movimentação de entrada que resultou nesse saldo
            InventoryMovement::create([
                'inventory_item_id' => $filtroOleo->id,
                'user_id' => $adminUser->id,
                'type' => 'in',
                'quantity' => 20,
                'reason' => 'Compra inicial - NF 102030',
                'movement_date' => now(),
            ]);
        }

        if ($categoryLubrificantes && $adminUser) {
            $oleoMotor = InventoryItem::create([
                'category_id' => $categoryLubrificantes->id,
                'name' => 'Óleo de Motor 5W30 Sintético',
                'sku' => 'LUB5W30',
                'quantity_on_hand' => 50,
                'unit_of_measure' => 'litro',
                'reorder_level' => 10,
            ]);

            InventoryMovement::create([
                'inventory_item_id' => $oleoMotor->id,
                'user_id' => $adminUser->id,
                'type' => 'in',
                'quantity' => 50,
                'reason' => 'Compra inicial - NF 102030',
                'movement_date' => now(),
            ]);
        }
    }
}
