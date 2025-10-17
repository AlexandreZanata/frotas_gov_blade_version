<?php

namespace App\Models\Manager;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ManagerStatus extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['name', 'description'];

    public function sectorManagers(): HasMany
    {
        return $this->hasMany(SecretariatSectorManager::class, 'manager_status_id');
    }
}
