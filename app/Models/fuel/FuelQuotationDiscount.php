<?php

namespace App\Models\fuel;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FuelQuotationDiscount extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'fuel_quotation_id',
        'fuel_type_id',
        'average_price',
        'discount_percentage',
        'final_price',
    ];

    protected $casts = [
        'average_price' => 'decimal:3',
        'discount_percentage' => 'decimal:2',
        'final_price' => 'decimal:3',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(FuelQuotation::class, 'fuel_quotation_id');
    }

    public function fuelType(): BelongsTo
    {
        return $this->belongsTo(FuelType::class);
    }

    /**
     * Calcular preço final com desconto
     */
    public function calculateFinalPrice(): float
    {
        $discount = $this->average_price * ($this->discount_percentage / 100);
        return round($this->average_price - $discount, 3);
    }
}

