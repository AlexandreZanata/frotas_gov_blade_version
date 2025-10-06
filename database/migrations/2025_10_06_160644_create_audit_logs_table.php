<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Quem realizou a ação (pode ser nulo para ações do sistema)
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('action'); // Ex: 'created', 'updated', 'deleted'

            // Relação polimórfica para identificar o registro auditado (ex: um Veículo, um Usuário)
            $table->uuidMorphs('auditable'); // Cria `auditable_id` (UUID) e `auditable_type` (string)

            $table->json('old_values')->nullable(); // Valores antes da mudança
            $table->json('new_values')->nullable(); // Valores depois da mudança
            $table->text('description')->nullable(); // Descrição manual, se necessário

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
