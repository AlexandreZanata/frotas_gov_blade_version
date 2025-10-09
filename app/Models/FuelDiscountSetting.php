<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FuelDiscountSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'fuel_type_id',
        'name',
        'percentage',
        'fixed_value',
        'discount_type',
        'is_active',
        'order',
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
        'fixed_value' => 'decimal:2',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function fuelType(): BelongsTo
    {
        return $this->belongsTo(FuelType::class);
    }
}

