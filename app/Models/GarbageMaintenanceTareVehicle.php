<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GarbageMaintenanceTareVehicle extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'garbage_vehicle_id',
        'user_id',
        'tare_weight_kg',
        'calibrated_at',
        'notes',
    ];

    protected $casts = ['calibrated_at' => 'datetime'];

    public function garbageVehicle(): BelongsTo
    {
        return $this->belongsTo(GarbageVehicle::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
