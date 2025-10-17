<?php

namespace Database\Seeders\fuel;

use App\Models\fuel\GasStation;
use App\Models\fuel\ScheduledGasStation;
use App\Models\user\User;
use Illuminate\Database\Seeder;

class ScheduledGasStationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gasStation = GasStation::where('name', 'Posto Shell')->first();

        $admin = User::where('email', 'admin@admin.com')->first();

        if ($gasStation && $admin) {
            ScheduledGasStation::create([
                'gas_station_id' => $gasStation->id,
                'admin_id' => $admin->id,
                'start_date' => now()->addWeek()->startOfWeek(),
                'end_date' => now()->addWeek()->endOfWeek(),
            ]);
        }
    }
}
