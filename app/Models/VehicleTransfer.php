<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleTransfer extends Model {
    use HasFactory, HasUuids;

    protected $fillable = [
        'vehicle_id', 'origin_secretariat_id', 'destination_secretariat_id',
        'requester_id', 'approver_id', 'type', 'status', 'processed_at',
        'start_date', 'end_date', 'returned_at', 'request_notes', 'approver_notes'
    ];

    // --- MODIFICADO AQUI ---
    // Alterado de 'date' para 'datetime'
    protected $casts = [
        'processed_at' => 'datetime',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'returned_at' => 'datetime',
    ];

    public function vehicle(): BelongsTo { return $this->belongsTo(Vehicle::class); }
    public function originSecretariat(): BelongsTo { return $this->belongsTo(Secretariat::class, 'origin_secretariat_id'); }
    public function destinationSecretariat(): BelongsTo { return $this->belongsTo(Secretariat::class, 'destination_secretariat_id'); }
    public function requester(): BelongsTo { return $this->belongsTo(User::class, 'requester_id'); }
    public function approver(): BelongsTo { return $this->belongsTo(User::class, 'approver_id'); }
    public function histories(): HasMany { return $this->hasMany(VehicleTransferHistory::class); }
}
