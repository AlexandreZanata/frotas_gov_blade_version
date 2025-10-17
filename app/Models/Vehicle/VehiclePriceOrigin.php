<?php

namespace App\Models\Vehicle;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class VehiclePriceOrigin extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'vehicle_id',
        'amount',
        'acquisition_date',
        'acquisition_type_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'acquisition_date' => 'date',
    ];

    /**
     * Relacionamento com Vehicle
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Relacionamento com AcquisitionType
     */
    public function acquisitionType()
    {
        return $this->belongsTo(AcquisitionType::class);
    }

    /**
     * Formata o valor para exibição
     */
    public function getFormattedAmountAttribute()
    {
        return 'R$ ' . number_format($this->amount, 2, ',', '.');
    }

    /**
     * Formata a data para exibição
     */
    public function getFormattedAcquisitionDateAttribute()
    {
        return $this->acquisition_date->format('d/m/Y');
    }
}
