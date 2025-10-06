<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends Model {
    use HasFactory, HasUuids;
    protected $fillable = ['inventory_item_id', 'user_id', 'type', 'quantity', 'reason', 'movement_date'];
    protected $casts = ['movement_date' => 'datetime'];
    public function item(): BelongsTo { return $this->belongsTo(InventoryItem::class, 'inventory_item_id'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
