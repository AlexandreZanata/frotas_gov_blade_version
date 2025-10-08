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
        Schema::table('runs', function (Blueprint $table) {
            // Remove o campo origin
            $table->dropColumn('origin');

            // Adiciona o campo stop_point (ponto de parada) - opcional
            $table->string('stop_point')->nullable()->after('destination');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('runs', function (Blueprint $table) {
            // Remove o campo stop_point
            $table->dropColumn('stop_point');

            // Restaura o campo origin
            $table->string('origin')->nullable()->after('destination');
        });
    }
};
