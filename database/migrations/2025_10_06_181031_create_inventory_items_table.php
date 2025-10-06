<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('category_id')->constrained('inventory_item_categories');
            $table->string('name'); // Ex: "Filtro de Óleo 5W30"
            $table->string('sku')->unique()->nullable(); // Código de barras/SKU
            $table->text('description')->nullable();
            $table->unsignedInteger('quantity_on_hand')->default(0); // Saldo atual
            $table->string('unit_of_measure'); // Ex: "unidade", "litro", "caixa"
            $table->unsignedInteger('reorder_level')->default(5); // Nível para alerta de estoque baixo
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('inventory_items'); }
};
