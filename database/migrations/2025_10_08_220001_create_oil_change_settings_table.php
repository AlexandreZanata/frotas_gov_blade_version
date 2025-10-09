<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('oil_change_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('vehicle_category_id')->nullable()->constrained('vehicle_categories')->onDelete('cascade');

            $table->unsignedInteger('km_interval')->default(10000); // Intervalo padrão em KM
            $table->unsignedInteger('days_interval')->default(180); // Intervalo padrão em dias
            $table->decimal('default_liters', 8, 2)->nullable(); // Litros padrão para esta categoria

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('oil_change_settings');
    }
};
