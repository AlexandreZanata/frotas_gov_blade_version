<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('vehicle_transfers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('vehicle_id')->constrained('vehicles');
            $table->foreignUuid('origin_secretariat_id')->constrained('secretariats');
            $table->foreignUuid('destination_secretariat_id')->constrained('secretariats');
            $table->foreignUuid('requester_id')->constrained('users');
            $table->foreignUuid('approver_id')->nullable()->constrained('users');
            $table->enum('type', ['permanent', 'temporary'])->default('permanent');
            $table->enum('status', ['pending', 'approved', 'rejected', 'returned'])->default('pending');

            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();


            $table->timestamp('processed_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->text('request_notes')->nullable();
            $table->text('approver_notes')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('vehicle_transfers'); }
};
