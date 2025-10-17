<?php

namespace App\Models\Manager;

use App\Models\user\Secretariat;
use App\Models\user\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecretariatSectorManager extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'secretariat_id',
        'manager_status_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function secretariat(): BelongsTo
    {
        return $this->belongsTo(Secretariat::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ManagerStatus::class, 'manager_status_id');
    }
}
