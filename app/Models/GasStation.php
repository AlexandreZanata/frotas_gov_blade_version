<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'price_per_liter',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'price_per_liter' => 'decimal:2',
    ];

    /**
     * Scope para postos ativos
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
