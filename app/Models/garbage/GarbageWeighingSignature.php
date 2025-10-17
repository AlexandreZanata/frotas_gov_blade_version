<?php

namespace App\Models\garbage;

use App\Models\DigitalSignature;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GarbageWeighingSignature extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'garbage_weighing_id',
        'operator_signature_id',
        'operator_signed_at',
        'admin_signature_id',
        'admin_signed_at',
    ];

    protected $casts = [
        'operator_signed_at' => 'datetime',
        'admin_signed_at' => 'datetime',
    ];

    public function weighing(): BelongsTo
    {
        return $this->belongsTo(GarbageWeighing::class, 'garbage_weighing_id');
    }

    public function operatorSignature(): BelongsTo
    {
        return $this->belongsTo(DigitalSignature::class, 'operator_signature_id');
    }

    public function adminSignature(): BelongsTo
    {
        return $this->belongsTo(DigitalSignature::class, 'admin_signature_id');
    }
}
