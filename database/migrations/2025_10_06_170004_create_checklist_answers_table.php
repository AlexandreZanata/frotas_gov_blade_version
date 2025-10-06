<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checklist_answers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('checklist_id')->constrained('checklists')->cascadeOnDelete();
            $table->foreignUuid('checklist_item_id')->constrained('checklist_items')->cascadeOnDelete();
            $table->enum('status', ['ok', 'attention', 'problem']);
            $table->text('notes')->nullable(); // Observação específica para este item
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checklist_answers');
    }
};
