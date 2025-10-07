<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vehicle extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'prefix_id',
        'name',
        'brand',
        'model_year',
        'plate',
        'chassis',
        'renavam',
        'registration',
        'fuel_tank_capacity',
        'fuel_type_id',
        'category_id',
        'status_id',
        'secretariat_id',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(VehicleCategory::class, 'category_id');
    }

    public function prefix(): BelongsTo
    {
        return $this->belongsTo(Prefix::class, 'prefix_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(VehicleStatus::class, 'status_id');
    }

    public function fuelType(): BelongsTo
    {
        return $this->belongsTo(\App\Models\FuelType::class, 'fuel_type_id');
    }
}
