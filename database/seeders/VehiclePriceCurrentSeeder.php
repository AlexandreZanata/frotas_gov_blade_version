<?php

namespace Database\Seeders;

use App\Models\Vehicle\VehiclePriceCurrent;
use App\Models\Vehicle\VehiclePriceOrigin;
use Illuminate\Database\Seeder;

class VehiclePriceCurrentSeeder extends Seeder
{
    public function run(): void
    {
        $originPrices = VehiclePriceOrigin::all();

        foreach ($originPrices as $origin) {
            VehiclePriceCurrent::firstOrCreate(['vehicle_id' => $origin->vehicle_id], [
                'current_amount' => $origin->amount,
            ]);
        }
    }
}
