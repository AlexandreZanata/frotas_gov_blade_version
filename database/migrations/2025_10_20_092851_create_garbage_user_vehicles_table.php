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
        // Tabela pivot para associar GarbageUser aos Veículos permitidos
        Schema::create('garbage_user_vehicles', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Chave estrangeira para a tabela garbage_users
            $table->foreignUuid('garbage_user_id')
                ->constrained('garbage_users') // Nome da tabela garbage_users
                ->cascadeOnDelete();

            // Chave estrangeira para a tabela garbage_vehicles
            $table->foreignUuid('garbage_vehicle_id')
                ->constrained('garbage_vehicles') // Nome da tabela garbage_vehicles
                ->cascadeOnDelete();

            $table->timestamps(); // Opcional

            // Garante que a combinação de usuário e veículo seja única
            $table->unique(['garbage_user_id', 'garbage_vehicle_id'], 'user_vehicle_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('garbage_user_vehicles');
    }
};
