<?php

namespace Database\Seeders;

use App\Models\GarbageNeighborhood;
use Illuminate\Database\Seeder;

class GarbageNeighborhoodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $neighborhoods = [
            ['name' => 'Centro'],
            ['name' => 'Bairro Industrial'],
            ['name' => 'Jardim das Flores'],
        ];

        foreach ($neighborhoods as $neighborhood) {
            GarbageNeighborhood::create($neighborhood);
        }
    }
}
