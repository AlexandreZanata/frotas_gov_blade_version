<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GasStation extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'address',
        'cnpj',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope para postos ativos
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Relacionamento com preços de cotação
     */
    public function quotationPrices(): HasMany
    {
        return $this->hasMany(FuelQuotationPrice::class);
    }

    /**
     * Relacionamento com preços de bomba
     */
    public function pumpPrices(): HasMany
    {
        return $this->hasMany(FuelPumpPrice::class);
    }

    /**
     * Relacionamento com abastecimentos
     */
    public function fuelings(): HasMany
    {
        return $this->hasMany(Fueling::class);
    }
}
