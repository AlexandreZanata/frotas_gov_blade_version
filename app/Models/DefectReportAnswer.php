<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DefectReportAnswer extends Model {
    use HasFactory, HasUuids;
    protected $fillable = ['defect_report_id', 'defect_report_item_id', 'severity', 'notes'];

    public function report(): BelongsTo { return $this->belongsTo(DefectReport::class); }
    public function item(): BelongsTo { return $this->belongsTo(DefectReportItem::class, 'defect_report_item_id'); }
}
