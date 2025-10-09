<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuelType extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    public function calculationMethods()
    {
        return $this->hasMany(FuelCalculationMethod::class);
    }

    public function discountSettings()
    {
        return $this->hasMany(FuelDiscountSetting::class);
    }
}
