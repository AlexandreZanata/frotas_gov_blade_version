<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_price_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained('users');
            $table->enum('type', ['credit', 'debit'])->comment('credit para valor de origem, debit para gastos');
            $table->decimal('amount', 15, 2);
            $table->string('description');

            $table->uuid('transactionable_id');
            $table->string('transactionable_type');
            $table->index(['transactionable_type', 'transactionable_id'], 'vph_transactionable_index'); // Nome do índice encurtado

            $table->timestamp('transaction_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_price_histories');
    }
};
