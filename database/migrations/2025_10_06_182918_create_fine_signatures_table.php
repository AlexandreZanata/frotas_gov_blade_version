<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('fine_signatures', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('fine_id')->unique()->constrained('fines')->cascadeOnDelete();
            $table->foreignUuid('digital_signature_id')->constrained('digital_signatures');
            $table->timestamp('signed_at');
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('fine_signatures'); }
};
