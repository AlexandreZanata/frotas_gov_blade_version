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
        Schema::create('default_passwords', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique(); // Nome identificador (ex: "reset_password", "default_driver")
            $table->string('password'); // Senha criptografada
            $table->text('description')->nullable(); // Descrição do uso
            $table->boolean('is_active')->default(true); // Se está ativa
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('default_passwords');
    }
};
