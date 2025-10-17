<?php
namespace App\Models\defect;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DefectReportItem extends Model
{
    use HasFactory, HasUuids;

    // Atualiza o fillable para usar category_id
    protected $fillable = [
        'category_id',
        'name',
        'description'
    ];

    // Cria a relação com o novo model
    public function category(): BelongsTo
    {
        return $this->belongsTo(DefectCategory::class);
    }
}
