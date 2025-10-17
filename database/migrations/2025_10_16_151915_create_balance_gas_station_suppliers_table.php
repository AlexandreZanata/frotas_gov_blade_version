<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('balance_gas_station_suppliers', function (Blueprint $table) {
            // --- Chaves e Relacionamentos ---
            $table->uuid('id')->primary();
            $table->foreignUuid('gas_station_id')->unique()->constrained('gas_stations')->cascadeOnDelete();

            // --- Tipo e Identificação do Processo ---
            $table->enum('procurement_type', ['bidding', 'direct_contract'])
                ->comment('Tipo de contratação: Licitação (bidding) ou Contratação Direta (direct_contract)');

            $table->string('bidding_modality')->nullable()
                ->comment('Modalidade da licitação (ex: Pregão, Concorrência)');

            $table->string('process_number')->nullable()->unique()
                ->comment('Número do processo administrativo ou da licitação');

            // --- Detalhes do Contrato ---
            $table->string('contract_number')->nullable()->unique()
                ->comment('Número do contrato com o órgão público');

            $table->string('encrypted_contract_key')->unique()
                ->comment('Chave criptografada única para representar e validar o contrato digitalmente');

            $table->date('contract_start_date')->nullable()
                ->comment('Data de início da vigência do contrato');

            $table->date('contract_end_date')->nullable()
                ->comment('Data de fim da vigência do contrato');

            // --- Documentação e Arquivos ---
            $table->json('document_paths')->nullable()
                ->comment('Caminhos para os arquivos do contrato (PDFs, fotos, etc.) em formato JSON');

            // --- Informações Legais e Financeiras ---
            $table->string('supplier_document')->unique()
                ->comment('CNPJ do fornecedor, que pode ser diferente do posto específico');

            $table->decimal('total_contract_value', 15, 2)->nullable()
                ->comment('Valor global do contrato ou da ata de registro de preços');

            $table->text('legal_notes')->nullable()
                ->comment('Observações legais, justificativas ou informações adicionais do contrato');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balance_gas_station_suppliers');
    }
};
