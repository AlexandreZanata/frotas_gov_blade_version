<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ChecklistAnswer extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = ['checklist_id', 'checklist_item_id', 'status', 'notes'];

    public function checklist(): BelongsTo { return $this->belongsTo(Checklist::class); }
    public function item(): BelongsTo { return $this->belongsTo(ChecklistItem::class, 'checklist_item_id'); }

    // Alias para manter compatibilidade
    public function checklistItem(): BelongsTo { return $this->item(); }
}
