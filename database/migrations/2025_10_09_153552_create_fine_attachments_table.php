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
        Schema::create('fine_attachments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('infraction_id')->nullable()->constrained('infractions')->cascadeOnDelete();
            $table->foreignUuid('fine_id')->constrained('fines')->cascadeOnDelete();
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type'); // 'proof', 'boleto', 'document'
            $table->integer('file_size')->nullable();
            $table->string('mime_type')->nullable();
            $table->foreignUuid('uploaded_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fine_attachments');
    }
};
