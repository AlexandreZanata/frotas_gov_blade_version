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
        // Verifica se a tabela já existe (por segurança)
        if (!Schema::hasTable('fueling_view_logs')) {
            Schema::create('fueling_view_logs', function (Blueprint $table) {
                $table->char('id', 36)->primary();
                $table->char('fueling_id', 36);
                $table->char('user_id', 36);
                $table->timestamp('viewed_at')->useCurrent();
                $table->string('ip_address', 255)->nullable();
                $table->text('user_agent')->nullable();

                // Índices com nomes únicos
                $table->index('fueling_id', 'fueling_view_logs_fueling_id_index');
                $table->index('user_id', 'fueling_view_logs_user_id_index');
                $table->index('viewed_at', 'fueling_view_logs_viewed_at_index');
            });

            // Adiciona as foreign keys separadamente para evitar conflitos
            Schema::table('fueling_view_logs', function (Blueprint $table) {
                $table->foreign('fueling_id', 'fueling_view_logs_fueling_id_foreign')
                    ->references('id')
                    ->on('fuelings')
                    ->onDelete('cascade');

                $table->foreign('user_id', 'fueling_view_logs_user_id_foreign')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove as foreign keys primeiro
        Schema::table('fueling_view_logs', function (Blueprint $table) {
            $table->dropForeign('fueling_view_logs_fueling_id_foreign');
            $table->dropForeign('fueling_view_logs_user_id_foreign');
        });

        // Depois dropa a tabela
        Schema::dropIfExists('fueling_view_logs');
    }
};
