<?php

namespace App\Models\garbage;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\user\User;

class GarbageRun extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'garbage_runs';

    protected $fillable = [
        'vehicle_id',
        'user_id',
        'weighing_id',
        'start_km',
        'end_km',
        'started_at',
        'finished_at',
        'stop_point',
        'status',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(GarbageVehicle::class, 'vehicle_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\user\User::class, 'user_id');
    }

    public function weighing(): BelongsTo
    {
        return $this->belongsTo(GarbageWeighing::class, 'weighing_id');
    }

    public function destinations(): HasMany
    {
        return $this->hasMany(GarbageRunDestination::class, 'garbage_run_id');
    }

    public function signature(): HasOne
    {
        return $this->hasOne(GarbageRunSignature::class, 'garbage_run_id');
    }

    public function checklist()
    {
        return $this->morphOne(\App\Models\checklist\Checklist::class, 'checklistable');
    }
}
