<?php

namespace Database\Seeders\defect;

use App\Models\defect\DefectCategory;
use Illuminate\Database\Seeder;

class DefectCategorySeeder extends Seeder
{
    public function run(): void
    {
        DefectCategory::create(['name' => 'Motor']);
        DefectCategory::create(['name' => 'Freios']);
        DefectCategory::create(['name' => 'Pneus e Rodas']);
        DefectCategory::create(['name' => 'Sistema Elétrico']);
        DefectCategory::create(['name' => 'Carroceria e Estrutura']);
        DefectCategory::create(['name' => 'Geral']);
    }
}
