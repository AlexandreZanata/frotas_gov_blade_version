<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            // 1. Chave primária como UUID
            $table->uuid('id')->primary();

            // 2. Colunas básicas
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // 3. NOVOS CAMPOS ADICIONADOS
            $table->string('cpf')->unique();
            $table->string('cnh_number')->nullable();
            $table->date('cnh_expiry_date')->nullable();

            // 4. Chaves estrangeiras, agora usando foreignUuid()
            $table->foreignUuid('role_id')->constrained('roles')->onDelete('restrict');
            $table->foreignUuid('secretariat_id')->constrained('secretariats')->onDelete('restrict');

            // 5. Outros campos
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->rememberToken();
            $table->timestamps();
        });

        // Tabelas de sessão e reset de senha permanecem as mesmas
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
