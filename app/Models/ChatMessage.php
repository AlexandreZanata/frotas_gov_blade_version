<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class ChatMessage extends Model {
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = ['chat_room_id', 'user_id', 'message', 'attachment_path', 'attachment_type'];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];
    protected $with = ['user', 'readReceipts'];
    protected $appends = ['attachment_url', 'is_image', 'is_file'];

    public function chatRoom(): BelongsTo {
        return $this->belongsTo(ChatRoom::class);
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function readReceipts(): HasMany {
        return $this->hasMany(ChatMessageReadReceipt::class);
    }

    /**
     * Verificar se mensagem foi lida por um usuário específico
     */
    public function isReadBy($userId): bool
    {
        return $this->readReceipts()->where('user_id', $userId)->exists();
    }

    /**
     * Marcar mensagem como lida por um usuário
     */
    public function markAsReadBy($userId): void
    {
        if (!$this->isReadBy($userId) && $this->user_id != $userId) {
            $this->readReceipts()->create([
                'user_id' => $userId,
                'read_at' => now(),
            ]);
        }
    }

    /**
     * Obter URL do anexo
     */
    public function getAttachmentUrlAttribute(): ?string
    {
        if (!$this->attachment_path) {
            return null;
        }

        return Storage::url($this->attachment_path);
    }

    /**
     * Verificar se o anexo é uma imagem
     */
    public function getIsImageAttribute(): bool
    {
        return in_array($this->attachment_type, ['image', 'jpg', 'jpeg', 'png', 'gif', 'webp']);
    }

    /**
     * Verificar se o anexo é um arquivo
     */
    public function getIsFileAttribute(): bool
    {
        return $this->attachment_path && !$this->is_image;
    }

    /**
     * Obter status de leitura da mensagem
     * - 'sent': Enviada
     * - 'delivered': Entregue (todos os participantes receberam)
     * - 'read': Lida (todos leram)
     */
    public function getReadStatus(): string
    {
        $roomParticipantsCount = $this->chatRoom->participants()->count() - 1; // Excluir o remetente

        if ($roomParticipantsCount === 0) {
            return 'sent';
        }

        $readCount = $this->readReceipts()->count();

        if ($readCount === $roomParticipantsCount) {
            return 'read';
        } elseif ($readCount > 0) {
            return 'delivered';
        }

        return 'sent';
    }

    /**
     * Formatar hora de envio
     */
    public function getFormattedTimeAttribute(): string
    {
        return $this->created_at->format('H:i');
    }

    /**
     * Verificar se é mensagem de hoje
     */
    public function getIsTodayAttribute(): bool
    {
        return $this->created_at->isToday();
    }

    /**
     * Obter data formatada
     */
    public function getFormattedDateAttribute(): string
    {
        if ($this->created_at->isToday()) {
            return 'Hoje';
        } elseif ($this->created_at->isYesterday()) {
            return 'Ontem';
        } elseif ($this->created_at->isCurrentWeek()) {
            return $this->created_at->locale('pt_BR')->dayName;
        }

        return $this->created_at->format('d/m/Y');
    }
}
