<?php
namespace Database\Seeders\fuel;

use App\Models\fuel\Fueling;
use App\Models\user\User;
use App\Models\Vehicle\Vehicle;
use App\Models\fuel\FuelType;
use App\Models\fuel\GasStation;
use App\Models\run\Run;
use Illuminate\Database\Seeder;

class FuelingSeeder extends Seeder
{
    public function run(): void
    {
        $driver = User::where('email', 'motorista.saude@frotas.gov')->first();
        $vehicle = Vehicle::where('plate', 'BRA2E19')->first();
        $fuelType = FuelType::where('name', 'Diesel S10')->first();
        $gasStation = GasStation::where('name', 'Posto Central')->first();
        $run = Run::first();

        if ($driver && $vehicle && $fuelType && $gasStation) {
            $liters = 50.500;
            $value_per_liter_example = 5.89; // Mantenha como referência se precisar
            $total_value = round($liters * $value_per_liter_example, 2); // Calcula o valor total

            Fueling::updateOrCreate(
                [
                    'user_id' => $driver->id,
                    'vehicle_id' => $vehicle->id,
                    'km' => 15230, // Condição para evitar duplicar este abastecimento exato
                ],
                [
                    'run_id' => $run ? $run->id : null,
                    'fuel_type_id' => $fuelType->id,
                    'gas_station_id' => $gasStation->id,
                    'fueled_at' => now(),
                    'liters' => $liters,
                    'value' => $total_value,

                    'invoice_path' => 'invoices/sample.pdf',

                ]
            );
        }
    }
}
