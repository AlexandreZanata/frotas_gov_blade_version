<?php

namespace App\Models\garbage;

use App\Models\user\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GarbageChecklist extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'garbage_run_id',
        'user_id',
        'notes',
        'has_defects',
        'approval_status',
        'approver_id',
        'approver_comment',
        'approved_at',
    ];

    protected $casts = [
        'has_defects' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function garbageRun(): BelongsTo
    {
        return $this->belongsTo(GarbageRun::class, 'garbage_run_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(GarbageChecklistAnswer::class, 'garbage_checklist_id');
    }
}
