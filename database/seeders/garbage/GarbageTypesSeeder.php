<?php

namespace Database\Seeders\garbage;

use App\Models\garbage\GarbageType;
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
