<?php

namespace App\Models\maintenance;

use App\Models\user\User;
use App\Models\Vehicle\Vehicle;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TireEvent extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'tire_id',
        'user_id',
        'vehicle_id',
        'event_type',
        'description',
        'km_at_event',
        'event_date',
    ];

    public function tire()
    {
        return $this->belongsTo(Tire::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
