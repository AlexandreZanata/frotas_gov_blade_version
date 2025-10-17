<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_price_origins', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('vehicle_id')->unique()->constrained('vehicles')->cascadeOnDelete();
            $table->decimal('amount', 15, 2)->comment('Valor que o veículo custou para ser adquirido');
            $table->date('acquisition_date')->comment('Data da aquisição');
            $table->string('acquisition_type')->nullable()->comment('Ex: Compra, Doação');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_price_origins');
    }
};
