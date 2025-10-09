<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fuel_pump_prices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('fuel_quotation_id')->constrained('fuel_quotations')->cascadeOnDelete();
            $table->foreignUuid('gas_station_id')->constrained('gas_stations')->cascadeOnDelete();
            $table->foreignUuid('fuel_type_id')->constrained('fuel_types')->cascadeOnDelete();
            $table->decimal('pump_price', 10, 3); // Preço de bomba
            $table->string('evidence_path')->nullable(); // Caminho da foto/comprovante
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuel_pump_prices');
    }
};

