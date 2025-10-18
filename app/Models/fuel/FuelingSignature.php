<?php

namespace App\Models\fuel;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FuelingSignature extends Model
{
    use HasFactory, HasUuids;


    protected $table = 'fuelings_signatures';

    protected $fillable = [
        'fueling_id',
        'driver_signature_id',
        'driver_signed_at',
        'admin_signature_id',
        'admin_signed_at',
    ];

    protected $casts = [
        'driver_signed_at' => 'datetime',
        'admin_signed_at' => 'datetime',
    ];

    public function fueling(): BelongsTo
    {
        return $this->belongsTo(Fueling::class);
    }

    // Ajuste o namespace para seu model DigitalSignature se for diferente
    public function driverSignature(): BelongsTo
    {
        return $this->belongsTo(\App\Models\DigitalSignature::class, 'driver_signature_id');
    }

    public function adminSignature(): BelongsTo
    {
        return $this->belongsTo(\App\Models\DigitalSignature::class, 'admin_signature_id');
    }
}
