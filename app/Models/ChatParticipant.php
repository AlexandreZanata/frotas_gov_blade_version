<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatParticipant extends Model {
    use HasFactory, HasUuids;
    protected $fillable = ['chat_room_id', 'user_id', 'last_read_at'];
    protected $casts = ['last_read_at' => 'datetime'];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function room(): BelongsTo { return $this->belongsTo(ChatRoom::class, 'chat_room_id'); }
}
