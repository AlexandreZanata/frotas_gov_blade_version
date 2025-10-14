<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FuelPrice extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'gas_station_id',
        'fuel_type_id',
        'price',
        'effective_date',
    ];

    protected $casts = [
        'effective_date' => 'datetime',
        'price' => 'decimal:3',
    ];

    public function gasStation(): BelongsTo
    {
        return $this->belongsTo(GasStation::class);
    }

    public function fuelType(): BelongsTo
    {
        return $this->belongsTo(FuelType::class);
    }
}
