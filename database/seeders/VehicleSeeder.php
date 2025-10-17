<?php

namespace Database\Seeders;

use App\Models\FuelType;
use App\Models\Secretariat;
use App\Models\Vehicle;
use App\Models\Vehicle\VehicleHeritage;
use App\Models\VehicleCategory;
use App\Models\VehicleStatus;
use App\Models\Vehicle\VehicleBrand;
use Illuminate\Database\Seeder;
use App\Models\Prefix;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $prefixes = Prefix::pluck('id', 'name');
        $secretariats = Secretariat::pluck('id', 'name');
        $fuelTypes = FuelType::pluck('id', 'name');
        $categories = VehicleCategory::pluck('id', 'name');
        $statuses = VehicleStatus::pluck('id', 'name');
        $brands = VehicleBrand::pluck('id', 'name');
        $heritage = VehicleHeritage::where('name', 'Oficial')->first();

        $vehicles = [
            [
                'prefix_name' => 'V-001',
                'name' => 'FORD RANGER 3.2',
                'brand_name' => 'Ford',
                'model_year' => '2020/2021',
                'plate' => 'BRA2E19',
                'chassis' => 'CHASSI123456789',
                'renavam' => 'RENAVAM123456789',
                'registration' => 'REG123',
                'fuel_tank_capacity' => 80,
                'fuel_type_name' => 'Diesel S10',
                'category_name' => 'Caminhonete',
                'status_name' => 'Disponível',
                'secretariat_name' => 'Obras',
            ],
            [
                'prefix_name' => 'V-002',
                'name' => 'MERCEDES-BENZ SPRINTER',
                'brand_name' => 'Mercedes-Benz',
                'model_year' => '2022/2022',
                'plate' => 'XYZ1234',
                'chassis' => 'CHASSI987654321',
                'renavam' => 'RENAVAM987654321',
                'registration' => 'REG456',
                'fuel_tank_capacity' => 75,
                'fuel_type_name' => 'Diesel S10',
                'category_name' => 'Ambulância',
                'status_name' => 'Disponível',
                'secretariat_name' => 'Saúde',
            ],
            [
                'prefix_name' => 'V-003',
                'name' => 'VOLKSWAGEN 15.190 ODR',
                'brand_name' => 'Volkswagen',
                'model_year' => '2019/2020',
                'plate' => 'ABC5678',
                'chassis' => 'CHASSI567891234',
                'renavam' => 'RENAVAM567891234',
                'registration' => 'REG789',
                'fuel_tank_capacity' => 150,
                'fuel_type_name' => 'Diesel S10',
                'category_name' => 'Ônibus',
                'status_name' => 'Em Manutenção',
                'secretariat_name' => 'Educação',
            ],
        ];

        if ($heritage) {
            foreach ($vehicles as $vehicleData) {
                if (
                    isset($prefixes[$vehicleData['prefix_name']]) &&
                    isset($brands[$vehicleData['brand_name']]) &&
                    isset($fuelTypes[$vehicleData['fuel_type_name']]) &&
                    isset($categories[$vehicleData['category_name']]) &&
                    isset($statuses[$vehicleData['status_name']]) &&
                    isset($secretariats[$vehicleData['secretariat_name']])
                ) {
                    Vehicle::firstOrCreate(
                        ['plate' => $vehicleData['plate']], // Chave única para evitar duplicatas
                        [
                            'prefix_id' => $prefixes[$vehicleData['prefix_name']],
                            'name' => $vehicleData['name'],
                            'brand_id' => $brands[$vehicleData['brand_name']],
                            'model_year' => $vehicleData['model_year'],
                            'chassis' => $vehicleData['chassis'],
                            'renavam' => $vehicleData['renavam'],
                            'registration' => $vehicleData['registration'],
                            'fuel_tank_capacity' => $vehicleData['fuel_tank_capacity'],
                            'fuel_type_id' => $fuelTypes[$vehicleData['fuel_type_name']],
                            'category_id' => $categories[$vehicleData['category_name']],
                            'status_id' => $statuses[$vehicleData['status_name']],
                            'secretariat_id' => $secretariats[$vehicleData['secretariat_name']],
                            'heritage_id' => $heritage->id,
                        ]
                    );
                }
            }
        }
    }
}
