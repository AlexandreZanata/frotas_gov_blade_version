<?php

namespace App\Models\Balance;

use App\Models\user\Secretariat;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BalanceCommitment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'secretariat_id',
        'supplier_id',
        'number',
        'year',
        'date',
        'total_value',
        'description',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'total_value' => 'decimal:2',
    ];

    public function secretariat(): BelongsTo
    {
        return $this->belongsTo(Secretariat::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(BalanceSupplier::class, 'supplier_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(BalanceCommitmentItem::class, 'commitment_id');
    }
}
