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
        Schema::create('vehicle_tire_layouts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Ex: Carro (4 Pneus), Caminhão Truck (10 Pneus)');
            $table->json('layout_data')->comment('Estrutura JSON com posições e coordenadas para o diagrama');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_tire_layouts');
    }
};
