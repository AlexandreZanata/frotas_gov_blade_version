<?php

namespace Database\Seeders\vehicle;

use App\Models\Vehicle\AcquisitionType;
use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\VehiclePriceOrigin;
use Illuminate\Database\Seeder;

class VehiclePriceOriginSeeder extends Seeder
{
    public function run(): void
    {
        $vehicle = Vehicle::where('plate', 'BRA2E19')->first();
        $acquisitionType = AcquisitionType::where('name', 'Compra por Licitação')->first();

        if ($vehicle && $acquisitionType) {
            VehiclePriceOrigin::firstOrCreate(['vehicle_id' => $vehicle->id], [
                'amount' => 120000.00,
                'acquisition_date' => '2021-05-10',
                'acquisition_type_id' => $acquisitionType->id,
            ]);
        }
    }
}
