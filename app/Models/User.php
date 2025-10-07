<?php

namespace App\Models;

// 1. IMPORTAR A TRAIT
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    // 2. USAR A TRAIT
    use HasFactory, Notifiable, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        // Adicionar os novos campos aqui!
        'cpf',
        'cnh_number',
        'cnh_expiry_date',
        'role_id',
        'secretariat_id',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function digitalSignature(): HasOne
    {
        return $this->hasOne(DigitalSignature::class);
    }

    /**
     * Get the role of the user
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the secretariat of the user
     */
    public function secretariat(): BelongsTo
    {
        return $this->belongsTo(Secretariat::class);
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(string $roleName): bool
    {
        return $this->role && $this->role->name === $roleName;
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole(array $roleNames): bool
    {
        return $this->role && in_array($this->role->name, $roleNames);
    }

    /**
     * Check if user is a general manager
     */
    public function isGeneralManager(): bool
    {
        return $this->hasRole('general_manager');
    }

    /**
     * Check if user is a sector manager
     */
    public function isSectorManager(): bool
    {
        return $this->hasRole('sector_manager');
    }

    /**
     * Check if user is a driver
     */
    public function isDriver(): bool
    {
        return $this->hasRole('driver');
    }

    /**
     * Check if user is a mechanic
     */
    public function isMechanic(): bool
    {
        return $this->hasRole('mechanic');
    }

    /**
     * Check if user is a manager (general or sector)
     */
    public function isManager(): bool
    {
        return $this->hasAnyRole(['general_manager', 'sector_manager']);
    }

    /**
     * Check if user has higher or equal hierarchy than another user
     */
    public function hasHigherOrEqualHierarchyThan(User $otherUser): bool
    {
        if (!$this->role || !$otherUser->role) {
            return false;
        }

        return $this->role->hierarchy_level >= $otherUser->role->hierarchy_level;
    }

    /**
     * Check if user has higher hierarchy than another user
     */
    public function hasHigherHierarchyThan(User $otherUser): bool
    {
        if (!$this->role || !$otherUser->role) {
            return false;
        }

        return $this->role->hierarchy_level > $otherUser->role->hierarchy_level;
    }

    /**
     * Check if user can manage another user (based on hierarchy)
     */
    public function canManage(User $otherUser): bool
    {
        // General managers can manage everyone
        if ($this->isGeneralManager()) {
            return true;
        }

        // Sector managers can manage drivers and mechanics in their secretariat
        if ($this->isSectorManager()) {
            return $this->secretariat_id === $otherUser->secretariat_id
                && $otherUser->hasAnyRole(['driver', 'mechanic']);
        }

        return false;
    }

    /**
     * Get hierarchy level
     */
    public function getHierarchyLevel(): int
    {
        return $this->role?->hierarchy_level ?? 0;
    }
}
