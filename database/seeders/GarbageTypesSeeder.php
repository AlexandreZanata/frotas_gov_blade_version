<?php

namespace Database\Seeders;

use App\Models\GarbageType;
use Illuminate\Database\Seeder;

class GarbageTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'Lixo Úmido'],
            ['name' => 'Lixo Seco'],
        ];

        foreach ($types as $type) {
            GarbageType::create($type);
        }
    }
}
