<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tire extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'inventory_item_id', // Adicionado
        'brand',
        'model',
        'serial_number',
        'dot_number',
        'purchase_date',
        'purchase_price',
        'lifespan_km',
        'current_km',
        'status',
        'condition',
        'current_vehicle_id',
        'current_position',
        'notes',
    ];

    // Relacionamento com o item de inventário (o "tipo" de pneu)
    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'current_vehicle_id');
    }

    public function events()
    {
        return $this->hasMany(TireEvent::class);
    }
}
