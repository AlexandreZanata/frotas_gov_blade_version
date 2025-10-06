<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('defect_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['open', 'in_progress', 'resolved'])->default('open');
            $table->text('notes')->nullable(); // Observações gerais do motorista
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('defect_reports'); }
};
