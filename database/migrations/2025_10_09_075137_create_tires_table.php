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
        Schema::create('tires', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // --- CONEXÃO COM O ESTOQUE ---
            $table->foreignUuid('inventory_item_id')->constrained('inventory_items')->comment('Link para o tipo de pneu no estoque');

            $table->string('brand')->comment('Marca do pneu');
            $table->string('model')->comment('Modelo do pneu');
            $table->string('serial_number')->unique()->comment('Número de série ou de fogo');
            $table->string('dot_number')->nullable()->comment('Código DOT do pneu');
            $table->date('purchase_date')->comment('Data da compra');
            $table->decimal('purchase_price', 10, 2)->nullable()->comment('Valor de compra');
            $table->integer('lifespan_km')->comment('Vida útil estimada em KM');
            $table->integer('current_km')->default(0)->comment('KM rodados pelo pneu');
            $table->enum('status', ['Em Estoque', 'Em Uso', 'Em Manutenção', 'Recapagem', 'Descartado'])->default('Em Estoque');
            $table->enum('condition', ['Novo', 'Bom', 'Atenção', 'Crítico'])->default('Novo');
            $table->foreignUuid('current_vehicle_id')->nullable()->constrained('vehicles')->onDelete('set null');
            $table->integer('current_position')->nullable()->comment('Posição no veículo (ex: 1=D.E, 2=D.D, ...)');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tires');
    }
};
