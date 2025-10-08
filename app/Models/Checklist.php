<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Checklist extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'run_id',
        'user_id',
        'notes',
        'has_defects',
        'approval_status',
        'approver_id',
        'approver_comment',
        'approved_at'
    ];

    protected $casts = [
        'has_defects' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function run(): BelongsTo { return $this->belongsTo(Run::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function approver(): BelongsTo { return $this->belongsTo(User::class, 'approver_id'); }
    public function answers(): HasMany { return $this->hasMany(ChecklistAnswer::class); }

    // Verificar se tem algum item com problema
    public function hasProblems(): bool
    {
        return $this->answers()->where('status', 'problem')->exists();
    }

    // Verificar se está pendente
    public function isPending(): bool
    {
        return $this->approval_status === 'pending';
    }

    // Verificar se foi aprovado
    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }

    // Verificar se foi rejeitado
    public function isRejected(): bool
    {
        return $this->approval_status === 'rejected';
    }
}
