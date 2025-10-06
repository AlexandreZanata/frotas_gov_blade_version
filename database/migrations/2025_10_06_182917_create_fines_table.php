<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('fines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('vehicle_id')->constrained('vehicles');
            $table->foreignUuid('driver_id')->constrained('users'); // O condutor que cometeu a infração
            $table->foreignUuid('registered_by_user_id')->constrained('users'); // Quem cadastrou no sistema

            $table->string('infraction_code')->nullable(); // Código da infração (ex: 501-00)
            $table->text('description');
            $table->string('location')->nullable();
            $table->timestamp('issued_at'); // Data e hora da infração

            $table->decimal('amount', 10, 2);
            $table->date('due_date')->nullable(); // Data de vencimento

            $table->enum('status', ['pending_acknowledgement', 'pending_payment', 'paid', 'appealed', 'cancelled'])->default('pending_acknowledgement');

            $table->string('attachment_path')->nullable(); // Anexo do auto de infração
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('fines'); }
};
