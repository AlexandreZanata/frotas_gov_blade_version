<?php

namespace App\Models\user;

use App\Models\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPhoto extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'photo_type',
        'path',
    ];

    /**
     * Relação para buscar o usuário dono da foto.
     */
    /**
     * Relação com o usuário proprietário da foto
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relação com usuários que usam esta foto como principal
     */
    public function usersUsingAsProfile(): HasMany
    {
        return $this->hasMany(User::class, 'photo_id');
    }

    /**
     * Acessor para obter a URL completa da foto
     */
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }
}
