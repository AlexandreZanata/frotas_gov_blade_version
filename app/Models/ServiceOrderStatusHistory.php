<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceOrderStatusHistory extends Model {
    use HasFactory, HasUuids;
    public const UPDATED_AT = null;
    protected $fillable = ['service_order_id', 'user_id', 'stage', 'notes'];

    public function serviceOrder(): BelongsTo { return $this->belongsTo(ServiceOrder::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
