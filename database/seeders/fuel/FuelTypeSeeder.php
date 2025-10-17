<?php

namespace Database\Seeders\fuel;

use App\Models\fuel\FuelType;
use Illuminate\Database\Seeder;

class FuelTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'Gasolina'],
            ['name' => 'Etanol'],
            ['name' => 'Diesel S10'],
            ['name' => 'Diesel S500']
        ];

        foreach ($types as $type) {
            FuelType::create($type);
        }
    }
}
