<?php

namespace App\Models;

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
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
