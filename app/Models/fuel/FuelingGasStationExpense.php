<?php

namespace App\Models\fuel;

use App\Models\fuel\GasStation;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FuelingGasStationExpense extends Model
{
    // Habilita o uso de HasFactory para seeders/testes e HasUuids para a chave primária
    use HasFactory, HasUuids;

    /**
     * O nome da tabela associada ao model.
     *
     * @var string
     */
    protected $table = 'fuelings_gas_stations_expenses';

    /**
     * Os atributos que podem ser atribuídos em massa (necessário para o firstOrCreate do Observer).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'gas_station_id',
        'total_fuel_cost',
    ];

    /**
     * Os atributos que devem ser convertidos para tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_fuel_cost' => 'decimal:2', // Garante que o valor seja tratado como decimal
    ];

    /**
     * Define a relação inversa: Uma despesa pertence a um Posto de Gasolina.
     */
    public function gasStation(): BelongsTo
    {
        return $this->belongsTo(GasStation::class, 'gas_station_id');
    }
}
