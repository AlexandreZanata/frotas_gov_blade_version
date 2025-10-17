<?php
namespace App\Models\maintenance;
use App\Models\defect\DefectReport;
use App\Models\user\User;
use App\Models\Vehicle\Vehicle;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceOrder extends Model {
    use HasFactory, HasUuids;
    protected $fillable = [
        'defect_report_id', 'vehicle_id', 'mechanic_id', 'status', 'quote_status',
        'quote_total_amount', 'quote_approver_id', 'quote_approved_at', 'approver_notes',
        'service_started_at', 'service_completed_at', 'mechanic_notes'
    ];
    protected $casts = [
        'quote_approved_at' => 'datetime',
        'service_started_at' => 'datetime',
        'service_completed_at' => 'datetime',
    ];

    public function defectReport(): BelongsTo { return $this->belongsTo(DefectReport::class); }
    public function vehicle(): BelongsTo { return $this->belongsTo(Vehicle::class); }
    public function mechanic(): BelongsTo { return $this->belongsTo(User::class, 'mechanic_id'); }
    public function quoteApprover(): BelongsTo { return $this->belongsTo(User::class, 'quote_approver_id'); }
    public function items(): HasMany { return $this->hasMany(ServiceOrderItem::class); }
    public function histories(): HasMany { return $this->hasMany(ServiceOrderStatusHistory::class); }
}
