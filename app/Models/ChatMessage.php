<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatMessage extends Model {
    use HasFactory, HasUuids, SoftDeletes;
    protected $fillable = ['chat_room_id', 'user_id', 'message', 'attachment_path', 'attachment_type'];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function room(): BelongsTo { return $this->belongsTo(ChatRoom::class, 'chat_room_id'); }
}
