<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Fine extends Model {
    use HasFactory, HasUuids;
    protected $fillable = ['vehicle_id', 'driver_id', 'registered_by_user_id', 'infraction_code', 'description', 'location', 'issued_at', 'amount', 'due_date', 'status', 'attachment_path'];
    protected $casts = ['issued_at' => 'datetime', 'due_date' => 'date'];

    public function vehicle(): BelongsTo { return $this->belongsTo(Vehicle::class); }
    public function driver(): BelongsTo { return $this->belongsTo(User::class, 'driver_id'); }
    public function registeredBy(): BelongsTo { return $this->belongsTo(User::class, 'registered_by_user_id'); }
    public function processes(): HasMany { return $this->hasMany(FineProcess::class); }
    public function signature(): HasOne { return $this->hasOne(FineSignature::class); }
}
