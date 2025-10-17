<?php
namespace App\Models\fines;
use App\Models\user\User;
use App\Models\Vehicle\Vehicle;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Fine extends Model {
    use HasFactory, HasUuids;
    protected $fillable = [
        'infraction_notice_id',
        'vehicle_id',
        'driver_id',
        'registered_by_user_id',
        'infraction_code',
        'description',
        'location',
        'issued_at',
        'amount',
        'due_date',
        'status',
        'attachment_path',
        'notification_sent',
        'notification_sent_at',
        'acknowledged_by_driver',
        'acknowledged_at',
        'first_viewed_at',
        'first_viewed_by'
    ];
    protected $casts = [
        'issued_at' => 'datetime',
        'due_date' => 'date',
        'notification_sent' => 'boolean',
        'notification_sent_at' => 'datetime',
        'acknowledged_by_driver' => 'boolean',
        'acknowledged_at' => 'datetime',
        'first_viewed_at' => 'datetime'
    ];

    public function vehicle(): BelongsTo {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver(): BelongsTo {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function registeredBy(): BelongsTo {
        return $this->belongsTo(User::class, 'registered_by_user_id');
    }

    public function infractionNotice(): BelongsTo {
        return $this->belongsTo(InfractionNotice::class);
    }

    public function infractions(): HasMany {
        return $this->hasMany(Infraction::class);
    }

    public function attachments(): HasMany {
        return $this->hasMany(FineAttachment::class);
    }

    public function processes(): HasMany {
        return $this->hasMany(FineProcess::class);
    }

    public function signature(): HasOne {
        return $this->hasOne(FineSignature::class);
    }

    public function viewLogs(): HasMany {
        return $this->hasMany(FineViewLog::class);
    }

    public function firstViewer(): BelongsTo {
        return $this->belongsTo(User::class, 'first_viewed_by');
    }

    public function getTotalAmountAttribute(): float
    {
        return $this->infractions->sum('final_amount');
    }

    public function getTotalPointsAttribute(): int
    {
        return $this->infractions->sum('points');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending_acknowledgement' => 'Aguardando Ciência',
            'pending_payment' => 'Aguardando Pagamento',
            'paid' => 'Pago',
            'appealed' => 'Recorrida',
            'cancelled' => 'Cancelada',
            default => $this->status
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending_acknowledgement' => 'yellow',
            'pending_payment' => 'orange',
            'paid' => 'green',
            'appealed' => 'blue',
            'cancelled' => 'gray',
            default => 'gray'
        };
    }
}
