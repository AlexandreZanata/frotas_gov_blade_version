<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('garbage_checklists', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('garbage_run_id')->constrained('garbage_runs')->cascadeOnDelete(); // FK para garbage_runs
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->boolean('has_defects')->default(false);
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignUuid('approver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('approver_comment')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('garbage_checklists');
    }
};
