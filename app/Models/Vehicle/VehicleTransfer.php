<?php

namespace App\Models\Vehicle;

use App\Models\user\Secretariat;
use App\Models\user\User;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleTransfer extends Model
{
    use HasFactory, HasUuids, Auditable;

    protected $fillable = [
        'vehicle_id',
        'origin_secretariat_id',
        'destination_secretariat_id',
        'requester_id',
        'approver_id',
        'type',
        'status',
        'start_date',
        'end_date',
        'processed_at',
        'returned_at',
        'request_notes',
        'approver_notes',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'processed_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    // Relationships
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function originSecretariat(): BelongsTo
    {
        return $this->belongsTo(Secretariat::class, 'origin_secretariat_id');
    }

    public function destinationSecretariat(): BelongsTo
    {
        return $this->belongsTo(Secretariat::class, 'destination_secretariat_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeTemporary($query)
    {
        return $query->where('type', 'temporary');
    }

    public function scopePermanent($query)
    {
        return $query->where('type', 'permanent');
    }

    // Helper Methods
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isReturned(): bool
    {
        return $this->status === 'returned';
    }

    public function isTemporary(): bool
    {
        return $this->type === 'temporary';
    }

    public function isPermanent(): bool
    {
        return $this->type === 'permanent';
    }

    public function canBeApprovedBy(User $user): bool
    {
        // Gestor Geral pode aprovar tudo
        if ($user->role->name === 'general_manager') {
            return true;
        }

        // Gestor Setorial só pode aprovar transferências da sua secretaria
        if ($user->role->name === 'sector_manager') {
            return $this->origin_secretariat_id === $user->secretariat_id;
        }

        return false;
    }

    public function canBeReturnedBy(User $user): bool
    {
        // Apenas transferências temporárias aprovadas podem ser devolvidas
        if (!$this->isApproved() || !$this->isTemporary()) {
            return false;
        }

        // Gestor Geral pode devolver qualquer veículo
        if ($user->role->name === 'general_manager') {
            return true;
        }

        // Gestor Setorial pode devolver apenas veículos da sua secretaria (origem)
        if ($user->role->name === 'sector_manager') {
            return $this->origin_secretariat_id === $user->secretariat_id;
        }

        // Usuário comum pode devolver apenas os carros que ele solicitou
        return $this->requester_id === $user->id;
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
            'approved' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
            'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
            'returned' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400',
        };
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'pending' => 'Pendente',
            'approved' => 'Aprovado',
            'rejected' => 'Rejeitado',
            'returned' => 'Devolvido',
            default => 'Desconhecido',
        };
    }

    public function getTypeLabel(): string
    {
        return match($this->type) {
            'permanent' => 'Permanente',
            'temporary' => 'Temporário',
            default => 'Desconhecido',
        };
    }
}

