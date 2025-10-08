<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class LogbookPermission extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'scope',
        'secretariat_id',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that owns the permission
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the secretariat if scope is 'secretariat'
     */
    public function secretariat(): BelongsTo
    {
        return $this->belongsTo(Secretariat::class);
    }

    /**
     * Get the vehicles if scope is 'vehicles'
     */
    public function vehicles(): BelongsToMany
    {
        return $this->belongsToMany(Vehicle::class, 'logbook_permission_vehicles', 'logbook_permission_id', 'vehicle_id')
            ->withTimestamps();
    }

    /**
     * Check if user can access a specific vehicle
     */
    public static function canAccessVehicle(User $user, Vehicle $vehicle): bool
    {
        // Gestores gerais podem acessar tudo
        if ($user->isGeneralManager()) {
            return true;
        }

        // Verifica se há permissões ativas para o usuário
        $permissions = self::where('user_id', $user->id)
            ->where('is_active', true)
            ->get();

        if ($permissions->isEmpty()) {
            return false;
        }

        foreach ($permissions as $permission) {
            // Se tem permissão para todas as secretarias
            if ($permission->scope === 'all') {
                return true;
            }

            // Se tem permissão para a secretaria do veículo
            if ($permission->scope === 'secretariat' && $permission->secretariat_id === $vehicle->secretariat_id) {
                return true;
            }

            // Se tem permissão para veículos específicos
            if ($permission->scope === 'vehicles') {
                if ($permission->vehicles()->where('vehicle_id', $vehicle->id)->exists()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get all vehicles a user can access
     */
    public static function getAccessibleVehicles(User $user)
    {
        // Gestores gerais podem acessar todos
        if ($user->isGeneralManager()) {
            return Vehicle::with(['prefix', 'secretariat', 'status']);
        }

        $permissions = self::where('user_id', $user->id)
            ->where('is_active', true)
            ->get();

        if ($permissions->isEmpty()) {
            return Vehicle::whereRaw('1 = 0'); // Retorna query vazia
        }

        $query = Vehicle::query()->with(['prefix', 'secretariat', 'status']);
        $hasCondition = false;

        foreach ($permissions as $permission) {
            if ($permission->scope === 'all') {
                return Vehicle::with(['prefix', 'secretariat', 'status']); // Todos os veículos
            }

            if ($permission->scope === 'secretariat') {
                if (!$hasCondition) {
                    $query->where('secretariat_id', $permission->secretariat_id);
                    $hasCondition = true;
                } else {
                    $query->orWhere('secretariat_id', $permission->secretariat_id);
                }
            }

            if ($permission->scope === 'vehicles') {
                $vehicleIds = $permission->vehicles()->pluck('vehicle_id')->toArray();
                if (!empty($vehicleIds)) {
                    if (!$hasCondition) {
                        $query->whereIn('id', $vehicleIds);
                        $hasCondition = true;
                    } else {
                        $query->orWhereIn('id', $vehicleIds);
                    }
                }
            }
        }

        return $hasCondition ? $query : Vehicle::whereRaw('1 = 0');
    }
}

