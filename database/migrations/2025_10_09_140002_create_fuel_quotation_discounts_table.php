<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fuel_quotation_discounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('fuel_quotation_id')->constrained('fuel_quotations')->cascadeOnDelete();
            $table->foreignUuid('fuel_type_id')->constrained('fuel_types')->cascadeOnDelete();
            $table->decimal('average_price', 10, 3); // Preço médio calculado
            $table->decimal('discount_percentage', 5, 2)->default(0); // Percentual de desconto
            $table->decimal('final_price', 10, 3); // Preço final com desconto
            $table->timestamps();

            $table->unique(['fuel_quotation_id', 'fuel_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuel_quotation_discounts');
    }
};

