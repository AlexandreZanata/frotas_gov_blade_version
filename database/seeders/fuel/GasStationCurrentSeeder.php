<?php

namespace Database\Seeders\fuel;

use App\Models\fuel\GasStation;
use App\Models\fuel\GasStationCurrent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GasStationCurrentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gasStations = GasStation::whereIn('name', ['Posto Central', 'Abastecimento Manual'])->get();

        foreach ($gasStations as $gasStation) {
            GasStationCurrent::updateOrCreate(
                [
                    'gas_station_id' => $gasStation->id,
                ],
                [
                    'id' => Str::uuid(),
                    'start_date' => now()->startOfWeek(),
                    'end_date' => now()->endOfWeek()->addWeeks(4), // 4 semanas de validade
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
