<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class GarbageWeighing extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'weighing_code',
        'garbage_type_id',
        'weight_kg',
        'weighed_at',
        'requester_id',
        'weighbridge_operator_id',
        'garbage_vehicle_id',
    ];

    protected $casts = [
        'weighed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->weighing_code)) {
                $model->weighing_code = 'PES-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
            }
        });
    }

    // Relacionamentos
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(GarbageWeighbridgeOperator::class, 'weighbridge_operator_id');
    }

    public function garbageVehicle(): BelongsTo
    {
        return $this->belongsTo(GarbageVehicle::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(GarbageType::class, 'garbage_type_id');
    }

    public function signature(): HasOne
    {
        return $this->hasOne(GarbageWeighingSignature::class);
    }
}
