<?php
namespace App\Models\Vehicle;
use App\Models\user\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleTransferHistory extends Model {
    use HasFactory, HasUuids;
    public const UPDATED_AT = null;
    protected $fillable = ['vehicle_transfer_id', 'user_id', 'status', 'notes'];

    public function transfer(): BelongsTo { return $this->belongsTo(VehicleTransfer::class, 'vehicle_transfer_id'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
