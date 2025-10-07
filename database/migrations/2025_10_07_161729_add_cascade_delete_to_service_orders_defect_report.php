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
        Schema::table('service_orders', function (Blueprint $table) {
            // Remover a constraint antiga
            $table->dropForeign(['defect_report_id']);

            // Adicionar a constraint com cascade delete
            $table->foreign('defect_report_id')
                  ->references('id')
                  ->on('defect_reports')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_orders', function (Blueprint $table) {
            // Remover a constraint com cascade
            $table->dropForeign(['defect_report_id']);

            // Restaurar a constraint sem cascade
            $table->foreign('defect_report_id')
                  ->references('id')
                  ->on('defect_reports');
        });
    }
};
