<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('run_signatures', function (Blueprint $table) {
            $table->uuid('id')->primary();
            // Cada corrida tem um único registro de assinaturas
            $table->foreignUuid('run_id')->unique()->constrained('runs')->cascadeOnDelete();

            // Assinatura do Motorista
            $table->foreignUuid('driver_signature_id')->constrained('digital_signatures');
            $table->timestamp('driver_signed_at');

            // Assinatura do Administrador (pode ser nula até que ele assine)
            $table->foreignUuid('admin_signature_id')->nullable()->constrained('digital_signatures');
            $table->timestamp('admin_signed_at')->nullable();

            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('run_signatures'); }
};
