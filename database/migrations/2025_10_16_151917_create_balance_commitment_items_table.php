<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('balance_commitment_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('commitment_id')->constrained('balance_commitments')->cascadeOnDelete();
            $table->string('description');
            $table->decimal('quantity', 15, 3);
            $table->string('unit_of_measure');
            $table->decimal('unit_price', 15, 2);
            $table->decimal('total_price', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('balance_commitment_items');
    }
};
