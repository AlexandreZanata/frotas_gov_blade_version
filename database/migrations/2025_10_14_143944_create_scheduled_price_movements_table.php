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
        Schema::create('scheduled_price_movements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('scheduled_price_id')->constrained('scheduled_prices')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('action'); // ex: 'created', 'updated', 'deleted'
            $table->decimal('old_price', 10, 3)->nullable();
            $table->decimal('new_price', 10, 3)->nullable();
            $table->timestamp('action_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_price_movements');
    }
};
