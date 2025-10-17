<?php

namespace App\Models\Balance;

use App\Models\Secretariat;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class BalanceExpense extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'secretariat_id',
        'user_id',
        'description',
        'amount',
        'expense_date',
        'type',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function secretariat(): BelongsTo
    {
        return $this->belongsTo(Secretariat::class);
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
