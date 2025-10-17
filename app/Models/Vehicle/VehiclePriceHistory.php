<?php

namespace App\Models\Vehicle;

use App\Models\user\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class VehiclePriceHistory extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'vehicle_id',
        'user_id',
        'acquisition_type_id',
        'amount',
        'description',
        'transactionable_type',
        'transactionable_id',
        'transaction_date',
    ];

    protected $casts = ['transaction_date' => 'datetime'];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactionable(): MorphTo
    {
        return $this->morphTo();
    }
}
