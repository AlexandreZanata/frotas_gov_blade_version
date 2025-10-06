<?php

namespace Database\Seeders;

use App\Models\FuelType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
