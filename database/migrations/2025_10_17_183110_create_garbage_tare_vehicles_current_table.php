<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('garbage_tare_vehicles_current', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('garbage_vehicle_id')->unique()->constrained('garbage_vehicles')->cascadeOnDelete();

            $table->foreignUuid('garbage_maintenance_tare_vehicle_id');

            $table->foreign('garbage_maintenance_tare_vehicle_id', 'current_tare_maintenance_fk')
                ->references('id')
                ->on('garbage_maintenance_tare_vehicles');

            $table->decimal('tare_weight_kg', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('garbage_tare_vehicles_current');
    }
};
