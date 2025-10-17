<?php
namespace Database\Seeders\fuel;
use App\Models\fuel\Fueling;
use App\Models\fuel\FuelType;
use App\Models\fuel\GasStation;
use App\Models\user\User;
use App\Models\Vehicle\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FuelingSeeder extends Seeder {
    public function run(): void {
        $driver = User::where('email', 'motorista@frotas.gov')->first();
        $vehicle = Vehicle::where('plate', 'BRA2E19')->first();
        $fuelType = FuelType::where('name', 'Diesel S10')->first();
        $gasStation = GasStation::where('name', 'Posto Central')->first();

        if ($driver && $vehicle && $fuelType && $gasStation) {
            Fueling::create([
                'user_id' => $driver->id,
                'vehicle_id' => $vehicle->id,
                'fuel_type_id' => $fuelType->id,
                'gas_station_id' => $gasStation->id,
                'fueled_at' => now(),
                'km' => 15230,
                'liters' => 50.500,
                'value_per_liter' => 5.89,
                'invoice_path' => 'invoices/sample.pdf',
                'public_code' => Str::random(10),
                'signature_path' => 'signatures/sample.png',
            ]);
        }
    }
}
