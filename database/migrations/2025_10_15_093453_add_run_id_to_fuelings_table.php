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
        Schema::table('fuelings', function (Blueprint $table) {
            // Adiciona a coluna run_id como nullable para não quebrar registros existentes
            $table->uuid('run_id')->nullable()->after('vehicle_id');

            // Adiciona a chave estrangeira
            $table->foreign('run_id')
                ->references('id')
                ->on('runs')
                ->onDelete('set null'); // Se a corrida for deletada, seta como null

            // Adiciona índice para melhor performance
            $table->index('run_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fuelings', function (Blueprint $table) {
            // Remove a chave estrangeira e o índice primeiro
            $table->dropForeign(['run_id']);
            $table->dropIndex(['run_id']);

            // Remove a coluna
            $table->dropColumn('run_id');
        });
    }
};
