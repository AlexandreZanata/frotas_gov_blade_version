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
        Schema::table('checklists', function (Blueprint $table) {
            $table->boolean('has_defects')->default(false)->after('notes');
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending')->after('has_defects');
            $table->foreignUuid('approver_id')->nullable()->constrained('users')->after('approval_status');
            $table->text('approver_comment')->nullable()->after('approver_id');
            $table->timestamp('approved_at')->nullable()->after('approver_comment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checklists', function (Blueprint $table) {
            $table->dropForeign(['approver_id']);
            $table->dropColumn(['has_defects', 'approval_status', 'approver_id', 'approver_comment', 'approved_at']);
        });
    }
};
