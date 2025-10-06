<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('service_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('defect_report_id')->unique()->constrained('defect_reports');
            $table->foreignUuid('vehicle_id')->constrained('vehicles');
            $table->foreignUuid('mechanic_id')->constrained('users'); // Mecânico responsável

            // Status Geral do Serviço
            $table->string('status')->default('pending_quote'); // pending_quote, in_progress, completed, cancelled

            // Orçamento
            $table->enum('quote_status', ['draft', 'pending_approval', 'approved', 'rejected'])->default('draft');
            $table->decimal('quote_total_amount', 10, 2)->nullable();
            $table->foreignUuid('quote_approver_id')->nullable()->constrained('users'); // Gestor que aprovou
            $table->timestamp('quote_approved_at')->nullable();
            $table->text('approver_notes')->nullable();

            // Cronômetro do Serviço
            $table->timestamp('service_started_at')->nullable();
            $table->timestamp('service_completed_at')->nullable();

            $table->text('mechanic_notes')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('service_orders'); }
};
