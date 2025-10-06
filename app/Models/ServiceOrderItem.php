<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceOrderItem extends Model {
    use HasFactory, HasUuids;
    protected $fillable = ['service_order_id', 'inventory_item_id', 'description', 'quantity', 'unit_price'];

    public function serviceOrder(): BelongsTo { return $this->belongsTo(ServiceOrder::class); }
    public function inventoryItem(): BelongsTo { return $this->belongsTo(InventoryItem::class); }
}
