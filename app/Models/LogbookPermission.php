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
     * Get the secretariat if scope is 'secretariat' (single - DEPRECATED)
     */
    public function secretariat(): BelongsTo
    {
        return $this->belongsTo(Secretariat::class);
    }

    /**
     * Get the secretariats if scope is 'secretariat' (multiple)
     */
    public function secretariats(): BelongsToMany
    {
        return $this->belongsToMany(Secretariat::class, 'logbook_permission_secretariats', 'logbook_permission_id', 'secretariat_id')
            ->withTimestamps();
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

            // Se tem permissão para secretaria(s) específica(s)
            if ($permission->scope === 'secretariat') {
                // Verifica se o veículo pertence a alguma das secretarias permitidas
                $hasAccess = $permission->secretariats()
                    ->where('secretariat_id', $vehicle->secretariat_id)
                    ->exists();

                if ($hasAccess) {
                    return true;
                }
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
     * Get all secretariat IDs that the user has permission to access
     */
    public static function getUserAccessibleSecretariatIds(User $user): array
    {
        // Gestores gerais podem acessar todas as secretarias
        if ($user->isGeneralManager()) {
            return \App\Models\Secretariat::pluck('id')->toArray();
        }

        $secretariatIds = [];

        // Verifica as permissões do usuário
        $permissions = self::where('user_id', $user->id)
            ->where('is_active', true)
            ->get();

        foreach ($permissions as $permission) {
            // Se tem permissão para todas as secretarias
            if ($permission->scope === 'all') {
                return \App\Models\Secretariat::pluck('id')->toArray();
            }

            // Se tem permissão para secretaria(s) específica(s)
            if ($permission->scope === 'secretariat') {
                $permissionSecretariatIds = $permission->secretariats()->pluck('secretariat_id')->toArray();
                $secretariatIds = array_merge($secretariatIds, $permissionSecretariatIds);
            }
        }

        return array_unique($secretariatIds);
    }

    /**
     * Get all vehicle IDs that the user has permission to access
     */
    public static function getUserAccessibleVehicleIds(User $user): array
    {
        // Gestores gerais podem acessar todos os veículos
        if ($user->isGeneralManager()) {
            return \App\Models\Vehicle::pluck('id')->toArray();
        }

        $vehicleIds = [];

        // Verifica as permissões do usuário
        $permissions = self::where('user_id', $user->id)
            ->where('is_active', true)
            ->get();

        foreach ($permissions as $permission) {
            // Se tem permissão para todas as secretarias
            if ($permission->scope === 'all') {
                return \App\Models\Vehicle::pluck('id')->toArray();
            }

            // Se tem permissão para secretaria(s) específica(s)
            if ($permission->scope === 'secretariat') {
                $secretariatIds = $permission->secretariats()->pluck('secretariat_id')->toArray();
                $secretariatVehicleIds = \App\Models\Vehicle::whereIn('secretariat_id', $secretariatIds)
                    ->pluck('id')
                    ->toArray();
                $vehicleIds = array_merge($vehicleIds, $secretariatVehicleIds);
            }

            // Se tem permissão para veículos específicos
            if ($permission->scope === 'vehicles') {
                $permissionVehicleIds = $permission->vehicles()->pluck('vehicle_id')->toArray();
                $vehicleIds = array_merge($vehicleIds, $permissionVehicleIds);
            }
        }

        return array_unique($vehicleIds);
    }

    /**
     * Check if user has any active permissions
     */
    public static function userHasActivePermissions(User $user): bool
    {
        if ($user->isGeneralManager()) {
            return true;
        }

        return self::where('user_id', $user->id)
            ->where('is_active', true)
            ->exists();
    }
}
