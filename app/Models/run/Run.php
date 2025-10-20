<?php

namespace App\Models\run;

use App\Models\checklist\Checklist;
use App\Models\fuel\Fueling;
use App\Models\user\User;
use App\Models\run\RunSignature;
use App\Models\Vehicle\Vehicle;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'stop_point',
        'status'
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
            'start_km' => 'integer',
            'end_km' => 'integer',
        ];
    }

    /**
     * Relação com os destinos da corrida
     */
    public function destinations(): HasMany
    {
        return $this->hasMany(RunDestination::class)->orderBy('order');
    }

    /**
     * Get the primary destination (first one) - para compatibilidade
     */
    public function getPrimaryDestinationAttribute(): ?string
    {
        return $this->destinations->first()->destination ?? null;
    }

    /**
     * Accessor para compatibilidade com código existente
     * Mantém a propriedade 'destination' funcionando
     */
    public function getDestinationAttribute(): ?string
    {
        return $this->primary_destination;
    }

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
    public function signatures(): HasMany
    {
        return $this->hasMany(RunSignature::class);
    }

    /**
     * Relação com o checklist da viagem
     */
    public function checklist(): HasOne
    {
        return $this->hasOne(Checklist::class);
    }

    /**
     * Relação com abastecimentos da viagem
     */
    public function fuelings(): HasMany
    {
        return $this->hasMany(Fueling::class, 'vehicle_id', 'vehicle_id')
            ->whereBetween('fueled_at', [$this->started_at, $this->finished_at ?? now()]);
    }

    /**
     * Scope para buscar a última corrida completada de um veículo
     */
    public function scopeLastCompletedForVehicle($query, $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId)
            ->where('status', 'completed')
            ->whereNotNull('end_km')
            ->orderBy('finished_at', 'desc');
    }

    /**
     * Get the distance traveled
     */
    public function getDistanceAttribute(): ?int
    {
        if ($this->start_km && $this->end_km) {
            return $this->end_km - $this->start_km;
        }

        return null;
    }

    /**
     * Check if run is completed
     */
    public function getIsCompletedAttribute(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if run is in progress
     */
    public function getIsInProgressAttribute(): bool
    {
        return $this->status === 'in_progress';
    }

    public function signature(): HasOne
    {
        // Certifique-se que seu model de assinatura se chama 'RunSignature'
        return $this->hasOne(RunSignature::class);
    }
}
