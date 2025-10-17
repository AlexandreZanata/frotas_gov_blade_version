<?php

namespace App\Models\Vehicle;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehiclePriceCurrent extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['vehicle_id', 'current_amount'];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
