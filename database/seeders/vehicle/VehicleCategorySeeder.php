<?php

namespace Database\Seeders\vehicle;

use App\Models\Vehicle\VehicleCategory;
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
