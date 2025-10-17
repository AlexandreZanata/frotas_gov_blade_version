<?php
namespace App\Models\fines;
use App\Models\DigitalSignature;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FineSignature extends Model {
    use HasFactory, HasUuids;
    protected $fillable = ['fine_id', 'digital_signature_id', 'signed_at', 'ip_address'];
    protected $casts = ['signed_at' => 'datetime'];

    public function fine(): BelongsTo { return $this->belongsTo(Fine::class); }
    public function digitalSignature(): BelongsTo { return $this->belongsTo(DigitalSignature::class); }
}
