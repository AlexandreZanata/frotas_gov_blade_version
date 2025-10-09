<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fine_view_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('fine_id')->constrained('fines')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users');
            $table->timestamp('viewed_at');
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fine_view_logs');
    }
};
