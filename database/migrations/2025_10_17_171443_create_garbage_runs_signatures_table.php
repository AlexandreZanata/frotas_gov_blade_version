<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('garbage_runs_signatures', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('garbage_run_id')->unique()->constrained('garbage_runs')->cascadeOnDelete();
            $table->foreignUuid('driver_signature_id')->constrained('digital_signatures');
            $table->timestamp('driver_signed_at');
            $table->foreignUuid('admin_signature_id')->nullable()->constrained('digital_signatures');
            $table->timestamp('admin_signed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('garbage_runs_signatures');
    }
};
