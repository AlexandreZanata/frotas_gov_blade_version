<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids; // Importe a trait
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory, HasUuids; // Use a trait

    protected $fillable = [
        'name',
        'description',
        'hierarchy_level',
    ];

    /**
     * Get all users with this role
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Check if this role has higher or equal hierarchy than another role
     */
    public function hasHigherOrEqualHierarchyThan(Role $otherRole): bool
    {
        return $this->hierarchy_level >= $otherRole->hierarchy_level;
    }

    /**
     * Check if this role has higher hierarchy than another role
     */
    public function hasHigherHierarchyThan(Role $otherRole): bool
    {
        return $this->hierarchy_level > $otherRole->hierarchy_level;
    }

    /**
     * Get display name (usa description se existir, senão usa name formatado)
     */
    public function getDisplayNameAttribute(): string
    {
        if (!empty($this->description)) {
            return $this->description;
        }

        // Formata o name: converte de snake_case para Title Case
        return ucwords(str_replace('_', ' ', $this->name));
    }
}
