<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('chat_message_read_receipts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('chat_message_id')->constrained('chat_messages')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete(); // Quem leu
            $table->timestamp('read_at')->useCurrent();
            $table->timestamps();
            $table->unique(['chat_message_id', 'user_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('chat_message_read_receipts'); }
};
