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
        // Cria a tabela para armazenar o total de despesas de combustível por posto de gasolina
        Schema::create('fuelings_gas_stations_expenses', function (Blueprint $table) {
            // Chave primária UUID
            $table->uuid('id')->primary();

            // Chave estrangeira para o posto de gasolina
            // Esta coluna também é única para garantir que haja apenas um registro de despesa por posto
            $table->foreignUuid('gas_station_id')
                ->unique()
                ->constrained('gas_stations') // Assume que sua tabela de postos se chama 'gas_stations'
                ->onDelete('cascade'); // Se o posto for deletado, o registro de despesa também será

            // Valor total acumulado de despesas para este posto
            $table->decimal('total_fuel_cost', 15, 2)->default(0.00);

            // Timestamps padrão (created_at e updated_at)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuelings_gas_stations_expenses');
    }
};
