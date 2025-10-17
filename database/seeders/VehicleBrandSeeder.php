<?php

namespace Database\Seeders;

use App\Models\Vehicle\VehicleBrand;
use Illuminate\Database\Seeder;

class VehicleBrandSeeder extends Seeder
{
    public function run(): void
    {
        VehicleBrand::firstOrCreate(['name' => 'Ford']);
        VehicleBrand::firstOrCreate(['name' => 'Chevrolet']);
        VehicleBrand::firstOrCreate(['name' => 'Volkswagen']);
        VehicleBrand::firstOrCreate(['name' => 'Fiat']);
        VehicleBrand::firstOrCreate(['name' => 'Toyota']);
        VehicleBrand::firstOrCreate(['name' => 'Honda']);
        VehicleBrand::firstOrCreate(['name' => 'Hyundai']);
        VehicleBrand::firstOrCreate(['name' => 'Mercedes-Benz']);
        VehicleBrand::firstOrCreate(['name' => 'Scania']);
        VehicleBrand::firstOrCreate(['name' => 'Volvo']);
    }
}
