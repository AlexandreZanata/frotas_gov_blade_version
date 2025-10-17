<?php
namespace App\Models\chat;
use App\Models\user\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessageReadReceipt extends Model {
    use HasFactory, HasUuids;
    protected $fillable = ['chat_message_id', 'user_id', 'read_at'];
    protected $casts = ['read_at' => 'datetime', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function message(): BelongsTo {
        return $this->belongsTo(ChatMessage::class, 'chat_message_id');
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
