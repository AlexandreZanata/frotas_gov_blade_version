<?php

namespace App\Models\fuel;

use App\Models\user\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FuelQuotation extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'name',
        'quotation_date',
        'calculation_method',
        'notes',
        'status',
    ];

    protected $casts = [
        'quotation_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function prices(): HasMany
    {
        return $this->hasMany(FuelQuotationPrice::class);
    }

    public function discounts(): HasMany
    {
        return $this->hasMany(FuelQuotationDiscount::class);
    }

    public function pumpPrices(): HasMany
    {
        return $this->hasMany(FuelPumpPrice::class);
    }

    /**
     * Calcular média de preços por tipo de combustível
     */
    public function calculateAverages(): array
    {
        $averages = [];

        $fuelTypes = FuelType::all();

        foreach ($fuelTypes as $fuelType) {
            $prices = $this->prices()
                ->where('fuel_type_id', $fuelType->id)
                ->pluck('price');

            if ($prices->count() > 0) {
                if ($this->calculation_method === 'simple_average') {
                    $average = $prices->avg();
                } else {
                    // Método personalizado pode ser implementado aqui
                    $average = $prices->avg();
                }

                $averages[$fuelType->id] = round($average, 3);
            }
        }

        return $averages;
    }

    /**
     * Verificar se a cotação está completa
     */
    public function isComplete(): bool
    {
        return $this->prices()->count() > 0 && $this->discounts()->count() > 0;
    }
}

