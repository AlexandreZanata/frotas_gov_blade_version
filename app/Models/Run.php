<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Run extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'vehicle_id',
        'user_id',
        'start_km',
        'end_km',
        'started_at',
        'finished_at',
        'destination',
        'origin',
        'status'
    ];

    /**
     * Adicione esta função para definir a relação com o Usuário.
     * Agora, $run->user vai funcionar.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * É uma boa prática adicionar a relação com Veículo também.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Relação com as assinaturas da viagem
     */
    public function signatures()
    {
        return $this->hasMany(RunSignature::class);
    }

    /**
     * Relação com o checklist da viagem
     */
    public function checklist()
    {
        return $this->hasOne(Checklist::class);
    }

    /**
     * Relação com abastecimentos da viagem
     */
    public function fuelings()
    {
        return $this->hasMany(Fueling::class, 'vehicle_id', 'vehicle_id')
                    ->whereBetween('fueled_at', [$this->started_at, $this->finished_at ?? now()]);
    }
}
