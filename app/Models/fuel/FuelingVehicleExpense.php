<?php

namespace App\Models\fuel;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FuelingVehicleExpense extends Model
{
    use HasFactory, HasUuids;


    protected $table = 'fuelings_vehicles_expenses';

    protected $fillable = ['vehicle_id', 'total_fuel_cost'];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
