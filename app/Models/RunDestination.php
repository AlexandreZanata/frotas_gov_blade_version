<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RunDestination extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'run_id',
        'destination',
        'order',
    ];

    /**
     * Get the run that owns the destination
     */
    public function run(): BelongsTo
    {
        return $this->belongsTo(Run::class);
    }
}
