<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('balance_supply_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('commitment_id')->constrained('balance_commitments');
            $table->foreignUuid('user_id')->constrained('users'); // Usuário que solicitou
            $table->string('number')->unique();
            $table->date('date');
            $table->decimal('value', 15, 2);
            $table->enum('status', ['pending', 'authorized', 'delivered', 'canceled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('balance_supply_orders');
    }
};
