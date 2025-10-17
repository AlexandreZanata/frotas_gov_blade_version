<?php
namespace App\Models\defect;
use App\Models\user\User;
use App\Models\Vehicle\Vehicle;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DefectReport extends Model {
    use HasFactory, HasUuids;
    protected $fillable = ['vehicle_id', 'user_id', 'status', 'notes'];

    public function vehicle(): BelongsTo { return $this->belongsTo(Vehicle::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function answers(): HasMany { return $this->hasMany(DefectReportAnswer::class); }
}
