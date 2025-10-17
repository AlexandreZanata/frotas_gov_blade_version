<?php

namespace App\Models\maintenance;

use App\Models\user\User;
use App\Models\Vehicle\Vehicle;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OilChange extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'vehicle_id',
        'user_id',
        'inventory_item_id',
        'km_at_change',
        'change_date',
        'liters_used',
        'cost',
        'next_change_km',
        'next_change_date',
        'notes',
        'service_provider',
    ];

    protected $casts = [
        'change_date' => 'date',
        'next_change_date' => 'date',
        'liters_used' => 'decimal:2',
        'cost' => 'decimal:2',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    /**
     * Verifica se a troca de óleo está vencida por KM
     */
    public function isOverdueByKm(int $currentKm): bool
    {
        return $currentKm >= $this->next_change_km;
    }

    /**
     * Verifica se a troca de óleo está vencida por data
     */
    public function isOverdueByDate(): bool
    {
        return now()->greaterThanOrEqualTo($this->next_change_date);
    }

    /**
     * Calcula o percentual de uso baseado em KM
     */
    public function getKmProgressPercentage(int $currentKm): float
    {
        $kmSinceLastChange = $currentKm - $this->km_at_change;
        $kmInterval = $this->next_change_km - $this->km_at_change;

        if ($kmInterval <= 0) {
            return 100;
        }

        return min(100, ($kmSinceLastChange / $kmInterval) * 100);
    }

    /**
     * Calcula o percentual de uso baseado em tempo
     */
    public function getDateProgressPercentage(): float
    {
        $daysSinceChange = now()->diffInDays($this->change_date);
        $totalDays = $this->next_change_date->diffInDays($this->change_date);

        if ($totalDays <= 0) {
            return 100;
        }

        return min(100, ($daysSinceChange / $totalDays) * 100);
    }

    /**
     * Retorna o status da próxima troca
     */
    public function getStatus(int $currentKm): string
    {
        $kmProgress = $this->getKmProgressPercentage($currentKm);
        $dateProgress = $this->getDateProgressPercentage();
        $maxProgress = max($kmProgress, $dateProgress);

        if ($maxProgress >= 100) {
            return 'vencido';
        } elseif ($maxProgress >= 90) {
            return 'critico';
        } elseif ($maxProgress >= 75) {
            return 'atencao';
        }

        return 'em_dia';
    }
}

