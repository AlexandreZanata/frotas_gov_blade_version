<?php

namespace App\Models\Balance;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class BalanceMovement extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'movable_id',
        'movable_type',
        'type',
        'amount',
        'description',
        'moved_at',
    ];

    protected $casts = [
        'moved_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function movable(): MorphTo
    {
        return $this->morphTo();
    }
}
