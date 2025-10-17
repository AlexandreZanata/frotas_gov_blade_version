<?php

namespace App\Models\fuel;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class FuelPumpPrice extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'fuel_quotation_id',
        'gas_station_id',
        'fuel_type_id',
        'pump_price',
        'evidence_path',
    ];

    protected $casts = [
        'pump_price' => 'decimal:3',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(FuelQuotation::class, 'fuel_quotation_id');
    }

    public function gasStation(): BelongsTo
    {
        return $this->belongsTo(GasStation::class);
    }

    public function fuelType(): BelongsTo
    {
        return $this->belongsTo(FuelType::class);
    }

    public function getEvidenceUrlAttribute(): ?string
    {
        return $this->evidence_path ? Storage::url($this->evidence_path) : null;
    }
}


