<?php

namespace Database\Seeders;

use App\Models\Secretariat; // 1. Importe o Model
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SecretariatSeeder extends Seeder
{
    public function run(): void
    {
        $secretariats = [
            ['name' => 'Administração'],
            ['name' => 'Saúde'],
            ['name' => 'Educação'],
            ['name' => 'Obras'],
        ];

        // 2. Itere e use o Model para criar
        foreach ($secretariats as $secretariat) {
            Secretariat::create($secretariat);
        }
    }
}
