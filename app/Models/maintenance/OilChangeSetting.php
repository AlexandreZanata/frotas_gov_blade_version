<?php

namespace App\Models\maintenance;

use App\Models\Vehicle\VehicleCategory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OilChangeSetting extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'vehicle_category_id',
        'km_interval',
        'days_interval',
        'default_liters',
    ];

    protected $casts = [
        'default_liters' => 'decimal:2',
    ];

    public function vehicleCategory(): BelongsTo
    {
        return $this->belongsTo(VehicleCategory::class);
    }
}

