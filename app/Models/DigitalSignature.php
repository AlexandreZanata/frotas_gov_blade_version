<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DigitalSignature extends Model {
    use HasFactory, HasUuids;
    protected $fillable = ['user_id', 'signature_code'];

    // Relação para buscar o usuário dono da assinatura
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
