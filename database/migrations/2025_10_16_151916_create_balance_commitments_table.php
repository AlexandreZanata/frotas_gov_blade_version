<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('balance_commitments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('secretariat_id')->constrained('secretariats');
            $table->foreignUuid('supplier_id')->constrained('balance_gas_station_suppliers');


            $table->string('commitment_number')->unique()->comment('Número da nota de empenho');
            $table->year('year')->comment('Ano de exercício do empenho');
            $table->date('commitment_date')->comment('Data de emissão do empenho');

            $table->decimal('total_amount', 15, 2)->comment('Valor total original do empenho');
            $table->decimal('balance', 15, 2)->comment('Saldo atualizado e disponível do empenho');

            $table->text('description')->nullable();


            $table->enum('status', ['pending', 'approved', 'partially_used', 'exhausted', 'canceled'])
                ->default('pending')
                ->comment('Status: pendente, aprovado, parcialmente utilizado, zerado, cancelado');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('balance_commitments');
    }
};
