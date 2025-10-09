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
        // Tabela de Métodos de Cálculo Personalizados
        Schema::create('fuel_calculation_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('fuel_type_id')->constrained('fuel_types')->onDelete('cascade');
            $table->string('name'); // Nome do método (ex: "Média Simples", "Média Ponderada")
            $table->text('formula')->nullable(); // Fórmula personalizada
            $table->string('calculation_type')->default('average'); // average, weighted_average, custom
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Tabela de Descontos Personalizados
        Schema::create('fuel_discount_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('fuel_type_id')->constrained('fuel_types')->onDelete('cascade');
            $table->string('name'); // Nome do desconto (ex: "Desconto Volume", "Desconto Contrato")
            $table->decimal('percentage', 5, 2)->default(0); // Porcentagem de desconto
            $table->decimal('fixed_value', 10, 2)->default(0); // Valor fixo de desconto
            $table->string('discount_type')->default('percentage'); // percentage, fixed, custom
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Adicionar campos de imagens à tabela fuel_quotation_prices
        // Verifique se a tabela 'fuel_quotation_prices' existe antes de tentar alterá-la.
        if (Schema::hasTable('fuel_quotation_prices')) {
            Schema::table('fuel_quotation_prices', function (Blueprint $table) {
                $table->string('image_1')->nullable()->after('price');
                $table->string('image_2')->nullable()->after('image_1');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('fuel_quotation_prices')) {
            Schema::table('fuel_quotation_prices', function (Blueprint $table) {
                if (Schema::hasColumn('fuel_quotation_prices', 'image_1')) {
                    $table->dropColumn('image_1');
                }
                if (Schema::hasColumn('fuel_quotation_prices', 'image_2')) {
                    $table->dropColumn('image_2');
                }
            });
        }

        Schema::dropIfExists('fuel_discount_settings');
        Schema::dropIfExists('fuel_calculation_methods');
    }
};
