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
        // Criar tabela pivot para múltiplas secretarias
        Schema::create('logbook_permission_secretariats', function (Blueprint $table) {
            $table->foreignUuid('logbook_permission_id')->constrained('logbook_permissions')->onDelete('cascade');
            $table->foreignUuid('secretariat_id')->constrained('secretariats')->onDelete('cascade');
            $table->timestamps();

            // Chave primária composta
            $table->primary(['logbook_permission_id', 'secretariat_id'], 'logbook_perm_secretariat_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logbook_permission_secretariats');
    }
};

