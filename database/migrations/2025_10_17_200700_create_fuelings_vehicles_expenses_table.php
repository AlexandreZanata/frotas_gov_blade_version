<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fuelings_vehicles_expenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            // Garante que cada veículo tenha apenas um registro de despesa total
            $table->foreignUuid('vehicle_id')->unique()->constrained('vehicles')->cascadeOnDelete();
            $table->decimal('total_fuel_cost', 15, 2)->default(0.00); // Acumula o custo total
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuelings_vehicles_expenses');
    }
};
