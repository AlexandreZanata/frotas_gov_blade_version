<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Infraction extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'fine_id',
        'infraction_code',
        'description',
        'base_amount',
        'extra_fees',
        'discount_amount',
        'discount_percentage',
        'final_amount',
        'points',
        'severity'
    ];

    protected $casts = [
        'base_amount' => 'decimal:2',
        'extra_fees' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'points' => 'integer'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->calculateFinalAmount();
        });
    }

    public function calculateFinalAmount(): void
    {
        $amount = $this->base_amount + $this->extra_fees;

        if ($this->discount_percentage > 0) {
            $amount -= ($amount * ($this->discount_percentage / 100));
        }

        $amount -= $this->discount_amount;

        $this->final_amount = max(0, $amount);
    }

    public function fine(): BelongsTo
    {
        return $this->belongsTo(Fine::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(FineAttachment::class);
    }
}
