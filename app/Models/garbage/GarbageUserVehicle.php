<?php

namespace App\Models\garbage;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// Removido: use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Adicionado:
use Illuminate\Database\Eloquent\Relations\Pivot;

class GarbageUserVehicle extends Pivot // <-- CORREÇÃO APLICADA AQUI
{
    use HasFactory, HasUuids;

    protected $table = 'garbage_user_vehicles';

    protected $fillable = [
        'garbage_user_id',
        'garbage_vehicle_id',
    ];

    // Relação com GarbageUser (Ajuste o namespace se necessário)
    public function garbageUser(): BelongsTo
    {
        return $this->belongsTo(GarbageUser::class); // Assumindo que o model é App\Models\GarbageUser
    }

    // Relação com GarbageVehicle (Ajuste o namespace se necessário)
    public function garbageVehicle(): BelongsTo
    {
        return $this->belongsTo(GarbageVehicle::class); // Assumindo que o model é App\Models\GarbageVehicle
    }
}
