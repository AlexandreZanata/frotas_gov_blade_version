<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('garbage_maintenance_tare_vehicles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('garbage_vehicle_id')->constrained('garbage_vehicles')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users'); // Usuário que registrou a calibração
            $table->decimal('tare_weight_kg', 10, 2); // Peso da tara em KG
            $table->timestamp('calibrated_at'); // Data da calibração
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('garbage_maintenance_tare_vehicles');
    }
};
