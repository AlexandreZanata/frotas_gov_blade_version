<?php

namespace App\Models\garbage;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GarbageChecklistAnswer extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'garbage_checklist_id',
        'garbage_checklist_item_id',
        'status',
        'notes',
    ];

    public function checklist(): BelongsTo
    {
        return $this->belongsTo(GarbageChecklist::class, 'garbage_checklist_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(GarbageChecklistItem::class, 'garbage_checklist_item_id');
    }
}
