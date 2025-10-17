<?php

namespace App\Models\garbage;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GarbageTareVehiclesCurrent extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'garbage_tare_vehicles_current';

    protected $fillable = [
        'garbage_vehicle_id',
        'garbage_maintenance_tare_vehicle_id',
        'tare_weight_kg',
    ];

    public function garbageVehicle(): BelongsTo
    {
        return $this->belongsTo(GarbageVehicle::class);
    }

    public function maintenanceRecord(): BelongsTo
    {
        return $this->belongsTo(GarbageMaintenanceTareVehicle::class, 'garbage_maintenance_tare_vehicle_id');
    }
}
