<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model {
    use HasFactory, HasUuids;
    protected $fillable = ['category_id', 'name', 'sku', 'description', 'quantity_on_hand', 'unit_of_measure', 'reorder_level'];
    public function category(): BelongsTo { return $this->belongsTo(InventoryItemCategory::class, 'category_id'); }
    public function movements(): HasMany { return $this->hasMany(InventoryMovement::class, 'inventory_item_id'); }
}
