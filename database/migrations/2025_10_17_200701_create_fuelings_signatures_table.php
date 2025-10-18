<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fuelings_signatures', function (Blueprint $table) {
            $table->uuid('id')->primary();
            // Cada abastecimento tem um único registro de assinatura
            $table->foreignUuid('fueling_id')->unique()->constrained('fuelings')->cascadeOnDelete();

            // Assinatura do Motorista/Usuário que abasteceu
            $table->foreignUuid('driver_signature_id')->constrained('digital_signatures');
            $table->timestamp('driver_signed_at');

            // Assinatura do Administrador (opcional, para confirmação)
            $table->foreignUuid('admin_signature_id')->nullable()->constrained('digital_signatures');
            $table->timestamp('admin_signed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuelings_signatures');
    }
};
