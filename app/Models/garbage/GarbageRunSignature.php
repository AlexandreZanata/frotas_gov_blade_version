<?php

namespace App\Models\garbage;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GarbageRunSignature extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'garbage_runs_signatures';

    protected $fillable = [
        'garbage_run_id',
        'driver_signature_id',
        'admin_signature_id',
        'driver_signed_at',
        'admin_signed_at',
    ];

    protected $casts = [
        'driver_signed_at' => 'datetime',
        'admin_signed_at' => 'datetime',
    ];

    public function garbageRun(): BelongsTo
    {
        return $this->belongsTo(GarbageRun::class, 'garbage_run_id');
    }

    public function driverSignature(): BelongsTo
    {
        return $this->belongsTo(\App\Models\DigitalSignature::class, 'driver_signature_id');
    }

    public function adminSignature(): BelongsTo
    {
        return $this->belongsTo(\App\Models\DigitalSignature::class, 'admin_signature_id');
    }
}
