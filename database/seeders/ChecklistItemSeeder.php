<?php
namespace Database\Seeders;
use App\Models\ChecklistItem;
use Illuminate\Database\Seeder;
class ChecklistItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name' => 'Nível do Óleo do Motor'],
            ['name' => 'Nível da Água do Radiador'],
            ['name' => 'Calibragem dos Pneus'],
            ['name' => 'Luzes e Setas'],
            ['name' => 'Freios'],
        ];
        foreach ($items as $item) { ChecklistItem::create($item); }
    }
}
