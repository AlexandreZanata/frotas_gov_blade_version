<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatRoom extends Model {
    use HasFactory, HasUuids;
    protected $fillable = ['name', 'type', 'last_message_id'];
    public function participants(): HasMany { return $this->hasMany(ChatParticipant::class); }
    public function messages(): HasMany { return $this->hasMany(ChatMessage::class); }
}
