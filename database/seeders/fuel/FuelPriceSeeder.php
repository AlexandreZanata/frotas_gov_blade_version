<?php

namespace Database\Seeders\fuel;

use App\Models\fuel\FuelPrice;
use App\Models\fuel\FuelType;
use App\Models\fuel\GasStation;
use Illuminate\Database\Seeder;

class FuelPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gasStation = GasStation::where('name', 'Posto Central')->first();
        $fuelTypeDiesel = FuelType::where('name', 'Diesel S10')->first();
        $fuelTypeGasolina = FuelType::where('name', 'Gasolina')->first();

        if ($gasStation && $fuelTypeDiesel && $fuelTypeGasolina) {
            FuelPrice::create([
                'gas_station_id' => $gasStation->id,
                'fuel_type_id' => $fuelTypeDiesel->id,
                'price' => 5.500,
                'effective_date' => now(),
            ]);

            FuelPrice::create([
                'gas_station_id' => $gasStation->id,
                'fuel_type_id' => $fuelTypeGasolina->id,
                'price' => 4.900,
                'effective_date' => now(),
            ]);
        }
    }
}
