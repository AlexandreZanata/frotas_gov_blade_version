<?php

// app/Models/Balance/BalanceGasStationSupplier.php
namespace App\Models\Balance;

use App\Models\fuel\GasStation;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BalanceGasStationSupplier extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'gas_station_id',
        'procurement_type',
        'bidding_modality',
        'process_number',
        'contract_number',
        'encrypted_contract_key',
        'contract_start_date',
        'contract_end_date',
        'document_paths',
        'supplier_document',
        'total_contract_value',
        'legal_notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'document_paths' => 'array', // Converte o JSON para array e vice-versa
        'total_contract_value' => 'decimal:2',
    ];

    /**
     * Define o relacionamento: Um fornecedor pertence a um Posto de Gasolina.
     */
    public function gasStation(): BelongsTo
    {
        return $this->belongsTo(GasStation::class);
    }
}
