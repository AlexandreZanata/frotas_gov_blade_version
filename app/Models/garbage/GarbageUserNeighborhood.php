<?php

namespace App\Models\garbage;

use App\Models\garbage\GarbageUser;
use App\Models\garbage\GarbageNeighborhood;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class GarbageUserNeighborhood extends Pivot
{
    use HasFactory, HasUuids;

    protected $table = 'garbage_user_neighborhoods';

    protected $fillable = [
        'garbage_user_id',
        'garbage_neighborhood_id',
    ];

    // Relação com GarbageUser
    public function garbageUser(): BelongsTo
    {
        return $this->belongsTo(GarbageUser::class);
    }

    // Relação com GarbageNeighborhood
    public function garbageNeighborhood(): BelongsTo
    {
        return $this->belongsTo(GarbageNeighborhood::class);
    }
}
