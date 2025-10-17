<?php

namespace App\Models\auditlog;

use App\Models\user\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    use HasFactory, HasUuids;

    // Um log só é criado, nunca atualizado
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'action',
        'auditable_id',
        'auditable_type',
        'old_values',
        'new_values',
        'description',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }

    /**
     * Relação para buscar o registro associado (um Veículo, Usuário, etc.).
     */
    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Relação para buscar o usuário que realizou a ação.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
