<?php

namespace App\Models\Balance;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BalanceCommitmentItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'commitment_id',
        'description',
        'quantity',
        'unit_of_measure',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function commitment(): BelongsTo
    {
        return $this->belongsTo(BalanceCommitment::class, 'commitment_id');
    }
}
