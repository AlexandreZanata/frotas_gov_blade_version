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
        Schema::create('infractions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('fine_id')->constrained('fines')->cascadeOnDelete();
            $table->string('infraction_code'); // Código da infração (ex: 501-00)
            $table->string('description');
            $table->decimal('base_amount', 10, 2); // Valor base da infração
            $table->decimal('extra_fees', 10, 2)->default(0); // Taxas extras
            $table->decimal('discount_amount', 10, 2)->default(0); // Desconto em valor fixo
            $table->decimal('discount_percentage', 5, 2)->default(0); // Desconto em percentual
            $table->decimal('final_amount', 10, 2); // Valor final calculado
            $table->integer('points')->default(0); // Pontos na CNH
            $table->enum('severity', ['leve', 'media', 'grave', 'gravissima'])->default('media');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('infractions');
    }
};
