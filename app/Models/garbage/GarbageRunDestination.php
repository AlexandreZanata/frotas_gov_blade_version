<?php

namespace App\Models\garbage;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GarbageRunDestination extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'garbage_run_id',
        'type',
        'garbage_neighborhood_id',
        'comment',
        'order',
    ];

    public function garbageRun(): BelongsTo
    {
        return $this->belongsTo(GarbageRun::class);
    }

    public function neighborhood(): BelongsTo
    {
        return $this->belongsTo(GarbageNeighborhood::class, 'garbage_neighborhood_id');
    }
}
