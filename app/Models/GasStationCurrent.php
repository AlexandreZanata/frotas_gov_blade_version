<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GasStationCurrent extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'gas_stations_current';

    protected $fillable = [
        'gas_station_id',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function gasStation(): BelongsTo
    {
        return $this->belongsTo(GasStation::class);
    }
}
