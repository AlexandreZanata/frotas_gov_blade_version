<?php

namespace App\Models\fuel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FuelCalculationMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'fuel_type_id',
        'name',
        'formula',
        'calculation_type',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function fuelType(): BelongsTo
    {
        return $this->belongsTo(FuelType::class);
    }
}

