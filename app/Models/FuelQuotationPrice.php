<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FuelQuotationPrice extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'fuel_quotation_id',
        'gas_station_id',
        'fuel_type_id',
        'price',
        'evidence_path',
        'image_1',
        'image_2',
    ];

    protected $casts = [
        'price' => 'decimal:3',
    ];

    public function fuelQuotation(): BelongsTo
    {
        return $this->belongsTo(FuelQuotation::class);
    }

    public function gasStation(): BelongsTo
    {
        return $this->belongsTo(GasStation::class);
    }

    public function fuelType(): BelongsTo
    {
        return $this->belongsTo(FuelType::class);
    }
}

