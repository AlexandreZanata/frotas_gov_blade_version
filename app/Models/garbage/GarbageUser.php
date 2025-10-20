<?php

namespace App\Models\garbage;

use App\Models\user\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\garbage\GarbageUserVehicle;
use App\Models\garbage\GarbageUserNeighborhood;

class GarbageUser extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['user_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicles(): BelongsToMany
    {
        return $this->belongsToMany(
            GarbageVehicle::class,
            'garbage_user_vehicles',
            'garbage_user_id',
            'garbage_vehicle_id'
        )
            ->using(GarbageUserVehicle::class)
            ->withTimestamps();
    }

    public function neighborhoods(): BelongsToMany
    {

        return $this->belongsToMany(
            GarbageNeighborhood::class,
            'garbage_user_neighborhoods',
            'garbage_user_id',
            'garbage_neighborhood_id'
        )
            ->using(GarbageUserNeighborhood::class)
            ->withTimestamps();
    }

    public function runs()
    {
        return $this->hasMany(GarbageRun::class, 'user_id');
    }

}
