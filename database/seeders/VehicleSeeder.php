<?php

namespace Database\Seeders;

use App\Models\FuelType;
use App\Models\Secretariat;
use App\Models\Vehicle;
use App\Models\Vehicle\VehicleHeritage; // 1. Importar o novo model
use App\Models\VehicleCategory;
use App\Models\VehicleStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Prefix;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $prefix = Prefix::where('name', 'V-001')->firstOrFail();
        // Pega os registros de outras tabelas para vincular o veículo
        $secretariat = Secretariat::where('name', 'Obras')->first();
        $fuelType = FuelType::where('name', 'Diesel S10')->first();
        $category = VehicleCategory::where('name', 'Caminhonete')->first();
        $status = VehicleStatus::where('name', 'Disponível')->first();

        // 2. Buscar o tipo de patrimônio que queremos associar
        $heritage = VehicleHeritage::where('name', 'Oficial')->first();

        // Só cria o veículo se encontrou todos os dados necessários (incluindo o heritage)
        if ($secretariat && $fuelType && $category && $status && $heritage) {
            Vehicle::firstOrCreate(['plate' => 'BRA2E19'], [ // Usa firstOrCreate para evitar duplicatas
                'prefix_id' => $prefix->id,
                'name' => 'FORD RANGER 3.2',
                'brand' => 'Ford',
                'model_year' => '2020/2021',
                'chassis' => 'CHASSI123456789',
                'renavam' => 'RENAVAM123456789',
                'registration' => 'REG123',
                'fuel_tank_capacity' => 80,
                'fuel_type_id' => $fuelType->id,
                'category_id' => $category->id,
                'status_id' => $status->id,
                'secretariat_id' => $secretariat->id,
                'heritage_id' => $heritage->id,
            ]);
        }
    }
}
