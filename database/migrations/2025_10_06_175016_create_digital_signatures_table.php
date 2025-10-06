<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('digital_signatures', function (Blueprint $table) {
            $table->uuid('id')->primary();
            // Cada usuário tem apenas uma assinatura digital
            $table->foreignUuid('user_id')->unique()->constrained('users')->cascadeOnDelete();
            // Campo para armazenar o código seguro da assinatura (pode ser um hash, chave pública, etc.)
            $table->text('signature_code');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('digital_signatures'); }
};
