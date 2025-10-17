<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GarbageVehicle extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['vehicle_id'];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
    public function currentTare(): HasOne
    {
        return $this->hasOne(GarbageTareVehiclesCurrent::class, 'garbage_vehicle_id');
    }

    public function tareHistory(): HasMany
    {
        return $this->hasMany(GarbageMaintenanceTareVehicle::class, 'garbage_vehicle_id');
    }
}
