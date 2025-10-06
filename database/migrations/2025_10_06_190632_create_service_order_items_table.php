<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('service_order_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('service_order_id')->constrained('service_orders')->cascadeOnDelete();
            $table->foreignUuid('inventory_item_id')->nullable()->constrained('inventory_items'); // Peça do estoque (opcional)
            $table->string('description'); // Descrição da peça ou serviço
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('service_order_items'); }
};
