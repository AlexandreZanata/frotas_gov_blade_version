<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('garbage_runs', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('vehicle_id')->constrained('garbage_vehicles'); // Verifique se esta tabela existe como 'garbage_vehicles'
            $table->foreignUuid('user_id')->constrained('garbage_users');       // Verifique se esta tabela existe como 'garbage_users'
            $table->foreignUuid('weighing_id')->nullable()->constrained('garbage_weighings');

            $table->unsignedBigInteger('start_km')->nullable();
            $table->unsignedBigInteger('end_km')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->string('stop_point')->nullable();
            $table->enum('status', ['in_progress', 'completed'])->default('in_progress');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('garbage_runs');
    }
};
