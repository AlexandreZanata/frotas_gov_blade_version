<?php

namespace App\Models\Manager;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneralManager extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'manager_status_id',
    ];

    /**
     * Define o relacionamento com o usuário.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define o relacionamento com o status do gestor.
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(ManagerStatus::class, 'manager_status_id');
    }
}
