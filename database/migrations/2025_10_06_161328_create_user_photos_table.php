<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_photos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();

            // Tipo da foto (perfil, cnh_frente, cnh_verso, etc.)
            $table->string('photo_type');

            // Caminho para o arquivo no storage
            $table->string('path');

            $table->timestamps();

            // Garante que um usuário só pode ter um tipo de foto (ex: apenas uma foto de perfil)
            $table->unique(['user_id', 'photo_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_photos');
    }
};
