<?php
namespace App\Models\maintenance;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItemCategory extends Model {
    use HasFactory, HasUuids;
    protected $fillable = ['name', 'description'];
    public function items(): HasMany { return $this->hasMany(InventoryItem::class, 'category_id'); }
}
