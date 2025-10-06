<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('inventory_item_id')->constrained('inventory_items')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users'); // Quem fez a movimentação
            $table->enum('type', ['in', 'out', 'adjustment']); // Entrada, Saída, Ajuste
            $table->integer('quantity'); // Positivo para entrada, negativo para saída
            $table->string('reason')->nullable(); // Ex: "Compra NF-123", "Uso na OS-456", "Ajuste de contagem"
            $table->timestamp('movement_date');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('inventory_movements'); }
};
