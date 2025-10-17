<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('balance_expenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('secretariat_id')->constrained('secretariats');
            $table->foreignUuid('user_id')->constrained('users');
            $table->string('description');
            $table->decimal('amount', 15, 2);
            $table->date('expense_date');
            $table->string('type'); // Ex: 'fuel', 'maintenance', 'other'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('balance_expenses');
    }
};
