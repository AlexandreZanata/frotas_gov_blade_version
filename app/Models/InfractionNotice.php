<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class InfractionNotice extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'notice_number',
        'security_code',
        'description',
        'issued_at',
        'issuing_authority'
    ];

    protected $casts = [
        'issued_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->security_code)) {
                $model->security_code = strtoupper(Str::random(8));
            }
        });
    }

    public function fines(): HasMany
    {
        return $this->hasMany(Fine::class);
    }
}
