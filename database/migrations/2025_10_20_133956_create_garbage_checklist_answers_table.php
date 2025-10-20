<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('garbage_checklist_answers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('garbage_checklist_id')->constrained('garbage_checklists')->cascadeOnDelete(); // FK para garbage_checklists
            $table->foreignUuid('garbage_checklist_item_id')->constrained('garbage_checklist_items')->cascadeOnDelete(); // FK para garbage_checklist_items
            $table->enum('status', ['ok', 'attention', 'problem']);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('garbage_checklist_answers');
    }
};
