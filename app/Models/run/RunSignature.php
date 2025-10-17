<?php
namespace App\Models\run;
use App\Models\DigitalSignature;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RunSignature extends Model {
    use HasFactory, HasUuids;
    protected $fillable = [
        'run_id', 'driver_signature_id', 'driver_signed_at',
        'admin_signature_id', 'admin_signed_at'
    ];
    protected $casts = [
        'driver_signed_at' => 'datetime',
        'admin_signed_at' => 'datetime',
    ];

    public function run(): BelongsTo { return $this->belongsTo(Run::class); }
    public function driverSignature(): BelongsTo { return $this->belongsTo(DigitalSignature::class, 'driver_signature_id'); }
    public function adminSignature(): BelongsTo { return $this->belongsTo(DigitalSignature::class, 'admin_signature_id'); }
}
