<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FineProcess extends Model {
    use HasFactory, HasUuids;
    public const UPDATED_AT = null; // Registros de processo são imutáveis
    protected $fillable = ['fine_id', 'user_id', 'stage', 'notes'];

    public function fine(): BelongsTo { return $this->belongsTo(Fine::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
