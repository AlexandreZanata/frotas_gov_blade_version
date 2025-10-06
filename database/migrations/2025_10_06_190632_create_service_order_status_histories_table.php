<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('service_order_status_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('service_order_id')->constrained('service_orders')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users'); // Usuário que atualizou a etapa
            $table->string('stage'); // "Diagnóstico", "Aguardando Peças", "Serviço em Execução"
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }
    public function down(): void { Schema::dropIfExists('service_order_status_histories'); }
};
