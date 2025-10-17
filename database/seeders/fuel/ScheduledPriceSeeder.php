<?php

namespace Database\Seeders\fuel;

use App\Models\fuel\FuelType;
use App\Models\fuel\GasStation;
use App\Models\fuel\ScheduledPrice;
use App\Models\user\User;
use Illuminate\Database\Seeder;

// Importar o Model User

class ScheduledPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gasStation = GasStation::where('name', 'Posto Central')->first();
        $fuelTypeDiesel = FuelType::where('name', 'Diesel S10')->first();
        $fuelTypeGasolina = FuelType::where('name', 'Gasolina')->first();
        $admin = User::first(); // Pega o primeiro usuário como admin

        if ($gasStation && $fuelTypeDiesel && $fuelTypeGasolina && $admin) {
            // Preço agendado para o futuro
            ScheduledPrice::create([
                'gas_station_id' => $gasStation->id,
                'fuel_type_id' => $fuelTypeDiesel->id,
                'price' => 5.750,
                'start_date' => now()->addDays(7),
                'end_date' => now()->addDays(14),
                'is_active' => true,
                'admin_id' => $admin->id, // Adicionado
            ]);

            // Preço ativo atualmente
            ScheduledPrice::create([
                'gas_station_id' => $gasStation->id,
                'fuel_type_id' => $fuelTypeGasolina->id,
                'price' => 4.990,
                'start_date' => now()->subDay(),
                'end_date' => now()->addDays(6),
                'is_active' => true,
                'admin_id' => $admin->id, // Adicionado
            ]);

            // Preço passado (inativo)
            ScheduledPrice::create([
                'gas_station_id' => $gasStation->id,
                'fuel_type_id' => $fuelTypeGasolina->id,
                'price' => 4.850,
                'start_date' => now()->subDays(10),
                'end_date' => now()->subDays(1),
                'is_active' => false,
                'admin_id' => $admin->id, // Adicionado
            ]);
        }
    }
}
