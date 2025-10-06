<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fueling extends Model {
    use HasFactory, HasUuids;
    protected $fillable = [
        'user_id', 'vehicle_id', 'fuel_type_id', 'gas_station_id',
        'fueled_at', 'km', 'liters', 'value_per_liter',
        'invoice_path', 'public_code', 'signature_path', 'viewed_by'
    ];
    protected $casts = [
        'fueled_at' => 'datetime',
        'viewed_by' => 'array',
    ];
}
