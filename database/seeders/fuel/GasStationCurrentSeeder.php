<?php

namespace Database\Seeders\fuel;

use App\Models\fuel\GasStation;
use App\Models\fuel\GasStationCurrent;
use Illuminate\Database\Seeder;

class GasStationCurrentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gasStation = GasStation::where('name', 'Posto Central')->first();

        if ($gasStation) {
            GasStationCurrent::create([
                'gas_station_id' => $gasStation->id,
                'start_date' => now()->startOfWeek(),
                'end_date' => now()->endOfWeek(),
                'is_active' => true,
            ]);
        }
    }
}
