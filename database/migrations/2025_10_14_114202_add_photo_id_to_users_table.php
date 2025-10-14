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
        Schema::table('users', function (Blueprint $table) {
            // Adicionar a coluna photo_id como UUID (compatível com a tabela user_photos)
            $table->uuid('photo_id')
                ->nullable()
                ->after('secretariat_id');

            // Adicionar a foreign key constraint
            $table->foreign('photo_id')
                ->references('id')
                ->on('user_photos')
                ->onDelete('set null');

            // Adicionar índice para melhor performance
            $table->index('photo_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remover a foreign key e a coluna
            $table->dropForeign(['photo_id']);
            $table->dropIndex(['photo_id']);
            $table->dropColumn('photo_id');
        });
    }
};
