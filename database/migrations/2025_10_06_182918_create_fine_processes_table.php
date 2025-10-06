<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('fine_processes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('fine_id')->constrained('fines')->cascadeOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained('users'); // Usuário que executou a ação (pode ser nulo para ações do sistema)

            $table->string('stage'); // Ex: "Cadastrada", "Notificada ao Condutor", "Ciência Confirmada", "Encaminhada ao PAD"
            $table->text('notes')->nullable(); // Detalhes da etapa

            $table->timestamp('created_at')->useCurrent();
        });
    }
    public function down(): void { Schema::dropIfExists('fine_processes'); }
};
