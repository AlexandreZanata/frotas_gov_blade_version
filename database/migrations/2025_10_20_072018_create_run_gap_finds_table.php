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
        Schema::create('run_gap_finds', function (Blueprint $table) {
            // Chave primária UUID
            $table->uuid('id')->primary();

            // Chave estrangeira para a corrida onde a diferença foi detectada
            // Usando cascadeOnDelete para remover o registro de gap se a corrida for deletada
            $table->foreignUuid('run_id')->constrained('runs')->cascadeOnDelete();

            // Chave estrangeira para o veículo
            // Usando cascadeOnDelete para remover o registro de gap se o veículo for deletado
            $table->foreignUuid('vehicle_id')->constrained('vehicles')->cascadeOnDelete();

            // Chave estrangeira para o usuário que registrou a corrida com KM divergente
            // Usando cascadeOnDelete para remover o registro de gap se o usuário for deletado
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();

            // Quilometragem inicial registrada pelo usuário
            $table->unsignedBigInteger('recorded_start_km');

            // Quilometragem final da última corrida (o que era esperado como inicial)
            $table->unsignedBigInteger('expected_start_km');

            // A diferença calculada (pode ser positiva ou negativa se o usuário digitou um valor menor)
            // Usamos ->integer() ou ->bigInteger() dependendo da magnitude esperada.
            // O tipo ->integer() padrão geralmente é suficiente.
            $table->integer('gap_km');

            // Timestamps padrão do Laravel
            $table->timestamps();

            // Índices para otimizar consultas
            $table->index('vehicle_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('run_gap_finds');
    }
};
