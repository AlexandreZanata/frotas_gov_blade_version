<?php

namespace App\Observers;

use App\Models\garbage\GarbageMaintenanceTareVehicle;
use App\Models\garbage\GarbageTareVehiclesCurrent;

class GarbageMaintenanceTareVehicleObserver
{
    /**
     * Handle the GarbageMaintenanceTareVehicle "created" event.
     */
    public function created(GarbageMaintenanceTareVehicle $maintenance): void
    {

        GarbageTareVehiclesCurrent::updateOrCreate(
            ['garbage_vehicle_id' => $maintenance->garbage_vehicle_id],
            [
                'garbage_maintenance_tare_vehicle_id' => $maintenance->id,
                'tare_weight_kg' => $maintenance->tare_weight_kg,
            ]
        );
    }
}
