<?php
// app/Models/LogbookRule.php

namespace App\Models\logbook;

use App\Models\user\User;
use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\VehicleCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LogbookRule extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'description',
        'rule_type',
        'fixed_value',
        'formula_type',
        'formula_value',
        'target_type',
        'target_id',
        'is_active'
    ];

    protected $casts = [
        'id' => 'string',
        'target_id' => 'string',
        'fixed_value' => 'integer',
        'formula_value' => 'integer',
        'is_active' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    // Relacionamentos
    public function vehicleCategory()
    {
        return $this->belongsTo(VehicleCategory::class, 'target_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'target_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'target_id');
    }

    // Escopos
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForTarget($query, $targetType, $targetId = null)
    {
        return $query->where(function($q) use ($targetType, $targetId) {
            $q->where('target_type', 'global')
                ->orWhere(function($q2) use ($targetType, $targetId) {
                    $q2->where('target_type', $targetType)
                        ->where('target_id', $targetId);
                });
        });
    }

    // Métodos de helper
    public function calculateMaxKm($dailyAverage = null)
    {
        if ($this->rule_type === 'fixed') {
            return $this->fixed_value;
        }

        if ($this->rule_type === 'formula' && $dailyAverage) {
            switch ($this->formula_type) {
                case 'daily_average_plus_fixed':
                    return $dailyAverage + $this->formula_value;
                case 'daily_average_plus_percentage':
                    return $dailyAverage + ($dailyAverage * $this->formula_value / 100);
            }
        }

        return null;
    }

    public function getTargetNameAttribute()
    {
        switch ($this->target_type) {
            case 'global':
                return 'Global';
            case 'vehicle_category':
                return $this->vehicleCategory->name ?? 'Categoria não encontrada';
            case 'user':
                return $this->user->name ?? 'Usuário não encontrado';
            case 'vehicle':
                return $this->vehicle->name ?? 'Veículo não encontrado';
            default:
                return 'Desconhecido';
        }
    }
}
