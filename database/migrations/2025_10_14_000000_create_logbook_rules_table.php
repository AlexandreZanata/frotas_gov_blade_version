<?php
// database/migrations/2025_10_14_000000_create_logbook_rules_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('logbook_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->enum('rule_type', ['fixed', 'formula']);
            $table->integer('fixed_value')->nullable(); // Valor fixo em km
            $table->enum('formula_type', ['daily_average_plus_fixed', 'daily_average_plus_percentage'])->nullable();
            $table->integer('formula_value')->nullable(); // Valor da fórmula (km ou percentual)
            $table->enum('target_type', ['global', 'vehicle_category', 'user', 'vehicle']);
            $table->uuid('target_id')->nullable(); // ID do alvo (categoria, usuário ou veículo)
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Índices
            $table->index(['target_type', 'target_id']);
            $table->index('is_active');
        });
    }

    public function down()
    {
        Schema::dropIfExists('logbook_rules');
    }
};
