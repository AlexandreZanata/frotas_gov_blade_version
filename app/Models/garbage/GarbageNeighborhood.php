<?php

namespace App\Models\garbage;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class GarbageNeighborhood extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['name'];

    public function garbageUsers(): BelongsToMany
    {
        return $this->belongsToMany(
            GarbageUser::class,
            'garbage_user_neighborhoods',
            'garbage_neighborhood_id',
            'garbage_user_id'
        )->withTimestamps();
    }

    public function runDestinations()
    {
        return $this->hasMany(GarbageRunDestination::class, 'garbage_neighborhood_id');
    }
}
