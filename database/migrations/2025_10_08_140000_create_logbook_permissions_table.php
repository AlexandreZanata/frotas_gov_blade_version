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
        Schema::create('logbook_permissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');

            // Escopo da permissão: 'all' = todas secretarias, 'specific' = específica, 'vehicles' = veículos específicos
            $table->enum('scope', ['all', 'secretariat', 'vehicles'])->default('vehicles');

            // Se scope = 'secretariat', referencia a secretaria
            $table->foreignUuid('secretariat_id')->nullable()->constrained('secretariats')->onDelete('cascade');

            // Descrição/observação da permissão
            $table->text('description')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Índices para performance
            $table->index(['user_id', 'is_active']);
            $table->index('scope');
        });

        // Tabela pivot para veículos específicos (quando scope = 'vehicles')
        Schema::create('logbook_permission_vehicles', function (Blueprint $table) {
            $table->foreignUuid('logbook_permission_id')->constrained('logbook_permissions')->onDelete('cascade');
            $table->foreignUuid('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->timestamps();

            // Chave primária composta
            $table->primary(['logbook_permission_id', 'vehicle_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logbook_permission_vehicles');
        Schema::dropIfExists('logbook_permissions');
    }
};
