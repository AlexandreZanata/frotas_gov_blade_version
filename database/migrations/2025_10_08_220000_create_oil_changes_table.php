<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('oil_changes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained('users'); // Quem registrou
            $table->foreignUuid('inventory_item_id')->nullable()->constrained('inventory_items'); // Tipo de óleo usado

            $table->unsignedInteger('km_at_change'); // KM no momento da troca
            $table->date('change_date'); // Data da troca
            $table->decimal('liters_used', 8, 2)->nullable(); // Litros utilizados
            $table->decimal('cost', 10, 2)->nullable(); // Custo da troca

            $table->unsignedInteger('next_change_km'); // KM prevista para próxima troca
            $table->date('next_change_date'); // Data prevista para próxima troca

            $table->text('notes')->nullable(); // Observações
            $table->string('service_provider')->nullable(); // Prestador de serviço

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('oil_changes');
    }
};

