<?php

namespace App\Models\garbage;

use App\Models\user\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class GarbageWeighing extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'weighing_code',
        'garbage_type_id',
        'gross_weight_kg',
        'tare_weight_kg',
        'net_weight_kg',
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

        // Gera o código único de auditoria
        static::creating(function ($model) {
            if (empty($model->weighing_code)) {
                $model->weighing_code = 'PES-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
            }
        });

        // Calcula o peso líquido sempre que o registro é salvo
        static::saving(function ($model) {
            if (isset($model->gross_weight_kg) && isset($model->tare_weight_kg)) {
                $model->net_weight_kg = $model->gross_weight_kg - $model->tare_weight_kg;
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
