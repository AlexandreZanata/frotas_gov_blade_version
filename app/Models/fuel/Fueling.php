<?php
namespace App\Models\fuel;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use App\Models\Vehicle\Vehicle;

class Fueling extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id', 'vehicle_id', 'run_id', 'fuel_type_id', 'gas_station_id', 'gas_station_name',
        'fueled_at', 'km',
        'liters',
        'value',
        'value_per_liter',
        'invoice_path', 'public_code',
        'signature_id',
        'viewed_by'
    ];

    protected $casts = [
        'fueled_at' => 'datetime',
        'viewed_by' => 'array',
        'liters' => 'decimal:3',
        'value' => 'decimal:2',
        'value_per_liter' => 'decimal:2',
    ];

    // --- Boot method para lógicas automáticas ---
    protected static function boot()
    {
        parent::boot();

        // Gera o código "ABS-..." automaticamente ao criar
        static::creating(function ($model) {
            if (empty($model->public_code)) {
                // Alterado prefixo para ABS
                $model->public_code = 'ABS-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
            }
        });

        // Calcula o value_per_liter sempre que salvar
        static::saving(function ($model) {
            if (isset($model->value) && isset($model->liters) && $model->liters > 0) {
                // Calcula e arredonda para 2 casas decimais
                $model->value_per_liter = round($model->value / $model->liters, 2);
            } else {
                $model->value_per_liter = null; // Ou 0.00 se preferir
            }
        });
    }

    // --- Relacionamentos ---

    public function signature(): HasOne
    {
        // Certifique-se que o namespace está correto
        return $this->hasOne(FuelingSignature::class);
    }

    // Mantenha seus relacionamentos existentes
    public function user(): BelongsTo
    {
        // Ajuste o namespace se o seu model User estiver em App\Models\user\User
        return $this->belongsTo(\App\Models\user\User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function run(): BelongsTo
    {
        return $this->belongsTo(Run::class);
    }

    public function fuelType(): BelongsTo
    {
        return $this->belongsTo(FuelType::class);
    }

    public function gasStation(): BelongsTo
    {
        return $this->belongsTo(GasStation::class);
    }
}
