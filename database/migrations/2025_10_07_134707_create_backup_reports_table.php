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
        Schema::create('backup_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->string('entity_type'); // Vehicle, ServiceOrder, etc
            $table->string('entity_id')->nullable();
            $table->string('entity_name');
            $table->string('file_path');
            $table->string('file_name');
            $table->bigInteger('file_size')->nullable();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable(); // dados adicionais do que foi deletado
            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backup_reports');
    }
};
