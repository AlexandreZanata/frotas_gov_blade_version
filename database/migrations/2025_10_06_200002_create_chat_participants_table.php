<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('chat_participants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('chat_room_id')->constrained('chat_rooms')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('last_read_at')->nullable(); // Última vez que o usuário leu as mensagens nesta sala
            $table->timestamps();
            $table->unique(['chat_room_id', 'user_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('chat_participants'); }
};
