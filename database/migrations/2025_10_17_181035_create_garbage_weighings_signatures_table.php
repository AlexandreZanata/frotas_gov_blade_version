<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // O NOME CORRETO É 'garbage_weighing_signatures' (weighing no singular)
        Schema::create('garbage_weighing_signatures', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('garbage_weighing_id')->unique()->constrained('garbage_weighings')->cascadeOnDelete();
            $table->foreignUuid('operator_signature_id')->constrained('digital_signatures');
            $table->timestamp('operator_signed_at');
            $table->foreignUuid('admin_signature_id')->nullable()->constrained('digital_signatures');
            $table->timestamp('admin_signed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('garbage_weighing_signatures');
    }
};
