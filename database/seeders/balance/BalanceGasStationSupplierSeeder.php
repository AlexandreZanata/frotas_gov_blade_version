<?php

// database/seeders/BalanceGasStationSupplierSeeder.php
namespace Database\Seeders\balance;

use App\Models\Balance\BalanceGasStationSupplier;
use App\Models\fuel\GasStation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BalanceGasStationSupplierSeeder extends Seeder
{
    public function run(): void
    {
        // Pega o primeiro posto de gasolina cadastrado para usar como exemplo
        $gasStation = GasStation::where('name', 'Posto Central')->first();

        // Só cria o registro se o posto existir e se ainda não foi cadastrado
        if ($gasStation && !BalanceGasStationSupplier::where('gas_station_id', $gasStation->id)->exists()) {

            $contractNumber = 'CONTRATO-2025/001';

            BalanceGasStationSupplier::create([
                'gas_station_id' => $gasStation->id,

                // --- Dados do Processo ---
                'procurement_type' => 'bidding', // 'bidding' ou 'direct_contract'
                'bidding_modality' => 'Pregão Eletrônico',
                'process_number' => 'PROC-ADM-2025/123',

                // --- Dados do Contrato ---
                'contract_number' => $contractNumber,
                'encrypted_contract_key' => Hash::make($contractNumber . Str::random(10)), // Gera uma chave única e segura
                'contract_start_date' => now()->startOfYear(),
                'contract_end_date' => now()->endOfYear(),

                // --- Documentação e Dados Legais ---
                'document_paths' => json_encode([
                    'contract_pdf' => 'contracts/2025/contrato_001.pdf',
                    'bidding_notice_pdf' => 'bids/2025/edital_123.pdf',
                ]),
                'supplier_document' => '00.111.222/0001-33', // CNPJ do fornecedor
                'total_contract_value' => 150000.00,
                'legal_notes' => 'Contrato referente ao fornecimento de combustível para a frota municipal, oriundo do Pregão Eletrônico 123/2025.',
            ]);
        }
    }
}
