<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduledPriceMovement extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'scheduled_price_id',
        'user_id',
        'action',
        'old_price',
        'new_price',
        'action_date',
    ];

    protected $casts = [
        'action_date' => 'datetime',
        'old_price' => 'decimal:3',
        'new_price' => 'decimal:3',
    ];

    public function scheduledPrice(): BelongsTo
    {
        return $this->belongsTo(ScheduledPrice::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
