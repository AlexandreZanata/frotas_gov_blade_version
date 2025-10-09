<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class ChatRoom extends Model {
    use HasFactory, HasUuids;
    protected $fillable = ['name', 'type'];
    protected $casts = ['created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function participants(): BelongsToMany {
        return $this->belongsToMany(User::class, 'chat_participants', 'chat_room_id', 'user_id')
            ->withPivot('last_read_at')
            ->withTimestamps();
    }

    public function messages(): HasMany {
        return $this->hasMany(ChatMessage::class)->orderBy('created_at', 'asc');
    }

    public function latestMessage() {
        return $this->hasOne(ChatMessage::class)->latestOfMany();
    }

    /**
     * Obter contagem de mensagens não lidas para um usuário
     */
    public function getUnreadCountForUser($userId): int
    {
        $participant = DB::table('chat_participants')
            ->where('chat_room_id', $this->id)
            ->where('user_id', $userId)
            ->first();

        if (!$participant) {
            return 0;
        }

        return $this->messages()
            ->where('user_id', '!=', $userId)
            ->where(function ($query) use ($participant) {
                if ($participant->last_read_at) {
                    $query->where('created_at', '>', $participant->last_read_at);
                }
            })
            ->count();
    }

    /**
     * Obter nome de exibição da sala
     */
    public function getDisplayName($currentUserId): string
    {
        if ($this->type === 'group') {
            return $this->name ?? 'Grupo sem nome';
        }

        // Para chats privados, mostrar nome do outro usuário
        $otherUser = $this->participants->firstWhere('id', '!=', $currentUserId);
        return $otherUser ? $otherUser->name : 'Usuário desconhecido';
    }

    /**
     * Obter avatar da sala
     */
    public function getAvatarUrl($currentUserId): string
    {
        if ($this->type === 'group') {
            return 'https://ui-avatars.com/api/?name=' . urlencode($this->name ?? 'Group') . '&background=10b981&color=fff';
        }

        $otherUser = $this->participants->firstWhere('id', '!=', $currentUserId);
        return $otherUser
            ? 'https://ui-avatars.com/api/?name=' . urlencode($otherUser->name) . '&background=3b82f6&color=fff'
            : 'https://ui-avatars.com/api/?name=?&background=6b7280&color=fff';
    }

    /**
     * Obter o outro usuário em chat privado
     */
    public function getOtherUser($currentUserId): ?User
    {
        if ($this->type !== 'private') {
            return null;
        }

        return $this->participants->firstWhere('id', '!=', $currentUserId);
    }
}
