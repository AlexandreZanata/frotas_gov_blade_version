<?php

namespace App\Models\user;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPhotoCnh extends Model
{
    use HasFactory;

    protected $table = 'user_photos_cnh';

    protected $fillable = [
        'user_id',
        'photo_type',
        'path'
    ];

    protected $casts = [
        'id' => 'string'
    ];

    /**
     * Relação com o usuário
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Acessor para a URL completa da foto
     */
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }

    /**
     * Acessor para o caminho completo do arquivo
     */
    public function getFullPathAttribute(): string
    {
        return storage_path('app/public/' . $this->path);
    }
}
