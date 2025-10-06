<?php

namespace Database\Seeders;

use App\Models\VehicleCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VehicleCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Carro de Passeio'],
            ['name' => 'Caminhonete'],
            ['name' => 'Caminhão'],
            ['name' => 'Motocicleta'],
            ['name' => 'Van/Utilitário']
        ];

        foreach ($categories as $category) {
            VehicleCategory::create($category);
        }
    }
}
