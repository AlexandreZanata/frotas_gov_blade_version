<?php
namespace Database\Seeders;
use App\Models\InventoryItemCategory;
use Illuminate\Database\Seeder;

class InventoryCategorySeeder extends Seeder {
    public function run(): void {
        InventoryItemCategory::create(['name' => 'Filtros']);
        InventoryItemCategory::create(['name' => 'Lubrificantes']);
        InventoryItemCategory::create(['name' => 'Pneus']);
        InventoryItemCategory::create(['name' => 'Peças de Freio']);
    }
}
