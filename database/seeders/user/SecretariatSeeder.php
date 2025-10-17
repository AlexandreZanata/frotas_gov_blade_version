<?php

namespace Database\Seeders\user;

use App\Models\user\Secretariat;
use Illuminate\Database\Seeder;

// 1. Importe o Model

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
