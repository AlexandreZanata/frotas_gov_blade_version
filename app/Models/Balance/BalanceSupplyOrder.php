<?php

namespace App\Models\Balance;

use App\Models\user\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class BalanceSupplyOrder extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'commitment_id',
        'user_id',
        'number',
        'date',
        'value',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'value' => 'decimal:2',
    ];

    public function commitment(): BelongsTo
    {
        return $this->belongsTo(BalanceCommitment::class, 'commitment_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function movements(): MorphMany
    {
        return $this->morphMany(BalanceMovement::class, 'movable');
    }
}
