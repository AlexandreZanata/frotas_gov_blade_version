<?php
namespace Database\Seeders\defect;

use App\Models\defect\DefectCategory;
use App\Models\defect\DefectReportItem;
use Illuminate\Database\Seeder;

class DefectReportItemSeeder extends Seeder
{
    public function run(): void
    {
        // Busca as categorias criadas pelo DefectCategorySeeder
        $catMotor = DefectCategory::where('name', 'Motor')->first();
        $catFreios = DefectCategory::where('name', 'Freios')->first();
        $catPneus = DefectCategory::where('name', 'Pneus e Rodas')->first();
        $catEletrica = DefectCategory::where('name', 'Sistema Elétrico')->first();

        $items = [
            ['name' => 'Ruído anormal no motor', 'category_id' => $catMotor->id],
            ['name' => 'Freios com barulho ou vibração', 'category_id' => $catFreios->id],
            ['name' => 'Pneu furado ou danificado', 'category_id' => $catPneus->id],
            ['name' => 'Farol ou lanterna queimada', 'category_id' => $catEletrica->id],
            ['name' => 'Luz de advertência acesa no painel', 'category_id' => $catEletrica->id],
            ['name' => 'Vazamento de óleo ou fluidos', 'category_id' => $catMotor->id],
        ];

        foreach ($items as $item) {
            DefectReportItem::create($item);
        }
    }
}
